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
        Schema::create('booking_customer', function (Blueprint $table) {
            $table->primary(array('customer_id', 'booking_id'));
            $table->foreignId('customer_id')->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');  
            $table->foreignId('booking_id')->constrained()
            ->onDelete('cascade')
            ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_customer');
    }
};
