<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VinylUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vinyl_id',
        'cost',
        'estimated_value',
        'purchase_price',
        'percentage',
        'sale_price',
    ];

    public function vinyl(){
        return $this->belongsTo('App\Models\Vinyl');
    }
}
