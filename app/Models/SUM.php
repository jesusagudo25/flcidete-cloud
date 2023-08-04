<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SUM extends Model
{
    use HasFactory;

    protected $table = 'sums';
    protected $fillable = [
        'area_id',
        'invoice_id',
        'unit',
        'quantity',
        'description',
        'number_hours',
        'cost_hour',
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

    public function filaments(){
        return $this->belongsToMany('App\Models\Filament', 'sum_filament', 'sum_id', 'filament_id')->withPivot('quantity', 'price');
    }

    public function materialsMilling(){
        return $this->belongsToMany('App\Models\MaterialMilling', 'sum_material_milling', 'sum_id', 'material_milling_id')->withPivot('quantity', 'price');
    }

    public function materialsLaser(){
        return $this->belongsToMany('App\Models\MaterialLaser', 'sum_material_laser', 'sum_id', 'material_laser_id')->withPivot('quantity', 'price', 'height', 'width');
    }

    public function components(){
        return $this->belongsToMany('App\Models\Component', 'sum_component', 'sum_id', 'component_id')->withPivot('quantity', 'price');
    }

    public function resins(){
        return $this->belongsToMany('App\Models\Resin', 'sum_resin', 'sum_id', 'resin_id')->withPivot('quantity', 'price');
    }

    public function vinyls(){
        return $this->belongsToMany('App\Models\Vinyl', 'sum_vinyl', 'sum_id', 'vinyl_id' )->withPivot('price', 'height', 'width');
    }
}
