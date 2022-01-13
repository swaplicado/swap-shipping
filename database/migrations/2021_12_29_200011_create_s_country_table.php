<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_country', function (Blueprint $table) {
            $table->bigIncrements('id_country');
            $table->string('country_key');
            $table->string('country');
            $table->string('country_lan');
            $table->string('country_abbr');
            $table->string('country_code');
            $table->string('country_group');
            $table->string('diot_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_country');
    }
}
