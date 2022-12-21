<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
     /**
      * Seed the application's database.
      *
      * @return void
      */
     public function run()
     {
          $this->call([
               AreaSeeder::class,
               ReasonVisitSeeder::class,
               EventCategorySeeder::class,
               AgeRangeSeeder::class,
               RoleSeeder::class,
               TypeSexSeeder::class,
          ]);

          \App\Models\User::factory(1)->create();
          \App\Models\ComponentCategory::factory(10)->create();
          \App\Models\Component::factory(10)->create();
          \App\Models\Event::factory(10)->create();
          \App\Models\Filament::factory(10)->create();
          \App\Models\MaterialLaser::factory(10)->create();
          \App\Models\MaterialMilling::factory(10)->create();
          \App\Models\Resin::factory(10)->create();
          \App\Models\Stabilizer::factory(10)->create();
          \App\Models\Vinyl::factory(10)->create();
          \App\Models\Thread::factory(10)->create();
          \App\Models\Software::factory(10)->create();
          \App\Models\TechExpense::factory(3)->create();

     }
}
