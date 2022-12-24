<?php

namespace App\Http\Controllers;

use App\Models\Resin;
use Illuminate\Http\Request;

class ResinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Resin::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $resin = Resin::create($request->all());

        $resin->current_weight = $resin->current_weight * $request->quantity;

        $resin->save();

        //Por mejorar presentacion del stock
        
        /* Quantitity ResinUpdates */
        if ($request->has('quantity') && $request->quantity > 1) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity - 1; $i++) {
                $resin->resinUpdates()->create([
                    'resin_id' => $resin->id,
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
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function show(Resin $resin)
    {
        return Resin::with('resinUpdates')->find($resin->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */

    public function search($search)
    {
        $resins = Resin::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $resins;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resin $resin)
    {
        Resin::where('id', $resin->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resin $resin)
    {
        //
    }
}
