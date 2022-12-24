<?php

namespace App\Http\Controllers;

use App\Models\Vinyl;
use App\Models\VinylUpdate;
use Illuminate\Http\Request;

class VinylUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VinylUpdate::all();
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
            VinylUpdate::create($request->all());
        }

        $vinyl = Vinyl::where('id', $request->vinyl_id)->first();

        if($vinyl->estimated_value < $request->estimated_value && $vinyl->sale_price < $request->sale_price){
            $vinyl->update([
                'area' => $vinyl->area + ($vinyl->width * $vinyl->height) * $request->quantity,
                'cost' => $request->cost,
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
            ]);
        }
        else{
            $vinyl->update([
                'area' => $vinyl->area + ($vinyl->width * $vinyl->height) * $request->quantity,
            ]);
        }

/*         $vinyl->update([
            'area' => $vinyl->area + ($vinyl->width * $vinyl->height),
            'cost' => $request->cost,
            'purchase_price' => $request->purchase_price,
            'estimated_value' => $request->estimated_value,
            'percentage' => $request->percentage,
            'sale_price' => $request->sale_price,
        ]); */
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VinylUpdate  $vinylUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(VinylUpdate $vinylUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VinylUpdate  $vinylUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VinylUpdate $vinylUpdate)
    {
        VinylUpdate::where('id', $vinylUpdate->id)->update([
            'active' => $request->active,
        ]);

        $vinyl = Vinyl::where('id', $vinylUpdate->vinyl_id)->first();

        if($request->active){
            $vinyl->update([
                'area' => $vinyl->area + ($vinyl->width * $vinyl->height),
            ]);
        }
        else{
            $vinyl->update([
                'area' => $vinyl->area - ($vinyl->width * $vinyl->height),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VinylUpdate  $vinylUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(VinylUpdate $vinylUpdate)
    {
        //
    }
}
