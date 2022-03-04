<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypePolicyToFVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_vehicles', function (Blueprint $table) {
            $table->boolean('is_civ_resp')->nullable()->default(1);
            $table->boolean('is_ambiental')->nullable()->default(0);
            $table->boolean('is_cargo')->nullable()->default(0);
        });

        Schema::table('f_insurances', function (Blueprint $table) {
            $table->dropColumn('is_civ_resp');
            $table->dropColumn('is_ambiental');
            $table->dropColumn('is_cargo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_vehicles', function (Blueprint $table) {
            $table->dropColumn('is_civ_resp');
            $table->dropColumn('is_ambiental');
            $table->dropColumn('is_cargo');
        });
    }
}
