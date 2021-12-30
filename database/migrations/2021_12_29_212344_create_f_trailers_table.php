<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFTrailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_trailers', function (Blueprint $table) {
            $table->bigIncrements('id_trailer');
            $table->string('plates');
            $table->bigInteger('trailer_subtype_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->timestamps();

            $table->foreign('trailer_subtype_id')->references('id')->on('sat_trailer_subtypes');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_trailers');
    }
}
