<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialMilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'purchase_price',
        'purchase_price_base',
        'estimated_value',
        'estimated_value_base',
        'percentage',
        'percentage_base',
        'sale_price',
        'sale_price_base',
        'stock',
        'quantity',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_material_milling', 'material_milling_id', 'sum_id');
    }

    public function millingUpdates(){
        return $this->hasMany('App\Models\MillingUpdate');
    }
}
