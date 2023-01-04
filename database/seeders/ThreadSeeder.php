<?php

namespace Database\Seeders;

use App\Models\Thread;
use App\Models\ThreadUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Thread::create(
            [
                'id' => 1,
                'name' => 'Hilo negro (Machetazo)',
                'price_purchase' => 0.5,
                'estimated_value' => 0.5,
                'purchased_amount' => 5000,
            ]
        );

        ThreadUpdate::create(
            [
                'id' => 1,
                'thread_id' => 1,
                'purchase_price' => 0.5,
                'estimated_value' => 0.5,
            ]
        );
    }
}
