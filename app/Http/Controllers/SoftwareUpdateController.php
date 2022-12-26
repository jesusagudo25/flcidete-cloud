<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Models\SoftwareUpdate;
use Illuminate\Http\Request;

class SoftwareUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SoftwareUpdate::all();
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
            SoftwareUpdate::create($request->all());
        }

        $software = Software::where('id', $request->softwares_id)->first();

        if($software->estimated_value < $request->estimated_value && $software->sale_price < $request->sale_price){
            $software->update([
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'sale_price' => $request->sale_price,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SoftwareUpdate  $softwareUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(SoftwareUpdate $softwareUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SoftwareUpdate  $softwareUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SoftwareUpdate $softwareUpdate)
    {
        SoftwareUpdate::where('id', $softwareUpdate->id)->update([
            'active' => $request->active,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SoftwareUpdate  $softwareUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(SoftwareUpdate $softwareUpdate)
    {
        //
    }
}
