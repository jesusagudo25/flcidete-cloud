<?php

namespace App\Http\Controllers;

use App\Imports\CustomersImport;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Visit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use PDO;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Visit::with('reasonVisit', 'areas', 'customers')->get();
    }

    public function endTimeNull()
    {
        // Get areas end_time is null
        $visits = Visit::with('reasonVisit', 'areas', 'customers')->whereHas('areas', function ($query) {
            $query->whereNull('end_time');
        })->where('active', 1)->get();

        return $visits;
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
        $customer_id = $request->has('customer_id') ? $request->customer_id : null;
        $booking_id = $request->has('booking_id') ? $request->booking_id : null;

        $customers = [];
        $temporalCustomers = [];
        $excelErrors = [];

        //Si hay un booking_id, se valida que el booking exista
        if (empty($booking_id)) {

            //Si no hay un booking_id, se valida que el archivo exista
            if (empty($filename)) {
                
                if(empty($customer_id)){
                    $customer = Customer::create([
                        'document_type' => $request->document_type,
                        'document_number' => $request->document_number,
                        'name' => $request->name,
                        'email' => $request->email,
                        'telephone' => $request->telephone,
                        'age_range_id' => $request->age_range_id,
                        'type_sex_id' => $request->type_sex_id,
                        'province_id' => $request->province_id,
                        'district_id' => $request->district_id,
                        'township_id' => $request->township_id,
                    ]);
                    $customers[] = $customer->id;
                }
                else{
                    $customers[] = $customer_id;
                }
            } else {
                $collectionExcel = Excel::toCollection(new CustomersImport, $filename);
                //Validate rows

                if ($collectionExcel->count() > 0) {
                    if ($collectionExcel[0]->count() > 0) {
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

                //Validate name column

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

                    if ($item['tipo_documento'] == 'Cédula' || substr($item['tipo_documento'], 0, 1) == 'C' || $item['tipo_documento'] == 'Cedula' || substr($item['tipo_documento'], 0, 1) == 'c') {
                        $item['tipo_documento'] = 'C';
                    } elseif ($item['tipo_documento'] == 'Pasaporte' || substr($item['tipo_documento'], 0, 1) == 'P' || $item['tipo_documento'] == 'pasaporte' || substr($item['tipo_documento'], 0, 1) == 'p') {
                        $item['tipo_documento'] = 'P';
                    } elseif ($item['tipo_documento'] == 'RUC' || substr($item['tipo_documento'], 0, 1) == 'R' || $item['tipo_documento'] == 'ruc' || substr($item['tipo_documento'], 0, 1) == 'r') {
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
                        $provinces = Http::get(config('config.geoptyapi').'/api/provinces')->collect();
                        $districts = Http::get(config('config.geoptyapi').'/api/districts')->collect();
                        $townships = Http::get(config('config.geoptyapi').'/api/townships')->collect();

                        if ($item['sexo'] == 'Masculino' || substr($item['sexo'], 0, 1) == 'M' || $item['sexo'] == 'masculino' || substr($item['sexo'], 0, 1) == 'm') {
                            $type_sex_id = 1;
                        } else if ($item['sexo'] == 'Femenino' || substr($item['sexo'], 0, 1) == 'F' || $item['sexo'] == 'femenino' || substr($item['sexo'],0 , 1) == 'f'){
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
                            if (strtolower($item['provincia']) == strtolower($province['name'])) {
                                $province_id = $province['id'];
                            }
                        });

                        $districts->each(function ($district, $key) use ($item, &$district_id) {
                            if (strtolower($item['distrito']) == strtolower($district['name'])) {
                                $district_id = $district['id'];
                            }
                        });

                        $townships->each(function ($township, $key) use ($item, &$township_id) {
                            if (strtolower($item['corregimiento']) == strtolower($township['name'])) {
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
        } else {
            //Se debe traer los visitantes de la reserva
            $booking = Booking::find($booking_id);
            $booking->status = 'D';
            $booking->save();
            if ($request->type == 'G') {
                $booking->customers()->each(function ($customer, $key) use (&$customers) {
                    $customers[] = $customer->id;
                });
            } else {
                if (empty($customer_id)) {
                    $customer = Customer::create($request->all());
                    $customers[] = $customer->id;
                } else {
                    $customers[] = $customer_id;
                }
            }
        }

        $visit = Visit::create([
            'reason_visit_id' => $request->reason_visit_id,
            'type' => $request->type,
        ]);

        $visit->customers()->attach($customers);

        $visit->areas()->attach(request()->areas);
    }

    public function storeAreas(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        $area_id = $request->has('area_id') ? $request->area_id : null;
        $start_time = $request->has('start_time') ? $request->start_time : null;
        $end_time = $request->has('end_time') ? $request->end_time : null;

        $visit->areas()->attach($area_id, ['start_time' => $start_time, 'end_time' => $end_time ? $end_time : null]);
    }

    public function storeCustomers(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        $customer_id = $request->has('customer_id') ? $request->customer_id : null;

        $visit->customers()->attach($customer_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Visit  $visit
     * @return \Illuminate\Http\Response
     */
    public function show(Visit $visit)
    {
        return $visit->load('customers', 'reasonVisit', 'areas');
    }

    public function showAreas(Visit $visit)
    {
        return Visit::join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->join('areas', 'areas.id', '=', 'area_visit.area_id')
            ->where('visits.id', '=', $visit->id)
            ->selectRaw('visits.created_at, areas.id, areas.name, area_visit.start_time, area_visit.end_time')
            ->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Visit  $visit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Visit $visit)
    {
        if($request->has('active') && !$request->reason_visit_id){
            Visit::where('id', $visit->id)->update(['active' => $request->active]);
            return;
        }
            
        Visit::where('id', $visit->id)->update($request->all());
    }

    public function updateAreas(Request $request, Visit $visit)
    {
        /* Detach where */
        $area_id = $request->has('area_id') ? $request->area_id : null;
        $start_time = $request->has('start_time') ? $request->start_time : null;
        $end_time = $request->has('end_time') ? $request->end_time : null;

        $visit->areas()->detach($area_id);

        /* Attach where */

        $visit->areas()->attach($area_id, ['start_time' => $start_time, 'end_time' => $end_time]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Visit  $visit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Visit $visit)
    {
        //
    }

    public function destroyAreas(Visit $visit, $area)
    {
        $area_id = $area ? $area : null;
        $visit->areas()->detach($area_id);
    }

    public function destroyCustomers(Visit $visit, Request $request)
    {
        $customer_id = $request->has('customer_id') ? $request->customer_id : null;
        $visit->customers()->detach($customer_id);
    }

    /* POST */

    public function updateAllAreas(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        /* Detach where area specified */
        /* Update areas selected foreach */
        foreach ($request->areas as $area) {
            $visit->areas()->detach($area['area_id']);
            $visit->areas()->attach($area['area_id'], ['start_time' => $area['start_time'], 'end_time' => $area['end_time']]);
        }
    }

    public function destroyAllAreas(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        /* Update areas selected foreach */
        foreach ($request->areas as $area) {
            $visit->areas()->detach($area);
        }
    }

    public function destroyAllCustomers(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        /* Update customers selected foreach */
        foreach ($request->customers as $customer) {
            $visit->customers()->detach($customer);
        }
    }
}
