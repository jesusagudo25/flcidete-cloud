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
        'estimated_value',
        'purchase_price',
        'percentage',
        'sale_price',
        'width',
        'height',
        'area',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_material_laser', 'material_laser_id', 'sum_id');
    }

    public function laserUpdates(){
        return $this->hasMany('App\Models\LaserUpdate');
    }
}
