<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManifestSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_manifest_signatures', function (Blueprint $table) {
            $table->bigIncrements('id_signature');
            $table->boolean('is_signed');
            $table->datetime('signed_at')->nullable();
            $table->bigInteger('signed_by')->nullable()->unsigned();
            $table->bigInteger('carrier_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('signed_by')->references('id')->on('users');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_manifest_signatures');
    }
}
