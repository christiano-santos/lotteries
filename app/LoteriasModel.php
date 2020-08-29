<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class LoteriasModel extends Model
{
    public function getTodosJogos(){

        $result = DB::select('select g.name, c.contest,c.accumulated, d.next_contest, d.total_collection, d.award, r.* from contests c left join games g on g.id = c.game_id left join results r on c.id = r.contest_id right join dresscreditions d on c.id = d.contest_id where c.contest >= (select MAX(c2.contest) from  contests c2 where  c2.game_id = c.game_id ) order by c.created_at desc');

        // $result = DB::table('contests')
        // ->leftJoin('games','contests.game_id','=','games.id')
        // ->leftJoin('results','results.contest_id','=','contests.id')
        // ->rightJoin('dresscreditions','contests.id','=','dresscreditions.contest_id')
        // ->select('games.name','contests.contest','contests.accumulated','results.*','dresscreditions.next_contest','dresscreditions.total_collection','dresscreditions.award')
        // // ->groupBy('games.name')
        // ->orderByDesc('contests.created_at')
        // ->limit(7)
        // ->get();
        // //print_r($result);
        return $result;
    }

    public function getNomeJogos(){
        $games = DB::table('games')->get();
        return $games;
        //print_r($games);
    }

    public function getPorJogo($game){
        $result = DB::table('contests')
        ->leftJoin('games','contests.game_id','=','games.id')
        ->leftJoin('results','results.contest_id','=','contests.id')
        ->rightJoin('dresscreditions','contests.id','=','dresscreditions.contest_id')
        ->select('games.name','contests.contest','contests.accumulated','results.*','dresscreditions.next_contest','dresscreditions.total_collection','dresscreditions.award')
        ->where('games.name', '=', $game)
        ->orderByDesc('contests.created_at')
        ->limit(3)
        ->get();
        //print_r($result);
        return $result;
    }

    public function getPorJogoConcurso($jogoConc){
        $result = DB::table('contests')
        ->leftJoin('games','contests.game_id','=','games.id')
        ->leftJoin('results','results.contest_id','=','contests.id')
        ->rightJoin('dresscreditions','contests.id','=','dresscreditions.contest_id')
        ->select('games.name','contests.contest','contests.accumulated','results.*','dresscreditions.next_contest','dresscreditions.total_collection','dresscreditions.award')
        ->where($jogoConc)
        ->orderByDesc('contests.created_at')
        ->get();
        //print_r($result);
        return $result;
    }
}
//Estimativa de prêmio do próximo concurso 13,00 250000 