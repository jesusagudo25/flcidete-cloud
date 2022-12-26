<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\ComponentUpdate;
use Illuminate\Http\Request;

class ComponentUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ComponentUpdate::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ComponentUpdate::create($request->all());

        $component = Component::where('id', $request->component_id)->first();

        if($component->estimated_value < $request->estimated_value && $component->sale_price < $request->sale_price){
            $component->update([
                'stock' => $component->stock + $request->quantity,
                'purchase_price' => $request->purchase_price,
                'estimated_value' => $request->estimated_value,
                'percentage' => $request->percentage,
                'sale_price' => $request->sale_price,
            ]);

        }
        else{
            $component->update([
                'stock' => $component->stock + $request->quantity,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComponentUpdate  $componentUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(ComponentUpdate $componentUpdate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComponentUpdate  $componentUpdate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComponentUpdate $componentUpdate)
    {
        ComponentUpdate::where('id', $componentUpdate->id)->update([
            'active' => $request->active,
        ]);

        $componentUpdate = ComponentUpdate::where('id', $componentUpdate->id)->first();
        $component = Component::where('id', $request->component_id)->first();

        if ($request->active) {
            $component->update([
                'stock' => $component->stock + $componentUpdate->quantity,
            ]);
        } else {
            $stock = $component->stock - $componentUpdate->quantity;
            if ($stock < 0) {
                $component->update([
                    'stock' => 0,
                ]);
            }
            else{
                $component->update([
                    'stock' => $stock,
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComponentUpdate  $componentUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComponentUpdate $componentUpdate)
    {
        //
    }
}
