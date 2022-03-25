<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFCarriersRates extends Migration
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
            $table->bigInteger('veh_type_id')->unsigned();
            $table->char('ship_type');
            // $table->Integer('veh_type_id');
            $table->bigInteger('mun_id')->unsigned();
            $table->bigInteger('state_id')->unsigned();
            $table->string('id_tarifa')->nullable();
            $table->float('rate');
            $table->boolean('is_official')->default(0);
            $table->boolean('is_reparto')->default(0);
            $table->timestamps();

            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('veh_type_id')->references('id_key')->on('f_vehicles_keys');
            $table->foreign('mun_id')->references('id')->on('sat_municipalities');
            $table->foreign('state_id')->references('id')->on('sat_states');
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
