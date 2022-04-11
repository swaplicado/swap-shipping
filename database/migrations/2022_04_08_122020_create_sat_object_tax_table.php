<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSatObjectTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_object_tax', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key_code');
            $table->string('description');
            $table->date('date_init');
        });

        $values = [['01','No objeto de impuesto', '2022-01-01'], ['02','Sí objeto de impuesto', '2022-01-01'], ['03','Sí objeto del impuesto y no obligado al desglose', '2022-01-01']];
        foreach($values as $v){
            $id = DB::table('sat_object_tax')->insertGetId(
                array(
                    'key_code' => $v[0],
                    'description' => $v[1],
                    'date_init' => $v[2]
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
        Schema::dropIfExists('sat_object_tax');
    }
}
