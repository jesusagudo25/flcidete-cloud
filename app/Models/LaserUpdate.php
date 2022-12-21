<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaserUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_laser_id',
        'cost',
        'estimated_value',
        'purchase_price',
        'percentage',
        'sale_price',
    ];

    public function materialLaser(){
        return $this->belongsTo('App\Models\MaterialLaser');
    }
}
