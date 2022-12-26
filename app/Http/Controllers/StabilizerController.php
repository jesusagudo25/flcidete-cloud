<?php

namespace App\Http\Controllers;

use App\Models\Stabilizer;
use Illuminate\Http\Request;

class StabilizerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Stabilizer::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stabilizer = Stabilizer::create($request->all());

        $stabilizer->area = $request->area * $request->quantity;

        $stabilizer->save();

        //Por mejorar presentacion del stock

        /* Quantitity StabilizerUpdates */
        if ($request->has('quantity')) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity; $i++) {
                $stabilizer->stabilizerUpdates()->create([
                    'stabilizer_id' => $stabilizer->id,
                    'estimated_value' => $request->estimated_value,
                    'purchase_price' => $request->purchase_price
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stabilizer  $stabilizer
     * @return \Illuminate\Http\Response
     */
    public function show(Stabilizer $stabilizer)
    {
        return Stabilizer::with('stabilizerUpdates')->find($stabilizer->id);
    }

    public function search($search)
    {
        $stabilizers = Stabilizer::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $stabilizers;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stabilizer  $stabilizer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stabilizer $stabilizer)
    {
        $stabilizerUpdates = $stabilizer->stabilizerUpdates->where('active', 1)->count();
        if($stabilizerUpdates > 0){
            if($request->width != $stabilizer->width || $request->height != $stabilizer->height){
                Stabilizer::where('id', $stabilizer->id)
                ->update($request->all());
    
                $stabilizer->area = ($request->area ) * $stabilizerUpdates;
                $stabilizer::where('id', $stabilizer->id)
                ->update([
                    'area' => $stabilizer->area
                ]);
            }
            else{

                Stabilizer::where('id', $stabilizer->id)
                ->update([
                    'name' => $request->name,
                    'estimated_value' => $request->estimated_value,
                    'purchase_price' => $request->purchase_price,
                ]);
            }
        }
        else{
            Stabilizer::where('id', $stabilizer->id)
            ->update([
                'name' => $request->name,
                'estimated_value' => $request->estimated_value,
                'purchase_price' => $request->purchase_price,
                'width' => $request->width,
                'height' => $request->height,
                'height_in_yd' => $request->height_in_yd,
            ]);
        }
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stabilizer  $stabilizer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stabilizer $stabilizer)
    {
        //
    }
}
