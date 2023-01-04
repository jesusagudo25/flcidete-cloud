<?php

namespace Database\Seeders;

use App\Models\Vinyl;
use App\Models\VinylUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VinylSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vinyl::create(
            [
                'name' => 'Vinilo negro glitter (excelsys)',
                'id' => 1,
                'cost' => 25,
                'estimated_value' => 25,
                'width' => 12,
                'height' => 600,
                'height_in_feet' => 50,
                'area' => 7200,
                'purchase_price' => 0.003,
                'sale_price' => 0.004,
                'percentage' => 25,
            ]
        );

        VinylUpdate::create(
            [
                'vinyl_id' => 1,
                'id' => 1,
                'cost' => 25,
                'estimated_value' => 25,
                'purchase_price' => 0.003,
                'sale_price' => 0.004,
                'percentage' => 25,
            ]
        );
    }
}
