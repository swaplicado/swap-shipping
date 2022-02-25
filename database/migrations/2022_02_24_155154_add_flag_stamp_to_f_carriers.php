<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagStampToFCarriers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_carriers', function (Blueprint $table) {
            $table->boolean('delega_stamp')->nullable()->default(0);
            $table->boolean('delega_edit_stamp')->nullable()->default(0);
            $table->boolean('carrier_stamp')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_carriers', function (Blueprint $table) {
            $table->dropColumn('delega_stamp');
            $table->dropColumn('delega_edit_stamp');
            $table->dropColumn('carrier_stamp');
        });
    }
}
