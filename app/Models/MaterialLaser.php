<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialLaser extends Model
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
        'area',
        'area_base',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_material_laser', 'material_laser_id', 'sum_id');
    }

    public function laserUpdates(){
        return $this->hasMany('App\Models\LaserUpdate');
    }
}
