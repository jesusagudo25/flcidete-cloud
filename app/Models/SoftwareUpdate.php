<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'softwares_id',
        'purchased_date',
        'purchase_price',
        'expiration_date',
        'sale_price',
        'estimated_value',
    ];

    public function software(){
        return $this->belongsTo('App\Models\Software', 'softwares_id', 'id');
    }
}
