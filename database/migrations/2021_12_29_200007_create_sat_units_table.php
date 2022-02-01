<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSatUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key_code');
            $table->string('description');
            $table->text('notes');
            $table->date('since_date');
            $table->string('symbol');
            $table->string('flag');
            $table->boolean('is_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_units');
    }
}
