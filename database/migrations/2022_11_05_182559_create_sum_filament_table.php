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
        Schema::create('sum_filament', function (Blueprint $table) {
            $table->primary(array('sum_id', 'filament_id'));
            $table->foreignId('sum_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('filament_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('quantity');
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
        Schema::dropIfExists('sum_filament');
    }
};
