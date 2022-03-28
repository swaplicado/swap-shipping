<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFMunZonesCpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_mun_zones_cp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origen_id');
            $table->bigInteger('mun_id')->unsigned();
            $table->string('zone');
            $table->string('zip_code');
            $table->timestamps();

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
        Schema::dropIfExists('f_mun_zones_cp');
    }
}
