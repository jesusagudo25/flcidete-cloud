<?php

namespace Database\Seeders;

use App\Models\Stabilizer;
use App\Models\StabilizerUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StabilizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stabilizer::create(
            [
                'name' => 'Stabilizer',
                'id' => 1,
                'width' => 10,
                'height' => 1800,
                'height_in_yd' => 50,
                'area' => 18000,
                'purchase_price' => 25,
                'estimated_value' => 25,
            ]
        );

        StabilizerUpdate::create(
            [
                'stabilizer_id' => 1,
                'id' => 1,
                'purchase_price' => 25,
                'estimated_value' => 25,
            ]
        );
    }
}
