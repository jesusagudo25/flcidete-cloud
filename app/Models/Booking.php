<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
        'reason_visit_id',
        'type',
        'date',
        'status'
    ];

    public function reasonVisit(){
        return $this->belongsTo(ReasonVisit::class);
    }

    public function areas(){
        return $this->belongsToMany(Area::class)->withPivot('start_time', 'end_time');
    }

    public function customers(){
        return $this->belongsToMany(Customer::class);
    }
}
