<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistanceToSatMunicipalities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sat_municipalities', function (Blueprint $table) {
            $table->decimal('distance', 8, 3)->default(1.000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sat_municipalities', function (Blueprint $table) {
            $table->dropColumn('distance');
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
        });
    }
}
