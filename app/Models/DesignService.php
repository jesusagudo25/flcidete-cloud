<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignService extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'invoice_id',
        'unit',
        'description',
        'quantity',
        'base_cost',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
