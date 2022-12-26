<?php

namespace App\Http\Controllers;

use App\Models\MaterialMilling;
use App\Models\MillingUpdate;
use Illuminate\Http\Request;

class MillingUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MillingUpdate::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        MillingUpdate::create($request->all());

        $milling = MaterialMilling::where('id', $request->material_milling_id)->first();

        if($milling->estimated_value < $request->estimated_value && $milling->sale_price < $request->sale_price){
            $milling->update([
                'stock' => $milling->stock + $request->quantity,
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
            ]);

        }
        else{
            $stock = $milling->stock + $request->quantity;
            if($stock < 0){
                $milling->update([
                    'stock' => 0,
                ]);
            }
            else{
                $milling->update([
                    'stock' => $stock,
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MillingUpdate  $millingUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(MillingUpdate $millingUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MillingUpdate  $millingUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MillingUpdate $millingUpdate)
    {
        MillingUpdate::where('id', $millingUpdate->id)->update(['active' => $request->active]);

        $millingUpdate = MillingUpdate::where('id', $millingUpdate->id)->first();

        $material = MaterialMilling::where('id', $request->material_milling_id)->first();

        if ($request->active) {
            $material->update([
                'stock' => $material->stock + $millingUpdate->quantity,
            ]);
        }
        else {
            $material->update([
                'stock' => $material->stock - $millingUpdate->quantity,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MillingUpdate  $millingUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(MillingUpdate $millingUpdate)
    {
        //
    }
}
