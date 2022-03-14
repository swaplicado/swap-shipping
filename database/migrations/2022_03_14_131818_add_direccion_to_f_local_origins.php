<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDireccionToFLocalOrigins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_local_origins', function (Blueprint $table) {
            $table->dropColumn('zip_code');
            $table->string('origin_code')->nullable();
            $table->string('tipoUbicacion')->nullable();
            $table->string('rfcRemitenteDestinatario')->nullable();
            $table->string('nombreRFC')->nullable();
            $table->string('calle')->nullable();
            $table->string('numeroExterior')->nullable();
            $table->string('numeroInterior')->nullable();
            $table->string('colonia')->nullable();
            $table->string('localidad')->nullable();
            $table->string('referencia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('estado')->nullable();
            $table->string('pais')->nullable();
            $table->string('codigoPostal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_local_origins', function (Blueprint $table) {
            $table->dropColumn('codigoPostal');
            $table->dropColumn('pais');
            $table->dropColumn('estado');
            $table->dropColumn('municipio');
            $table->dropColumn('referencia');
            $table->dropColumn('localidad');
            $table->dropColumn('colonia');
            $table->dropColumn('numeroInterior');
            $table->dropColumn('numeroExterior');
            $table->dropColumn('calle');
            $table->dropColumn('nombreRFC');
            $table->dropColumn('rfcRemitenteDestinatario');
            $table->dropColumn('tipoUbicacion');
            $table->dropColumn('origin_code');
            $table->string('zip_code');
        });
    }
}
