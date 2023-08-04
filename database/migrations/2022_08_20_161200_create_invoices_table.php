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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('user_id')->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('subsidiary_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('observations')->nullable();
            $table->bigInteger('receipt')->nullable();
            $table->char('type_invoice', 1);
            $table->decimal('total', 6, 2);
            /*
            * A = Adeudo: Una factura es de tipo adeudo cuando se ha realizado una venta a credito, 50% de anticipo y 50% al finalizar la venta
            * F = Finished: Una factura es finalizada cuando su venta se ha completado, puede ser al momento de la venta o despues de mantener la factura en estado P
            */
            $table->char('status', 1)->default('F');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('invoices');
    }
};
