<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class IndexController extends Controller
{
    public function index()
    {
        $response = DB::select('select g.id as id_jogo, g.name as nome_jogo, c.contest as concurso, r.first_decade, r.second_decade, r.third_decade, r.fourth_decade, r.fifth_decade, r.sixth_decade, r.seventh_decade, r.eighth_decade, r.ninth_decade, r.tenth_decade, r.eleventh_decade, r.twelfth_decade, r.thirteenth_decade, r.fourteenth_decade, r.fifteenth_decade, r.sixteenth_decade, r.seventeenth_decade, r.eighteenth_decade, r.nineteenth_decade, r.twentieth_decade, d.next_contest, d.total_collection, d.award from lotteries.games as g join lotteries.contests as c on c.game_id = g.id join lotteries.results as r on c.id = r.contest_id join lotteries.dresscreditions d on c.id = d.contest_id where c.contest = (select MAX(c2.contest) from lotteries.contests c2 where c2.game_id = g.id )');
        return response($response,'200');
    }

    public function getLastFiveGame(Request $request){
        $response = DB::select("select g.name as nome_jogo, c.contest as concurso, r.first_decade, r.second_decade, r.third_decade, r.fourth_decade, r.fifth_decade, r.sixth_decade, r.seventh_decade, r.eighth_decade,	r.ninth_decade,	r.tenth_decade,	r.eleventh_decade, r.twelfth_decade, r.thirteenth_decade, r.fourteenth_decade, r.fifteenth_decade, r.sixteenth_decade, r.seventeenth_decade, r.eighteenth_decade, r.nineteenth_decade, r.twentieth_decade, d.next_contest, d.total_collection, d.award from lotteries.games as g join lotteries.contests as c on c.game_id = g.id join lotteries.results as r on c.id = r.contest_id join lotteries.dresscreditions d on c.id = d.contest_id where g.id = $request->id_game order by d.next_contest desc limit 2");
        return response($response,'200');
    }

    public function getGameByContest(Request $request){
        $response = DB::select("select g.name as nome_jogo, c.contest as concurso, r.first_decade, r.second_decade,	r.third_decade,	r.fourth_decade, r.fifth_decade, r.sixth_decade, r.seventh_decade, r.eighth_decade,	r.ninth_decade,	r.tenth_decade,	r.eleventh_decade, r.twelfth_decade, r.thirteenth_decade, r.fourteenth_decade, r.fifteenth_decade, r.sixteenth_decade, r.seventeenth_decade, r.eighteenth_decade, r.nineteenth_decade, r.twentieth_decade, d.next_contest, d.total_collection, d.award from lotteries.games as g join lotteries.contests as c on c.game_id = g.id join lotteries.results as r on c.id = r.contest_id join lotteries.dresscreditions d on c.id = d.contest_id where g.id = $request->id_game and c.contest = $request->concurso");
        return response($response,'200');
    }
}
