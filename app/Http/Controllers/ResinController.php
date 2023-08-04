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
        if ($request->has('quantity')) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity; $i++) {
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
        if ($request->has('active') && count($request->all()) == 1) {
            Resin::where('id', $resin->id)
            ->update([
                'active' => $request->active
            ]);

            return response()->json([
                'message' => 'Resin updated successfully'
            ], 200);

        }

        $resinUpdates = $resin->resinUpdates()->where('active',1)->count();
        
        if($resinUpdates > 0){
            if($request->purchased_weight != $resin->purchased_weight){
                Resin::where('id', $resin->id)->update($request->all());

                $resin->current_weight = $request->purchase_weight * $resinUpdates;

                $resin::where('id', $resin->id)->update($resin->current_weight);
            }
            else{
                Resin::where('id', $resin->id)->update([
                    'name' => $request->name,
                    'estimated_value' => $request->estimated_value,
                    'purchase_price' => $request->purchase_price,
                    'percentage' => $request->percentage,
                    'sale_price' => $request->sale_price,
                ]);
            }
        }
        else{
            Resin::where('id', $resin->id)->update([
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
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resin $resin)
    {
        //
    }
}
