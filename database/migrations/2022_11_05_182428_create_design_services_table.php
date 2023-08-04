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
        Schema::create('design_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('invoice_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('unit');
            $table->text('description');
            $table->integer('quantity');
            $table->decimal('base_cost', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('design_services');
    }
};
