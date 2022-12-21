<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'software_id',
        'purchased_date',
        'purchased_date',
        'expiration_date',
        'sale_price',
        'estimated_value',
    ];
}
