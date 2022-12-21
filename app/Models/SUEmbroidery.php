<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SUEmbroidery extends Model
{
    use HasFactory;

    protected $table = 'su_embroideries';

    protected $fillable = [
        'area_id',
        'invoice_id',
        'stabilizer_id',
        'hoop_size',
        'embroidery_size',
        'embroidery_cost',
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

    public function stabilizer(){
        return $this->belongsTo(Stabilizer::class);
    }

    public function threads(){
        return $this->belongsToMany('App\Models\Thread', 'sue_thread', 'su_embroidery_id', 'thread_id');
    }
}
