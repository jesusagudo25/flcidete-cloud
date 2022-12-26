<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'estimated_value',
        'percentage',
        'purchase_price',
        'sale_price',
        'purchased_weight',
        'current_weight',
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_resin', 'resin_id', 'sum_id');
    }

    public function resinUpdates(){
        return $this->hasMany('App\Models\ResinUpdate');
    }
}
