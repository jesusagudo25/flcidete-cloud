<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeSex;

class TypeSexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TypeSex::all();
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
     * @param  \App\Models\TypeSex  $typeSex
     * @return \Illuminate\Http\Response
     */
    public function show(TypeSex $typeSex)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeSex  $typeSex
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeSex $typeSex)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeSex  $typeSex
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeSex $typeSex)
    {
        //
    }

    public function byType()
    {
        return TypeSex::whereNotIn('name', ['Persona jurídica'])->get();
    }
}
