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
        Schema::create('milling_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_milling_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('estimated_value', 10, 2);
            $table->integer('quantity');
            $table->integer('percentage'); // Percentage of profit
            $table->decimal('sale_price', 10, 2);
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
        Schema::dropIfExists('milling_updates');
    }
};
