<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use App\Contest;
use App\Game;
use App\Result;
use App\Dresscredition;

class WebCrawller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:WebCrawller Comando para alimentar
                            a basede dados com novas informações.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faz um Get no site de loteiras da caixa e pega o resultado de
                             todos os jogos por concurso e armazena na base de dados.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * $game = Game::create(['name']=>'MEGA-SENA', ['name']=>'LOTOFÁCIL', ['name']=>'QUINA', ['name']=>'LOTOMANIA', ['name']=>'TIMEMANIA', ['name']=>'DUPLA SENA', ['name']=>'DIA DE SORTE');
     *
     * @return mixed
     */
    public function resultDiv($jogo,$descricoes,$sorteioAcumulado){
        $dresscreditions = new Dresscredition();
        echo 'Chamou função resultDiv'."\n";
        $pos = ["first_decade","second_decade","third_decade","fourth_decade","fifth_decade","sixth_decade","seventh_decade","eighth_decade","ninth_decade","tenth_decade","eleventh_decade","twelfth_decade","thirteenth_decade","fourteenth_decade","fifteenth_decade","sixteenth_decade","seventeenth_decade","eighteenth_decade","nineteenth_decade","twentieth_decade"];
        $atual = $jogo;
        $nomeAtual = $atual->filter('div > h3')->text();
        echo $nomeAtual."\n";
        $resultadoJogo = array();
                        $nome = $atual->filter('div > h3')->text();
                        for ($j=0; $j < $atual->filter('div > ul > li')->count(); $j++) {
                            $resultado = $atual->filter('div > ul > li')->eq($j)->text();
                            array_push($resultadoJogo,$resultado);
                        }
                        //FALTA ADICIONAR VERIFICAÇÃO SE CONCURSO ACUMULOU NO BANCO O CÓDIGO
                        //instancia um objeto do tipo Game para acesso a base de dados
                        $game = new Game;
                        //instancia um objeto do tipo Contests para acesso a base de dados
                        $contest = new Contest;
                        //instancia um objeto do tipo Result para acesso a base de dados
                        $result_Tab = new Result;
                        //pega o id do jogo atual na tabela, retorna um array
                        $consulta = $game::where('name',$nomeAtual)->pluck('id');
                        //atributo recebe o valor do id do jogo atual.
                        $contest->game_id = $consulta[0];
                        $descricao = $atual->filter('div > p')->text();
                        //quebra o conteudo da variavel descricao em um array para extrair o numero do concurso e o ano
                        $concurso = explode(" ",$descricao);
                        //atributo contest recebe o concurso atual.
                        $contest->contest = (int) $concurso[1];
                        $conc = (int) $concurso[1];
                        //atributo ano recebe o ano do concurso.
                        $contest->year = $concurso[8];
                        //atributo accumulated diz se concurso acumulou
                        $contest->accumulated = $sorteioAcumulado;
                        //verifica se concurso já existe no banco antes de salva-lo, caso sim encerra o fluxo
                        $concursoAtual = $contest::where('contest',$conc)->pluck('contest');
                        //echo count($concursoAtual);
                        //print_r($concursoAtual);
                        if (count($concursoAtual) > 0) {
                            echo 'concurso '.$concursoAtual[0].' já foi salvo!'."\n";
                        }else{
                            //salva concurso
                            $contest->save();
                            //atributos para resultado result
                            //atribui valor da chave primaria equivalente ao concurso salvo no condigo acima a chamva estrangeira na tabela de resultados
                            $numeroconcurso = $concurso[1];
                            $numeroconcurso = (int) $numeroconcurso;
                            $consulta = $contest::where('contest',$numeroconcurso)->pluck('id');
                            $result_Tab->contest_id = $consulta[0];
                            for ($y=0; $y < count($resultadoJogo); $y++) {
                                $result_Tab->{$pos[$y]} = $resultadoJogo[$y];
                            }
                            $result_Tab->save();
                            //popula tabela dresscreditions
                            $dresscreditions->contest_id = $contest::where('contest',$conc)->pluck('id');
                            //pega id do último concurso salvo
                            //pega o valor da chave estrangeira game_id que diz qual o jogo que está sendo salvo no momento
                            $dresscreditions->contest_id = $dresscreditions->contest_id[0];
                            $dresscreditions->game_id = $contest::where('contest',$conc)->pluck('game_id');
                            $dresscreditions->game_id = $dresscreditions->game_id[0];
                            $dresscreditions->next_contest = $descricoes->next_contest;
                            $dresscreditions->award = $descricoes->award;
                            $dresscreditions->total_collection = $descricoes->total_collection;
                            $dresscreditions->save();
                            echo "Salvo com sucesso!"."\n";
                        }
                        $acumulou = $atual->filter('div > h3 > a')->text();

                        // echo $nome."\n";
                        // print_r($resultadoJogo);
                        // echo $acumulou."\n";
                        // echo $descricao."\n";
                        // print_r($contest);
                        // echo '***********************************************************************'."\n";
                        // print_r($result_Tab);
                        echo '-----------------------------------------------------------------------'."\n";

    }

    public function resultTable($jogo,$descricoes,$sorteioAcumulado){
        $dresscreditions = new Dresscredition();
        echo 'Chamou função resultTable'."\n";
        $pos = ["first_decade","second_decade","third_decade","fourth_decade","fifth_decade","sixth_decade","seventh_decade","eighth_decade","ninth_decade","tenth_decade","eleventh_decade","twelfth_decade","thirteenth_decade","fourteenth_decade","fifteenth_decade","sixteenth_decade","seventeenth_decade","eighteenth_decade","nineteenth_decade","twentieth_decade"];
        $atual = $jogo;
        $nomeAtual = $atual->filter('div > h3')->text();
        echo $nomeAtual."\n";
        $resultadoJogo = array();
                        $nome = $atual->filter('div > h3')->text();
                        for ($j=0; $j < $atual->filter('div > table > tbody > tr > td')->count(); $j++) {
                            $resultado = $atual->filter('div > table > tbody > tr > td')->eq($j)->text();
                            array_push($resultadoJogo,$resultado);
                        }
                        //instancia um objeto do tipo Game para acesso a base de dados
                        $game = new Game;
                        //instancia um objeto do tipo Contests para acesso a base de dados
                        $contest = new Contest;
                        //instancia um objeto do tipo Result para acesso a base de dados
                        $result_Tab = new Result;
                        //pega o id do jogo atual na tabela, retorna um array
                        $consulta = $game::where('name',$nomeAtual)->pluck('id');
                        //atributo recebe o valor do id do jogo atual.
                        $contest->game_id = $consulta[0];
                        $descricao = $atual->filter('div > p')->text();
                        //quebra o conteudo da variavel descricao em um array para extrair o numero do concurso e o ano
                        $concurso = explode(" ",$descricao);
                        //atributo contest recebe o concurso atual.
                        $contest->contest = (int) $concurso[1];
                        $conc = (int) $concurso[1];
                        //atributo ano recebe o ano do concurso.
                        $contest->year = $concurso[8];
                        //atributo accumulated diz se concurso acumulou
                        $contest->accumulated = $sorteioAcumulado;
                        //verifica se concurso já existe no banco antes de salva-lo, caso sim encerra o fluxo
                        $concursoAtual = $contest::where('contest',$conc)->pluck('contest');
                        //echo count($concursoAtual);
                        //print_r($concursoAtual);
                        if (count($concursoAtual) > 0) {
                            echo 'concurso '.$concursoAtual[0].' já foi salvo!'."\n";
                        }else{
                            //salva concurso
                            $contest->save();
                            //atributos para resultado result
                            //atribui valor da chave primaria equivalente ao concurso salvo no condigo acima a chamva estrangeira na tabela de resultados
                            $numeroconcurso = $concurso[1];
                            $numeroconcurso = (int) $numeroconcurso;
                            $consulta = $contest::where('contest',$numeroconcurso)->pluck('id');
                            $result_Tab->contest_id = $consulta[0];
                            for ($y=0; $y < count($resultadoJogo); $y++) {
                                $result_Tab->{$pos[$y]} = $resultadoJogo[$y];
                            }
                            $result_Tab->save();
                            //popula tabela dresscreditions
                            $dresscreditions->contest_id = $contest::where('contest',$conc)->pluck('id');
                            //pega id do último concurso salvo
                            //pega o valor da chave estrangeira game_id que diz qual o jogo que está sendo salvo no momento
                            $dresscreditions->contest_id = $dresscreditions->contest_id[0];
                            $dresscreditions->game_id = $contest::where('contest',$conc)->pluck('game_id');
                            $dresscreditions->game_id = $dresscreditions->game_id[0];
                            $dresscreditions->next_contest = $descricoes->next_contest;
                            $dresscreditions->award = $descricoes->award;
                            $dresscreditions->total_collection = $descricoes->total_collection;
                            $dresscreditions->save();
                            echo "Salvo com sucesso!"."\n";
                        }
                        // $acumulou = $atual->filter('div > h3 > a')->text();
                        // $descricao = $atual->filter('div > p')->text();
                        // echo $nome."\n";
                        // print_r($resultadoJogo);
                        // echo $acumulou."\n";
                        // echo $descricao."\n";
                        echo '-----------------------------------------------------------------------'."\n";

    }
    //implementar try catch
    public function getDetalhes($link,$fimUrl){
        $client = new Client();
        $clienteHttp = HttpClient::create();
        $headers = "";
        $json = "";
        $crawlerGou = "";
        $resultGetDetalhes = [];
        $controleReq = 0;
        while($controleReq <= 10){
            try {
                sleep(3);
                $respostaSym = $clienteHttp->request('GET',$link,['timeout' => 60]); //usando Symfony
                $headers = (string) $respostaSym->getHeaders()['ibm-web2-location'][0];
                if (!is_null($headers)) {
                    print 'headers:'."\n";
                    print $headers."\n";
                    $controleReq = 11;
                    sleep(3);
                }else{
                    echo 'Resposta do Goutte null, tentando novamente...'.$controleReq."\n";
                    sleep(3);
                }
            } catch (\Throwable $th) {
                $clienteHttp = HttpClient::create();
                print 'Falha na requisição do Symfony tentando novamente...'.$controleReq."\n";
                sleep(60);
                $controleReq++;
                //throw $th;
            }
        }

        $controleReq = 0;

        while ($controleReq <= 100) {
            try {
                sleep(3);
                $respostaJson = $clienteHttp->request('GET',BASE_URL.$headers.$fimUrl,[
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'timeout' => 60
               ]);
               $json = json_decode($respostaJson->getContent());
               if (!is_null($json)) {
                print 'JSON:'."\n";
                print_r($json)."\n";
                $controleReq = 111;
                sleep(3);
               }else{
                   echo 'JSON null, tentando novamente...'.$controleReq."\n";
                   sleep(3);
               }
            } catch (\Throwable $th) {
                $clienteHttp = HttpClient::create();
                print 'Falha na requisição do JSON tentando novamente...'.$controleReq."\n";
                sleep(20);
                $controleReq++;
                //throw $th;
            }
        }
        array_push($resultGetDetalhes, $json);
        $controleReq = 0;

        while ($controleReq <= 10) {
            try {
                sleep(2);
                $crawlerGou = $client->request('GET',$link,['timeout' => 60]); //usando Goutte
                echo 'link requisição Goutte';
                print $link."\n";
                $testaVazio = $crawlerGou->filter('div.resultado-loteria > div > p')->text();
                echo 'Teste se variável é vazia: '."\n";
                print $testaVazio."\n";
                sleep(2);
                if (!is_null($testaVazio)) {
                    $controleReq = 11;
                }else{
                    echo 'Resposta do Goutte null, tentando novamente...'.$controleReq."\n";
                }
            } catch (\Throwable $th) {
                $client = new Client();
                print 'Falha na requisição do Goutte tentando novamente...'.$controleReq."\n";
                sleep(60);
                $controleReq++;
                //throw $th;
            }
        }

        array_push($resultGetDetalhes, $crawlerGou);

        return $resultGetDetalhes;

    }





    public function handle()
    {
        define('BASE_URL','http://loterias.caixa.gov.br');
        $pos = ["first_decade","second_decade","third_decade","fourth_decade","fifth_decade","sixth_decade","seventh_decade","eighth_decade","ninth_decade","tenth_decade","eleventh_decade","twelfth_decade","thirteenth_decade","fourteenth_decade","fifteenth_decade","sixteenth_decade","seventeenth_decade","eighteenth_decade","nineteenth_decade","twentieth_decade"];
        $client = new Client();
        $crawler = $client->request('GET', 'http://loterias.caixa.gov.br/wps/portal/loterias', [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        //pega as divs que contem os nomes e resultados dos jogos
        $result =  $crawler->filter('div.products > div.product');
        //itera sobre as divs dos jogos
        for ($i=0; $i < $result->count(); $i++) {
            //seleciona a div, fazendo com que a variável receba seu conteudo
            $atual =  $result->filter('div.product')->eq($i);
            //variável recebe o nome do jogo da div selecionada para que o switch possa descatar as divs de jogos que não interessam
            $nomeAtual = $atual->filter('div > h3')->text();
            $link = $atual->selectLink('Confira o resultado ›');
            $link = (string) $link->link()->getUri(); //pega o endereço do link
            // echo $link."\n";
            $dresscreditions = new Dresscredition();
            $sorteioAcumulado = "";
            echo $nomeAtual."\n";
            switch ($nomeAtual) {
                case 'MEGA-SENA':
                        print "Mega";
                        $fimUrl = 'pw/Z7_HGK818G0KO6H80AU71KG7J0072/res/id=buscaResultado';
                        $retorno = $this->getDetalhes($link,$fimUrl);
                        $resposta = $retorno[0];
                        $crawlerTeste = $retorno[1];
                        $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                        $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                        //extrai dadas para popular coluna total_collection na tabela desscreditions
                        $awardTemp = $resultadoPremiacao->filter('p')->each(function ($node) use($resposta) {
                            $ganhador = $node->text()."\n";
                            $ganhador = explode(" ",$ganhador);
                            if ($ganhador[0] == 'Sena') {
                                $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'ganhadores'}.' apostas ganhadoras, '.$resposta->{'valor'}." "."\n";
                                return $ganhadores;
                            }elseif ($ganhador[0] == 'Quina') {
                                $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'ganhadores_quina'}.' apostas ganhadoras, '.$resposta->{'valor_quina'}." "."\n";
                                return $ganhadores;
                            }elseif ($ganhador[0] == 'Quadra') {
                                $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'ganhadores_quadra'}.' apostas ganhadoras, '.$resposta->{'valor_quadra'}."\n";
                                return $ganhadores;
                            }
                        });
                        //extrai dados para popular coluna award
                        $award = '';
                        foreach($awardTemp as $value){
                            $award = $award.$value;
                        }
                        $dresscreditions->award = $award;//set objeto
                        print $award;
                        //extrai dados para popular coluna total_collection
                        $total_collection = 'Arrecadação total '.$resposta->{'vr_arrecadado'};
                        print $total_collection."\n";
                        $dresscreditions->total_collection = $total_collection;//set objeto
                        //extrai dados para popular coluna next_context na tabela desscreditions
                        //print_r($resultadosCrawlerTeste->filter('div > div > p'));
                        $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->eq(0)->text();
                        $temp = explode(" ",$resultadosCrawlerTeste);
                        for ($s=0; $s <= 5; $s++) {
                            $temp2[$s] = $temp[$s]." ";
                        }
                        $resultadosCrawlerTeste = implode($temp2);
                        $next_contest = $resposta->{'dataStr'}." ".$resultadosCrawlerTeste.$resposta->{'dt_proximo_concursoStr'}.' '.$resposta->{'vr_estimativa'}."\n";
                        $dresscreditions->next_contest = $next_contest;
                        echo $next_contest;
                        $sorteioAcumulado = $resposta->{'sorteioAcumulado'};
                        if($sorteioAcumulado==false){
                            $sorteioAcumulado=0;
                        }else{
                            $sorteioAcumulado=1;
                        }
                        print "sorteio acumulado: ".$sorteioAcumulado."\n";
                        echo '*****************************************************************'."\n";
                        $this->resultDiv($atual,$dresscreditions,$sorteioAcumulado);
                    break;

                case 'LOTOFÁCIL':
                    sleep(2);
                    print "Lotofácil"."\n";
                    $fimUrl = 'pw/Z7_61L0H0G0J0VSC0AC4GLFAD2003/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $resposta = $retorno[0];
                    $crawlerTeste = $retorno[1];
                    $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                    $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                    //extrai dadas para popular coluna total_collection na tabela desscreditions
                    $awardTemp = $resultadoPremiacao->filter('p')->each(function ($node) use($resposta) {
                        $ganhador = $node->text()."\n";
                        $ganhador = explode(" ",$ganhador);
                        if ($ganhador[0] == '15') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]."".", ".$resposta->{'qt_ganhador_faixa1'}.' apostas ganhadoras, '.$resposta->{'vr_rateio_faixa1'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '14') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]."".", ".$resposta->{'qt_ganhador_faixa2'}.' apostas ganhadoras, '.$resposta->{'vr_rateio_faixa2'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '13') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]."".", ".$resposta->{'qt_ganhador_faixa3'}.' apostas ganhadoras, '.$resposta->{'vr_rateio_faixa3'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '12') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]."".", ".$resposta->{'qt_ganhador_faixa4'}.' apostas ganhadoras, '.$resposta->{'vr_rateio_faixa4'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '11') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]."".", ".$resposta->{'qt_ganhador_faixa5'}.' apostas ganhadoras, '.$resposta->{'vr_rateio_faixa5'}." "."\n";
                            return $ganhadores;
                        }
                    });
                    //extrai dados para popular coluna award
                    $award = '';
                    foreach($awardTemp as $value){
                        $award = $award.$value;
                    }
                    print $award;
                    $dresscreditions->award = $award;//set objeto
                    //extrai dados para popular coluna total_collection
                    $total_collection = 'Arrecadação total '.$resposta->{'vrArrecadado'}."\n";
                    print $total_collection;
                    $dresscreditions->total_collection = $total_collection;//set objeto
                    //extrai dados para popular coluna next_context na tabela desscreditions
                    $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->eq(0)->text();
                    $temp = explode(" ",$resultadosCrawlerTeste);
                    for ($s=0; $s <= 5; $s++) {
                        $temp2[$s] = $temp[$s]." ";
                    }
                    $resultadosCrawlerTeste = implode($temp2);
                    $next_contest = $resposta->{'dt_apuracaoStr'}." ".$resultadosCrawlerTeste.$resposta->{'dtProximoConcursoStr'}.' '.$resposta->{'vrEstimativa'}."\n";
                    $dresscreditions->next_contest = $next_contest;
                    echo $next_contest;
                    $sorteioAcumulado = $resposta->{'sorteioAcumulado'};
                    if($sorteioAcumulado==false){
                        $sorteioAcumulado=0;
                    }else{
                        $sorteioAcumulado=1;
                    }
                    print "sorteio acumulado: ".$sorteioAcumulado."\n";
                    echo '*****************************************************************'."\n";
                    $this->resultTable($atual,$dresscreditions,$sorteioAcumulado);
                    break;
                case 'QUINA':
                    sleep(2);
                    print 'Quina';
                    $fimUrl = 'pw/Z7_61L0H0G0J0VSC0AC4GLFAD20G6/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $resposta = $retorno[0];
                    $crawlerTeste = $retorno[1];
                    $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                    $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                    //extrai dadas para popular coluna total_collection na tabela desscreditions
                    $awardTemp = $resultadoPremiacao->filter('p')->each(function ($node) use($resposta) {
                        $ganhador = $node->text()."\n";
                        $ganhador = explode(" ",$ganhador);
                        if ($ganhador[0] == 'Quina') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'ganhadores'}.' apostas ganhadoras, '.$resposta->{'valor'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == 'Quadra') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'ganhadores_quadra'}.' apostas ganhadoras, '.$resposta->{'valor_quadra'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == 'Terno') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'ganhadores_terno'}.' apostas ganhadoras, '.$resposta->{'valor_terno'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == 'Duque') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]." ".$ganhador[3]." ".$ganhador[4]."".", ".$resposta->{'qt_ganhador_duque'}.' apostas ganhadoras, '.$resposta->{'vr_rateio_duque'}." "."\n";
                            return $ganhadores;
                        }
                    });
                    //extrai dados para popular coluna award
                    $award = '';
                    foreach($awardTemp as $value){
                        $award = $award.$value;
                    }
                    print $award;
                    $dresscreditions->award = $award;//set objeto
                    //extrai dados para popular coluna total_collection
                    $total_collection = 'Arrecadação total '.$resposta->{'vrArrecadado'}."\n";
                    print $total_collection;
                    $dresscreditions->total_collection = $total_collection;//set objeto
                    //extrai dados para popular coluna next_context na tabela desscreditions
                    $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->text();
                    $temp = explode(" ",$resultadosCrawlerTeste);
                    for ($s=0; $s <= 5; $s++) {
                        $temp2[$s] = $temp[$s]." ";
                    }
                    $resultadosCrawlerTeste = implode($temp2);
                    $next_contest = $resposta->{'dataStr'}." ".$resultadosCrawlerTeste.$resposta->{'dtProximoConcursoStr'}.' '.$resposta->{'vrEstimado'}."\n";
                    echo $next_contest;
                    $dresscreditions->next_contest = $next_contest;
                    $sorteioAcumulado = $resposta->{'sorteioAcumulado'};
                    if($sorteioAcumulado==false){
                        $sorteioAcumulado=0;
                    }else{
                        $sorteioAcumulado=1;
                    }
                    print "sorteio acumulado: ".$sorteioAcumulado."\n";
                    echo '*****************************************************************'."\n";
                       $this->resultDiv($atual,$dresscreditions,$sorteioAcumulado);
                    break;
                case 'LOTOMANIA':
                    sleep(2);
                    print 'Lomania';
                    $fimUrl = 'pw/Z7_61L0H0G0JGJVA0AKLR5T3K00V0/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $resposta = $retorno[0];
                    $crawlerTeste = $retorno[1];
                    $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                    $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                    //extrai dadas para popular coluna total_collection na tabela desscreditions
                    $awardTemp = $resultadoPremiacao->filter('p')->each(function ($node) use($resposta) {
                        $ganhador = $node->text()."\n";
                        $ganhador = explode(" ",$ganhador);
                        if ($ganhador[0] == '20') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qtGanhadoresFaixa1'}.' apostas ganhadoras, '.$resposta->{'vrRateioFaixa1'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '19') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qtGanhadoresFaixa2'}.' apostas ganhadoras, '.$resposta->{'vrRateioFaixa2'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '18') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qtGanhadoresFaixa3'}.' apostas ganhadoras, '.$resposta->{'vrRateioFaixa3'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '17') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qtGanhadoresFaixa4'}.' apostas ganhadoras, '.$resposta->{'vrRateioFaixa4'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '16') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qtGanhadoresFaixa5'}.' apostas ganhadoras, '.$resposta->{'vrRateioFaixa5'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '15') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qtGanhadoresFaixa7'}.' apostas ganhadoras, '.$resposta->{'vrRateioFaixa7'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '0') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1].' não houve acertador, '."\n";
                            return $ganhadores;
                        }
                    });
                    //extrai dados para popular coluna award
                    $award = '';
                    foreach($awardTemp as $value){
                        $award = $award.$value;
                    }
                    print $award;
                    $dresscreditions->award = $award;//set objeto
                    //extrai dados para popular coluna total_collection
                    $total_collection = 'Arrecadação total '.$resposta->{'vrArrecadado'}."\n";
                    print $total_collection;
                    $dresscreditions->total_collection = $total_collection;//set objeto
                    //extrai dados para popular coluna next_context na tabela desscreditions
                    $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->text();
                    $temp = explode(" ",$resultadosCrawlerTeste);
                    for ($s=0; $s <= 5; $s++) {
                        $temp2[$s] = $temp[$s]." ";
                    }
                    $resultadosCrawlerTeste = implode($temp2);
                    $next_contest = $resposta->{'dtApuracaoStr'}." ".$resultadosCrawlerTeste.$resposta->{'dtProximoConcursoStr'}.' '.$resposta->{'vrEstimativa'}."\n";
                    echo $next_contest;
                    $dresscreditions->next_contest = $next_contest;
                    $sorteioAcumulado = $resposta->{'sorteioAcumulado'};
                    if($sorteioAcumulado==false){
                        $sorteioAcumulado=0;
                    }else{
                        $sorteioAcumulado=1;
                    }
                    print "sorteio acumulado: ".$sorteioAcumulado."\n";
                    echo '*****************************************************************'."\n";
                      $this->resultTable($atual,$dresscreditions,$sorteioAcumulado);
                    break;
                case 'TIMEMANIA':
                    sleep(2);
                    print 'Timemania';
                    $fimUrl = 'pw/Z7_61L0H0G0JGJVA0AKLR5T3K00M4/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $resposta = $retorno[0];
                    $crawlerTeste = $retorno[1];
                    $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                    $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                    //extrai dadas para popular coluna total_collection na tabela desscreditions
                    $awardTemp = $resultadoPremiacao->filter('p')->each(function ($node) use($resposta) {
                        $ganhador = $node->text()."\n";
                        //print $ganhador;
                        $ganhador = explode(" ",$ganhador);
                        if ($ganhador[0] == '7') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_1'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_1'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '6') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_2'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_2'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '5') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_3'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_3'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '4') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_4'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_4'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '3') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_5'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_5'}." "."\n";
                            return $ganhadores;
                        }elseif ($ganhador[0] == '2') {
                            $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_6'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_6'}." "."\n";
                            return $ganhadores;
                        }
                    });
                    //extrai dados para popular coluna award
                    $award = '';
                    foreach($awardTemp as $value){
                        $award = $award.$value;
                    }
                    $award = $award."Time do coração: ".$resposta->{'timeCoracao'}."\n";
                    print $award;
                    $dresscreditions->award = $award;//set objeto
                    //extrai dados para popular coluna total_collection
                    $total_collection = 'Arrecadação total '.$resposta->{'vr_ARRECADADO'}."\n";
                    print $total_collection;
                    $dresscreditions->total_collection = $total_collection;//set objeto
                    //extrai dados para popular coluna next_context na tabela desscreditions
                    $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->text();
                    $temp = explode(" ",$resultadosCrawlerTeste);
                    for ($s=0; $s <= 5; $s++) {
                        $temp2[$s] = $temp[$s]." ";
                    }
                    $resultadosCrawlerTeste = implode($temp2);
                    $next_contest = $resposta->{'dt_APURACAOStr'}." ".$resultadosCrawlerTeste.$resposta->{'dt_PROXIMO_CONCURSOStr'}.' '.$resposta->{'vr_ESTIMATIVA_FAIXA_1'}."\n";
                    echo $next_contest;
                    $dresscreditions->next_contest = $next_contest;
                    $sorteioAcumulado = $resposta->{'sorteioAcumulado'};
                    if($sorteioAcumulado==false){
                        $sorteioAcumulado=0;
                    }else{
                        $sorteioAcumulado=1;
                    }
                    print "sorteio acumulado: ".$sorteioAcumulado."\n";
                    echo '*****************************************************************'."\n";
                      $this->resultDiv($atual,$dresscreditions,$sorteioAcumulado);
                    break;
                case 'DUPLA SENA':
                    sleep(2);
                    print 'Dupla Sena';
                    $fimUrl = 'pw/Z7_61L0H0G0J0I280A4EP2VJV30N4/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $resposta = $retorno[0];
                    $crawlerTeste = $retorno[1];
                    $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                    $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                    //extrai dadas para popular coluna total_collection na tabela desscreditions
                    $controle = 0;
                    //$awardTemp1 = $resultadoPremiacao->filter('p')
                    $awardTemp = [];
                    while ($controle <= 7) {
                        if ($controle <= 3) {
                            if ($controle == 0) {
                                $ganhadores = "Premiação - 1º Sorteio "."Sena "."- "."6".", ".$resposta->{'ganhadores_sena1'}.' apostas ganhadoras, '.$resposta->{'valor_sena1'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }elseif ($controle == 1) {
                                $ganhadores = "Quina "."- "."5".", ".$resposta->{'qt_ganhador_quina_faixa1'}.' apostas ganhadoras, '.$resposta->{'vr_quina_faixa1'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }elseif ($controle == 2) {
                                $ganhadores = "Quadra "."- "."4".", ".$resposta->{'qt_ganhador_quadra_faixa1'}.' apostas ganhadoras, '.$resposta->{'vr_quadra_faixa1'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }elseif ($controle == 3) {
                                $ganhadores = "Terno "."- "."3".", ".$resposta->{'qt_ganhador_terno_faixa1'}.' apostas ganhadoras, '.$resposta->{'vr_terno_faixa1'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }
                        }else{
                            if ($controle == 4) {
                                $ganhadores = "Premiação - 2º Sorteio "."Sena "."- "."6".", ".$resposta->{'ganhadores_sena2'}.' apostas ganhadoras, '.$resposta->{'valor_sena2'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }elseif ($controle == 5) {
                                $ganhadores = "Quina "."- "."5".", ".$resposta->{'ganhadores_quina2'}.' apostas ganhadoras, '.$resposta->{'valor_quina2'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }elseif ($controle == 6) {
                                $ganhadores = "Quadra "."- "."4".", ".$resposta->{'ganhadores_quadra2'}.' apostas ganhadoras, '.$resposta->{'valor_quadra2'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }elseif ($controle == 7) {
                                $ganhadores = "Terno "."- "."3".", ".$resposta->{'qt_ganhador_terno_faixa2'}.' apostas ganhadoras, '.$resposta->{'vr_terno_faixa2'}." "."\n";
                                array_push($awardTemp, $ganhadores);
                            }
                        }
                        $controle++;
                    }

                    //extrai dados para popular coluna award
                    $award = '';
                    foreach($awardTemp as $value){
                        $award = $award.$value;
                    }
                    print $award;
                    $dresscreditions->award = $award;//set objeto
                    //extrai dados para popular coluna total_collection
                    $total_collection = 'Arrecadação total '.$resposta->{'vr_arrecadado'}."\n";
                    print $total_collection;
                    $dresscreditions->total_collection = $total_collection;//set objeto
                    //extrai dados para popular coluna next_context na tabela desscreditions
                    $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->text();
                    $temp = explode(" ",$resultadosCrawlerTeste);
                    for ($s=0; $s <= 5; $s++) {
                        $temp2[$s] = $temp[$s]." ";
                    }
                    $resultadosCrawlerTeste = implode($temp2);
                    $next_contest = $resposta->{'dataStr'}." ".$resultadosCrawlerTeste.$resposta->{'data_proximo_concursoStr'}.' '.$resposta->{'valor_estimativa'}."\n";
                    echo $next_contest;
                    $dresscreditions->next_contest = $next_contest;
                    $sorteioAcumulado = $resposta->{'acumulado'};
                    if($sorteioAcumulado==false){
                        $sorteioAcumulado=0;
                    }else{
                        $sorteioAcumulado=1;
                    }
                    print "sorteio acumulado: ".$sorteioAcumulado."\n";
                    echo '*****************************************************************'."\n";
                       $this->resultDiv($atual,$dresscreditions,$sorteioAcumulado);
                    break;
                case 'DIA DE SORTE':
                        sleep(2);
                        print 'Dia de sorte';
                        $fimUrl = 'pw/Z7_HGK818G0KO5GE0Q8PTB11800G3/res/id=buscaResultado';
                        $retorno = $this->getDetalhes($link,$fimUrl);
                        $resposta = $retorno[0];
                        $crawlerTeste = $retorno[1];
                        $resultadoJogo = array();
                        for ($j=0; $j < $atual->filter('div > ul > li')->count(); $j++) {
                            $resultado = $atual->filter('div > ul > li')->eq($j)->text();
                            array_push($resultadoJogo,$resultado);
                        }
                        $sorteioAcumulado = $resposta->{'sorteioAcumulado'};
                            if($sorteioAcumulado==false){
                                $sorteioAcumulado=0;
                            }else{
                                $sorteioAcumulado=1;
                            }
                            print "sorteio acumulado: ".$sorteioAcumulado."\n";
                        //popula tabela dresscreditions
                        //instancia um objeto do tipo Game para acesso a base de dados
                        $game = new Game;
                        //instancia um objeto do tipo Contests para acesso a base de dados
                        $contest = new Contest;
                        //instancia um objeto do tipo Result para acesso a base de dados
                        $result_Tab = new Result;
                        //pega o id do jogo atual na tabela, retorna um array
                        $consulta = $game::where('name',$nomeAtual)->pluck('id');
                        //atributo recebe o valor do id do jogo atual.
                        $contest->game_id = $consulta[0];
                        $descricao = $atual->filter('div > p')->eq(1)->text();
                        //quebra o conteudo da variavel descricao em um array para extrair o numero do concurso e o ano
                        $concurso = explode(" ",$descricao);
                        //atributo contest recebe o concurso atual.
                        $contest->contest = (int) $concurso[1];
                        $conc = (int) $concurso[1];
                        //atributo ano recebe o ano do concurso.
                        $contest->year = $concurso[8];
                        //atributo accumulated diz se concurso acumulou
                        $contest->accumulated = $sorteioAcumulado;
                        //verifica se concurso já existe no banco antes de salva-lo, caso sim encerra o fluxo
                        $concursoAtual = $contest::where('contest',$conc)->pluck('contest');
                        //echo count($concursoAtual);
                        //print_r($concursoAtual);
                        if (count($concursoAtual) > 0) {
                            echo 'concurso '.$concursoAtual[0].' já foi salvo!'."\n";
                        }else{
                            //salva concurso
                            $contest->save();
                            //atributos para resultado result
                            //atribui valor da chave primaria equivalente ao concurso salvo no condigo acima a chamva estrangeira na tabela de resultados
                            $numeroconcurso = $concurso[1];
                            $numeroconcurso = (int) $numeroconcurso;
                            $consulta = $contest::where('contest',$numeroconcurso)->pluck('id');
                            $result_Tab->contest_id = $consulta[0];
                            for ($y=0; $y < count($resultadoJogo); $y++) {
                                $result_Tab->{$pos[$y]} = $resultadoJogo[$y];
                            }
                            $resultadosCrawlerTeste = $crawlerTeste->filter('div.resultado-loteria > div');
                            $resultadoPremiacao = $crawlerTeste->filter('div.gray-text');
                            //extrai dadas para popular coluna total_collection na tabela desscreditions
                            $awardTemp = $resultadoPremiacao->filter('p')->each(function ($node) use($resposta) {
                                $ganhador = $node->text()."\n";
                                //print $ganhador;
                                $ganhador = explode(" ",$ganhador);
                                if ($ganhador[0] == '7') {
                                    $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_1'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_1'}." "."\n";
                                    return $ganhadores;
                                }elseif ($ganhador[0] == '6') {
                                    $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_2'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_2'}." "."\n";
                                    return $ganhadores;
                                }elseif ($ganhador[0] == '5') {
                                    $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_3'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_3'}." "."\n";
                                    return $ganhadores;
                                }elseif ($ganhador[0] == '4') {
                                    $ganhadores = $ganhador[0]." ".$ganhador[1]." ".$ganhador[2]."".", ".$resposta->{'qt_GANHADOR_FAIXA_4'}.' apostas ganhadoras, '.$resposta->{'vr_RATEIO_FAIXA_4'}." "."\n";
                                    return $ganhadores;
                                }
                            });
                            //extrai dados para popular coluna award
                            $award = '';
                            foreach($awardTemp as $value){
                                $award = $award.$value;
                            }
                            $award = $award."Mês da Sorte ".$resposta->{'mes_DE_SORTE'}.' '."\n";
                            print $award;
                            //extrai dados para popular coluna total_collection
                            $total_collection = 'Arrecadação total '.$resposta->{'vr_ARRECADADO'}."\n";
                            print $total_collection;
                            //extrai dados para popular coluna next_context na tabela desscreditions
                            $resultadosCrawlerTeste = $resultadosCrawlerTeste->filter('p')->text();
                            $temp = explode(" ",$resultadosCrawlerTeste);
                            for ($s=0; $s <= 5; $s++) {
                                $temp2[$s] = $temp[$s]." ";
                            }
                            $resultadosCrawlerTeste = implode($temp2);
                            $next_contest = $resposta->{'dt_APURACAOStr'}." ".$resultadosCrawlerTeste.$resposta->{'dt_PROXIMO_CONCURSOStr'}.' '.$resposta->{'vr_ESTIMATIVA'}."\n";
                            print $next_contest;
                            $dresscreditions->contest_id = $contest::where('contest',$conc)->pluck('id');
                            //pega id do último concurso salvo
                            //pega o valor da chave estrangeira game_id que diz qual o jogo que está sendo salvo no momento
                            $dresscreditions->contest_id = $dresscreditions->contest_id[0];
                            $dresscreditions->game_id = $contest::where('contest',$conc)->pluck('game_id');
                            $dresscreditions->game_id = $dresscreditions->game_id[0];
                            $dresscreditions->next_contest = $next_contest;
                            $dresscreditions->award = $award;
                            $dresscreditions->total_collection = $total_collection;
                            $dresscreditions->save();
                            echo $next_contest;
                            echo '*****************************************************************'."\n";

                            $result_Tab->save();

                            echo "Salvo com sucesso!"."\n";
                        }
                    break;
                default:
                        echo $nomeAtual." Não entrou em nenhuma opção"."\n";
                    break;
            }

            $atual = '';
            $nomeAtual = '';
        }
    }
}
