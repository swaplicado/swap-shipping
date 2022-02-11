<?php

use Illuminate\Support\Facades\Schema;
// use Illuminate\Database\Schema\Blueprint;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsCollection extends Migration
{
    protected $connection = 'mongodb';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create('m_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('body_request');
            $table->string('xml_cfdi');
            $table->bigInteger('carrier_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->drop('m_documents');
    }
}
