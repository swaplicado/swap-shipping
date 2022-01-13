<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSatMunicipalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_municipalities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key_code');
            $table->string('municipality_name');
            $table->bigInteger('state_id')->unsigned();
            
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
        Schema::dropIfExists('sat_municipalities');
    }
}
