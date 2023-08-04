<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PDF;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Event::with('eventCategory', 'areas')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = Event::create(
            [
                'name' => $request->name,
                'event_category_id' => $request->event_category_id,
                'expenses' => $request->expenses,
                'description_expenses' => $request->description_expenses,
                'initial_date' => $request->initial_date,
                'final_date' => $request->final_date,
                'initial_time' => $request->initial_time,
                'final_time' => $request->final_time,
                'max_participants' => $request->max_participants,
                'price' => $request->price,
                'quotas' => $request->max_participants,
            ]
        );

        $event->areas()->attach($request->areas);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return Event::with('eventCategory', 'areas')->where('id', $event->id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */

    public function showParticipants($id)
    {
        $customers = Event::join('event_invoice', 'events.id', '=', 'event_invoice.event_id')
            ->join('invoices', 'event_invoice.invoice_id', '=', 'invoices.id')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->where('events.id', '=', $id)
            ->select('customers.*', 'events.name as event_name', 'events.id as event_id')
            ->get();

        $provinces = Http::get(config('config.geoptyapi').'/api/provinces')->collect();

        
        foreach ($customers as $customer) {
            $result = $provinces->where('id', $customer->province_id)->first();
            $customer->province_id = $result['name'];
        }

        $pdf = PDF::loadView('participants', compact('customers'));
        return $pdf->stream();
    }


    public function search($category, $search)
    {
        $events = Event::where([
            ['name', 'like', '%' . $search . '%'],
            ['event_category_id', '=', $category],
            ['max_participants', '>', 0],
            ['active', '=', 1]
        ])->get();
        return $events;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //if only has active
        if($request->has('active') && !$request->name){
            Event::where('id', $event->id)->update(
                [
                    'active' => $request->active,
                ]
            );
            return;
        }

        Event::where('id', $event->id)->update(
            [
                'name' => $request->name,
                'event_category_id' => $request->event_category_id,
                'expenses' => $request->expenses,
                'description_expenses' => $request->description_expenses,
                'initial_date' => $request->initial_date,
                'final_date' => $request->final_date,
                'initial_time' => $request->initial_time,
                'final_time' => $request->final_time,
                'max_participants' => $request->max_participants,
                'price' => $request->price,
            ]
        );

        $event->areas()->detach();
        $event->areas()->attach($request->areas);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
