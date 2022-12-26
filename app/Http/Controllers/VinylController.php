<?php

namespace App\Http\Controllers;

use App\Models\Vinyl;
use Illuminate\Http\Request;

class VinylController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Vinyl::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vinyl = Vinyl::create($request->all());

        $vinyl->area = $request->area * $request->quantity;

        $vinyl->save();

        //Por mejorar presentacion del stock

        /* Quantitity VinylUpdates */
        if ($request->has('quantity')) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity; $i++) {
                $vinyl->vinylUpdates()->create([
                    'vinyl_id' => $vinyl->id,
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
     * @param  \App\Models\Vinyl  $vinyl
     * @return \Illuminate\Http\Response
     */
    public function show(Vinyl $vinyl)
    {
        return Vinyl::with('VinylUpdates')->find($vinyl->id);
    }

    public function search($search)
    {
        $vinyls = Vinyl::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $vinyls;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vinyl  $vinyl
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vinyl $vinyl)
    {
        $vinylUpdates = $vinyl->vinylUpdates()->where('active', 1)->count();

        if($vinylUpdates > 0){
            if($request->width != $vinyl->width || $request->height != $vinyl->height){
                Vinyl::where('id', $vinyl->id)
                ->update($request->all());
    
                $vinyl->area = ($request->area ) * $vinylUpdates;
    
                $vinyl::where('id', $vinyl->id)
                ->update([
                    'area' => $vinyl->area
                ]);
            }
            else{
                Vinyl::where('id', $vinyl->id)
                ->update([
                    'name' => $request->name,
                    'cost' => $request->cost,
                    'purchase_price' => $request->purchase_price,
                    'estimated_value' => $request->estimated_value,
                    'percentage' => $request->percentage,
                    'sale_price' => $request->sale_price,
                ]);
            }
        }
        else{
            Vinyl::where('id', $vinyl->id)
            ->update([
                'name' => $request->name,
                'cost' => $request->cost,
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
                'width' => $request->width,
                'height' => $request->height,
                'height_in_feet' => $request->height_in_feet,
            ]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vinyl  $vinyl
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vinyl $vinyl)
    {
        //
    }
}
