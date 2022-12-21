<?php

namespace App\Http\Controllers;

use App\Models\TechExpense;
use Illuminate\Http\Request;

class TechExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TechExpense::with('area','user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'area_id' => 'required',
            'user_id' => 'required',
            'description' => 'required',
            'amount' => 'required',
        ]);

        TechExpense::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TechExpense  $techExpense
     * @return \Illuminate\Http\Response
     */
    public function show(TechExpense $techExpense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechExpense  $techExpense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechExpense $techExpense)
    {
        TechExpense::where('id', $techExpense->id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechExpense  $techExpense
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechExpense $techExpense)
    {
        //
    }
}
