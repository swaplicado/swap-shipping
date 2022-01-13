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
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('trailer_subtype_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

            $table->foreign('trailer_subtype_id')->references('id')->on('sat_trailer_subtypes');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('usr_new_id')->references('id')->on('users');
            $table->foreign('usr_upd_id')->references('id')->on('users');
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
