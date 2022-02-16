<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIsActiveInSatUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sat_units', function (Blueprint $table) {
            $table->dropColumn('flag');
            $table->boolean('is_active')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sat_units', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
