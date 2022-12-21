<?php

namespace App\Http\Controllers;

use App\Models\Filament;
use App\Models\FilamentUpdate;
use Illuminate\Http\Request;

class FilamentUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FilamentUpdate::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        FilamentUpdate::create($request->all());

        $filament = Filament::where('id', $request->filament_id)->first();

        $filament->update([
            'current_weight' => $filament->current_weight + $filament->purchased_weight,
            'purchase_price' => $request->purchase_price,
            'estimated_value' => $request->estimated_value,
            'percentage' => $request->percentage,
            'sale_price' => $request->sale_price,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FilamentUpdate  $filamentUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(FilamentUpdate $filamentUpdate)
    {
        FilamentUpdate::with('filament')->find($filamentUpdate->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FilamentUpdate  $filamentUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FilamentUpdate $filamentUpdate)
    {
        FilamentUpdate::where('id', $filamentUpdate->id)->update([
            'active' => $request->active,
        ]);

        $filament = Filament::where('id', $filamentUpdate->filament_id)->first();

        if($request->active) {
            $filament->update([
                'current_weight' => $filament->current_weight + $filament->purchased_weight,
            ]);
        } else {
            $filament->update([
                'current_weight' => $filament->current_weight - $filament->purchased_weight,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FilamentUpdate  $filamentUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(FilamentUpdate $filamentUpdate)
    {
        //
    }
}
