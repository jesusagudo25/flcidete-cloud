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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_category_id')->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->string('name');
            $table->date('initial_date');
            $table->date('final_date');
            $table->time('initial_time');
            $table->time('final_time');
            $table->integer('max_participants')->nullable(true);
            $table->integer('quotas')->nullable(true);
            $table->decimal('price', 8, 2);
            $table->decimal('expenses', 8, 2)->nullable(true);
            $table->string('description_expenses')->nullable(true);
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
        Schema::dropIfExists('events');
    }
};
