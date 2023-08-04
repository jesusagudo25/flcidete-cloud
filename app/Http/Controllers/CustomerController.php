<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Subsidiary;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Customer::with('ageRange', 'typeSex', 'subsidiaries')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Customer::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    public function subsidiaries($id)
    {
        return Subsidiary::where('customer_id', $id)->get();
    }

    public function isExist($type, $search)
    {
        $customer = Customer::where(
            [
                ['document_type', '=', $type],
                ['document_number', '=', $search],
                ['active', '=', '1']
            ]
        )->first();
        return $customer;
    }

    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'unique:customers',
        ]);
        return response()->json($request->email, 200);
    }

    public function validateDocument(Request $request)
    {
        $request->validate([
            'document_number' => 'unique:customers',
        ]);
        return response()->json($request->document_number, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */

    public function search($type, $search)
    {
        $customers = Customer::where(
            [
                ['document_type', '=', $type],
                ['document_number', 'like', '%' . $search . '%'],
                ['active', '=', '1']
            ]
        )->get();
        return $customers;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //if only active parameter is received
        if ($request->has('active') && !$request->has('document_type')) {
            $customer->active = $request->active;
            $customer->save();
            return response()->json($customer, 200);
        } else {
            //Get document type previous
            $document_type_previous = $customer->document_type;
            //Get document type current
            $document_type_current = $request->document_type;

            if ($document_type_current !== 'R') {
                $customer->update([
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'name' => $request->name,
                    'age_range_id' => $request->age_range_id,
                    'type_sex_id' => $request->type_sex_id,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'province_id' => $request->province_id,
                    'district_id' => $request->district_id,
                    'township_id' => $request->township_id,
                    'active' => 1
                ]);

                //If document type previous is different to document type current, delete subsidiaries
                if ($document_type_previous === 'R') {
                    Subsidiary::where('customer_id', $customer->id)->delete();
                }
            } else {
                $customer->update([
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                ]);
                if($document_type_previous !== 'R'){
                    $customer->update([
                        'name' => null,
                        'age_range_id' => null,
                        'type_sex_id' => 3,
                        'email' => null,
                        'telephone' => null,
                        'province_id' => null,
                        'district_id' => null,
                        'township_id' => null,
                        'active' => 1
                    ]);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function template()
    {
        return response()->download(storage_path('app/public/templates/customers.xlsx'));
    }
}
