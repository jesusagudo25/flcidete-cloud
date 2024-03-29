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
        'unit',
        'quantity',
        'description',
        'hoop_size',
        'embroidery_size',
        'embroidery_cost',
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
}
