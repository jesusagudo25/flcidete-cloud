<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrinterMaterialUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'printer_material_id',
        'cost',
        'estimated_value',
        'sale_price',
        'active',
    ];

    public function printerMaterial()
    {
        return $this->belongsTo(PrinterMaterial::class);
    }
}
