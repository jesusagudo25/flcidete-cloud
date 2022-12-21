<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilamentUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'filament_id',
        'percentage',
        'purchase_price',
        'sale_price',
        'estimated_value',
    ];

    public function filament(){
        return $this->belongsTo('App\Models\Filament');
    }
}
