<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLogTable extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create('m_request_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->int('shipping_folio');
            $table->text('request_body');
            $table->int('response_code');
            $table->int('response_message');
            $table->int('document_id')->nullable();
            $table->int('mongo_document_id')->nullable();
            $table->datetime('dt_request');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('request_log');
    }
}
