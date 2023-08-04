<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Subsidiary;
use Illuminate\Http\Request;

class SubsidiaryController extends Controller
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
        Subsidiary::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subsidiary  $subsidiary
     * @return \Illuminate\Http\Response
     */
    public function show(Subsidiary $subsidiary)
    {
        //
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */

     public function search($customer, $search)
     {
         $subsidiaries = Subsidiary::where(
             [
                 ['customer_id', '=', $customer],
                 ['name', 'like', '%' . $search . '%'],
                 ['active', '=', '1']
             ]
         )->get();
         return $subsidiaries;
     }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subsidiary  $subsidiary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subsidiary $subsidiary)
    {
        Subsidiary::where('id', $subsidiary->id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subsidiary  $subsidiary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subsidiary $subsidiary)
    {
        //
    }
}
