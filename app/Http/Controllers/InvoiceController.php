<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Customer;
use App\Models\Event;
use App\Models\EventInvoice;
use App\Models\Filament;
use App\Models\Invoice;
use App\Models\MaterialLaser;
use App\Models\MaterialMilling;
use App\Models\Resin;
use App\Models\SUEmbroidery;
use App\Models\SUM;
use App\Models\SUS;
use App\Models\Thread;
use App\Models\Vinyl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Invoice::with('customer', 'user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_id = $request->has('customer_id') ? $request->customer_id : null;

        if (empty($customer_id)) {
            $customer = Customer::create($request->all());
            $customer_id = $customer->id;
        } else {
            $customer_id = $customer_id;
        }

        $invoice = Invoice::create([
            'customer_id' => $customer_id,
            'user_id' => $request->id,
            'total' => $request->total,
            'type_sale' => $request->typeSale,
            'labor_time' => empty($request->laborTime) ? 0 : $request->laborTime,
            'date_delivery' => $request->dateDelivery,
        ]);

        foreach ($request->items as $item) {

            $hours = isset($item['details']['hours']) ? $item['details']['hours'] : 0;
            $minutes = isset($item['details']['minutes']) ? $item['details']['minutes'] : 0;
            $hoursArea = isset($item['details']['hours_area']) ? $item['details']['hours_area'] : 0;

            $number_hours = 0;

            if ($hours != 0 || $minutes != 0) {
                $number_hours = $hours + ($minutes / 60);
            } else {
                $number_hours = $hoursArea;
            }

            if ($item['category_service'] == 'a') {
                if ($item['name'] == 'Software de diseño') {
                    $sum = SUS::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'softwares_id' => $item['details']['software']['id'],
                        'number_hours' => $number_hours,
                        'cost_hour' => isset($item['details']['cost_hours']) ? $item['details']['cost_hours'] : null,
                        'extra' => isset($item['details']['extra']) ? $item['details']['extra'] : null,
                        'extra_description' => isset($item['details']['extra_description']) ? $item['details']['extra_description'] : null,
                        'base_cost' => $item['details']['base_cost'],
                    ]);
                } elseif ($item['name'] == 'Bordadora CNC') {
                    $sum = SUEmbroidery::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'stabilizer_id' => $item['details']['stabilizer']['id'],
                        'hoop_size' => isset($item['details']['hoop_size']) ? $item['details']['hoop_size'] : null,
                        'embroidery_size' => isset($item['details']['width']) && isset($item['details']['height']) ? $item['details']['width'] . 'x' . $item['details']['height'] : null,
                        'embroidery_cost' => isset($item['details']['embroidery_cost']) ? $item['details']['embroidery_cost'] : null,
                        'number_hours' => $number_hours,
                        'cost_hour' => isset($item['details']['cost_hours']) ? $item['details']['cost_hours'] : null,
                        'extra' => isset($item['details']['extra']) ? $item['details']['extra'] : null,
                        'extra_description' => isset($item['details']['extra_description']) ? $item['details']['extra_description'] : null,
                        'base_cost' => $item['details']['base_cost'],
                    ]);
                } else {
                    $sum = SUM::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'number_hours' => $number_hours,
                        'cost_hour' => isset($item['details']['cost_hours']) ? $item['details']['cost_hours'] : null,
                        'extra' => isset($item['details']['extra']) ? $item['details']['extra'] : null,
                        'extra_description' => isset($item['details']['extra_description']) ? $item['details']['extra_description'] : null,
                        'base_cost' => $item['details']['base_cost'],
                    ]);
                }

                $type = match ($item['name']) {
                    'Electrónica' => function ($valor) use ($sum) {
    
                        if (isset($valor['details']['components'])) {
                            foreach ($valor['details']['components'] as $component) {
                                //Validate stock
                                $componentSelected = Component::find($component['id']);
                                if ($componentSelected->stock >=  $component['quantity']) {
                                    $componentSelected->stock = $componentSelected->stock - $component['quantity'];
                                    $componentSelected->stock == 0 ? $componentSelected->active = 0 : $componentSelected->active = 1;
                                    $componentSelected->save();
                                    $sum->components()->attach($component['id'], ['quantity' => $component['quantity'], 'price' => $component['total']]);
                                } else {
                                    return response()->json([
                                        'message' => 'No hay suficiente stock del componente '
                                    ], 400);
                                }
                            }
                        }
                    },
                    'Mini Fresadora CNC' => function ($valor) use ($sum) {
                        if (isset($valor['details']['materials'])) {
                            foreach ($valor['details']['materials'] as $material) {
                                //Validate stock
                                $materialSelected = MaterialMilling::find($material['id']);
                                if ($materialSelected->stock >=  $material['quantity']) {
                                    $materialSelected->stock = $materialSelected->stock - $material['quantity'];
                                    $materialSelected->stock == 0 ? $materialSelected->active = 0 : $materialSelected->active = 1;
                                    $materialSelected->save();
                                    $sum->materialsMilling()->attach($material['id'], ['quantity' => $material['quantity'], 'price' => $material['total']]);
                                } else {
                                    return response()->json([
                                        'message' => 'No hay suficiente stock del material '
                                    ], 400);
                                }
                            }
                        }
                    },
                    'Láser CNC' => function ($valor) use ($sum){
                        if(isset($valor['details']['materials'])){
                            foreach ($valor['details']['materials'] as $material) {
                                //Validate stock
                                $materialSelected = MaterialLaser::find($material['id']);
                                if ($materialSelected->area >=  ($material['width'] * $material['height'])) {
                                    $materialSelected->area = $materialSelected->area - ($material['width'] * $material['height']);
                                    
                                    $materialSelected->area == 0 ? $materialSelected->active = 0 : $materialSelected->active = 1;
                                    $materialSelected->save();

                                    $sum->materialsLaser()->attach($material['id'], ['quantity' => $material['quantity'], 'width' => $material['width'], 'height' => $material['height'], 'price' => $material['total']]);
                                } else {
                                    return response()->json([
                                        'message' => 'No hay suficiente stock del material '
                                    ], 400);
                                }
                            }
                        }
                    },
                    'Cortadora de Vinilo' => function ($valor) use ($sum) {
                        if (isset($valor['details']['vinyls'])) {
                            foreach ($valor['details']['vinyls'] as $vinyl) {
                                //Validate stock
                                $vinylSelected = Vinyl::find($vinyl['id']);
                                if ($vinylSelected->area >= ($vinylSelected->width * $vinyl['height'])) {
                                    $vinylSelected->area = $vinylSelected->area - ($vinylSelected->width * $vinyl['height']);
    
                                    $vinylSelected->area == 0 ? $vinylSelected->active = 0 : $vinylSelected->active = 1;
                                    $vinylSelected->save();
    
                                    //width and height
                                    $sum->vinyls()->attach($vinyl['id'], ['width' => $vinylSelected->width, 'height' => $vinyl['height'], 'price' => $vinyl['total']]);
                                } else {
                                    return response()->json([
                                        'message' => 'No hay suficiente stock del vinilo '
                                    ], 400);
                                }
                            }
                        }
                    },
                    'Impresión 3D en filamento' => function ($valor) use ($sum) {
                        if (isset($valor['details']['filaments'])) {
                            foreach ($valor['details']['filaments'] as $filament) {
                                //Validate stock
                                $filamentSelected = Filament::find($filament['id']);
                                if ($filamentSelected->current_weight >=  $filament['weight']) {
                                    $filamentSelected->current_weight = $filamentSelected->current_weight - $filament['weight'];
                                    $filamentSelected->current_weight == 0 ? $filamentSelected->active = 0 : $filamentSelected->active = 1;
                                    $filamentSelected->save();
                                    $sum->filaments()->attach($filament['id'], ['quantity' => $filament['weight'], 'price' => $filament['total']]);
                                } else {
                                    return response()->json([
                                        'message' => 'No hay suficiente stock del filamento '
                                    ], 400);
                                }
                            }
                        }
                    },
                    'Impresión 3D en resina' => function ($valor) use ($sum) {
                        if (isset($valor['details']['resins'])) {
                            foreach ($valor['details']['resins'] as $resin) {
                                //Validate stock
                                $resinSelected = Resin::find($resin['id']);
                                if ($resinSelected->current_weight >=  $resin['weight']) {
                                    $resinSelected->current_weight = $resinSelected->current_weight - $resin['weight'];
                                    $resinSelected->current_weight == 0 ? $resinSelected->active = 0 : $resinSelected->active = 1;
                                    $resinSelected->save();
                                    $sum->resins()->attach($resin['id'], ['quantity' => $resin['weight'], 'price' => $resin['total']]);
                                } else {
                                    return response()->json([
                                        'message' => 'No hay suficiente stock de la resina '
                                    ], 400);
                                }
                            }
                        }
                    },
                    'Bordadora CNC' => function ($valor) use ($sum) {
                        if (isset($valor['details']['threads'])) {
                            foreach ($valor['details']['threads'] as $thread) {
                                $sum->threads()->attach($thread['id']);
                            }
                        }
                    },
                    default => function ($valor){
                    }
                };
    
                $type($item);

            } else {
                $invoice->events()->attach($item['details']['event']['id']);
                $event = Event::find($item['details']['event']['id']);
                $event->quotas = $event->quotas - 1;
                $event->save();
            }
        }

        return response()->json([
            'message' => 'Factura creada correctamente',
            'success' => true,
            'invoice' => $invoice
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        Invoice::where('id', $invoice->id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function pdf(Invoice $invoice)
    {

        $provinces = Http::get('http://127.0.0.1:8001/api/provinces')->collect();
        $districts = Http::get('http://127.0.0.1:8001/api/districts')->collect();
        $townships = Http::get('http://127.0.0.1:8001/api/townships')->collect();

        $province_id = null;
        $district_id = null;
        $township_id = null;

        $customer = $invoice->customer;

        $provinces->each(function($province, $key) use ($customer, &$province_id){
            if($customer['province_id'] == $province['id']){
                $province_id = $province['name'];
            }
        });

        $districts->each(function($district, $key) use ($customer, &$district_id){
            if($customer['district_id'] == $district['id']){
                $district_id = $district['name'];
            }
        });

        $townships->each(function($township, $key) use ($customer, &$township_id){
            if($customer['township_id'] == $township['id']){
                $township_id = $township['name'];
            }
        });

        $items = [];

        $items[] = SUM::where('invoice_id', $invoice->id)->with('area')->get();
        $items[] = SUS::where('invoice_id', $invoice->id)->with('area')->get();
        $items[] = $invoice->events()->get();
        $items[] = SUEmbroidery::where('invoice_id', $invoice->id)->with('area')->get();

        $pdf = PDF::loadView('invoice', with(['invoice' => $invoice, 'user' => $invoice->user, 'customer' => $customer, 'items' => $items, 'province_id' => $province_id, 'district_id' => $district_id, 'township_id' => $township_id]));
        return $pdf->stream();
    }
}
