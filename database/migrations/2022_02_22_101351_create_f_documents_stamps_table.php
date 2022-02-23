<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFDocumentsStampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_documents_stamps', function (Blueprint $table) {
            $table->bigIncrements('id_doc_stamp');
            $table->datetime('dt_stamp');
            $table->enum('stamp_type', ['timbre', 'cancelacion']);
            $table->bigInteger('document_id')->unsigned();
            $table->bigInteger('usr_new_id')->unsigned();
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
        Schema::dropIfExists('f_documents_stamps');
    }
}
