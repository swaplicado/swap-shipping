<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalOriginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_local_origins', function (Blueprint $table) {
            $table->bigIncrements('id_local_origin');
            $table->string('zip_code', 5);
            $table->boolean('is_deleted');
        });

        DB::table('f_local_origins')->insert(array(
            ['id_local_origin' => 1, 'zip_code' => "54616", 'is_deleted' => false],
        ));

        Schema::create('f_local_locations', function (Blueprint $table) {
            $table->bigIncrements('id_local_l');
            $table->string('zip_code', 5)->nullable();
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->bigInteger('municipality_id')->unsigned()->nullable();
            $table->bigInteger('origin_id')->unsigned();
            $table->boolean('is_deleted');

            $table->foreign('state_id')->references('id')->on('sat_states');
            $table->foreign('municipality_id')->references('id')->on('sat_municipalities');
            $table->foreign('origin_id')->references('id_local_origin')->on('f_local_origins');
        });

        DB::table('f_local_locations')->insert(array(
            ['zip_code' => null, 'state_id' => 15, 'municipality_id' => 1626, 'origin_id' => 1, 'is_deleted' => false],
            ['zip_code' => "54616", 'state_id' => null, 'municipality_id' => null, 'origin_id' => 1, 'is_deleted' => false],
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_local_locations');
        Schema::dropIfExists('f_local_origins');
    }
}
