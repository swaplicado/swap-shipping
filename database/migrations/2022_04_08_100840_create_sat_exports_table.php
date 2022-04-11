<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSatExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_exports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key_code');
            $table->string('description');
            $table->date('Date_init');
            $table->date('Date_end')->nullable();
        });

        $values = [['01','No aplica', '2022-01-01', null], ['02','Definitiva', '2022-01-01', null], ['03','Temporal', '2022-01-01', null]];
        foreach($values as $v){
            $id = DB::table('sat_exports')->insertGetId(
                array(
                    'key_code' => $v[0],
                    'description' => $v[1],
                    'Date_init' => $v[2],
                    'Date_end' => $v[3]
                )
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_exports');
    }
}
