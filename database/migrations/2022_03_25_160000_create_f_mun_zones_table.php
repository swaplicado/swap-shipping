<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFMunZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_mun_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('origen_id')->unsigned();
            $table->bigInteger('state_id')->unsigned();
            $table->bigInteger('mun_id')->unsigned();
            $table->string('zone');
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('sat_states');
            $table->foreign('mun_id')->references('id')->on('sat_municipalities');
            $table->foreign('origen_id')->references('id_local_origin')->on('f_local_origins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_mun_zones');
    }
}
