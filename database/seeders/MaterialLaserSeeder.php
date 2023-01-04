<?php

namespace Database\Seeders;

use App\Models\LaserUpdate;
use App\Models\MaterialLaser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialLaserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MaterialLaser::create(
            [
                'name' => 'Plywood de 3 mm (Spiegel)',
                'id' => 1,
                'cost' => 25,
                'estimated_value' => 25,    
                'purchase_price' => 0.78,
                'percentage' => 25,
                'sale_price' => 0.98,
                'width' => 4,
                'height' => 8,
                'area' => 32,
            ]
        );

        LaserUpdate::create(
            [
                'material_laser_id' => 1,
                'id' => 1,
                'cost' => 25,
                'purchase_price' => 0.78,
                'estimated_value' => 25,
                'percentage' => 25,
                'sale_price' => 0.98,
            ]
        );
    }
}
