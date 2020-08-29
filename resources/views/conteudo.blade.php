@extends('layout.principal')
@section('cabecalho')
<header>
    <nav class="navhead1 navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" style="font-family: 'Russo One', sans-serif;font-size: 2em;color:white" href="/">
            Loterias</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSite" aria-controls="navbarSite" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSite">
            <ul class="navbar-nav ml-auto">
                <li class="navbar-item"><a class="nav-link text-white menu" href="MEGA-SENA">Mega-Sena</a></li>
                <li class="navbar-item"><a class="nav-link text-white menu" href="LOTOFÁCIL">Lotofácil</a></li>
                <li class="navbar-item"><a class="nav-link text-white menu" href="QUINA">Quina</a></li>
                <li class="navbar-item"><a class="nav-link text-white menu" href="LOTOMANIA">Lotomania</a></li>
                <li class="navbar-item"><a class="nav-link text-white menu" href="TIMEMANIA">Timemania</a></li>
                <li class="navbar-item"><a class="nav-link text-white menu" href="DUPLA SENA">Dupla Sena</a></li>
                <li class="navbar-item"><a class="nav-link text-white menu" href="DIA DE SORTE">Dia de Sorte</a></li>
            </ul>
        </div>
    </nav>
    <nav class="navhead2 navbar navbar-light justify-content-end">
        <div class="d-flex align-items-baseline">
            <h5 class="mr-3" style="font-family: 'Russo One', sans-serif;color:white">Busca Rápida</h5>
            <form action="busca" method="POST">
                <!-- <input type="hidden" name="_method" value="PUT"> -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-row" style="margin-right: -50px;">
                    <div class="form-col mr-3">
                        <select class="form-control" name="jogo" id="">
                            <option value="">Selecione o Jogo</option>
                            @foreach ($nameGames as $name)
                            <option value="{{$name->name}}">{{$name->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <input class="form-control w-25 mr-2" placeholder="Nº Concurso" aria-label="Search" name="concurso">
                    <button class="btn btn btn-success" type="submit">Buscar</button>
            </form>
        </div>
    </nav>
    <div class="container-fluid d-flex justify-content-center align-items-center mt-3">
        <h1 style="font-family: 'Russo One', sans-serif;font-size: 2em;color: #006bae" >Resultado Loterias</h1>
    </div>
    @if ($jogoNaoEncotrado)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Concurso não encontrado!</strong> Verifique se o jogo e concurso estão corretos.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        Listamos os últimos conursos do jogo selecionado, <strong>{{$jogo}}</strong>. Para retornar à página anterior click em <strong>Loterias</strong>.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
</header>
@endsection
@section('conteudo')
@foreach ($resultData as $result)
<div class="d-flex justify-content-center mt-5 mb-5">
    <div class="bg-secondary">
        <span>PUBLICIDADE TOPO</span>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 col-sm-12">
            <div class="bg-secondary">
                <span>PUB ESQUERDA</span>
            </div>
        </div>
        <div class="col-lg-8 col-sm-12 mt-3">
            <div class="container-fluid">
                <article class="mb-3">
                    <div class="card p-2 resultadoCard">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid" src="img/{{$result['image']}}" alt="{{$result['name']}}">
                            <h4 class="pl-1 mt-1 mb-1 {{$result['css']}}">{{$result['name']}}</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Concurso {{$result['contest']}}</h5>
                            <!-- colocar a arrecadação total -->
                            <p class="card-text">Resultado {{$result['name']}} concurso {{$result['contest']}} realizado {{$result['dateContest']}}.</p>
                            <div class="d-flex justify-content-center mb-4">
                                @if($result['name'] == 'DUPLA SENA')
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-center mb-1">
                                        <span class="acumulou" style="font-size: 1em">1º Sorteio</span>
                                    </div>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <div class="d-flex justify-content-center mb-1">
                                        <span class="acumulou" style="font-size: 1em">2º Sorteio</span>
                                    </div>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn2'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @if($result['name'] == 'LOTOMANIA')
                                <div class="d-flex flex-column">
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn2'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn3'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn4'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @if($result['name'] == 'LOTOFÁCIL')
                                <div class="d-flex flex-column">
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn2'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <ul class="d-flex resultadosColunas">
                                        @foreach ($result['numbers_drawn3'] as $numbers)
                                        <li>
                                            <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                @if ($result['name'] != 'DUPLA SENA' && $result['name'] != 'LOTOMANIA' && $result['name'] != 'LOTOFÁCIL')
                                @foreach ($result['numbers_drawn'] as $numbers)
                                <div class="resultado {{$result['cssb']}}"><span>{{$numbers}}</span></div>
                                @endforeach
                                @endif
                            </div>
                            @if($result['lucky'])
                            @if($result['name'] == 'TIMEMANIA')
                            <div class="d-flex justify-content-center mb-1">
                                <img class="time" src="img/time.png" alt="time do coração">
                                <span>{{$result['lucky']}}</span>
                            </div>
                            @else
                            <div class="d-flex justify-content-center mb-1">
                                <span>{{$result['lucky']}}</span>
                            </div>
                            @endif
                            @endif
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-center mb-1">
                                    @if ($result['accumulated'])
                                    <span class="acumulou">acumulou!</span>
                                    @else
                                    <span class="acumulou pb-3">SAIU!</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-center">
                                    <!-- se acumulou mostrar o acumulado para o proximo concurso e a data do proximo concurso -->
                                    <p>
                                        {{$result['estimativeNextPrize']}}
                                    </p>
                                </div>
                                <div class="d-flex justify-content-center ganhadores mb-1">
                                    <span>Veja os ganhadores</span>
                                </div>
                                <div class="conteiner-fluid table-responsive-xl">
                                    @if($result['name'] == 'DUPLA SENA')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                @foreach ($result['awardTitles'] as $aw)
                                                <th scope="col">{{$aw}}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach ($result['awardResults'] as $awR)
                                                <td>{{$awR}}</td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach ($result['awardResults2'] as $awR)
                                                <td>{{$awR}}</td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                    @else
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                @foreach ($result['awardTitles'] as $aw)
                                                <th scope="col">{{$aw}}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @foreach ($result['awardResults'] as $awR)
                                                <td>{{$awR}}</td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between">
                                <a href="{{$result['name']}}" class="btn btn-primary btn-sm botao">Concursos Anteriores</a>
                                <span class="arrecadacaoTotal">{{$result['total_collection']}}</span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        <div class="col-lg-2 col-sm-12">
            <div class="bg-secondary">
                <span>PUB DIREITA</span>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
@section('rodape')
<div class="d-flex justify-content-center mt-5 mb-5">
    <div class="bg-secondary">
        <span>PUBLICIDADE FIM</span>
    </div>
</div>
<footer class="container-fluid d-flex justify-content-center align-items-center">
    <span class="mr-1">&copy;</span> <span>Resultado Loterias</span>
</footer>
@endsection