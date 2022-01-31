<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPivotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pivotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('trans_figure_id')->unsigned()->nullable();
            $table->bigInteger('carrier_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('trans_figure_id')->references('id_trans_figure')->on('f_trans_figures');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_pivotes');
    }
}
