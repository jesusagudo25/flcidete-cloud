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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('component_category_id')->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->decimal('purchase_price_base', 8, 2);
            $table->decimal('purchase_price', 8, 2);
            
            $table->decimal('estimated_value_base', 8, 2);
            $table->decimal('estimated_value', 8, 2);
            
            $table->integer('percentage_base'); // Percentage of profit (
            $table->integer('percentage'); // Percentage of profit (
                    
            $table->decimal('sale_price_base', 8, 2);
            $table->decimal('sale_price', 8, 2);
                    
            $table->integer('quantity'); // Quantity of components (e.g. 1000
            $table->integer('stock');

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
        Schema::dropIfExists('components');
    }
};
