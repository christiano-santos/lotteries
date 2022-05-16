<?php

namespace App\Console\Commands;

use App\Contest;
use App\Dresscredition;
use App\Game;
use App\Result;
use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;

class GetLastGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GetLastGames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para buscar os 10 ultimos';

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
     *
     * @return mixed
     */

    public function handle()
    {

        define('BASE_URL', 'https://servicebus2.caixa.gov.br/portaldeloterias/api/');
        $fimUrls =
            [
                'megasena/',
                'lotofacil/',
                'quina/',
                'lotomania/',
                'timemania/',
                'duplasena/',
                'diadesorte/'
            ];

        $games =
            [
                'megasena/'     => 'MEGA-SENA',
                'lotofacil/'    => 'LOTOFÁCIL',
                'quina/'        => 'QUINA',
                'lotomania/'    => 'LOTOMANIA',
                'timemania/'    => 'TIMEMANIA',
                'duplasena/'    => 'DUPLA SENA',
                'diadesorte/'   => 'DIA DE SORTE'
            ];

        foreach ($fimUrls as $key => $fimUrl) {
            echo (print_r($games[$fimUrl])) . "\n";
            $i = 0;
            $concurso = 0;
            while ($i <= 9) {

                echo("Indice: ". $i)."\n";

                if ($concurso == 0) {
                    $fullUrl = BASE_URL . $fimUrl;
                } else {
                    $fullUrl = BASE_URL . $fimUrl . '/' . $concurso;
                }

                $clienteHttp = HttpClient::create();
                $json = "";
                $controleReq = 0;

                while ($controleReq <= 110) {
                    sleep(3);
                    echo "Buscando JSON" . "\n";
                    $respostaJson = $clienteHttp->request('GET', $fullUrl, [
                        'headers' => [
                            'Accept' => 'application/json',
                        ],
                        'timeout' => 60
                    ]);
                    $json = json_decode($respostaJson->getContent());
                    if (!is_null($json)) {
                        print_r($json) . "\n";
                        $controleReq = 111;
                        sleep(3);
                    } else {
                        echo 'JSON null, tentando novamente...' . $controleReq . "\n";
                        $controleReq++;
                        sleep(5);
                    }

                    $retorno = $json;
                }

                $dresscreditions = new Dresscredition();
                $award = json_encode($retorno->listaRateioPremio);
                if ($fimUrl == 'diadesorte/' || $fimUrl == 'timemania/') {
                    $nomeTimeCoracaoMesSorte = $retorno->nomeTimeCoracaoMesSorte;
                    array_push($retorno->listaRateioPremio, ['nomeTimeCoracaoMesSorte' => $nomeTimeCoracaoMesSorte]);
                    $award = json_encode($retorno->listaRateioPremio);
                    $dresscreditions->award = $award;
                } else {
                    $dresscreditions->award = $award;
                }

                echo "ganhadores" . "\n";
                echo " " . $dresscreditions->award . "\n";
                echo "Total arrecadado" . "\n";
                $dresscreditions->total_collection = $retorno->valorArrecadado;
                echo " " . $dresscreditions->total_collection . "\n";
                echo "Próximo concurso" . "\n";
                $dresscreditions->next_contest = $retorno->dataProximoConcurso;
                echo $dresscreditions->next_contest . "\n";
                echo '*****************************************************************' . "\n";

                $pos = ["first_decade", "second_decade", "third_decade", "fourth_decade", "fifth_decade", "sixth_decade", "seventh_decade", "eighth_decade", "ninth_decade", "tenth_decade", "eleventh_decade", "twelfth_decade", "thirteenth_decade", "fourteenth_decade", "fifteenth_decade", "sixteenth_decade", "seventeenth_decade", "eighteenth_decade", "nineteenth_decade", "twentieth_decade"];
                $game = new Game;
                $contest = new Contest;
                $result_Tab = new Result;
                //pega o id do jogo atual na tabela, retorna um array
                $consulta = $game::where('name', $games[$fimUrl])->pluck('id');
                echo $consulta;
                //atributo recebe o valor do id do jogo atual.
                $contest->game_id = $consulta[0]; //
                echo "Numero do concurso" . "\n";
                $contest->contest = $retorno->numero;
                $concurso = (int) $retorno->numero; // concuro
                $contest->valorEstimadoProximoConcurso = $retorno->valorEstimadoProximoConcurso;
                echo " " . $contest->contest . "\n";
                //atributo ano recebe o ano do concurso.
                $contest->year = $retorno->dataApuracao;
                echo "Data apuração" . "\n";
                echo " " . $contest->year . "\n";
                //atributo accumulated diz se concurso acumulou
                echo "Data apuração" . "\n";
                $contest->accumulated = $retorno->acumulado; //$sorteioAcumulado;
                echo " " . $contest->accumulated . "\n";
                //verifica se concurso já existe no banco antes de salva-lo, caso sim encerra o fluxo
                $concursoAtual = $contest::where('contest', $contest->contest)->where('game_id', $contest->game_id)->pluck('contest');
                if (count($concursoAtual) > 0) {
                    echo 'concurso ' . $concursoAtual[0] . ' já foi salvo!' . "\n";
                } else {
                    //salva concurso
                    $contest->save();
                    //atributos para resultado result
                    //atribui valor da chave primaria equivalente ao concurso salvo no condigo acima a chama estrangeira na tabela de resultados
                    $numeroconcurso = (int) $retorno->numero;
                    $consulta = $contest::where('contest', $numeroconcurso)->pluck('id');
                    $result_Tab->contest_id = $consulta[0];
                    foreach ($retorno->dezenasSorteadasOrdemSorteio as $key => $value) {
                        $result_Tab->{$pos[$key]} = $value;
                    }
                    echo "Resultado dezenas" . "\n";
                    echo $result_Tab . "\n";
                    $result_Tab->save();
                    $dresscreditions->contest_id = $contest::where('contest', $contest->contest)->pluck('id');
                    //pega o valor da chave estrangeira game_id que diz qual o jogo que está sendo salvo no momento
                    $dresscreditions->contest_id = $dresscreditions->contest_id[0];
                    $dresscreditions->game_id = $contest::where('contest', $contest->contest)->pluck('game_id');
                    $dresscreditions->game_id = $dresscreditions->game_id[0];
                    //$dresscreditions->next_contest = $retorno->numeroConcursoProximo;
                    //$dresscreditions->award = $dresscreditions->award;
                    //$dresscreditions->total_collection = $retorno->valorArrecadado;
                    $dresscreditions->save();
                    echo "Salvo com sucesso!" . "\n";
                }
                echo '-----------------------------------------------------------------------' . "\n";

                $i++;
                $concurso--;
            }

        }
    }
}
