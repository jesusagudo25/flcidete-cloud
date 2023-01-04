<?php

namespace Database\Seeders;

use App\Models\MaterialMilling;
use App\Models\MillingUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialMillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MaterialMilling::create(
            [
                'id' => 1,
                'name' => 'PCB',
                'purchase_price' => 2,
                'estimated_value' => 2,
                'percentage' => 50,
                'sale_price' => 3,
                'stock' => 3,
            ]
        );
        
        MillingUpdate::create(
            [
                'id' => 1,
                'material_milling_id' => 1,
                'purchase_price' => 2,
                'estimated_value' => 2,
                'percentage' => 50,
                'sale_price' => 3,
                'quantity' => 3,
            ]
        );
    }
}
