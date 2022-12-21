<?php

namespace App\Http\Controllers;

use App\Imports\CustomersImport;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Visit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

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
        })->get();

        return $visits;
    }

    public function attend(){
        
        /* Get visits isAttended false */
        $visits = Visit::with('reasonVisit', 'areas', 'customers')->where('isAttended', false)->get();
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

        $visit = Visit::create([
            'reason_visit_id' => $request->reason_visit_id,
            'type' => $request->type,
        ]);

        if (empty($booking_id)) {
            if (empty($filename)) {
                if (empty($customer_id)) {
                    $customer = Customer::create($request->all());
                    $customers[] = $customer->id;
                } else {
                    $customers[] = $customer_id;
                }
            } else {

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
        } else {
            if ($request->type == 'G') {

                //Se debe traer los visitantes de la reserva
                $booking = Booking::find($booking_id);
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

    public function updateCustomers(Request $request, Visit $visit)
    {
        $customer_id = $request->has('customer_id') ? $request->customer_id : null;

        $visit->customers()->detach($customer_id);

        $visit->customers()->attach($customer_id);
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

    public function destroyCustomers(Visit $visit, $customer)
    {
        $customer_id = $customer ? $customer : null;
        $visit->customers()->detach($customer_id);
    }

    /* POST */

    public function updateAllAreas(Request $request)
    {
        $visit = Visit::find($request->visit_id);
        /* Detach where area specified */
        /* Update areas selected foreach */
        foreach ($request->areas as $area) 
        {
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
