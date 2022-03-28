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
            $table->string('origen_id');
            $table->char('Local_foreign')->nullable();
            $table->bigInteger('veh_type_id')->unsigned();
            $table->bigInteger('state_id')->unsigned();
            $table->bigInteger('zone_state_id')->unsigned()->nullable();
            $table->bigInteger('mun_id')->unsigned();
            $table->bigInteger('zone_mun_id')->unsigned()->nullable();
            $table->string('zip_code')->nullable();
            $table->string('id_rate')->nullable();
            $table->float('rate')->unsigned();
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
        Schema::dropIfExists('f_carriers_rates');
    }
}
