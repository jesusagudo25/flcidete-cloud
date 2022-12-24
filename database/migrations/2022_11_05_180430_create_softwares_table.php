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
        Schema::create('softwares', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->decimal('estimated_value_base', 8, 2);
            $table->decimal('estimated_value', 8, 2);
            
            $table->decimal('purchase_price_base', 10, 2);
            $table->decimal('purchase_price', 10, 2);
            
            $table->decimal('sale_price_base', 10, 2); //hourly rate
            $table->decimal('sale_price', 10, 2); //hourly rate
            
            $table->date('purchased_date_base');
            $table->date('purchased_date');
            
            $table->date('expiration_date_base');
            $table->date('expiration_date');

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
        Schema::dropIfExists('softwares');
    }
};
