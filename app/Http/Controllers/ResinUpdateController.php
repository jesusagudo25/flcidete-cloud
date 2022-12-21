<?php

namespace App\Http\Controllers;

use App\Models\Resin;
use App\Models\ResinUpdate;
use Illuminate\Http\Request;

class ResinUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ResinUpdate::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ResinUpdate::create($request->all());

        $resin = Resin::where('id', $request->resin_id)->first();

        $resin->update([
            'current_weight' => $resin->current_weight + $resin->purchased_weight,
            'purchase_price' => $request->purchase_price,
            'estimated_value' => $request->estimated_value,
            'percentage' => $request->percentage,
            'sale_price' => $request->sale_price,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ResinUpdate  $resinUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(ResinUpdate $resinUpdate)
    {
        ResinUpdate::with('resin')->find($resinUpdate->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ResinUpdate  $resinUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ResinUpdate $resinUpdate)
    {
        ResinUpdate::where('id', $resinUpdate->id)->update([
            'active' => $request->active,
        ]);

        $resin = Resin::where('id', $resinUpdate->resin_id)->first();

        if($request->active) {
            $resin->update([
                'current_weight' => $resin->current_weight + $resin->purchased_weight,
            ]);
        } else {
            $resin->update([
                'current_weight' => $resin->current_weight - $resin->purchased_weight,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ResinUpdate  $resinUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResinUpdate $resinUpdate)
    {
        //
    }
}
