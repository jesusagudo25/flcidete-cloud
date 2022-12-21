<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_id',
        'purchase_price',
        'estimated_value',
        'quantity',
        'percentage',
        'sale_price'
    ];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
