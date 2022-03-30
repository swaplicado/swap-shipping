<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRemolqueToSatVehCfgs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sat_veh_cfgs', function (Blueprint $table) {
            $table->Integer('trailer');
        });

        $veh_conf = DB::table('sat_veh_cfgs')->get();

        $values = [2,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,0,0,0,0,0,0,0,0,0];
        $index = 0;
        $id = 1;
        foreach($veh_conf as $v){
            DB::table('sat_veh_cfgs')->where('id', $id)->update(['trailer' => $values[$index]]);
            $id++;
            $index++;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sat_veh_cfgs', function (Blueprint $table) {
            $table->dropColumn('trailer');
        });
    }
}
