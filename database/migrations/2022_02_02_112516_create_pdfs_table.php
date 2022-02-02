<?php

use Illuminate\Support\Facades\Schema;
// use Illuminate\Database\Schema\Blueprint;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdfsTable extends Migration
{

    protected $connection = 'mongodb';
    public function up()
    {
        Schema::connection($this->connection)->create('pdfs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('pdf');
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
        Schema::connection($this->connection)->drop('pdfs');
    }
}
