<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('role_id');
            $table->bigInteger('user_id')->unsigned();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        $id = DB::table('users')->insertGetId(
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

        DB::table('role_user')->insert(
            array(
                'role_id' => 1,
                'user_id' => $id,
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
        Schema::dropIfExists('role_user');
    }
}
