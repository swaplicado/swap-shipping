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
            $table->string('license_sct_num');
            $table->string('drvr_reg_trib');
            $table->string('policy');
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('license_sct_id')->unsigned();
            $table->bigInteger('veh_cfg_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

            $table->foreign('license_sct_id')->references('id')->on('sat_sct_licenses');
            $table->foreign('veh_cfg_id')->references('id')->on('sat_veh_cfgs');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('usr_new_id')->references('id')->on('users');
            $table->foreign('usr_upd_id')->references('id')->on('users');
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
