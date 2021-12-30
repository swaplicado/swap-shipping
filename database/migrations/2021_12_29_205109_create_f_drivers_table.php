<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_drivers', function (Blueprint $table) {
            $table->bigIncrements('id_driver');
            $table->string('fullname');
            $table->string('fiscal_id');
            $table->string('drvr_license');
            $table->string('drvr_reg_trib');
            $table->bigInteger('fis_address_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('fis_address_id')->references('id')->on('sat_fiscal_addresses');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_drivers');
    }
}
