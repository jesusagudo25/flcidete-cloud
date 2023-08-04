<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuoteDetail;
use App\Models\Subsidiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use League\CommonMark\Extension\SmartPunct\Quote;
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
        $subsidiary_id = $request->has('subsidiary_id') ? $request->subsidiary_id : null;

        if (empty($customer_id)) {
            if ($request->document_type != 'R') {
                $customer = Customer::create([
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'name' => $request->name,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'age_range_id' => $request->age_range_id,
                    'type_sex_id' => $request->type_sex_id,
                    'province_id' => $request->province_id,
                    'district_id' => $request->district_id,
                    'township_id' => $request->township_id,
                ]);
                $customer_id = $customer->id;
            } else {
                $customer = Customer::create([
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'name' => null,
                    'email' => null,
                    'telephone' => null,
                    'age_range_id' => null,
                    'type_sex_id' => 3,
                    'province_id' => null,
                    'district_id' => null,
                    'township_id' => null,
                ]);

                $subsidiary = Subsidiary::create([
                    'customer_id' => $customer->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'province_id' => $request->province_id,
                    'district_id' => $request->district_id,
                    'township_id' => $request->township_id,
                ]);

                $customer_id = $customer->id;
                $subsidiary_id = $subsidiary->id;
            }
        } else if (empty($subsidiary_id) && Customer::find($customer_id)->document_type == 'R') {
            $subsidiary = Subsidiary::create([
                'customer_id' => $customer_id,
                'name' => $request->name,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'township_id' => $request->township_id,
            ]);

            $customer_id = $customer_id;
            $subsidiary_id = $subsidiary->id;
        } else {
            $customer_id = $customer_id;
            $subsidiary_id = $subsidiary_id;
        }

        $quotation = Quotation::create([
            'customer_id' => $customer_id,
            'user_id' => $request->id,
            'subsidiary_id' => $subsidiary_id,
            'total' => $request->total,
            'observations' => $request->observations,
        ]);

        foreach ($request->items as $item) {
            QuoteDetail::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => QuotationController::getUnit($item['unit']),
                'unit_price' => $item['details']['base_cost'],
                'total' => $item['details']['base_cost'] * $item['quantity'],
            ]);
        }

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
        $provinces = Http::get(config('config.geoptyapi') . '/api/provinces')->collect();
        $districts = Http::get(config('config.geoptyapi') . '/api/districts')->collect();
        $townships = Http::get(config('config.geoptyapi') . '/api/townships')->collect();

        $province_id = null;
        $district_id = null;
        $township_id = null;

        $customer = $quotation->customer;
        $subsidiary = $quotation->subsidiary;

        if (!empty($subsidiary)) {
            $provinces->each(function ($province, $key) use ($subsidiary, &$province_id) {
                if ($subsidiary['province_id'] == $province['id']) {
                    $province_id = $province['name'];
                }
            });

            $districts->each(function ($district, $key) use ($subsidiary, &$district_id) {
                if ($subsidiary['district_id'] == $district['id']) {
                    $district_id = $district['name'];
                }
            });

            $townships->each(function ($township, $key) use ($subsidiary, &$township_id) {
                if ($subsidiary['township_id'] == $township['id']) {
                    $township_id = $township['name'];
                }
            });
        } else {
            $provinces->each(function ($province, $key) use ($customer, &$province_id) {
                if ($customer['province_id'] == $province['id']) {
                    $province_id = $province['name'];
                }
            });

            $districts->each(function ($district, $key) use ($customer, &$district_id) {
                if ($customer['district_id'] == $district['id']) {
                    $district_id = $district['name'];
                }
            });

            $townships->each(function ($township, $key) use ($customer, &$township_id) {
                if ($customer['township_id'] == $township['id']) {
                    $township_id = $township['name'];
                }
            });
        }

        $pdf = PDF::loadView('quotation', with([
            'quotation' => $quotation,
            'details' => $quotation->quoteDetails,
            'user' => $quotation->user,
            'customer' => $customer,
            'subsidiary' => $subsidiary,
            'province_id' => $province_id,
            'district_id' => $district_id,
            'township_id' => $township_id
        ]));

        return $pdf->stream();
    }

    
    public function getUnit($unit){
        switch ($unit) {
            case 'u':
                return 'Unidad';
                break;
            case 'p':
                return 'Paquete';
                break;
            case 'doc':
                return 'Docena';
                break;
        }
    }
}
