<?php

namespace App\Http\Controllers;

use App\Models\AgeRange;
use Illuminate\Http\Request;

class AgeRangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AgeRange::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AgeRange  $ageRange
     * @return \Illuminate\Http\Response
     */
    public function show(AgeRange $ageRange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AgeRange  $ageRange
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgeRange $ageRange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AgeRange  $ageRange
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgeRange $ageRange)
    {
        //
    }
}
