<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelStatusDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_documents', function (Blueprint $table) {
            $table->enum('cancel_status', ['cancelado', 'pendiente', 'vigente'])->default('vigente')->after('is_canceled');
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
            $table->dropColumn('cancel_status');
        });
    }
}
