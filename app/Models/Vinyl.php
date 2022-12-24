<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vinyl extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cost',
        'cost_base',
        'estimated_value',
        'estimated_value_base',
        'purchase_price',
        'purchase_price_base',
        'percentage',
        'percentage_base',
        'sale_price',
        'sale_price_base',
        'width',
        'width_base',
        'height',
        'height_base',
        'height_in_feet',
        'area',
        'area_base',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_vinyl', 'vinyl_id', 'sum_id');
    }

    public function vinylUpdates(){
        return $this->hasMany('App\Models\VinylUpdate');
    }
}