<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsidiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'telephone',
        'province_id',
        'district_id',
        'township_id',
        'active',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
