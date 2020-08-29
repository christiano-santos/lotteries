<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->references('id')->on('contests');
            $table->integer('first_decade')->nullable();
            $table->integer('second_decade')->nullable();
            $table->integer('third_decade')->nullable();
            $table->integer('fourth_decade')->nullable();
            $table->integer('fifth_decade')->nullable();
            $table->integer('sixth_decade')->nullable();
            $table->integer('seventh_decade')->nullable();
            $table->integer('eighth_decade')->nullable();
            $table->integer('ninth_decade')->nullable();
            $table->integer('tenth_decade')->nullable();
            $table->integer('eleventh_decade')->nullable();
            $table->integer('twelfth_decade')->nullable();
            $table->integer('thirteenth_decade')->nullable();
            $table->integer('fourteenth_decade')->nullable();
            $table->integer('fifteenth_decade')->nullable();
            $table->integer('sixteenth_decade')->nullable();
            $table->integer('seventeenth_decade')->nullable();
            $table->integer('eighteenth_decade')->nullable();
            $table->integer('nineteenth_decade')->nullable();
            $table->integer('twentieth_decade')->nullable();
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
        Schema::dropIfExists('results');
    }
}
