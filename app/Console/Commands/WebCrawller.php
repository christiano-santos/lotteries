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

    protected $signature = 'command:WebCrawller Comando para alimentar
                            a basede dados com novas informações.';

    protected $description = 'Faz um Get no site de loteiras da caixa e pega o resultado de
                             todos os jogos por concurso e armazena na base de dados.';

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
    public function resultDiv($jogo,$descricoes,$retorno){
        $dresscreditions = new Dresscredition();
        echo 'Chamou função resultDiv'."\n";
        $pos = ["first_decade","second_decade","third_decade","fourth_decade","fifth_decade","sixth_decade","seventh_decade","eighth_decade","ninth_decade","tenth_decade","eleventh_decade","twelfth_decade","thirteenth_decade","fourteenth_decade","fifteenth_decade","sixteenth_decade","seventeenth_decade","eighteenth_decade","nineteenth_decade","twentieth_decade"];
        $atual = $jogo;
        $nomeAtual = $atual->filter('div > h3')->text();
        echo $nomeAtual."\n";
        $game = new Game;
        $contest = new Contest;
        $result_Tab = new Result;
        //pega o id do jogo atual na tabela, retorna um array
        $consulta = $game::where('name',$nomeAtual)->pluck('id');
        //atributo recebe o valor do id do jogo atual.
        $contest->game_id = $consulta[0];
        $descricao = $atual->filter('div > p')->text();
        //quebra o conteudo da variavel descricao em um array para extrair o numero do concurso e o ano
        $concurso = explode(" ",$descricao);
        //atributo contest recebe o concurso atual.
        echo "Numero do concurso"."\n";
        $contest->contest = $retorno->numero;
        echo " ".$contest->contest."\n";
        //atributo ano recebe o ano do concurso.
        $contest->year = $retorno->dataApuracao;
        echo "Data apuração"."\n";
        echo " ".$contest->year."\n";
        //atributo accumulated diz se concurso acumulou
        echo "Data apuração"."\n";
        $contest->accumulated = $retorno->acumulado; //$sorteioAcumulado;
        echo " ".$contest->accumulated."\n";
        //verifica se concurso já existe no banco antes de salva-lo, caso sim encerra o fluxo
        $concursoAtual = $contest::where('contest',$contest->contest)->pluck('contest');
        if (count($concursoAtual) > 0) {
            echo 'concurso '.$concursoAtual[0].' já foi salvo!'."\n";
        }else{
            //salva concurso
            $contest->save();
            //atributos para resultado result
            //atribui valor da chave primaria equivalente ao concurso salvo no condigo acima a chama estrangeira na tabela de resultados
            $numeroconcurso = (int) $retorno->numero;
            $consulta = $contest::where('contest',$numeroconcurso)->pluck('id');
            $result_Tab->contest_id = $consulta[0];
            foreach ($retorno->dezenasSorteadasOrdemSorteio as $key => $value) {
                $result_Tab->{$pos[$key]} = $value;
            }
            echo "Resultado dezenas"."\n";
            echo $result_Tab."\n";
            $result_Tab->save();
            $dresscreditions->contest_id = $contest::where('contest',$contest->contest)->pluck('id');
            //pega o valor da chave estrangeira game_id que diz qual o jogo que está sendo salvo no momento
            $dresscreditions->contest_id = $dresscreditions->contest_id[0];
            $dresscreditions->game_id = $contest::where('contest',$contest->contest)->pluck('game_id');
            $dresscreditions->game_id = $dresscreditions->game_id[0];
            $dresscreditions->next_contest = $descricoes->next_contest;
            $dresscreditions->award = $descricoes->award;
            $dresscreditions->total_collection = $descricoes->total_collection;
            $dresscreditions->save();
            echo "Salvo com sucesso!"."\n";
        }
        echo '-----------------------------------------------------------------------'."\n";

    }

    public function getDetalhes($link,$fimUrl){
        $clienteHttp = HttpClient::create();
        $headers = "";
        $json = "";
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
            }
        }

        $controleReq = 0;

        while ($controleReq <= 110) {
            sleep(3);
            echo "Buscando JSON"."\n";
            echo BASE_URL.$headers.$fimUrl."\n";
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
                $controleReq++;
                sleep(5);
            }

        }
        return $json;
    }

    public function dresscreditions($atual,$retorno,$nomeAtual){
        $dresscreditions = new Dresscredition();
        $award = json_encode($retorno->listaRateioPremio);
        if($nomeAtual == 'DIA DE SORTE' || $nomeAtual == 'TIMEMANIA'){
            $awd = [];
            array_push($awd, $retorno->listaRateioPremio, $retorno->nomeTimeCoracaoMesSorte);
            $award = json_encode($awd);
            $dresscreditions->award = $award;
        }else{
            $dresscreditions->award = $award;
        }
        echo "ganhadores"."\n";
        echo " ".$dresscreditions->award."\n";
        echo "Total arrecadado"."\n";
        $dresscreditions->total_collection = $retorno->valorArrecadado;
        echo " ".$dresscreditions->total_collection."\n";
        echo "Próximo concurso"."\n";
        $dresscreditions->next_contest = $retorno->dataProximoConcurso;
        echo $dresscreditions->next_contest."\n";
        echo '*****************************************************************'."\n";
        $this->resultDiv($atual,$dresscreditions,$retorno);
    }

    public function handle()
    {
        define('BASE_URL','http://loterias.caixa.gov.br');
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
            $dresscreditions = new Dresscredition();
            echo $nomeAtual."\n";
            switch ($nomeAtual) {
                case 'MEGA-SENA':
                    $fimUrl = 'pw/Z7_HGK818G0KO6H80AU71KG7J0072/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $this->dresscreditions($atual,$retorno,$nomeAtual);
                break;
                case 'LOTOFÁCIL':
                    sleep(2);
                    $fimUrl = 'pw/Z7_61L0H0G0J0VSC0AC4GLFAD2003/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $this->dresscreditions($atual,$retorno,$nomeAtual);
                break;
                case 'QUINA':
                    sleep(2);
                    $fimUrl = 'pw/Z7_HGK818G0K8ULB0QT4MEM8L0086/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $this->dresscreditions($atual,$retorno,$nomeAtual);
                break;
                case 'LOTOMANIA':
                    sleep(2);
                    $fimUrl = 'pw/Z7_61L0H0G0JGJVA0AKLR5T3K00V0/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $this->dresscreditions($atual,$retorno,$nomeAtual);
                break;
                case 'TIMEMANIA':
                    sleep(2);
                    $fimUrl = 'pw/Z7_61L0H0G0JGJVA0AKLR5T3K00M4/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $this->dresscreditions($atual,$retorno,$nomeAtual);
                break;
                case 'DUPLA SENA':
                    sleep(2);
                    $fimUrl = 'pw/Z7_HGK818G0KGSE30Q3I6OOK60006/res/id=buscaResultado';
                    $retorno = $this->getDetalhes($link,$fimUrl);
                    $this->dresscreditions($atual,$retorno,$nomeAtual);
                break;
                case 'DIA DE SORTE':
                        sleep(2);
                        print 'Dia de sorte';
                        $fimUrl = 'pw/Z7_HGK818G0KO5GE0Q8PTB11800G3/res/id=buscaResultado';
                        $retorno = $this->getDetalhes($link,$fimUrl);
                        $this->dresscreditions($atual,$retorno,$nomeAtual);
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
