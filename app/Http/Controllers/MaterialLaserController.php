<?php

namespace App\Http\Controllers;

use App\Models\MaterialLaser;
use Illuminate\Http\Request;

class MaterialLaserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MaterialLaser::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        MaterialLaser::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MaterialLaser  $materialLaser
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialLaser $materialLaser)
    {
        return MaterialLaser::with('laserUpdates')->find($materialLaser->id);
    }

    public function search($search)
    {
        $materialsLaser = MaterialLaser::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $materialsLaser;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialLaser  $materialLaser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialLaser $materialLaser)
    {
        MaterialLaser::where('id', $materialLaser->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialLaser  $materialLaser
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialLaser $materialLaser)
    {
        //
    }
}
