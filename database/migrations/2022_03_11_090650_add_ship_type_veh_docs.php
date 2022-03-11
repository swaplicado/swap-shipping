<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipTypeVehDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_documents', function (Blueprint $table) {
            $table->enum('ship_type', ['F', 'L'])->default('F')->after('scale_ticket');
            $table->bigInteger('veh_key_id')->unsigned()->after('carrier_id')->default(1);

            $table->foreign('veh_key_id')->references('id_key')->on('f_vehicles_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_documents', function (Blueprint $table) {
            $table->dropColumn('ship_type');
            $table->dropForeign(['veh_key_id']);
            $table->dropColumn('veh_key_id');
        });
    }
}
