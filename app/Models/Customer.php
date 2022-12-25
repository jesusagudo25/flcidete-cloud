<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
        'age_range_id',
        'type_sex_id',
        'email',
        'telephone',
        'province_id',
        'district_id',
        'township_id',
    ];

    public function ageRange(){
        return $this->belongsTo(AgeRange::class);
    }

    public function typeSex(){
        return $this->belongsTo(TypeSex::class);
    }

    public function visits(){
        return $this->belongsToMany(Visit::class);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }
    
    public function bookings(){
        return $this->belongsToMany(Booking::class);
    }

    public function quotations(){
        return $this->hasMany(Quotation::class);
    }
    
}
