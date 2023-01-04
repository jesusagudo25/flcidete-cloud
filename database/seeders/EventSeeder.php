<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::create(
            [
                'name' => 'Taller de Arduino',
                'event_category_id' => 1,
                'id' => 1,
                'initial_date' => '2023-01-01',
                'final_date' => '2023-01-05',
                'initial_time' => '10:00:00',
                'final_time' => '12:00:00',
                'max_participants' => 10,
                'quotas' => 10,
                'price' => 25,
                'expenses' => 50,
                'description_expenses' => '50 en expositores',
            ],
        );
    }
}
