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
    ];

    public function suss(){
        return $this->hasMany(SUS::class);
    }

    public function softwareUpdate(){
        return $this->hasMany('App\Models\SoftwareUpdate', 'softwares_id', 'id');
    }
}
