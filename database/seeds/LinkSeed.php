<?php

use App\Game;
use Illuminate\Database\Seeder;

class LinkSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $links = [
            ["id" => 1, "link_frontend" => "mega"],
            ["id" => 2, "link_frontend" => "lotofacil"],
            ["id" => 3, "link_frontend" => "quina"],
            ["id" => 4, "link_frontend" => "lotomania"],
            ["id" => 5, "link_frontend" => "timemania"],
            ["id" => 6, "link_frontend" => "dupla-sena"],
            ["id" => 7, "link_frontend" => "dia-de-sorte"]
        ];

        foreach ($links as $link) {
            print 'inserindo link jogo: ' . $link["link_frontend"] . "\n";
            Game::where(['id' => $link['id']])->update(['link_frontend' => $link["link_frontend"]]);
        }
    }
}
