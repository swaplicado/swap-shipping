<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignLogCollection extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)->create('sign_cfdi_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message');
            $table->string('idError', 20);
            $table->string('mongoDocumentId');
            $table->bigInteger('idDocument');
            $table->bigInteger('idUser');
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
        Schema::connection($this->connection)->drop('sign_cfdi_log');
    }
}
