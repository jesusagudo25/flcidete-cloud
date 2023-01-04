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
        Schema::create('sum_vinyl', function (Blueprint $table) {
            $table->primary(array('sum_id', 'vinyl_id'));
            $table->foreignId('sum_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('vinyl_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->decimal('width', 8, 2); //Inches
            $table->decimal('height', 8, 2); //Inches
            $table->decimal('price', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sum_vinyl');
    }
};
