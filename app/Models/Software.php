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
        'estimated_value_base',
        'purchase_price',
        'purchase_price_base',
        'sale_price',
        'sale_price_base',
        'purchased_date',
        'purchased_date_base',
        'expiration_date',
        'expiration_date_base',
    ];

    public function suss(){
        return $this->hasMany(SUS::class);
    }

    public function softwareUpdate(){
        return $this->hasMany('App\Models\SoftwareUpdate', 'softwares_id', 'id');
    }
}
