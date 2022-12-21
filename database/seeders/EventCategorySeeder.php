<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventCategory;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EventCategory::create([
            'name' => 'Capacitaciones',
        ]);

        EventCategory::create([
            'name' => 'Workshop',
        ]);

        EventCategory::create([
            'name' => 'Fab Lab Kids',
        ]);
    }
}
