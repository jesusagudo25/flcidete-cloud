<?php

namespace App\Http\Controllers;

use App\Models\Filament;
use Illuminate\Http\Request;

class FilamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Filament::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $filament = Filament::create($request->all());

        $filament->current_weight = $filament->current_weight * $request->quantity;

        $filament->save();

        //Por mejorar presentacion del stock

        /* Quantitity FilamentUpdates */
        if ($request->has('quantity')) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity; $i++) {
                $filament->filamentUpdates()->create([
                    'filament_id' => $filament->id,
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
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */
    public function show(Filament $filament)
    {
        return Filament::with('filamentUpdates')->find($filament->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */

    public function search($search)
    {
        $filaments = Filament::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $filaments;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Filament $filament)
    {
        $filamentUpdates = $filament->filamentUpdates()->where('active',1)->count();
        if($filamentUpdates > 0){
            if($request->purchased_weight != $filament->purchased_weight){
                Filament::where('id', $filament->id)->update($request->all());

                $filament->current_weight = $request->purchase_weight * $filamentUpdates;
    
                $filament::where('id', $filament->id)->update($filament->current_weight);
            }
            else{
                Filament::where('id', $filament->id)->update([
                    'name' => $request->name,
                    'estimated_value' => $request->estimated_value,
                    'purchase_price' => $request->purchase_price,
                    'percentage' => $request->percentage,
                    'sale_price' => $request->sale_price,
                ]);
            }
        }
        else{
            Filament::where('id', $filament->id)->update([
                'name' => $request->name,
                'estimated_value' => $request->estimated_value,
                'purchase_price' => $request->purchase_price,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
                'purchased_weight' => $request->purchased_weight,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */
    public function destroy(Filament $filament)
    {
        //
    }
}
