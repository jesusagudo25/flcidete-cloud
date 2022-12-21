<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResinUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'resin_id',
        'percentage',
        'purchase_price',
        'sale_price',
        'estimated_value',
    ];

    public function resin(){
        return $this->belongsTo('App\Models\Resin');
    }
}
