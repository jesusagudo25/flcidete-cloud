<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stabilizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'width',
        'width_base',
        'height',
        'height_base',
        'height_in_yd',
        'area_base',
        'purchase_price',
        'purchase_price_base',
        'estimated_value',
        'estimated_value_base',
    ];

    public function suEmbroideries(){
        return $this->hasMany(SUEmbroidery::class);
    }

    public function stabilizerUpdates(){
        return $this->hasMany(StabilizerUpdate::class);
    }
    
}
