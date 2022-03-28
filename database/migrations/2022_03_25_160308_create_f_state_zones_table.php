<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFStateZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_state_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origen_id');
            $table->bigInteger('state_id')->unsigned();
            $table->string('zone');
            $table->bigInteger('mun_id')->unsigned();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('sat_states');
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
        Schema::dropIfExists('f_state_zones');
    }
}
