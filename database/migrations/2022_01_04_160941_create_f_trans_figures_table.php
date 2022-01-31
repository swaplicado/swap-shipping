<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFTransFiguresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_trans_figures', function (Blueprint $table) {
            $table->bigIncrements('id_trans_figure');
            $table->string('fullname');
            $table->string('fiscal_id')->unique();
            $table->string('fiscal_fgr_id')->nullable();
            $table->string('driver_lic');
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('tp_figure_id')->unsigned();
            $table->bigInteger('fis_address_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

            $table->foreign('tp_figure_id')->references('id')->on('sat_figure_types');
            $table->foreign('fis_address_id')->references('id')->on('sat_fiscal_addresses');
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
        Schema::dropIfExists('f_trans_figures');
    }
}
