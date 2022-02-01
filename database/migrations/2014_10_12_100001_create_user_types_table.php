<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->bigIncrements('id_user_type');
            $table->string('code_user_type');
            $table->string('user_type');
            $table->boolean('is_deleted')->default(0);
        });

        DB::table('user_types')->insert(
            array(
                'code_user_type' => 'ADM',
                'user_type' => 'Administrador'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_types');
    }
}
