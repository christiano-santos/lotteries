<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDresscreditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dresscreditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->references('id')->on('games');
            $table->foreignId('contest_id')->references('id')->on('contests');
            $table->text('next_contest')->nullable();//estimativa para o proximo concurso
            $table->string('total_collection');//total arrecadado
            $table->text('award');//premiação
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
        Schema::dropIfExists('dresscreditions');
    }
}
