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
        Schema::create('sums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('invoice_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('number_hours',4,2)->nullable();
            $table->decimal('cost_hour', 10, 2)->nullable();
            $table->decimal('extra', 8, 2)->nullable();
            $table->text('extra_description')->nullable();
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
        Schema::dropIfExists('sums');
    }
};
