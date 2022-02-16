<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptsVsGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_concepts_vs_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('concept_id')->unsigned();
            $table->bigInteger('group_id')->unsigned();
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->timestamps();

            $table->foreign('concept_id')->references('id')->on('sat_prod_serv');
            $table->foreign('group_id')->references('id_group')->on('f_concepts_groups');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('usr_new_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_concepts_vs_groups');
    }
}
