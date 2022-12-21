<?php

namespace App\Http\Controllers;

use App\Models\Resin;
use Illuminate\Http\Request;

class ResinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Resin::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Resin::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function show(Resin $resin)
    {
        return Resin::with('resinUpdates')->find($resin->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */

    public function search($search)
    {
        $resins = Resin::where([
            ['name', 'like', '%' . $search . '%'],
            ['active', '=', 1]
        ])->get();
        return $resins;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resin $resin)
    {
        Resin::where('id', $resin->id)
            ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resin  $resin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resin $resin)
    {
        //
    }
}
