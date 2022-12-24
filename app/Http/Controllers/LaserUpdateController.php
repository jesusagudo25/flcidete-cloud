<?php

namespace App\Http\Controllers;

use App\Models\LaserUpdate;
use App\Models\MaterialLaser;
use Illuminate\Http\Request;

class LaserUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            LaserUpdate::create($request->all());
        }

        $laser = MaterialLaser::where('id', $request->material_laser_id)->first();

        if($laser->estimated_value < $request->estimated_value && $laser->sale_price < $request->sale_price){
            $laser->update([
                'area' => $laser->area + ($laser->width * $laser->height) * $request->quantity,
                'cost' => $request->cost,
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
            ]);
        }
        else{
            $laser->update([
                'area' => $laser->area + ($laser->width * $laser->height) * $request->quantity,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LaserUpdate  $laserUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(LaserUpdate $laserUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LaserUpdate  $laserUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LaserUpdate $laserUpdate)
    {
        LaserUpdate::where('id', $laserUpdate->id)->update([
            'active' => $request->active,
        ]);

        $laser = MaterialLaser::where('id', $laserUpdate->material_laser_id)->first();
        if ($request->active) {

            $laser->update([
                'area' => $laser->area + ($laser->width * $laser->height),
            ]);
        }
        else{
            $laser->update([
                'area' => $laser->area - ($laser->width * $laser->height),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LaserUpdate  $laserUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(LaserUpdate $laserUpdate)
    {
        //
    }
}
