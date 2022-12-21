<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'user_id',
        'name',
        'description',
        'amount',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
