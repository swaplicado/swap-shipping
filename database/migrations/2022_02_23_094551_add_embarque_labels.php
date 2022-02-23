<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmbarqueLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_documents', function (Blueprint $table) {
            $table->string('shipping_folio', 15)->default("00")->after("folio");
            $table->string('scale_ticket', 15)->default("00")->after("shipping_folio");
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
            $table->dropColumn('shipping_folio');
            $table->dropColumn('scale_ticket');
        });
    }
}
