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
            $table->text('description')->nullable();
            $table->bigInteger('receipt')->nullable();
            $table->char('type_sale', 1);
            $table->char('type_invoice', 1);
            $table->date('date_delivery')->nullable();
            $table->decimal('labor_time',4,2)->nullable();
            $table->decimal('total', 6, 2);
            /*
            * A = Active: Una factura es activa cuando se crea y se puede editar (Contrario a cuando se finaliza inmediatamente)
            * P = Payment: Una factura es abonada cuando se le agrega un abono (Contrario a cuando se cancela inmediatamente)
            * C = Cancelled: Una factura es cancelada cuando su estado es A y se desactiva (No se puede volver a activar)
            * F = Finished: Una factura es finalizada cuando su venta se ha completado, puede ser al momento de la venta o despues de mantener la factura en estado A
            */
            $table->char('status', 1)->default('F');
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
