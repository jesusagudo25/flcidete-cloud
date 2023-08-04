<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrinterMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cost',
        'estimated_value',
        'purchase_price',
        'sale_price',
        'width',
        'width_in_inches',
        'height',
        'height_in_meters',
        'area',
        'active',
    ];

    public function printerMaterialUpdates(){
        return $this->hasMany('App\Models\PrinterMaterialUpdate');
    }

    
}
