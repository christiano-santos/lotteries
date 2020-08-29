<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoteriasModel;
class LoteriasController extends Controller
{

    public function getNextContest($game){
        $estimativeNextPrize = '';
        $nextContest = $game->next_contest;
        $contest = explode(" ",$nextContest);
        if ($game->name == 'LOTOFÁCIL') {
            $contest[8] = 'R$ '.$contest[8];
            for ($i=1; $i <= 8; $i++) { 
                $estimativeNextPrize = $estimativeNextPrize.$contest[$i]." ";
            }
            return $estimativeNextPrize; 
        }
        $valor = (double) $contest[8];
        $contest[8] = "R$ ".number_format($valor,2,',','.');
        for ($i=1; $i <= 8; $i++) { 
            $estimativeNextPrize = $estimativeNextPrize.$contest[$i]." ";
        }
        return $estimativeNextPrize; 
    }
    public function getDate($nextContest){
        $dateContest = explode(" ",$nextContest);
        return $dateContest[0];
    }
    public function getNumbers($data){
        $numbers = [];
        $pos = ["first_decade","second_decade","third_decade","fourth_decade","fifth_decade","sixth_decade","seventh_decade","eighth_decade","ninth_decade","tenth_decade","eleventh_decade","twelfth_decade","thirteenth_decade","fourteenth_decade","fifteenth_decade","sixteenth_decade","seventeenth_decade","eighteenth_decade","nineteenth_decade","twentieth_decade"];
        if($data->name == 'DUPLA SENA'){
            $primeiroSorteio = [];
            $segundoSorteio = [];
            for($i = 0; $i <= count($pos); $i++){
                if($i <= 5){
                   $num =  $data->{$pos[$i]};
                   if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                    }
                    array_push($primeiroSorteio,$num);
                }else{
                    if(isset($data->{$pos[$i]})){
                        $num = $data->{$pos[$i]};
                        if ($num < 10) {
                            $num = '0'.$data->{$pos[$i]};
                        }
                        array_push($segundoSorteio,$num);
                    }else{
                    break;
                    }
                }
            }
            array_push($numbers,$primeiroSorteio);
            array_push($numbers,$segundoSorteio);
            //print_r($numbers);
            return $numbers;
        }
        if ($data->name == 'LOTOMANIA') {
            $numbers1 = [];
            $numbers2 = [];
            $numbers3 = [];
            $numbers4 = [];
            for ($i=0; $i <= 4; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers1,$num);
            }
            for ($i=5; $i <= 9; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers2,$num);
            }
            for ($i=10; $i <= 14; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers3,$num);
            }
            for ($i=15; $i <= 19; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers4,$num);
            }
            array_push($numbers,$numbers1);
            array_push($numbers,$numbers2);
            array_push($numbers,$numbers3);
            array_push($numbers,$numbers4);
            return $numbers;
        }
        if ($data->name == 'LOTOFÁCIL') {
            $numbers1 = [];
            $numbers2 = [];
            $numbers3 = [];

            for ($i=0; $i <= 4; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers1,$num);
            }
            for ($i=5; $i <= 9; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers2,$num);
            }
            for ($i=10; $i <= 14; $i++) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers3,$num);
            }
            array_push($numbers,$numbers1);
            array_push($numbers,$numbers2);
            array_push($numbers,$numbers3);
            //print_r($numbers);
        }
        for ($i=0; $i <= count($pos); $i++) {
            if (isset($data->{$pos[$i]})) {
                $num = $data->{$pos[$i]};
                if ($num < 10) {
                    $num = '0'.$data->{$pos[$i]};
                }
                array_push($numbers,$num);
            }else{
                break;
            }
        }
        return $numbers;
    }
    public function getAward($game,$award){
        $results = [];
        $resultsAwars = [];//array de titutos das tabelas
        $resultsAwarsValues = [];
        $resultsAwarsQuantity = [];
        switch ($game) {
            case 'MEGA-SENA':
                $tempGetAward = explode(" ",$award);
                array_push($resultsAwars,$tempGetAward[0]);
                array_push($resultsAwars,$tempGetAward[9]);
                array_push($resultsAwars,$tempGetAward[18]);
                $tF = true;
                $x = 5;$y = 6;$z = 7;$w = 8;
                for ($i=0; $i <= 2; $i++) { 
                    if($tempGetAward[5] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity,$temp2);
                    $x+=9;$y=$x+1;$z=$y+1;$w+=9;
                }
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity);
                array_push($results,$resultsAwarsValues);
                return $results;
                break;
            case 'DIA DE SORTE':
                $tempGetAward = explode(" ",$award);
                $x = 0;$y = 1; $z = 2;
                for($i = 0; $i <= 3; $i++){
                    $tempDia = $tempGetAward[$x].' '.$tempGetAward[$y].' '.$tempGetAward[$z];
                    $tempDiaS = explode(",",$tempDia);
                    $tempDia = $tempDiaS[0];
                    array_push($resultsAwars,$tempDia);
                    $x+=7;$y=$x+1;$z=$y+1;
                }
                $meses = ['JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTRUBRO','NOVEMBRO','DEZEMBRO'];
                $mes = (int) $tempGetAward[31];
                $mes -= 1;
                $mesSorteExtenso = $meses[$mes];
                $mesSorte = $tempGetAward[28].' '.$tempGetAward[29].' '.$tempGetAward[30].' '.$mesSorteExtenso;
                array_push($resultsAwars,$mesSorte);
                //print_r($resultsAwars);
                $tF = true;
                $x = 3;$y = 4;$z = 5;$w = 6;
                for ($i=0; $i <= 3; $i++) { 
                    if($tempGetAward[6] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity,$temp2);
                    $x+=7;$y=$x+1;$z=$y+1;$w=$z+1;
                }
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity);
                // print_r($results);
                //array_push($results,$resultsAwarsValues);
                return $results;
                break;
            case 'DUPLA SENA':
                $resultsAwarsQuantity1 = [];
                $resultsAwarsQuantity2 = [];
                $tempGetAward = explode(" ",$award);
                array_push($resultsAwars,$tempGetAward[3]);
                array_push($resultsAwars,$tempGetAward[4]);
                array_push($resultsAwars,$tempGetAward[11]);
                array_push($resultsAwars,$tempGetAward[18]);
                array_push($resultsAwars,$tempGetAward[25]);
                $tF = true;
                $x = 7;$y = 8;$z = 9;$w = 10;
                for ($i=0; $i <= 3; $i++) { 
                    if($tempGetAward[7] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity1,$temp2);
                    $x+=7;$y=$x+1;$z=$y+1;$w=$z+1;
                }
                //adiona o numero do sorteio na primeira posição do array
                array_unshift($resultsAwarsQuantity1,'1º');
                //segundo sorteio
                $tF = true;
                $x = 39;$y = 40;$z = 41;$w = 42;
                for ($i=0; $i <= 3; $i++) { 
                    if($tempGetAward[7] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity2,$temp2);
                    $x+=7;$y=$x+1;$z=$y+1;$w=$z+1;
                }
                //adiona o numero do sorteio na primeira posição do array
                array_unshift($resultsAwarsQuantity2,'2º');
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity1);
                array_push($results,$resultsAwarsQuantity2);
                array_push($results,$resultsAwarsValues);
                return $results;
                break;
            case 'TIMEMANIA':
                $tempGetAward = explode(" ",$award);
                $x = 0;$y = 1; $z = 2;
                for($i = 0; $i <= 4; $i++){
                    $tempDia = $tempGetAward[$x].' '.$tempGetAward[$y].' '.$tempGetAward[$z];
                    $tempDiaS = explode(",",$tempDia);
                    $tempDia = $tempDiaS[0];
                    array_push($resultsAwars,$tempDia);
                    $x+=7;$y=$x+1;$z=$y+1;
                }
                $time = $tempGetAward[35]." ".$tempGetAward[36]." ".$tempGetAward[37]." ".$tempGetAward[38];
                array_push($resultsAwars,$time);
                $tF = true;
                $x = 3;$y = 4;$z = 5;$w = 6;
                for ($i=0; $i <= 4; $i++) { 
                    if($tempGetAward[6] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity,$temp2);
                    $x+=7;$y=$x+1;$z=$y+1;$w=$z+1;
                }
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity);
                return $results;
                //print_r($results);
                break;
            case 'LOTOMANIA':
                $resultsAwarsQuantity1 = [];
                $tempGetAward = explode(" ",$award);
                $x = 0;$y = 1;$z = 2;
                for($i = 0; $i <= 5; $i++){
                    $tempDia = $tempGetAward[$x].' '.$tempGetAward[$y].' '.$tempGetAward[$z];
                    $tempDiaS = explode(",",$tempDia);
                    $tempDia = $tempDiaS[0];
                    array_push($resultsAwars,$tempDia);
                    $x+=7;$y=$x+1;$z=$y+1;
                }
                //pega o acertors
                $tempDia = $tempGetAward[42].' '.$tempGetAward[43];
                array_push($resultsAwars,$tempDia);
                $tF = true;
                $x = 3;$y = 4;$z = 5;$w = 6;
                for ($i=0; $i <= 5; $i++) { 
                    if($tempGetAward[3] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity1,$temp2);
                    $x+=7;$y=$x+1;$z=$y+1;$w=$z+1;
                }
                //pega se não houve nenhum número acerrtado
                if($tempGetAward[42] == '0'){
                    $temp2 = 'Não houve acertador';
                    $tF = false;  
                }else{
                    $temp2 = $tempGetAward[44]." ".$tempGetAward[45]." ".$tempGetAward[46]." ".$tempGetAward[47];
                    $temp2 = explode(",",$temp2);
                    array_pop($temp2);
                    //formata valores dos premios
                    $valor = (double) $tempGetAward[47];
                    $temp3 = number_format($valor,2,',','.');
                    $temp3 = 'R$ '.$temp3;
                    $temp2 = implode($temp2);
                    $temp2 = $temp2.', '.$temp3;
                }
                array_push($resultsAwarsQuantity1,$temp2);
                //print_r($resultsAwarsQuantity1);
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity1);
                return $results;
                break;
            case 'QUINA':
                $tempGetAward = explode(" ",$award);
                array_push($resultsAwars,$tempGetAward[0]);
                array_push($resultsAwars,$tempGetAward[9]);
                array_push($resultsAwars,$tempGetAward[18]);
                array_push($resultsAwars,$tempGetAward[27]);
                $tF = true;
                $x = 5;$y = 6;$z = 7;$w = 8;
                for ($i=0; $i <= 3; $i++) { 
                    if($tempGetAward[5] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity,$temp2);
                    $x+=9;$y=$x+1;$z=$y+1;$w+=9;
                }
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity);
                array_push($results,$resultsAwarsValues);
                return $results;
                break;
            case 'LOTOFÁCIL':
                $resultsAwarsQuantity1 = [];
                $tempGetAward = explode(" ",$award);
                $x = 0;$y = 1;
                for($i = 0; $i <= 4; $i++){
                    $tempDia = $tempGetAward[$x].' '.$tempGetAward[$y];
                    $tempDiaS = explode(",",$tempDia);
                    $tempDia = $tempDiaS[0];
                    array_push($resultsAwars,$tempDia);
                    $x+=6;$y=$x+1;$z=$y+1;
                }
                //pega o acertors
                $tF = true;
                $x = 2;$y = 3;$z = 4;$w = 5;
                for ($i=0; $i <= 4; $i++) { 
                    if($tempGetAward[2] == '0' && $tF){
                        $temp2 = 'Não houve acertador';
                        $tF = false;  
                    }else{
                        $temp2 = $tempGetAward[$x]." ".$tempGetAward[$y]." ".$tempGetAward[$z];
                        $temp2 = explode(",",$temp2);
                        array_pop($temp2);
                        //formata valores dos premios
                        $valor = (double) $tempGetAward[$w];
                        $temp3 = number_format($valor,2,',','.');
                        $temp3 = 'R$ '.$temp3;
                        $temp2 = implode($temp2);
                        $temp2 = $temp2.', '.$temp3;
                    }
                    array_push($resultsAwarsQuantity1,$temp2);
                    $x+=6;$y=$x+1;$z=$y+1;$w=$z+1;
                }
                array_push($results,$resultsAwars);
                array_push($results,$resultsAwarsQuantity1);
                return $results;
                break;
            default:
                # code...
                break;
        }
    }
    public function getTotalCollection($game){
        $totalCollection = $game->total_collection;
        $tempTotalCollection = explode(" ",$totalCollection);
        if ($game->name == 'LOTOFÁCIL') {
            $tempTotalCollection[2] = 'R$ '.$tempTotalCollection[2];
            $resultTotalCollection = $tempTotalCollection[0].' '.$tempTotalCollection[1].' '.$tempTotalCollection[2];
            return $resultTotalCollection;
        }
        $valor = (double) $tempTotalCollection[2];
        $valor = number_format($valor,2,',','.');
        $valor = ' R$ '.$valor;
        //array_pop($tempTotalCollection);
        $resultTotalCollection = $tempTotalCollection[0].' '.$tempTotalCollection[1].$valor;
        //print_r($resultTotalCollection);
        return $resultTotalCollection;
    }

    public function formataDados($games){
        $resultData = [];
        foreach ($games as $game) {

            $data['name'] = $game->name;
            $data['contest'] = $game->contest;
            $data['dateContest'] = $this->getDate($game->next_contest);
            $numRetorno = $this->getNumbers($game);
            //print $data['name']." ".print_r($numRetorno)."\n";
            if($game->name == 'DUPLA SENA'){
                $data['numbers_drawn'] = $numRetorno[0];
                $data['numbers_drawn2'] = $numRetorno[1];
            }
            if ($game->name == 'LOTOMANIA') {
                $data['numbers_drawn'] = $numRetorno[0];
                $data['numbers_drawn2'] = $numRetorno[1];
                $data['numbers_drawn3'] = $numRetorno[2];
                $data['numbers_drawn4'] = $numRetorno[3];
            }
            if ($game->name == 'LOTOFÁCIL') {
                $data['numbers_drawn'] = $numRetorno[0];
                $data['numbers_drawn2'] = $numRetorno[1];
                $data['numbers_drawn3'] = $numRetorno[2];
            }
            if($game->name != 'DUPLA SENA' && $game->name != 'LOTOMANIA' && $game->name != 'LOTOFÁCIL'){
                $data['numbers_drawn'] = $numRetorno;
            }
            //$data['numbers_drawn'] = $this->getNumbers($game);
            $data['accumulated'] = $game->accumulated;
            $data['estimativeNextPrize'] = $this->getNextContest($game);
            $returnAward = $this->getAward($game->name,$game->award);
            if ($game->name == 'DIA DE SORTE' ||$game->name == 'TIMEMANIA') {
                $data['lucky'] = end($returnAward[0]);//copia a ultima posição do array
                array_pop($returnAward[0]);//remove mes da sorte
            }else{
                $data['lucky'] = false;
            }
            $css = explode(" ",$game->name);
            $data['css'] = $css[0];
            $data['cssb'] = $css[0].'b'; //cor backgroud
            $logo = implode($css).'.png';
            //print $logo;
            $data['image'] = $logo;
            $data['awardTitles'] = $returnAward[0];
            $data['awardResults'] = $returnAward[1];
            if($game->name == 'DUPLA SENA'){
                $data['awardResults2'] = $returnAward[2];
            }else{
                $data['awardResults2'] = false;
            }
            $data['total_collection'] = $this->getTotalCollection($game);
            array_push($resultData, $data);
            //print_r($data);
            //print "<br>";
        }

        return $resultData;
    }

    //array 1º nome do jogo, 2º concurso, 3º data de realização do jogo, 4º numeros sorteados
    //5º acumulou ou não acumulou, 6º estimativa do proximo premio + valor + data do próximo sorteio
    //7º ganhadores por dezena, 8ª valor do premio das dezenas, 9º total arrecadado no concurso

    public function index(){
        $jogoNaoEncotrado = false;
        $resultData = [];
        $loteriasModel = new LoteriasModel();
        $games = $loteriasModel->getTodosJogos();
        $resultData = $this->formataDados($games);
        //pega nome dos jogos para alimentar o menu suspenso
        $nameGames = $loteriasModel->getNomeJogos();
        //print_r($nameGames);
        return view('conteudo', compact('resultData','nameGames','jogoNaoEncotrado'));
    }

    //retorna 5 ultimos resultados do jogo passado por parâmetro
    public function forGame($game){
        $jogoNaoEncotrado = false;
        $loteriasModel = new LoteriasModel();
        $games = $loteriasModel->getPorJogo($game);
        $resultData = $this->formataDados($games);
        $nameGames = $loteriasModel->getNomeJogos();
        return view('conteudo', compact('resultData','nameGames','jogoNaoEncotrado'));
    }

    //retorna um jogo específico informado pelo o usuário ou os 5 ultimos jogos do jogo selecionado se o usuário não passar o concurso
    public function searchGame(Request $request){
        $loteriasModel = new LoteriasModel();
        if($request->input('jogo') == 'Selecione o Jogo' || $request->input('jogo') == ''){   
            //se jogo vazio chama função index e retorna o retorno para a view 
            $retorno = $this->index();
            return $retorno;
        }
        if($request->input('concurso')){
            $concurso = (int) $request->input('concurso');
            $jogoConc = ['games.name' => $request->input('jogo'),'contests.contest' => $concurso];
            $games = $loteriasModel->getPorJogoConcurso($jogoConc);
            //transforma o objeto em array e verifica se é vazio
            if (empty((array) $games)) {
                print_r($games);
            }else{
                $jogoNaoEncotrado = true;
                $game = $request->input('jogo');
                $games = $loteriasModel->getPorJogo($game);
                $resultData = $this->formataDados($games);
                $nameGames = $loteriasModel->getNomeJogos();
                $jogo = $resultData[0]['name'];
                return view('conteudo', compact('resultData','nameGames','jogoNaoEncotrado','jogo'));
            }
            $resultData = $this->formataDados($games);
            $nameGames = $loteriasModel->getNomeJogos();
            return view('conteudo', compact('resultData','nameGames'));
        }else{
            $jogoNaoEncotrado = false;
            $game = $request->input('jogo');
            $games = $loteriasModel->getPorJogo($game);
            $resultData = $this->formataDados($games);
            $nameGames = $loteriasModel->getNomeJogos();
            return view('conteudo', compact('resultData','nameGames','jogoNaoEncotrado'));
        }

        
    }
}
