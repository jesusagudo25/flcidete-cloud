<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'component_category_id',
        'estimated_value',
        'estimated_value_base',
        'purchase_price',
        'purchase_price_base',
        'percentage',
        'percentage_base',
        'sale_price',
        'sale_price_base',
        'stock',
        'quantity'
    ];

    public function sums(){
        return $this->belongsToMany('App\Models\SUM', 'sum_component', 'component_id', 'sum_id');
    }

    public function component_category(){
        return $this->belongsTo(ComponentCategory::class);
    }

    public function componentUpdates(){
        return $this->hasMany(ComponentUpdate::class);
    }
}
