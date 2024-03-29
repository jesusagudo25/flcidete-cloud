<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason_visit_id',
        'type',
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function reasonVisit()
    {
        return $this->belongsTo(ReasonVisit::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class)->withPivot('area_id', 'visit_id', 'start_time', 'end_time');
    }

}
