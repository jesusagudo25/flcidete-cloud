<?php

namespace App\Http\Controllers;

use App\Models\ComponentCategory;
use Illuminate\Http\Request;

class ComponentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ComponentCategory::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ComponentCategory::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComponentCategory  $componentCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ComponentCategory $componentCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComponentCategory  $componentCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComponentCategory $componentCategory)
    {
        ComponentCategory::where('id', $componentCategory->id)
        ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComponentCategory  $componentCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComponentCategory $componentCategory)
    {
        //
    }
}
