<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxesConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_tax_configurations', function (Blueprint $table) {
            $table->bigIncrements('id_config');
            $table->enum('config_type', ['traslado', 'retencion']);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->enum('person_type_emisor', ['fisica', 'moral'])->nullable();
            $table->enum('person_type_receptor', ['fisica', 'moral'])->nullable();
            $table->boolean('is_neg_fiscal_reg')->default(false);
            $table->bigInteger('fiscal_regime_id')->unsigned()->nullable();
            $table->boolean('is_neg_concept')->default(false);
            $table->bigInteger('concept_id')->unsigned()->nullable();
            $table->boolean('is_neg_group')->default(false);
            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->bigInteger('tax_id')->unsigned()->nullable();
            $table->decimal('rate', 5, 5)->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->bigInteger('carrier_id')->unsigned()->nullable();
            $table->bigInteger('usr_new_id')->unsigned();
            $table->bigInteger('usr_upd_id')->unsigned();
            $table->timestamps();

            $table->foreign('fiscal_regime_id')->references('id')->on('sat_tax_regimes');
            $table->foreign('concept_id')->references('id')->on('sat_prod_serv');
            $table->foreign('group_id')->references('id_group')->on('f_concepts_groups');
            $table->foreign('tax_id')->references('id')->on('sat_taxes');
            $table->foreign('carrier_id')->references('id_carrier')->on('f_carriers');
            $table->foreign('usr_new_id')->references('id')->on('users');
            $table->foreign('usr_upd_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_tax_configurations');
    }
}
