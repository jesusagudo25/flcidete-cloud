<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UseLargePrinter extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'invoice_id',
        'unit',
        'description',
        'printer_material_id',
        'width',
        'height',
        'quantity',
        'extra',
        'extra_description',
        'base_cost',
    ];

    public $timestamps = false;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function printerMaterial()
    {
        return $this->belongsTo(PrinterMaterial::class);
    }
}
