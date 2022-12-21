<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MillingUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_milling_id',
        'quantity',
        'purchase_price',
        'estimated_value',
        'percentage',
        'sale_price',
    ];

    public function materialMilling()
    {
        return $this->belongsTo(MaterialMilling::class);
    }
}
