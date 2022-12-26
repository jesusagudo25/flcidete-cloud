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
        Schema::create('software_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('softwares_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('estimated_value', 8, 2);
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2); //hourly rate
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
        Schema::dropIfExists('software_updates');
    }
};
