<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ComponentCategory;
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
               ComponentCategorySeeder::class,
               ComponentSeeder::class,
               EventSeeder::class,
               FilamentSeeder::class,
               MaterialLaserSeeder::class,
               MaterialMillingSeeder::class,
               ResinSeeder::class,
               StabilizerSeeder::class,
               VinylSeeder::class,
               ThreadSeeder::class,
               SoftwareSeeder::class,
               CustomerSeeder::class,
          ]);

          \App\Models\User::factory(1)->create();
/*           \App\Models\ComponentCategory::factory(10)->create();
          \App\Models\Component::factory(3)->create();
          \App\Models\Event::factory(3)->create();
          \App\Models\Filament::factory(3)->create();
          \App\Models\MaterialLaser::factory(3)->create();
          \App\Models\MaterialMilling::factory(3)->create();
          \App\Models\Resin::factory(3)->create();
          \App\Models\Stabilizer::factory(3)->create();
          \App\Models\Vinyl::factory(3)->create();
          \App\Models\Thread::factory(3)->create();
          \App\Models\Software::factory(3)->create();
          \App\Models\TechExpense::factory(3)->create(); */

     }
}
