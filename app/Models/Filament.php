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
        'purchase_price',
        'percentage',
        'sale_price',
        'purchased_weight',
        'current_weight',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_filament', 'filament_id', 'sum_id');
    }

    public function filamentUpdates(){
        return $this->hasMany('App\Models\FilamentUpdate');
    }
}
