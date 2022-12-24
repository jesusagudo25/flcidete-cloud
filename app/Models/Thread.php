<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'estimated_value',
        'estimated_value_base',
        'price_purchase',
        'price_purchase_base',
        'purchased_amount',
        'purchased_amount_base',
    ];

    public function suEmbroideries(){
        return $this->belongsToMany('App\Models\SUEmbroidery', 'sue_thread', 'thread_id', 'su_embroidery_id');
    }

    public function threadUpdates(){
        return $this->hasMany('App\Models\ThreadUpdate');
    }
}
