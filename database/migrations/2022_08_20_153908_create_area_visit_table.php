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
        Schema::create('area_visit', function (Blueprint $table) {
            $table->primary(array('area_id', 'visit_id'));
            $table->foreignId('area_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('visit_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->time('start_time');
            $table->time('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_visit');
    }
};
