<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySignLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE f_documents_stamps 
                        CHANGE COLUMN stamp_type stamp_type ENUM('timbre', 'cancelacion', 'decremento', 'carga') NOT NULL,
                        CHANGE COLUMN document_id document_id BIGINT(20) UNSIGNED NULL,
                        CHANGE COLUMN usr_new_id user_by_id BIGINT(20) UNSIGNED NOT NULL;");

        Schema::table('f_documents_stamps', function (Blueprint $table) {
            $table->integer('increase')->after('stamp_type');
            $table->integer('decrement')->after('increase');
            $table->bigInteger('carrier_id')->after('document_id')->unsigned()->default(7);

            $table->foreign('document_id')->references('id_document')->on('f_documents');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('user_by_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_documents_stamps', function (Blueprint $table) {
            $table->dropColumn('increase');
            $table->dropColumn('decrement');
            
            $table->dropForeign(['carrier_id']);
            $table->dropColumn('carrier_id');
            $table->dropForeign(['user_by_id']);
        });

        DB::statement("ALTER TABLE f_documents_stamps 
                        CHANGE COLUMN stamp_type stamp_type ENUM('timbre', 'cancelacion') NOT NULL, 
                        CHANGE COLUMN document_id document_id BIGINT(20) UNSIGNED NOT NULL,
                        CHANGE COLUMN user_by_id usr_new_id BIGINT(20) UNSIGNED NOT NULL;");
    }
}
