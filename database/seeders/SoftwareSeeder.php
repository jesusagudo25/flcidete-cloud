<?php

namespace Database\Seeders;

use App\Models\Software;
use App\Models\SoftwareUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Software::create(
            [
                'name' => 'Autodesk Fusion 360',
                'id' => 1,
                'estimated_value' => 25,
                'purchase_price' => 25,
                'sale_price' => 2,
            ],
        );

        SoftwareUpdate::create(
            [
                'softwares_id' => 1,
                'id' => 1,
                'purchase_price' => 25,
                'estimated_value' => 25,
                'sale_price' => 2,
            ],
        );
    }
}
