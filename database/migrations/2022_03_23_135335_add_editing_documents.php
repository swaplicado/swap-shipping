<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditingDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_documents', function (Blueprint $table) {
            $table->boolean('is_editing')->after('is_deleted');
            $table->datetime('dt_editing')->nullable()->after('is_editing');
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
            $table->dropColumn('is_editing');
            $table->dropColumn('dt_editing');
        });
    }
}
