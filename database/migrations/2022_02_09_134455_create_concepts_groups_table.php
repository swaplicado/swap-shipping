<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptsGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_concepts_groups', function (Blueprint $table) {
            $table->bigIncrements('id_group');
            $table->string('group_name', 50);
            $table->string('description');
            $table->boolean('is_deleted')->default(false);
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

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
        Schema::dropIfExists('f_concepts_groups');
    }
}
