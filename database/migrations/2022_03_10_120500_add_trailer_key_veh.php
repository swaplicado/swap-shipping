<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrailerKeyVeh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_vehicles_keys', function (Blueprint $table) {
            $table->bigIncrements('id_key');
            $table->string('key_code');
            $table->string('description');
            $table->integer('local_digit');
            $table->integer('foreign_digit');
        });

        DB::table('f_vehicles_keys')->insert(array(
            ['id_key' => 1, 'key_code' => 'T48', 'description' => 'Tráiler 48', 'local_digit' => 0, 'foreign_digit' => 5],
            ['id_key' => 2, 'key_code' => 'T53', 'description' => 'Tráiler 53', 'local_digit' => 0, 'foreign_digit' => 5],
            ['id_key' => 3, 'key_code' => 'M', 'description' => 'Mudancero', 'local_digit' => 1, 'foreign_digit' => 6],
            ['id_key' => 4, 'key_code' => 'TH', 'description' => 'Thorton', 'local_digit' => 2, 'foreign_digit' => 7],
            ['id_key' => 5, 'key_code' => 'C1', 'description' => 'Camioneta Grande', 'local_digit' => 3, 'foreign_digit' => 8],
            ['id_key' => 6, 'key_code' => 'C2', 'description' => 'Camioneta Chica', 'local_digit' => 4, 'foreign_digit' => 9],
        ));

        Schema::table('f_vehicles', function (Blueprint $table) {
            $table->bigInteger('veh_key_id')->unsigned()->nullable()->after('veh_cfg_id')->default(1);

            $table->foreign('veh_key_id')->references('id_key')->on('f_vehicles_keys');
        });

        Schema::dropIfExists('f_trailers_keys');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_vehicles', function (Blueprint $table) {
            $table->dropForeign(['veh_key_id']);
            $table->dropColumn('veh_key_id');
        });

        Schema::dropIfExists('f_vehicles_keys');
    }
}
