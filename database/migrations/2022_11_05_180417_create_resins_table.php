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
        Schema::create('resins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->decimal('purchase_price_base', 10, 2);
            $table->decimal('purchase_price', 10, 2);
            
            $table->decimal('estimated_value_base', 8, 2);
            $table->decimal('estimated_value', 8, 2);
            
            $table->integer('percentage_base'); // Percentage of profit (
            $table->integer('percentage'); // Percentage of profit (
            
            $table->decimal('sale_price_base', 10, 2);
            $table->decimal('sale_price', 10, 2);
            
            $table->integer('purchased_weight_base');
            $table->integer('purchased_weight');
            
            $table->integer('current_weight');

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
        Schema::dropIfExists('resins');
    }
};
