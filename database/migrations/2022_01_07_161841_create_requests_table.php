<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_document_requests', function (Blueprint $table) {
            $table->bigIncrements('id_request');
            $table->date('dt_request');
            $table->string('comp_version');
            $table->string('xml_version');
            $table->boolean('is_processed');
            $table->boolean('is_deleted');
            $table->string('body_request_id');
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
        Schema::dropIfExists('f_document_requests');
    }
}
