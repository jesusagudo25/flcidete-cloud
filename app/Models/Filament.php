<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'estimated_value',
        'estimated_value_base',
        'purchase_price',
        'purchase_price_base',
        'percentage',
        'percentage_base',
        'sale_price',
        'sale_price_base',
        'purchased_weight',
        'purchased_weight_base',
        'current_weight',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_filament', 'filament_id', 'sum_id');
    }

    public function filamentUpdates(){
        return $this->hasMany('App\Models\FilamentUpdate');
    }
}
