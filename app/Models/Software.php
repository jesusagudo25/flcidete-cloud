<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasFactory;

    protected $table = 'softwares';

    protected $fillable = [
        'name',
        'estimated_value',
        'purchase_price',
        'sale_price',
        'purchased_date',
        'expiration_date',
    ];

    public function suss(){
        return $this->hasMany(SUS::class);
    }
}
