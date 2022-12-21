<?php

namespace App\Http\Controllers;

use App\Models\Filament;
use Illuminate\Http\Request;

class FilamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Filament::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Filament::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */
    public function show(Filament $filament)
    {
        return Filament::with('filamentUpdates')->find($filament->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */

    public function search($search)
    {
        $filaments = Filament::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $filaments;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Filament $filament)
    {
        Filament::where('id', $filament->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Filament  $filament
     * @return \Illuminate\Http\Response
     */
    public function destroy(Filament $filament)
    {
        //
    }
}
