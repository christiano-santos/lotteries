<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ListCityStateWinners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_city_state_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dresscreditions_id')->references('id')->on('dresscreditions');
            $table->integer('posicao');
            $table->integer('ganhadores');
            $table->string('municipio',60);
            $table->string('uf',2);
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
        Schema::dropIfExists('list_city_state_winners');
    }
}
