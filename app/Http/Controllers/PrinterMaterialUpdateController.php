<?php

namespace App\Http\Controllers;

use App\Models\PrinterMaterial;
use App\Models\PrinterMaterialUpdate;
use Illuminate\Http\Request;

class PrinterMaterialUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PrinterMaterialUpdate::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $quantity = $request->quantity;
        for ($i = 0; $i < $quantity; $i++) {
            PrinterMaterialUpdate::create($request->all());
        }

        $printerMaterial = PrinterMaterial::where('id', $request->printer_material_id)->first();

        if($printerMaterial->estimated_value < $request->estimated_value && $printerMaterial->sale_price < $request->sale_price){
            $printerMaterial->update([
                'area' => $printerMaterial->area + ($printerMaterial->width * $printerMaterial->height) * $request->quantity,
                'cost' => $request->cost,
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
            ]);
        }
        else{
            $printerMaterial->update([
                'area' => $printerMaterial->area + ($printerMaterial->width * $printerMaterial->height) * $request->quantity,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrinterMaterialUpdate  $printerMaterialUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(PrinterMaterialUpdate $printerMaterialUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrinterMaterialUpdate  $printerMaterialUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrinterMaterialUpdate $printerMaterialUpdate)
    {
        PrinterMaterialUpdate::where('id', $printerMaterialUpdate->id)->update([
            'active' => $request->active,
        ]);

        $printerMaterial = PrinterMaterialUpdate::where('id', $printerMaterialUpdate->printer_material_id)->first();

        if($request->active){
            $printerMaterial->update([
                'area' => $printerMaterial->area + ($printerMaterial->width * $printerMaterial->height),
            ]);
        }
        else{
            $area = $printerMaterial->area - ($printerMaterial->width * $printerMaterial->height);
            if($area < 0){
                $printerMaterial->update([
                    'area' => 0,
                ]);
            }
            else{
                $printerMaterial->update([
                    'area' => $area,
                ]);            
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrinterMaterialUpdate  $printerMaterialUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrinterMaterialUpdate $printerMaterialUpdate)
    {
        //
    }
}
