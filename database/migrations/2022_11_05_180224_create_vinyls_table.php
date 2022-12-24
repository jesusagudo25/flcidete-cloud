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
        Schema::create('vinyls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->decimal('cost_base', 8, 2);
            $table->decimal('cost', 8, 2);
            
            $table->decimal('estimated_value_base', 8, 2);
            $table->decimal('estimated_value', 8, 2);
            
            $table->decimal('purchase_price_base', 5, 3);
            $table->decimal('purchase_price', 5, 3);
            
            $table->integer('percentage_base'); // Percentage of profit
            $table->integer('percentage'); // Percentage of profit
            
            $table->decimal('sale_price_base', 5, 3);
            $table->decimal('sale_price', 5, 3);
            
            $table->integer('width_base'); // Inches
            $table->integer('width'); // Inches
            
            $table->integer('height_base'); //Inches
            $table->integer('height'); //Inches
            $table->integer('height_in_feet'); 

            $table->integer('area_base'); // Inches
            $table->integer('area'); // Inches
            
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
        Schema::dropIfExists('vinyls');
    }
};
