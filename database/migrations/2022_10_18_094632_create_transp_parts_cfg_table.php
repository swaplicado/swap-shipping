<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranspPartsCfgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_trans_figures_cfg', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trans_part_id')->unsigned();
            $table->bigInteger('veh_tra_id')->unsigned();
            $table->bigInteger('figure_type_id')->unsigned();
            $table->bigInteger('figure_trans_id')->unsigned();

            $table->foreign('trans_part_id')->references('id')->on('sat_transp_parts')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('figure_type_id')->references('id')->on('sat_figure_types')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('figure_trans_id')->references('id_trans_figure')->on('f_trans_figures')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_trans_figures_cfg');
    }
}
