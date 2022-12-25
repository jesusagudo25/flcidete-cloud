<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt',
        'user_id',
        'customer_id',
        'type_sale',
        'date_delivery',
        'labor_time',
        'total',
        'description',
        'type_invoice',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function sums()
    {
        return $this->hasMany(SUM::class);
    }

    public function sus()
    {
        return $this->hasMany(SUS::class);
    }

    public function suEmbroidery()
    {
        return $this->hasMany(SUEmbroidery::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
