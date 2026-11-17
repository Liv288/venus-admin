<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Vênus</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/sidebar-pesquisa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfil-config.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    @include('layouts.sidebar')

    <div class="direita">
        @include('layouts.barra-pesquisa')

        <div class="wrap">

            @if (session('sucesso'))
                <div class="alerta ok" role="status">{{ session('sucesso') }}</div>
            @endif

            {{-- Cabeçalho + escolha do período --}}
            <div class="head">
                <div>
                    <h1>Olá, {{ strtok($admin->nomeAdmin, ' ') }}!</h1>
                    <p class="sm">Bem-vindo(a) ao painel administrativo da Vênus.</p>
                </div>

                <nav class="dash-periodo" aria-label="Período">
                    @foreach ([7, 30, 90] as $dias)
                        <a href="{{ route('painel.dashboard', ['periodo' => $dias]) }}"
                           class="{{ $periodo === $dias ? 'on' : '' }}"
                           @if ($periodo === $dias) aria-current="true" @endif>
                            {{ $dias }} dias
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Aviso: só aparece se existe cadastro esperando análise --}}
            @if ($resumo['cadastrosPendentes'] > 0)
                <div class="dash-aviso" role="status">
                    <i data-lucide="bell-ring"></i>
                    <span>
                        <b>{{ $resumo['cadastrosPendentes'] }}</b>
                        {{ $resumo['cadastrosPendentes'] === 1 ? 'cadastro de profissional aguarda' : 'cadastros de profissionais aguardam' }}
                        a sua análise.
                    </span>
                </div>
            @endif

            {{-- Cards de resumo --}}
            <div class="dash-kpis">
                <div class="card dash-kpi">
                    <span class="ico"><i data-lucide="users"></i></span>
                    <div>
                        <b>{{ number_format($resumo['usuariasAtivas'], 0, ',', '.') }}</b>
                        <span>Usuárias ativas</span>
                        <small class="dash-novos">+{{ $resumo['usuariasNovas'] }} nos últimos {{ $periodo }} dias</small>
                    </div>
                </div>

                <div class="card dash-kpi">
                    <span class="ico"><i data-lucide="stethoscope"></i></span>
                    <div>
                        <b>{{ number_format($resumo['profissionaisAprovados'], 0, ',', '.') }}</b>
                        <span>Profissionais aprovados</span>
                        <small class="dash-novos">+{{ $resumo['profissionaisNovos'] }} nos últimos {{ $periodo }} dias</small>
                    </div>
                </div>

                <div class="card dash-kpi">
                    <span class="ico"><i data-lucide="user-check"></i></span>
                    <div>
                        <b>{{ $resumo['cadastrosPendentes'] }}</b>
                        <span>Cadastros pendentes</span>
                        <small>Aguardando aprovação</small>
                    </div>
                </div>

                <div class="card dash-kpi">
                    <span class="ico"><i data-lucide="flag"></i></span>
                    <div>
                        <b>{{ $resumo['denunciasPendentes'] }}</b>
                        <span>Denúncias pendentes</span>
                        <small>Ainda sem resolução</small>
                    </div>
                </div>

                <div class="card dash-kpi">
                    <span class="ico"><i data-lucide="headset"></i></span>
                    <div>
                        <b>{{ $resumo['chamadosAbertos'] }}</b>
                        <span>Chamados de suporte</span>
                        <small>Em aberto</small>
                    </div>
                </div>
            </div>

            <div class="dash-grid">

                {{-- Gráfico: total acumulado ao longo do período --}}
                <div class="card dash-card-grafico">
                    <h3>Crescimento da comunidade</h3>
                    <p class="sm">Total acumulado de usuárias e profissionais nos últimos {{ $periodo }} dias</p>
                    <div id="graficoCrescimento" class="dash-grafico" role="img"
                         aria-label="Gráfico de linhas com o total de usuárias e profissionais no período"></div>
                </div>

                {{-- Fila prioritária --}}
                <div class="card">
                    <h3>Fila prioritária</h3>
                    <p class="sm">O que está esperando há mais tempo</p>

                    @forelse ($fila as $item)
                        <div class="dash-linha">
                            <span class="ico dash-ico-pq"><i data-lucide="{{ $item['icone'] }}"></i></span>
                            <div class="dash-linha-texto">
                                <b>{{ $item['titulo'] }}</b>
                                <span class="sm">
                                    {{ $item['detalhe'] }} · há {{ $item['data']->locale('pt_BR')->diffForHumans(now(), \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}
                                </span>
                            </div>
                            <span class="dash-chip">{{ $item['tipo'] }}</span>
                        </div>
                    @empty
                        <p class="dash-vazio">Nenhuma pendência. Tudo em dia!</p>
                    @endforelse
                </div>
            </div>

            {{-- Últimos cadastros de profissionais --}}
            <div class="card">
                <h3>Últimos cadastros de profissionais</h3>
                <p class="sm">Os 5 cadastros mais recentes, com a situação da verificação</p>

                @forelse ($ultimosProfissionais as $p)
                    @php
                        $status = $p->status_rotulo;
                        $classe = match ($p->status_codigo) {
                            'a' => 'ok',
                            'x' => 'neg',
                            default => 'pend',
                        };
                    @endphp
                    <div class="dash-linha">
                        <span class="dash-avatar">{{ mb_strtoupper(mb_substr(preg_replace('/^(Dra?\.)\s*/u', '', $p->nomeProfissionalSaude), 0, 1)) }}</span>
                        <div class="dash-linha-texto">
                            <b>{{ $p->nomeProfissionalSaude }}</b>
                            <span class="sm">{{ $p->categoriaProfissional }} · cadastrado em {{ $p->dataCadastro->format('d/m/Y') }}</span>
                        </div>
                        <span class="dash-status dash-status--{{ $classe }}">{{ $status }}</span>
                    </div>
                @empty
                    <p class="dash-vazio">Nenhum profissional cadastrado ainda.</p>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Dados do gráfico vêm do controller --}}
    <script>window.DASHBOARD = @json($grafico);</script>

    <script src="{{ asset('js/adm.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>lucide.createIcons();</script>

</body>
</html>
