<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StabilizerUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'stabilizer_id',
        'purchase_price',
        'estimated_value',
    ];

    public function stabilizer(){
        return $this->belongsTo(Stabilizer::class);
    }
}
