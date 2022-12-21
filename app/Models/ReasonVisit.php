<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'isGroup',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
