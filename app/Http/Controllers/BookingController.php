<?php

namespace App\Http\Controllers;

use App\Imports\CustomersImport;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Event;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Booking::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $filename = $request->hasFile('file') ? $request->file('file') : null;

        $customers = [];
        $temporalCustomers = [];
        $excelErrors = [];

        if (!empty($filename)) {
            $collectionExcel = Excel::toCollection(new CustomersImport, $filename);

            if ($collectionExcel->count() > 0) {
                if (count($collectionExcel[0][0]) == 10) {
                    if ($collectionExcel[0][0]->has('tipo_documento') & $collectionExcel[0][0]->has('documento') & $collectionExcel[0][0]->has('nombre') & $collectionExcel[0][0]->has('sexo') & $collectionExcel[0][0]->has('edad') & $collectionExcel[0][0]->has('telefono') & $collectionExcel[0][0]->has('correo') & $collectionExcel[0][0]->has('provincia') & $collectionExcel[0][0]->has('distrito') & $collectionExcel[0][0]->has('corregimiento')) {
                        $message = 'El archivo tiene el formato correcto';
                    } else {
                        return response()->json([
                            'message' => 'El archivo no tiene el formato correcto',
                            'type' => 'format'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'El archivo no tiene el formato correcto',
                        'type' => 'format'
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'El archivo no tiene el formato correcto',
                    'type' => 'format'
                ], 400);
            }

            collect($collectionExcel[0])->each(function ($item, $key) use (&$customers, &$temporalCustomers, &$excelErrors) {

                if ($item['tipo_documento'] == 'Cédula' || substr($item['tipo_documento'], 0, 1) == 'C') {
                    $item['tipo_documento'] = 'C';
                } elseif ($item['tipo_documento'] == 'Pasaporte' || substr($item['tipo_documento'], 0, 1) == 'P') {
                    $item['tipo_documento'] = 'P';
                } elseif ($item['tipo_documento'] == 'RUC' || substr($item['tipo_documento'], 0, 1) == 'R') {
                    $item['tipo_documento'] = 'R';
                } else {
                    $excelErrors['tipo_documento'][] = $key + 1;
                }


                if (count($excelErrors) == 0) {

                    $customer = Customer::where([
                        ['document_number', '=', $item['documento']],
                        ['document_type', '=', $item['tipo_documento']],
                        ['active', '=', 1]
                    ])->first();
                } else {
                    $customer = null;
                }

                if ($customer == null) {
                    $provinces = Http::get('http://127.0.0.1:8001/api/provinces')->collect();
                    $districts = Http::get('http://127.0.0.1:8001/api/districts')->collect();
                    $townships = Http::get('http://127.0.0.1:8001/api/townships')->collect();

                    if ($item['sexo'] == 'Masculino' || substr($item['sexo'], 0, 1) == 'M') {
                        $type_sex_id = 1;
                    } else if ($item['sexo'] == 'Femenino' || substr($item['sexo'], 0, 1) == 'F') {
                        $type_sex_id = 2;
                    } else {
                        $excelErrors['sexo'][] = $key + 1;
                    }

                    if (is_numeric($item['edad'])) {
                        $age_range_id = null;
                        if ($item['edad'] <= 18) {
                            $age_range_id = 1; //1 - 18
                        } else if ($item['edad']  > 18 && $item['edad'] <= 26) {
                            $age_range_id = 2; //19 - 26
                        } else if ($item['edad']  > 26 && $item['edad']  <= 35) {
                            $age_range_id = 3; //26 - 35
                        } else {
                            $age_range_id = 4; //36 +
                        }
                    } else {
                        $excelErrors['edad'][] = $key + 1;
                    }

                    $email = null;
                    if($item['correo'] != null){
                        if (filter_var($item['correo'], FILTER_VALIDATE_EMAIL)) {
                            $email = $item['correo'];
                        }
    
                        $resul = Customer::where('email', $item['correo'])->first();
    
                        if ($resul) {
                            $excelErrors['correo'][] = $key + 1;
                        } else {
                            $email = $item['correo'];
                        }
                    }

                    $province_id = null;
                    $district_id = null;
                    $township_id = null;
                    $provinces->each(function ($province, $key) use ($item, &$province_id) {
                        if ($item['provincia'] == $province['name']) {
                            $province_id = $province['id'];
                        }
                    });

                    $districts->each(function ($district, $key) use ($item, &$district_id) {
                        if ($item['distrito'] == $district['name']) {
                            $district_id = $district['id'];
                        }
                    });

                    $townships->each(function ($township, $key) use ($item, &$township_id) {
                        if ($item['corregimiento'] == $township['name']) {
                            $township_id = $township['id'];
                        }
                    });

                    if ($province_id == null || $district_id == null || $township_id == null) {
                        $excelErrors['direccion'][] = $key + 1;
                    }

                    if (count($excelErrors) === 0) {

                        $temporalCustomers[] = [
                            'document_type' => $item['tipo_documento'],
                            'document_number' => $item['documento'],
                            'name' => $item['nombre'],
                            'type_sex_id' => $type_sex_id,
                            'age_range_id' => $age_range_id,
                            'telephone' => $item['telefono'],
                            'email' => $email,
                            'province_id' => $province_id,
                            'district_id' => $district_id,
                            'township_id' => $township_id,
                        ];
                    }
                } else {
                    $customers[] = $customer->id;
                }
            });
            
            if (count($excelErrors) > 0) {
                return response()->json([
                    'errors' => $excelErrors
                ], 422);
            } else {
                if (count($temporalCustomers) > 0) {
                    foreach ($temporalCustomers as $temporalCustomer) {
                        $customer = Customer::create($temporalCustomer);
                        $customers[] = $customer->id;
                    }
                }
            }
        }

        $booking = Booking::create([
            'reason_visit_id' => $request->reason_visit_id,
            'type' => $request->type,
            'date' => $request->date,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'name' => $request->name,
        ]);

        if (!empty($customers)) {
            $booking->customers()->attach($customers);
        }

        if (!empty(request()->areas)) {
            $booking->areas()->attach(request()->areas);
        }
    }

    public function storePut(Request $request)
    {

        $filename = $request->hasFile('file') ? $request->file('file') : null;
        $customers = [];

        if (!empty($filename)) {
            $collectionExcel = Excel::toCollection(new CustomersImport, $filename);
            collect($collectionExcel[0])->each(function ($item, $key) use (&$customers) {

                $customer = Customer::where('document_number', '=', $item['documento'])->first();
                if ($customer == null) {
                    $provinces = Http::get('http://127.0.0.1:8001/api/provinces')->collect();
                    $districts = Http::get('http://127.0.0.1:8001/api/districts')->collect();
                    $townships = Http::get('http://127.0.0.1:8001/api/townships')->collect();

                    if ($item['tipo_documento'] == 'Cédula' || substr($item['tipo_documento'], 0, 1) == 'C') {
                        $item['tipo_documento'] = 'C';
                    } elseif ($item['tipo_documento'] == 'Pasaporte' || substr($item['tipo_documento'], 0, 1) == 'P') {
                        $item['tipo_documento'] = 'P';
                    } else {
                        $item['tipo_documento'] = 'R';
                    }

                    if ($item['sexo'] == 'Masculino' || substr($item['sexo'], 0, 1) == 'M') {
                        $type_sex_id = 1;
                    } else {
                        $type_sex_id = 2;
                    }

                    $age_range_id = null;
                    if ($item['edad'] <= 18) {
                        $age_range_id = 1; //1 - 18
                    } else if ($item['edad']  > 18 && $item['edad'] <= 26) {
                        $age_range_id = 2; //19 - 26
                    } else if ($item['edad']  > 26 && $item['edad']  <= 35) {
                        $age_range_id = 3; //26 - 35
                    } else {
                        $age_range_id = 4; //36 +
                    }

                    $province_id = null;
                    $district_id = null;
                    $township_id = null;
                    $provinces->each(function ($province, $key) use ($item, &$province_id) {
                        if ($item['provincia'] == $province['name']) {
                            $province_id = $province['id'];
                        }
                    });

                    $districts->each(function ($district, $key) use ($item, &$district_id) {
                        if ($item['distrito'] == $district['name']) {
                            $district_id = $district['id'];
                        }
                    });

                    $townships->each(function ($township, $key) use ($item, &$township_id) {
                        if ($item['corregimiento'] == $township['name']) {
                            $township_id = $township['id'];
                        }
                    });

                    $result = Customer::create([
                        'document_type' => $item['tipo_documento'],
                        'document_number' => $item['documento'],
                        'name' => $item['nombre'],
                        'type_sex_id' => $type_sex_id,
                        'age_range_id' => $age_range_id,
                        'telephone' => $item['telefono'],
                        'email' => $item['correo'],
                        'province_id' => $province_id,
                        'district_id' => $district_id,
                        'township_id' => $township_id,
                    ]);

                    $customers[] = $result->id;
                } else {
                    $customers[] = $customer->id;
                }
            });
        }

        $booking = Booking::where('id', $request->id)->update([
            'reason_visit_id' => $request->reason_visit_id,
            'type' => $request->type,
            'date' => $request->date,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        $booking = Booking::find($request->id);

        if (!empty($customers)) {
            $booking->customers()->sync($customers);
        }

        if (!empty($request->areas)) {
            $booking->areas()->detach();
            $booking->areas()->attach($request->areas);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
    }

    public function pdf(Booking $booking)
    {
        $customersBooking = $booking->customers;

        $provinces = Http::get('http://127.0.0.1:8001/api/provinces')->collect();

        foreach ($customersBooking as $customer) {
            $result = $provinces->where('id', $customer->province_id)->first();
            $customer->province_id = $result['name'];
        }

        $pdf = PDF::loadView('customersBooking', compact('booking', 'customersBooking'));

        return $pdf->stream();
    }

    public function showSchedule($startStr, $endDate,)
    {
        $bookings = Booking::with('areas', 'customers')->where('date', '>=', $startStr)->where('date', '<=', $endDate)->join('reason_visits', 'reason_visits.id', '=', 'bookings.reason_visit_id')->select('bookings.*', 'reason_visits.name as reason_visit')->get();

        $events = Event::with('eventCategory')->where('initial_date', '>=', $startStr)->orwhere('final_date', '<=', $endDate)->get();
        $data = array();

        foreach ($events as $row) {
            for (
                $i = strtotime($row->initial_date);
                $i <= strtotime($row->final_date);
                $i = strtotime('+1 day', $i)
            ) {
                $data[] = array(
                    'groupId' => $row->id,
                    'title' => $row->name,
                    'start' => date('Y-m-d', $i) . ' ' . $row->initial_time,
                    'end' => date('Y-m-d', $i) . ' ' . $row->final_time,
                    'color' => '#16a34a',
                    'className' => 'event',
                    'startEditable' => false,
                    'eventCategory' => $row->eventCategory->name,
                    'initial_date' => $row->initial_date,
                    'final_date' => $row->final_date,
                    'initial_time' => $row->initial_time,
                    'final_time' => $row->final_time,
                    'price' => $row->price,
                    'quotas' => $row->quotas,
                );
            }
        }

        foreach ($bookings as $row) {
            $data[] = array(
                'groupId' => $row->id,
                'title' => $row['reason_visit'],
                'start' => $row->date,
                'end' => $row->date,
                'color' => $row->status == 'S' ? '#2563eb' : ($row->status == 'D' ? '#34d399' : '#f87171'),
                'className' => 'booking',
                'startEditable' => true,
                'status' => $row->status,
                'document_type' => $row->document_type,
                'document_number' => $row->document_number,
                'name' => $row->name,
                'type' => $row->type,
                'reason_visit_id' => $row->reason_visit_id,
                'areas' => $row->areas,
                'customers' => $row->customers,
            );
        }

        return response()->json($data);
    }

    public function search($type, $search)
    {
        $bookings = Booking::with('customers', 'areas')
            ->where([
                ['document_type', '=', $type],
                ['document_number', 'like', '%' . $search . '%'],
                ['date', '=', date('Y-m-d')],
                ['status', '=', 'S'],
            ])
            ->get();

        return $bookings;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
