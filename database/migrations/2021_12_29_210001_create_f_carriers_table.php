<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_carriers', function (Blueprint $table) {
            $table->bigIncrements('id_carrier');
            $table->string('fullname');
            $table->string('fiscal_id')->unique();
            $table->string('telephone1');
            $table->string('telephone2')->nullable();
            $table->string('contact1');
            $table->string('contact2')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->bigInteger('tax_regimes_id')->unsigned()->nullable();
            $table->bigInteger('prod_serv_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('tax_regimes_id')->references('id')->on('sat_tax_regimes');
            $table->foreign('prod_serv_id')->references('id')->on('sat_prod_serv');
            $table->foreign('usr_new_id')->references('id')->on('users');
            $table->foreign('usr_upd_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_carriers');
    }
}
