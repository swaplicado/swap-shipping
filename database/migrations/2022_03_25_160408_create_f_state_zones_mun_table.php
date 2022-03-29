<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFStateZonesMunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_state_zones_mun', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('state_zone_id')->unsigned();
            $table->bigInteger('mun_id')->unsigned();
            $table->timestamps();

            $table->foreign('state_zone_id')->references('id')->on('f_state_zones');
            $table->foreign('mun_id')->references('id')->on('sat_municipalities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_state_zones_mun');
    }
}
