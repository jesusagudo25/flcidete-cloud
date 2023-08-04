<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'total',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
