<?php

use App\ColorTitleGame;
use Illuminate\Database\Seeder;

class CssClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classCss = [
            [1,'mega'],
            [2,'lotofacilcolor'],
            [3,'quina'],
            [4,'lotomania'],
            [5,'timemaniadezcolor'],
            [6,'duplasenacolor'],
            [7,'diadesorte']
        ];

        foreach ($classCss as $css) {
            print 'inserindo jogo: '.$css[1]."\n";
            ColorTitleGame::create(['game_id'=>$css[0], 'name'=>$css[1]]);
        }
    }
}
