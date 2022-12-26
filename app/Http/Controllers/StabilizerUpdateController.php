<?php

namespace App\Http\Controllers;

use App\Models\Stabilizer;
use App\Models\StabilizerUpdate;
use Illuminate\Http\Request;

class StabilizerUpdateController extends Controller
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
            StabilizerUpdate::create($request->all());
        }

        $stabilizer = Stabilizer::where('id', $request->stabilizer_id)->first();

        $stabilizer->update([
            'area' => $stabilizer->area + (($stabilizer->width * $stabilizer->height) * $request->quantity),
            'purchase_price' => $request->purchase_price,
            'estimated_value' => $request->estimated_value
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StabilizerUpdate  $stabilizerUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(StabilizerUpdate $stabilizerUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StabilizerUpdate  $stabilizerUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StabilizerUpdate $stabilizerUpdate)
    {
        StabilizerUpdate::where('id', $stabilizerUpdate->id)->update([
            'active' => $request->active,
        ]);

        $stabilizer = Stabilizer::where('id', $stabilizerUpdate->stabilizer_id)->first();

        if($request->active) {
            $stabilizer->update([
                'area' => $stabilizer->area + ($stabilizer->width * $stabilizer->height),
            ]);
        } else {
            $area = $stabilizer->area - ($stabilizer->width * $stabilizer->height);
            if($area < 0) {
                $area = 0;
            }
            else{
                $stabilizer->update([
                    'area' => $area,
                ]);
            }
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StabilizerUpdate  $stabilizerUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(StabilizerUpdate $stabilizerUpdate)
    {
        //
    }
}
