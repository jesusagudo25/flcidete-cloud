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
        Schema::create('laser_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_laser_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('cost', 10, 2);
            $table->decimal('estimated_value', 10, 2);
            $table->decimal('purchase_price', 10, 2);
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
        Schema::dropIfExists('laser_updates');
    }
};
