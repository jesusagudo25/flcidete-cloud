<?php

namespace App\Http\Controllers;

use App\Models\Software;
use Illuminate\Http\Request;

class SoftwareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Software::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $software = Software::create($request->all());

        /* Quantitity SoftwareUpdates */
        if ($request->has('quantity')) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity; $i++) {
                $software->softwareUpdate()->create([
                    'softwares_id' => $software->id,
                    'purchase_price' => $request->purchase_price,
                    'estimated_value' => $request->estimated_value,
                    'sale_price' => $request->sale_price,
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Software  $software
     * @return \Illuminate\Http\Response
     */
    public function show(Software $software)
    {
        return Software::with('softwareUpdate')->find($software->id);
    }

    public function search($search)
    {
        $softwares = Software::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', '1']
        ])->get();
        return $softwares;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Software  $software
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Software $software)
    {
        Software::where('id', $software->id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Software  $software
     * @return \Illuminate\Http\Response
     */
    public function destroy(Software $software)
    {
        //
    }
}
