<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('full_name');
            $table->bigInteger('user_type_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_type_id')->references('id_user_type')->on('user_types');
        });

        DB::table('users')->insert(
            array(
                'username' => 'admin',
                'email' => 'edwin.carmona@swaplicado.com.mx',
                'password' => bcrypt('123456'),
                'full_name' => 'Edwin Carmona',
                'user_type_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
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
        Schema::dropIfExists('users');
    }
}
