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
        if ($request->has('quantity') && $request->quantity > 1) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity - 1; $i++) {
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
        /* 
        El usuario hace un edit del vinilo... Que se debe verificar:

            1. El ancho y largo del vinilo no se puede modificar si ya tiene un stock
            2. El area del vinilo no se puede modificar si ya tiene un stock ?????

            1. Si el ancho y largo del vinilo se modifican: Entonces se debe recalcular el area del vinilo
            2. Si el costo del vinilo se modifica: No pasa nada
            3. Si el precio de compra del vinilo se modifica: No pasa nada
            4. Si el valor estimado del vinilo se modifica: No pasa nada
            5. Si el porcentaje del vinilo se modifica: No pasa nada
            6. Si la descripcion del vinilo se modifica: No pasa nada

        
        */

        Vinyl::where('id', $vinyl->id)
            ->update($request->all());
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
