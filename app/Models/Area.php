<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function visits()
    {
        return $this->belongsToMany(Visit::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }

    public function sums()
    {
        return $this->hasMany(SUM::class);
    }

    public function suEmbroideries()
    {
        return $this->hasMany(SUEmbroidery::class);
    }

    public function suss(){
        return $this->hasMany(SUS::class);
    }

    public function techExpenses()
    {
        return $this->hasMany(TechExpense::class);
    }

    public function Events()
    {
        return $this->belongsToMany(Event::class);
    }
}
