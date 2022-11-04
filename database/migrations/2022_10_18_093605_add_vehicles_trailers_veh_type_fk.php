<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehiclesTrailersVehTypeFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_vehicles', function (Blueprint $table) {
            $table->boolean('is_own')->after('policy')->default(true);
            $table->bigInteger('trans_part_n_id')->after('veh_key_id')->default(null)->nullable()->unsigned();
            
            $table->foreign('trans_part_n_id')->references('id')->on('sat_transp_parts')->onDelete('restrict')->onUpdate('restrict');
        });

        Schema::table('f_trailers', function (Blueprint $table) {
            $table->boolean('is_own')->after('plates')->default(true);
            $table->bigInteger('trans_part_n_id')->after('trailer_subtype_id')->default(null)->nullable()->unsigned();

            $table->foreign('trans_part_n_id')->references('id')->on('sat_transp_parts')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_trailers', function (Blueprint $table) {
            $table->dropForeign(['trans_part_n_id']);
            $table->dropColumn('trans_part_n_id');
            $table->dropColumn('is_own');
        });
        Schema::table('f_vehicles', function (Blueprint $table) {
            $table->dropForeign(['trans_part_n_id']);
            $table->dropColumn('trans_part_n_id');
            $table->dropColumn('is_own');
        });
    }
}
