<?php

namespace Database\Seeders;

use App\Models\Filament;
use App\Models\FilamentUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Filament::create(
            [
                'name' => 'PLA Blanco Esun 1.75mm',
                'id' => 1,
                'estimated_value' => 25,    
                'purchase_price' => 25,
                'percentage' => 25,
                'sale_price' => 31.25,
                'purchased_weight' => 1000,
                'current_weight' => 1000,
            ]
        );

        FilamentUpdate::create(
            [
                'filament_id' => 1,
                'id' => 1,
                'purchase_price' => 25,
                'estimated_value' => 25,
                'percentage' => 25,
                'sale_price' => 31.25,
            ]
        );

    }
}
