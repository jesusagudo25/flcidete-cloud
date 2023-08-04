<?php

namespace App\Http\Controllers;

use App\Models\PrinterMaterial;
use Illuminate\Http\Request;

class PrinterMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PrinterMaterial::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $printerMaterial = PrinterMaterial::create($request->all());

        $printerMaterial->area = $request->area * $request->quantity;

        $printerMaterial->save();

        //Por mejorar presentacion del stock

        /* Quantitity VinylUpdates */
        if ($request->has('quantity')) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity; $i++) {
                $printerMaterial->printerMaterialUpdates()->create([
                        'printer_material_id' => $printerMaterial->id,
                        'cost' => $request->cost,
                        'purchase_price' => $request->purchase_price,
                        'estimated_value' => $request->estimated_value,
                        'percentage' => $request->percentage,
                        'sale_price' => $request->sale_price,
                    ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrinterMaterial  $printerMaterial
     * @return \Illuminate\Http\Response
     */
    public function show(PrinterMaterial $printerMaterial)
    {
        return PrinterMaterial::with('PrinterMaterialUpdates')->find($printerMaterial->id);
    }

    public function search($search)
    {
        $printerMaterial = PrinterMaterial::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();

        return $printerMaterial;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrinterMaterial  $printerMaterial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrinterMaterial $printerMaterial)
    {
        if ($request->has('active') && count($request->all()) == 1) {
            PrinterMaterial::where('id', $printerMaterial->id)
                ->update([
                    'active' => $request->active
                ]);

            return response()->json([
                'message' => 'Printer Material updated successfully'
            ], 200);
        }

        $printerMaterialUpdates = $printerMaterial->printerMaterialUpdates()->where('active', 1)->count();

        if ($printerMaterialUpdates > 0) {
            if ($request->width != $printerMaterial->width || $request->height != $printerMaterial->height) {
                PrinterMaterial::where('id', $printerMaterial->id)
                    ->update($request->all());

                $printerMaterial->area = ($request->area) * $printerMaterialUpdates;

                $printerMaterial::where('id', $printerMaterial->id)
                    ->update([
                        'area' => $printerMaterial->area,
                    ]);
            } else {
                PrinterMaterial::where('id', $printerMaterial->id)
                    ->update([
                        'name' => $request->name,
                        'cost' => $request->cost,
                        'purchase_price' => $request->purchase_price,
                        'estimated_value' => $request->estimated_value,
                        'percentage' => $request->percentage,
                        'sale_price' => $request->sale_price,
                    ]);
            }
        } else {
            PrinterMaterial::where('id', $printerMaterial->id)
                ->update([
                    'name' => $request->name,
                    'cost' => $request->cost,
                    'purchase_price' => $request->purchase_price,
                    'estimated_value' => $request->estimated_value,
                    'percentage' => $request->percentage,
                    'sale_price' => $request->sale_price,
                    'width' => $request->width,
                    'width_in_inches' => $request->width_in_inches,
                    'height' => $request->height,
                    'height_in_meters' => $request->height_in_meters,
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrinterMaterial  $printerMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrinterMaterial $printerMaterial)
    {
        //
    }
}
