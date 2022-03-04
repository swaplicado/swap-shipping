<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFTrailersKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_trailers_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trailer_key');
            $table->string('key_code');
            $table->string('description');
            $table->timestamps();
        });

        DB::table('f_trailers_keys')->insert(array(
            array ('trailer_key' => 0, 'key_code'  => "FT53", 'description' => "Fóraneo Tráiler 53´", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 0, 'key_code'  => "FT48", 'description' => "Fóraneo Tráiler 48´", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 1, 'key_code'  => "FM", 'description' => "Fóraneo Mudancero", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 2, 'key_code'  => "FTH", 'description' => "Fóraneo Thorton", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 3, 'key_code'  => "FC1", 'description' => "Fóraneo Camioneta Grande", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 4, 'key_code'  => "FC2", 'description' => "Fóraneo Camioneta Chica", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 5, 'key_code'  => "LT53", 'description' => "Local Tráiler 53´", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 5, 'key_code'  => "LT48", 'description' => "Local Tráiler 48´", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 6, 'key_code'  => "LM", 'description' => "Local Mudancero", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 7, 'key_code'  => "LTH", 'description' => "Local Thorton", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 8, 'key_code'  => "LC1", 'description' => "Local Camioneta Grande", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array ('trailer_key' => 9, 'key_code'  => "LC2", 'description' => "Local Camioneta Chica", 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_trailers_keys');
    }
}
