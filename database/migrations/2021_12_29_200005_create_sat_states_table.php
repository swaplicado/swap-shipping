<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSatStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_states', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key_code');
            $table->string('state_name');
            $table->decimal('rate', 8, 3)->default(1.000);
            $table->decimal('distance', 8, 3)->default(1.000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_states');
    }
}
