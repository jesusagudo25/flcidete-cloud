<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'estimated_value',
        'purchase_price',
    ];

    public function thread(){
        return $this->belongsTo('App\Models\Thread');
    }
}
