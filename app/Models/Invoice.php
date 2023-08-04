<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt',
        'user_id',
        'customer_id',
        'subsidiary_id',
        'total',
        'observations',
        'type_invoice',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function sums()
    {
        return $this->hasMany(SUM::class);
    }

    public function designServices()
    {
        return $this->hasMany(DesignService::class);
    }

    public function useLargePrinters()
    {
        return $this->hasMany(UseLargePrinter::class);
    }

    public function suEmbroderies()
    {
        return $this->hasMany(SuEmbroidery::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class);
    }



}
