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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->char('document_type',1);
            $table->string('document_number');
            $table->string('name');
            $table->char('type',1);
            $table->foreignId('reason_visit_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->date('date');
            //  S: SCHEDULED, C: CANCELLED, D: DONE
            $table->char('status',1)->default('S');
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
        Schema::dropIfExists('bookings');
    }
};
