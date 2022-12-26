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
        Schema::create('material_lasers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->decimal('cost', 8, 2); //

            $table->decimal('estimated_value', 8, 2);

            $table->decimal('purchase_price', 5, 3); //Cost per square foot (ft^2
            
            $table->integer('percentage'); // Percentage of profit
            
            $table->decimal('sale_price', 5, 3);
            
            $table->integer('width'); // Feet

            $table->integer('height'); // Feet

            $table->integer('area'); //Feet

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
        Schema::dropIfExists('material_lasers');
    }
};
