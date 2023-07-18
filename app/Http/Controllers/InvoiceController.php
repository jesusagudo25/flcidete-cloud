<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Customer;
use App\Models\DesignService;
use App\Models\Event;
use App\Models\EventInvoice;
use App\Models\Filament;
use App\Models\Invoice;
use App\Models\MaterialLaser;
use App\Models\MaterialMilling;
use App\Models\Payment;
use App\Models\PrinterMaterial;
use App\Models\Resin;
use App\Models\Stabilizer;
use App\Models\Subsidiary;
use App\Models\SUEmbroidery;
use App\Models\SUM;
use App\Models\SUS;
use App\Models\Thread;
use App\Models\UseLargePrinter;
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
        return Invoice::with('customer', 'user')->where('type_invoice', 'T')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPayments()
    {
        return Invoice::with('customer', 'user', 'payments')->where('type_invoice', 'A')->get();
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
        $subsidiary_id = $request->has('subsidiary_id') ? $request->subsidiary_id : null;

        if (empty($customer_id)) {
            if ($request->document_type != 'R') {
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
                $customer_id = $customer->id;
            } else {
                $customer = Customer::create([
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'name' => null,
                    'email' => null,
                    'telephone' => null,
                    'age_range_id' => null,
                    'type_sex_id' => 3,
                    'province_id' => null,
                    'district_id' => null,
                    'township_id' => null,
                ]);

                Subsidiary::create([
                    'customer_id' => $customer->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'province_id' => $request->province_id,
                    'district_id' => $request->district_id,
                    'township_id' => $request->township_id,
                ]);

                $customer_id = $customer->id;
            }
        } else if (empty($subsidiary_id)) {
            Subsidiary::create([
                'customer_id' => $customer_id,
                'name' => $request->name,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'township_id' => $request->township_id,
            ]);

            $customer_id = $customer_id;
        } else {
            $customer_id = $customer_id;
        }

        $invoice = Invoice::create([
            'customer_id' => $customer_id,
            'user_id' => $request->id,
            'total' => $request->total,
            'type_invoice' => $request->typeInvoice,
            'observations' => empty($request->observations) ? null : $request->observations,
            'status' => $request->typeInvoice == 'A' ? 'A' : 'F',
        ]);

        if ($request->typeInvoice == 'A') {
            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_amount' => $request->payment,
                'balance' => $request->balance,
            ]);
        }

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
                if ($item['name'] == 'Bordadora CNC') {
                    SUEmbroidery::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'hoop_size' => isset($item['details']['hoop_size']) ? $item['details']['hoop_size'] : null,
                        'embroidery_size' => isset($item['details']['width']) && isset($item['details']['height']) ? $item['details']['width'] . 'x' . $item['details']['height'] : null,
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'description' => $item['description'],
                        'embroidery_cost' => isset($item['details']['embroidery_cost']) ? $item['details']['embroidery_cost'] : null,
                        'extra' => isset($item['details']['extra']) ? $item['details']['extra'] : null,
                        'extra_description' => isset($item['details']['extra_description']) ? $item['details']['extra_description'] : null,
                        'base_cost' => $item['details']['base_cost'],
                    ]);
                } else if ($item['name'] == 'Impresora de gran formato') {

                    $materialSelected = PrinterMaterial::find($item['details']['materials']['id']);

                    if ($materialSelected->area >= (($materialSelected->width * $item['details']['materials']['height']) * $item['quantity'])) {
                        $materialSelected->area = $materialSelected->area - (($materialSelected->width * $item['details']['materials']['height']) * $item['quantity']);
                        $materialSelected->area == 0 ? $materialSelected->active = 0 : $materialSelected->active = 1;
                        $materialSelected->save();
                    } else {
                        return response()->json([
                            'message' => 'El material seleccionado no tiene suficiente área para realizar la impresión',
                            'status' => 400
                        ], 400);
                    }

                    UseLargePrinter::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'printer_material_id' => $item['details']['materials']['id'],
                        'width' => $item['details']['materials']['width'],
                        'height' => $item['details']['materials']['height'],
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'extra' => isset($item['details']['extra']) ? $item['details']['extra'] : null,
                        'extra_description' => isset($item['details']['extra_description']) ? $item['details']['extra_description'] : null,
                        'base_cost' => $item['details']['base_cost'],
                    ]);
                } else if ($item['name'] == "Diseño") {
                    DesignService::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'quantity' => $item['quantity'],
                        'description' => $item['description'],
                        'unit' => $item['unit'],
                        'base_cost' => $item['details']['base_cost'],
                    ]);
                } else {
                    $sum = SUM::create([
                        'invoice_id' => $invoice->id,
                        'area_id' => $item['id_service'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'description' => $item['description'],
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
                                if ($componentSelected->stock >=  ($component['quantity'] * $valor['quantity'])) {
                                    $componentSelected->stock = $componentSelected->stock - ($component['quantity'] * $valor['quantity']);
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
                                if ($materialSelected->stock >=  ($material['quantity'] * $valor['quantity'])) {
                                    $materialSelected->stock = $materialSelected->stock - ($material['quantity'] * $valor['quantity']);
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
                    'Láser CNC' => function ($valor) use ($sum) {
                        if (isset($valor['details']['materials'])) {
                            foreach ($valor['details']['materials'] as $material) {
                                //Validate stock
                                $materialSelected = MaterialLaser::find($material['id']);
                                if ($materialSelected->area >=  (($material['width'] * $material['height']) * $valor['quantity'])) {
                                    $materialSelected->area = $materialSelected->area - (($material['width'] * $material['height']) * $valor['quantity']);

                                    $materialSelected->area == 0 ? $materialSelected->active = 0 : $materialSelected->active = 1;
                                    $materialSelected->save();

                                    $sum->materialsLaser()->attach($material['id'], ['quantity' => $valor['quantity'], 'width' => $material['width'], 'height' => $material['height'], 'price' => $material['total']]);
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
                                if ($vinylSelected->area >= (($vinylSelected->width * $vinyl['height']) * $valor['quantity'])) {
                                    $vinylSelected->area = $vinylSelected->area - (($vinylSelected->width * $vinyl['height']) * $valor['quantity']);

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
                                if ($filamentSelected->current_weight >=  ($filament['weight'] * $valor['quantity'])) {
                                    $filamentSelected->current_weight = $filamentSelected->current_weight - ($filament['weight'] * $valor['quantity']);
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
                                if ($resinSelected->current_weight >=  ($resin['weight'] * $valor['quantity'])) {
                                    $resinSelected->current_weight = $resinSelected->current_weight - ($resin['weight'] * $valor['quantity']);
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
                    default => function ($valor) {
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
        $provinces = Http::get(config('config.geoptyapi') . '/api/provinces')->collect();
        $districts = Http::get(config('config.geoptyapi') . '/api/districts')->collect();
        $townships = Http::get(config('config.geoptyapi') . '/api/townships')->collect();

        $province_id = null;
        $district_id = null;
        $township_id = null;

        $customer = $invoice->customer;

        $provinces->each(function ($province, $key) use ($customer, &$province_id) {
            if ($customer['province_id'] == $province['id']) {
                $province_id = $province['name'];
            }
        });

        $districts->each(function ($district, $key) use ($customer, &$district_id) {
            if ($customer['district_id'] == $district['id']) {
                $district_id = $district['name'];
            }
        });

        $townships->each(function ($township, $key) use ($customer, &$township_id) {
            if ($customer['township_id'] == $township['id']) {
                $township_id = $township['name'];
            }
        });

        if ($invoice->type_invoice == 'T') {
            $pdf = PDF::loadView('invoice', with(['invoice' => $invoice, 'user' => $invoice->user, 'customer' => $customer, 'province_id' => $province_id, 'district_id' => $district_id, 'township_id' => $township_id]));
        } else {
            $pdf = PDF::loadView('payment', with(['invoice' => $invoice, 'user' => $invoice->user, 'customer' => $customer, 'province_id' => $province_id, 'district_id' => $district_id, 'township_id' => $township_id, 'payments' => $invoice->payments]));
        }

        return $pdf->stream();
    }
}
