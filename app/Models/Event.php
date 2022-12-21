<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_category_id',
        'name',
        'initial_date',
        'final_date',
        'initial_time',
        'final_time',
        'max_participants',
        'quotas',
        'price',
        'expenses',
        'description_expenses',
    ];

    public function eventCategory(){
        return $this->belongsTo(EventCategory::class);
    }

    public function invoices(){
        return $this->belongsToMany(Invoice::class);
    }

    public function areas(){
        return $this->belongsToMany(Area::class);
    }
}
