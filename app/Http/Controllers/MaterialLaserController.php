<?php

namespace App\Http\Controllers;

use App\Models\MaterialLaser;
use Illuminate\Http\Request;

class MaterialLaserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MaterialLaser::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $material = MaterialLaser::create($request->all());

        $material->area = $request->area * $request->quantity;

        $material->save();

        //Por mejorar presentacion del stock

        /* Quantitity MaterialLaserUpdates */
        if ($request->has('quantity') && $request->quantity > 1) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity - 1; $i++) {
                $material->laserUpdates()->create([
                    'material_laser_id' => $material->id,
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
     * @param  \App\Models\MaterialLaser  $materialLaser
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialLaser $materialLaser)
    {
        return MaterialLaser::with('laserUpdates')->find($materialLaser->id);
    }

    public function search($search)
    {
        $materialsLaser = MaterialLaser::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $materialsLaser;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialLaser  $materialLaser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialLaser $materialLaser)
    {
        MaterialLaser::where('id', $materialLaser->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialLaser  $materialLaser
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialLaser $materialLaser)
    {
        //
    }
}
