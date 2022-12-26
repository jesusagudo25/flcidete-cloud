<?php

namespace App\Http\Controllers;

use App\Models\MaterialMilling;
use Illuminate\Http\Request;

class MaterialMillingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MaterialMilling::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $materialMilling = MaterialMilling::create($request->all());

        $materialMilling->millingUpdates()->create([
            'material_milling_id' => $materialMilling->id,
            'estimated_value' => $request->estimated_value,
            'purchase_price' => $request->purchase_price,
            'percentage' => $request->percentage,
            'quantity' => $request->quantity,
            'sale_price' => $request->sale_price,
        ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MaterialMilling  $materialMilling
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialMilling $materialMilling)
    {
        return MaterialMilling::with('millingUpdates')->find($materialMilling->id);
    }

    public function search($search)
    {
        $materialsMillings = MaterialMilling::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $materialsMillings;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialMilling  $materialMilling
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialMilling $materialMilling)
    {
        MaterialMilling::where('id', $materialMilling->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialMilling  $materialMilling
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialMilling $materialMilling)
    {
        //
    }
}
