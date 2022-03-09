<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsCancelReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_documents_cancel_reasons', function (Blueprint $table) {
            $table->bigIncrements('id_reason');
            $table->string('reason_code');
            $table->string('reason');
            $table->longText('description');
            $table->boolean('with_reference')->default(false);
        });

        Schema::table('f_documents', function (Blueprint $table) {
            $table->bigInteger('cancel_reason_id')->unsigned()->nullable()->after('carrier_id');

            $table->foreign('cancel_reason_id')->references('id_reason')->on('f_documents_cancel_reasons');
        });

        DB::table('f_documents_cancel_reasons')->insert([
            [
                'id_reason' => '1', 
                'reason_code' => '01', 
                'reason' => 'Comprobante emitido con errores con relación', 
                'description' => 'Este supuesto aplica cuando la factura generada contiene un error en la clave del producto, valor unitario, descuento o cualquier otro dato, por lo que se debe reexpedir. En este caso, primero se sustituye la factura y cuando se solicita la cancelación, se incorpora el folio de la factura que sustituye a la cancelada', 
                'with_reference' => true
            ],
            [
                'id_reason' => '2', 
                'reason_code' => '02', 
                'reason' => 'Comprobante emitido con errores sin relación', 
                'description' => 'Se aplica cuando la factura generada contiene un error en la clave del producto, valor unitario, descuento o cualquier otro dato y no se requiera relacionar con otra factura generada.', 
                'with_reference' => false
            ],
            [
                'id_reason' => '3', 
                'reason_code' => '03', 
                'reason' => 'No se llevó a cabo la operación', 
                'description' => 'Operación nominativa relacionada en la factura global', 
                'with_reference' => false
            ],
            [
                'id_reason' => '4', 
                'reason_code' => '04', 
                'reason' => 'Operación nominativa relacionada en la factura global', 
                'description' => 'Este supuesto aplica cuando se incluye una venta en la factura global de operaciones con el público en general y posterior a ello, el cliente solicita su factura nominativa, lo que conlleva a cancelar la factura global y reexpedirla, así como generar la factura nominativa al cliente', 
                'with_reference' => false
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_documents', function (Blueprint $table) {
                $table->dropForeign(['cancel_reason_id']);
                $table->dropColumn('cancel_reason_id');
        });

        Schema::dropIfExists('f_documents_cancel_reasons');
    }
}
