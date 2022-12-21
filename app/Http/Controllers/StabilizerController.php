<?php

namespace App\Http\Controllers;

use App\Models\Stabilizer;
use Illuminate\Http\Request;

class StabilizerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Stabilizer::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Stabilizer::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stabilizer  $stabilizer
     * @return \Illuminate\Http\Response
     */
    public function show(Stabilizer $stabilizer)
    {
        return Stabilizer::with('stabilizerUpdates')->find($stabilizer->id);
    }
    
    public function search($search)
    {
        $stabilizers = Stabilizer::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $stabilizers;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stabilizer  $stabilizer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stabilizer $stabilizer)
    {
        Stabilizer::where('id', $stabilizer->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stabilizer  $stabilizer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stabilizer $stabilizer)
    {
        //
    }
}
