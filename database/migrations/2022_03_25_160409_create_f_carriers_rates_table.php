<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFCarriersRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_carriers_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('origen_id')->unsigned();
            $table->char('Local_foreign')->nullable();
            $table->bigInteger('veh_type_id')->unsigned();
            $table->bigInteger('state_id')->unsigned();
            $table->bigInteger('zone_state_id')->unsigned()->nullable();
            $table->bigInteger('mun_id')->unsigned();
            $table->bigInteger('zone_mun_id')->unsigned()->nullable();
            $table->string('id_rate')->nullable();
            $table->float('rate')->unsigned();
            $table->timestamps();

            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('origen_id')->references('id_local_origin')->on('f_local_origins');
            $table->foreign('veh_type_id')->references('id_key')->on('f_vehicles_keys');
            $table->foreign('state_id')->references('id')->on('sat_states');
            $table->foreign('zone_state_id')->references('id')->on('f_state_zones');
            $table->foreign('mun_id')->references('id')->on('sat_municipalities');
            $table->foreign('zone_mun_id')->references('id')->on('sat_municipalities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_carriers_rates');
    }
}
