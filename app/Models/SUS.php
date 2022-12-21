<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SUS extends Model
{
    use HasFactory;

    protected $table = 'suss';

    protected $fillable = [
        'invoice_id',
        'area_id',
        'softwares_id',
        'number_hours',
        'cost_hour',
        'extra',
        'extra_description',
        'base_cost'
    ];

    public $timestamps = false;

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function area(){
        return $this->belongsTo(Area::class);
    }

    public function software(){
        return $this->belongsTo(Software::class);
    }
}
