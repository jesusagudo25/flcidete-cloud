<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadUpdate;
use Illuminate\Http\Request;

class ThreadUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ThreadUpdate::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ThreadUpdate::create($request->all());

        Thread::where('id', $request->thread_id)->update([
            'estimated_value' => $request->estimated_value, 
            'price_purchase' => $request->purchase_price
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ThreadUpdate  $threadUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(ThreadUpdate $threadUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ThreadUpdate  $threadUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ThreadUpdate $threadUpdate)
    {
        ThreadUpdate::where('id', $threadUpdate->id)
        ->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ThreadUpdate  $threadUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ThreadUpdate $threadUpdate)
    {
        //
    }
}
