<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id_vehicle');
            $table->string('plates');
            $table->Integer('year_model');
            $table->bigInteger('license_sct_id')->unsigned();
            $table->string('license_sct_num');
            $table->string('drvr_reg_trib');
            $table->bigInteger('veh_cfg_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->timestamps();

            $table->foreign('license_sct_id')->references('id')->on('sat_sct_licenses');
            $table->foreign('veh_cfg_id')->references('id')->on('sat_veh_cfgs');
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
        Schema::dropIfExists('f_vehicles');
    }
}
