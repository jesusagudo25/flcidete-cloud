<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PDF;

class QuotationController extends Controller
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
        $customer_id = $request->has('customer_id') ? $request->customer_id : null;

        if (empty($customer_id)) {
            $customer = Customer::create($request->all());
            $customer_id = $customer->id;
        } else {
            $customer_id = $customer_id;
        }

        $quotation = Quotation::create([
            'customer_id' => $customer_id,
            'user_id' => $request->id,
            'total' => $request->total,
            'description' => empty($request->description) ? null : $request->description,
        ]);

        return response()->json([
            'message' => 'Cotización creada correctamente',
            'success' => true,
            'quotation' => $quotation,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function show(Quotation $quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quotation $quotation)
    {
        //
    }

    public function pdf(Quotation $quotation)
    {
        $provinces = Http::get('http://cloud.geoptyapi.xyz/api/provinces')->collect();
        $districts = Http::get('http://cloud.geoptyapi.xyz/api/districts')->collect();
        $townships = Http::get('http://cloud.geoptyapi.xyz/api/townships')->collect();

        $province_id = null;
        $district_id = null;
        $township_id = null;

        $customer = $quotation->customer;

        $provinces->each(function($province, $key) use ($customer, &$province_id){
            if($customer['province_id'] == $province['id']){
                $province_id = $province['name'];
            }
        });

        $districts->each(function($district, $key) use ($customer, &$district_id){
            if($customer['district_id'] == $district['id']){
                $district_id = $district['name'];
            }
        });

        $townships->each(function($township, $key) use ($customer, &$township_id){
            if($customer['township_id'] == $township['id']){
                $township_id = $township['name'];
            }
        });

        $pdf = PDF::loadView('quotation', with(['quotation' => $quotation, 'user' => $quotation->user, 'customer' => $customer, 'province_id' => $province_id, 'district_id' => $district_id, 'township_id' => $township_id]));

        return $pdf->stream();
    }
}
