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
            $table->bigInteger('mun_zone_id')->unsigned();
            $table->string('zip_code');
            $table->timestamps();

            $table->foreign('mun_zone_id')->references('id')->on('f_mun_zones');
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
