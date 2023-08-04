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
        Schema::create('use_large_printers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('invoice_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('unit');
            $table->foreignId('printer_material_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('description');
            $table->integer('width'); // in ft
            $table->integer('height'); // in ft
            $table->integer('quantity');
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
        Schema::dropIfExists('use_large_printers');
    }
};
