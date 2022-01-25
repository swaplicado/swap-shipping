<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_documents', function (Blueprint $table) {
            $table->bigIncrements('id_document');
            $table->date('dt_request');
            $table->date('dt_generated');
            $table->string('comp_version');
            $table->string('xml_version');
            $table->boolean('is_processed')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->string('mongo_document_id');
            $table->bigInteger('carrier_id')->unsigned();
            $table->bigInteger('usr_gen_id')->unsigned();
            $table->bigInteger('usr_sign_id')->unsigned();
            $table->bigInteger('usr_can_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('usr_gen_id')->references('id')->on('users');
            $table->foreign('usr_sign_id')->references('id')->on('users');
            $table->foreign('usr_can_id')->references('id')->on('users');
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
        Schema::dropIfExists('f_documents');
    }
}
