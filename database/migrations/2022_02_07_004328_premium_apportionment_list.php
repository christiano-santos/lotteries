<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PremiumApportionmentList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_apportionment_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dresscreditions_id')->references('id')->on('dresscreditions');
            $table->integer('faixa');
            $table->integer('numeroDeGanhadores');
            $table->decimal('valorPremio');
            $table->integer('descricaoFaixa');
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
        Schema::dropIfExists('premium_apportionment_lists');
    }
}
