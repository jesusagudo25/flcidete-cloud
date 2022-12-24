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
        if ($request->has('quantity') && $request->quantity > 1) {
            $quantity = $request->quantity;
            for ($i = 0; $i < $quantity - 1; $i++) {
                $software->softwareUpdate()->create([
                    'softwares_id' => $software->id,
                    'purchase_price' => $request->purchase_price,
                    'estimated_value' => $request->estimated_value,
                    'sale_price' => $request->sale_price,
                    'purchased_date' => $request->purchased_date,
                    'expiration_date' => $request->expiration_date,
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
            ['expiration_date' , '>', date('Y-m-d')],
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
