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
use App\Models\SUMComponent;
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
        return Invoice::with('customer', 'user', 'subsidiary')->where('status', 'F')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPayments()
    {
        return Invoice::with('customer', 'user', 'payments', 'subsidiary')->where('type_invoice', 'A')->where('status', 'A')->get();
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

                $subsidiary = Subsidiary::create([
                    'customer_id' => $customer->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'province_id' => $request->province_id,
                    'district_id' => $request->district_id,
                    'township_id' => $request->township_id,
                ]);

                $customer_id = $customer->id;
                $subsidiary_id = $subsidiary->id;
            }
        } else if (empty($subsidiary_id) && Customer::find($customer_id)->document_type == 'R') {
            $subsidiary = Subsidiary::create([
                'customer_id' => $customer_id,
                'name' => $request->name,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'township_id' => $request->township_id,
            ]);

            $customer_id = $customer_id;
            $subsidiary_id = $subsidiary->id;
        } else {
            $customer_id = $customer_id;
            $subsidiary_id = $subsidiary_id;
        }

        $invoice = Invoice::create([
            'customer_id' => $customer_id,
            'user_id' => $request->id,
            'subsidiary_id' => $subsidiary_id,
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
                        $materialSelected->area = 0;
                        $materialSelected->active = 0;
                        $materialSelected->save();
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

                                //Se verifica si el componente tiene stock, multiplicando la cantidad de componentes por la cantidad de unidades
                                if ($componentSelected->stock >=  ($component['quantity'] * $valor['quantity'])) {
                                    $componentSelected->stock = $componentSelected->stock - ($component['quantity'] * $valor['quantity']);
                                    $componentSelected->stock == 0 ? $componentSelected->active = 0 : $componentSelected->active = 1;
                                    $componentSelected->save();
                                    $sum->components()->attach($component['id'], ['quantity' => $component['quantity'], 'price' => $component['total']]);
                                } else {
                                    $componentSelected->stock = 0;
                                    $componentSelected->active = 0;
                                    $componentSelected->save();
                                    $sum->components()->attach($component['id'], ['quantity' => $component['quantity'], 'price' => $component['total']]);
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
                                    $materialSelected->stock = 0;
                                    $materialSelected->active = 0;
                                    $materialSelected->save();
                                    $sum->materialsMilling()->attach($material['id'], ['quantity' => $material['quantity'], 'price' => $material['total']]);
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

                                    $sum->materialsLaser()->attach(
                                        $material['id'], 
                                        ['quantity' => $valor['quantity'], 
                                        'width' => $material['width'], 
                                        'height' => $material['height'], 
                                        'price' => $material['total']]
                                    );
                                } else {
                                    $materialSelected->area = 0;
                                    $materialSelected->active = 0;
                                    $materialSelected->save();

                                    $sum->materialsLaser()->attach(
                                        $material['id'], 
                                        ['quantity' => $valor['quantity'], 
                                        'width' => $material['width'], 
                                        'height' => $material['height'], 
                                        'price' => $material['total']]
                                    );
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
                                    $vinylSelected->area = 0;
                                    $vinylSelected->active = 0;
                                    $vinylSelected->save();

                                    //width and height
                                    $sum->vinyls()->attach($vinyl['id'], ['width' => $vinylSelected->width, 'height' => $vinyl['height'], 'price' => $vinyl['total']]);
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
                                    $filamentSelected->current_weight = 0;
                                    $filamentSelected->active = 0;
                                    $filamentSelected->save();
                                    $sum->filaments()->attach($filament['id'], ['quantity' => $filament['weight'], 'price' => $filament['total']]);
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
                                    $resinSelected->current_weight = 0;
                                    $resinSelected->active = 0;
                                    $resinSelected->save();
                                    $sum->resins()->attach($resin['id'], ['quantity' => $resin['weight'], 'price' => $resin['total']]);
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

        foreach ($invoice->events as $event) {
            $event = Event::find($event->id);
            if ($request->is_active) {
                if ($event->quotas > 0) {
                    $event->quotas = $event->quotas - 1;
                    $event->save();
                } else {
                    return response()->json([
                        'message' => 'No hay cupos disponibles para el evento ' . $event->name
                    ], 400);
                }
            } else {
                $event->quotas = $event->quotas + 1;
                $event->save();
            }
        }

        foreach ($invoice->sums as $sum) {

            $type = match ($sum->area->name) {
                'Electrónica' => function ($request) use ($sum) {

                    $useComponents = $sum->components;
                    $verified = [];
                    if($request->is_active){
                        foreach ($useComponents as $useComponent) {
                            $componentSelected = Component::find($useComponent->id);

                            //Se verifica si el componente tiene stock, multiplicando la cantidad de componentes por la cantidad de unidades
                            if($componentSelected->stock >= ($useComponent->pivot->quantity * $sum->quantity)){
                                $verified[] = true;
                            }
                            else{
                                $verified[] = false;
                            }
                        }
                        
                        if(in_array(false, $verified)){
                            return response()->json([
                                'message' => 'No hay suficiente stock de los componentes seleccionados'
                            ], 400);
                        }
                        else{
                            foreach ($useComponents as $useComponent) {
                                $componentSelected = Component::find($useComponent->id);
                                $componentSelected->stock = $componentSelected->stock - ($useComponent->pivot->quantity * $sum->quantity);
                                $componentSelected->stock == 0 ? $componentSelected->active = 0 : $componentSelected->active = 1;
                                $componentSelected->save();
                            }
                        }  
                    }else{
                        foreach ($useComponents as $useComponent) {
                            $componentSelected = Component::find($useComponent->id);
                            $componentSelected->stock = $componentSelected->stock + ($useComponent->pivot->quantity * $sum->quantity);
                            $componentSelected->save();
                        }
                    }
                },
                'Mini Fresadora CNC' => function ($request) use ($sum) {

                    $useMaterials = $sum->materialsMilling;
                    $verified = [];

                    if($request->is_active){
                        foreach ($useMaterials as $useMaterial) {
                            $materialSelected = MaterialMilling::find($useMaterial->id);

                            //Se verifica si el material tiene stock, multiplicando la cantidad de material por la cantidad de unidades
                            if($materialSelected->stock >= ($useMaterial->pivot->quantity * $sum->quantity)){
                                $verified[] = true;
                            }
                            else{
                                $verified[] = false;
                            }
                        }
                        
                        if(in_array(false, $verified)){
                            return response()->json([
                                'message' => 'No hay suficiente stock de los materiales seleccionados'
                            ], 400);
                        }
                        else{
                            foreach ($useMaterials as $useMaterial) {
                                $materialSelected = MaterialMilling::find($useMaterial->id);
                                $materialSelected->stock = $materialSelected->stock - ($useMaterial->pivot->quantity * $sum->quantity);
                                $materialSelected->stock == 0 ? $materialSelected->active = 0 : $materialSelected->active = 1;
                                $materialSelected->save();
                            }
                        }  
                    }else{
                        foreach ($useMaterials as $useMaterial) {
                            $materialSelected = MaterialMilling::find($useMaterial->id);
                            $materialSelected->stock = $materialSelected->stock + ($useMaterial->pivot->quantity * $sum->quantity);
                            $materialSelected->save();
                        }
                    }
                },
                'Láser CNC' => function ($request) use ($sum) {

                    $useMaterials = $sum->materialsLaser;
                    $verified = [];

                    if($request->is_active){
                        foreach ($useMaterials as $useMaterial) {
                            $materialSelected = MaterialLaser::find($useMaterial->id);

                            //Se verifica si el material tiene stock, multiplicando la cantidad de material por la cantidad de unidades
                            if($materialSelected->area >= ($useMaterial->pivot->width * $useMaterial->pivot->height * $sum->quantity)){
                                $verified[] = true;
                            }
                            else{
                                $verified[] = false;
                            }
                        }
                        
                        if(in_array(false, $verified)){
                            return response()->json([
                                'message' => 'No hay suficiente stock de los materiales seleccionados'
                            ], 400);
                        }
                        else{
                            foreach ($useMaterials as $useMaterial) {
                                $materialSelected = MaterialLaser::find($useMaterial->id);
                                $materialSelected->area = $materialSelected->area - ($useMaterial->pivot->width * $useMaterial->pivot->height * $sum->quantity);
                                $materialSelected->area == 0 ? $materialSelected->active = 0 : $materialSelected->active = 1;
                                $materialSelected->save();
                            }
                        }  
                    }else{
                        foreach ($useMaterials as $useMaterial) {
                            $materialSelected = MaterialLaser::find($useMaterial->id);
                            $materialSelected->area = $materialSelected->area + ($useMaterial->pivot->width * $useMaterial->pivot->height * $sum->quantity);
                            $materialSelected->save();
                        }
                    }
                },
                'Cortadora de Vinilo' => function ($request) use ($sum) {
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

                    $useVinyls = $sum->vinyls;
                    $verified = [];

                    if($request->is_active){
                        foreach ($useVinyls as $useVinyl) {
                            $vinylSelected = Vinyl::find($useVinyl->id);

                            //Se verifica si el vinilo tiene stock, multiplicando el ancho y alto del vinilo por la cantidad de unidades
                            if($vinylSelected->area >= ($useVinyl->pivot->width * $useVinyl->pivot->height * $sum->quantity)){
                                $verified[] = true;
                            }
                            else{
                                $verified[] = false;
                            }
                        }
                        
                        if(in_array(false, $verified)){
                            return response()->json([
                                'message' => 'No hay suficiente stock de los vinilos seleccionados'
                            ], 400);
                        }
                        else{
                            foreach ($useVinyls as $useVinyl) {
                                $vinylSelected = Vinyl::find($useVinyl->id);
                                $vinylSelected->area = $vinylSelected->area - ($useVinyl->pivot->width * $useVinyl->pivot->height * $sum->quantity);
                                $vinylSelected->area == 0 ? $vinylSelected->active = 0 : $vinylSelected->active = 1;
                                $vinylSelected->save();
                            }
                        }
                    }else{
                        foreach ($useVinyls as $useVinyl) {
                            $vinylSelected = Vinyl::find($useVinyl->id);
                            $vinylSelected->area = $vinylSelected->area + ($useVinyl->pivot->width * $useVinyl->pivot->height * $sum->quantity);
                            $vinylSelected->save();
                        }
                    }
                },
                'Impresión 3D en filamento' => function ($request) use ($sum) {
                    $useFilaments = $sum->filaments;
                    $verified = [];

                    if($request->is_active){
                        foreach ($useFilaments as $useFilament) {
                            $filamentSelected = Filament::find($useFilament->id);

                            //Se verifica si el filamento tiene stock, multiplicando el peso del filamento por la cantidad de unidades
                            if($filamentSelected->current_weight >= ($useFilament->pivot->quantity * $sum->quantity)){
                                $verified[] = true;
                            }
                            else{
                                $verified[] = false;
                            }
                        }
                        
                        if(in_array(false, $verified)){
                            return response()->json([
                                'message' => 'No hay suficiente stock de los filamentos seleccionados'
                            ], 400);
                        }
                        else{
                            foreach ($useFilaments as $useFilament) {
                                $filamentSelected = Filament::find($useFilament->id);
                                $filamentSelected->current_weight = $filamentSelected->current_weight - ($useFilament->pivot->quantity * $sum->quantity);
                                $filamentSelected->current_weight == 0 ? $filamentSelected->active = 0 : $filamentSelected->active = 1;
                                $filamentSelected->save();
                            }
                        }
                    }else{
                        foreach ($useFilaments as $useFilament) {
                            $filamentSelected = Filament::find($useFilament->id);
                            $filamentSelected->current_weight = $filamentSelected->current_weight + ($useFilament->pivot->quantity * $sum->quantity);
                            $filamentSelected->save();
                        }
                    }
                },
                'Impresión 3D en resina' => function ($request) use ($sum) {
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

                    $useResins = $sum->resins;
                    $verified = [];

                    if($request->is_active){
                        foreach ($useResins as $useResin) {
                            $resinSelected = Resin::find($useResin->id);

                            //Se verifica si la resina tiene stock, multiplicando el peso de la resina por la cantidad de unidades
                            if($resinSelected->current_weight >= ($useResin->pivot->quantity * $sum->quantity)){
                                $verified[] = true;
                            }
                            else{
                                $verified[] = false;
                            }
                        }
                        
                        if(in_array(false, $verified)){
                            return response()->json([
                                'message' => 'No hay suficiente stock de las resinas seleccionadas'
                            ], 400);
                        }
                        else{
                            foreach ($useResins as $useResin) {
                                $resinSelected = Resin::find($useResin->id);
                                $resinSelected->current_weight = $resinSelected->current_weight - ($useResin->pivot->quantity * $sum->quantity);
                                $resinSelected->current_weight == 0 ? $resinSelected->active = 0 : $resinSelected->active = 1;
                                $resinSelected->save();
                            }
                        }
                    }else{
                        foreach ($useResins as $useResin) {
                            $resinSelected = Resin::find($useResin->id);
                            $resinSelected->current_weight = $resinSelected->current_weight + ($useResin->pivot->quantity * $sum->quantity);
                            $resinSelected->save();
                        }
                    }
                },
                default => function ($request) {
                }
            };

            //Se verifica si retorna un json con un mensaje de error
            if (!empty($type($request))) {
                return $type($request);
            }
        }

        foreach ($invoice->useLargePrinters as $useLargePrinter) {
   
            $printerMaterialSelected = $useLargePrinter->printerMaterial;

            if($request->is_active){
                if ($printerMaterialSelected->area >= (($printerMaterialSelected->width * $useLargePrinter->height) * $useLargePrinter->quantity)) {
                    $printerMaterialSelected->area = $printerMaterialSelected->area - (($printerMaterialSelected->width * $useLargePrinter->height) * $useLargePrinter->quantity);
                    $printerMaterialSelected->area == 0 ? $printerMaterialSelected->active = 0 : $printerMaterialSelected->active = 1;
                    $printerMaterialSelected->save();
                } else {
                    return response()->json([
                        'message' => 'No hay suficiente stock del material '
                    ], 400);
                }
            }else{
                $printerMaterialSelected->area = $printerMaterialSelected->area + (($printerMaterialSelected->width * $useLargePrinter->height) * $useLargePrinter->quantity);
                $printerMaterialSelected->save();
            }
        }
        
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
        $subsidiary = $invoice->subsidiary;

        if (!empty($subsidiary)) {
            $provinces->each(function ($province, $key) use ($subsidiary, &$province_id) {
                if ($subsidiary['province_id'] == $province['id']) {
                    $province_id = $province['name'];
                }
            });

            $districts->each(function ($district, $key) use ($subsidiary, &$district_id) {
                if ($subsidiary['district_id'] == $district['id']) {
                    $district_id = $district['name'];
                }
            });

            $townships->each(function ($township, $key) use ($subsidiary, &$township_id) {
                if ($subsidiary['township_id'] == $township['id']) {
                    $township_id = $township['name'];
                }
            });
        } else {
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
        }

        $details = [];

        foreach ($invoice->events as $event) {
            $event = Event::find($event->id);
            $details[] = [
                'description' => $event->name,
                'quantity' => 1,
                'unit' => 'Unidad',
                'price' => $event->price,
                'total' => $event->price,
            ];
        }

        foreach ($invoice->sums as $sum) {
            $details[] = [
                'description' => $sum->description,
                'quantity' => $sum->quantity,
                'unit' => InvoiceController::getUnit($sum->unit),
                'price' => $sum->base_cost,
                'total' => $sum->base_cost * $sum->quantity,
            ];
        }

        foreach ($invoice->designServices as $designService) {
            $details[] = [
                'description' => $designService->description,
                'quantity' => $designService->quantity,
                'unit' => InvoiceController::getUnit($designService->unit),
                'price' => $designService->base_cost,
                'total' => $designService->base_cost * $designService->quantity,
            ];
        }


        foreach ($invoice->useLargePrinters as $useLargePrinter) {
            $details[] = [
                'description' => $useLargePrinter->description,
                'quantity' => $useLargePrinter->quantity,
                'unit' => InvoiceController::getUnit($useLargePrinter->unit),
                'price' => $useLargePrinter->base_cost,
                'total' => $useLargePrinter->base_cost * $useLargePrinter->quantity,
            ];
        }
        $suEmbroideries = SUEmbroidery::where('invoice_id', $invoice->id)->get();
        foreach ($suEmbroideries as $suEmbroidery) {
            $details[] = [
                'description' => $suEmbroidery->description,
                'quantity' => $suEmbroidery->quantity,
                'unit' => InvoiceController::getUnit($suEmbroidery->unit),
                'price' => $suEmbroidery->base_cost,
                'total' => $suEmbroidery->base_cost * $suEmbroidery->quantity,
            ];
        }

        if ($invoice->type_invoice == 'T') {
            $pdf = PDF::loadView('invoice', with([
                'invoice' => $invoice,
                'details' => $details,
                'user' => $invoice->user,
                'customer' => $customer,
                'subsidiary' => $subsidiary,
                'province_id' => $province_id,
                'district_id' => $district_id,
                'township_id' => $township_id
            ]));
        } else {
            $pdf = PDF::loadView('payment', with([
                'invoice' => $invoice,
                'details' => $details,
                'user' => $invoice->user,
                'customer' => $customer,
                'subsidiary' => $subsidiary,
                'province_id' => $province_id,
                'district_id' => $district_id,
                'township_id' => $township_id,
                'payments' => $invoice->payments
            ]));
        }

        return $pdf->stream();
    }

    public function getUnit($unit)
    {
        switch ($unit) {
            case 'u':
                return 'Unidad';
                break;
            case 'p':
                return 'Paquete';
                break;
            case 'doc':
                return 'Docena';
                break;
        }
    }
}
