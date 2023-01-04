<?php

namespace Database\Seeders;

use App\Models\Resin;
use App\Models\ResinUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Resin::create(
            [
                'name' => 'AC Resina Blanca UV',
                'id' => 1,
                'estimated_value' => 25,
                'purchase_price' => 25,
                'percentage' => 25,
                'sale_price' => 31.25,
                'purchased_weight' => 1000,
                'current_weight' => 1000,
            ]
        );

        ResinUpdate::create(
            [
                'resin_id' => 1,
                'id' => 1,
                'purchase_price' => 25,
                'estimated_value' => 25,
                'percentage' => 25,
                'sale_price' => 31.25,
            ]
        );


    }
}
