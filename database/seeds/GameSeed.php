<?php

use Illuminate\Database\Seeder;
use App\Game;
class GameSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = ['MEGA-SENA','LOTOFÁCIL','QUINA','LOTOMANIA','TIMEMANIA','DUPLA SENA','DIA DE SORTE'];
        foreach ($games as $game) {
            print 'inserindo jogo: '.$game."\n";
            Game::create(['name'=>$game]);
        }
    }
}
