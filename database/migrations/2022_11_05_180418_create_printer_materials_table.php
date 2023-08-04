<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_materials', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            
            $table->decimal('cost', 8, 2);
            
            $table->decimal('estimated_value', 8, 2);

            $table->decimal('purchase_price', 5, 3); // cost per square foot  (feet^2)

            $table->decimal('sale_price', 5, 2); 

            $table->integer('width'); // feet
            $table->integer('width_in_inches');
            
            $table->integer('height'); //Feet
            $table->integer('height_in_meters'); 

            $table->integer('area'); // Feet
            
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('printer_materials');
    }
};
