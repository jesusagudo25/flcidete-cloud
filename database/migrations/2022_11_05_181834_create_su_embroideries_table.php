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
        Schema::create('su_embroideries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('invoice_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('description');
            $table->string('unit');
            $table->string('hoop_size')->nullable();
            $table->string('embroidery_size')->nullable();
            $table->integer('quantity');
            $table->decimal('embroidery_cost', 10, 2)->nullable();
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
        Schema::dropIfExists('su_embroideries');
    }
};
