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
        'estimated_value',
        'purchase_price',
        'percentage',
        'sale_price',
        'width',
        'height',
        'height_in_feet',
        'area',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_vinyl', 'vinyl_id', 'sum_id');
    }

    public function vinylUpdates(){
        return $this->hasMany('App\Models\VinylUpdate');
    }
}