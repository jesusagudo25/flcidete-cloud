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
          ]);

          \App\Models\User::factory(1)->create();
     }
}
