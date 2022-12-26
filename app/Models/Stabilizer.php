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
        'height',
        'height_in_yd',
        'area',
        'purchase_price',
        'estimated_value',
    ];

    public function suEmbroideries(){
        return $this->hasMany(SUEmbroidery::class);
    }

    public function stabilizerUpdates(){
        return $this->hasMany(StabilizerUpdate::class);
    }
    
}
