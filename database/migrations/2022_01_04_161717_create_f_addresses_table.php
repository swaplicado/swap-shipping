<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_addresses', function (Blueprint $table) {
            $table->bigIncrements('id_address');
            $table->string('telephone')->nullable();
            $table->string('street')->nullable();
            $table->string('street_num_ext')->nullable();
            $table->string('street_num_int')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('reference')->nullable();
            $table->string('locality')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('trans_figure_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

            $table->foreign('trans_figure_id')->references('id_trans_figure')->on('f_trans_figures');
            $table->foreign('country_id')->references('id_country')->on('s_country');
            $table->foreign('state_id')->references('id')->on('sat_states');
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
        Schema::dropIfExists('f_addresses');
    }
}
