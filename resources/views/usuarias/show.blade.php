@extends('layouts.app')

@section('titulo', 'Conta da usuária - Vênus')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@php
    $u = $usuaria;
    $ativa = $u->statusUsuario === 'ativa';
    $suspensa = $u->statusUsuario === 'suspensa';

    // Texto de cada situação da conta
    $explicacao = match ($u->statusUsuario) {
        'ativa' => 'A usuária pode usar o aplicativo normalmente.',
        'suspensa' => 'Acesso bloqueado pela administração. A conta pode ser reativada.',
        default => 'A própria usuária desativou a conta. Só ela pode voltar a usá-la.',
    };

    // Cor do selo das denúncias (em aberto = amarelo, encerrada = verde)
    $corItem = fn ($status) => in_array($status, \App\Models\Denuncia::ABERTAS) ? 'lst-status--pendente' : 'lst-status--ok';
@endphp

@section('content')
<div class="lst-page">

    {{-- Topo: voltar --}}
    <div class="lst-cabeca">
        <a href="{{ route('usuarias.index') }}" class="lst-btn lst-btn--neutro lst-btn--pequeno">
            <i data-lucide="arrow-left"></i> Voltar para a lista
        </a>
    </div>

    {{-- Resumo da usuária --}}
    <div class="lst-hero">
        <div class="lst-hero-topo">
            <span class="lst-hero-avatar">{{ $u->inicial }}</span>
            <div class="lst-hero-info">
                <h1>{{ $u->nomeUsuario }}</h1>
                <p class="lst-hero-sub">Usuária do aplicativo</p>
                <div class="lst-pilulas">
                    <span class="lst-pilula"><i data-lucide="mail"></i> {{ $u->emailUsuario }}</span>
                    <span class="lst-pilula"><i data-lucide="calendar"></i> Cadastro em {{ $u->dataCadastro->format('d/m/Y') }}</span>
                </div>
            </div>
            <span class="lst-status lst-pilula--grande {{ $u->status_classe }}">{{ $u->status_rotulo }}</span>
        </div>
    </div>

    <div class="lst-detalhe lst-espaco">

        {{-- Coluna principal --}}
        <div class="lst-pilha">

            {{-- Dados da conta --}}
            <div class="lst-card">
                <h3 class="lst-card-titulo">Dados da conta</h3>
                <div class="lst-dados">
                    <div><span class="lst-dados-rotulo">Nome</span><b>{{ $u->nomeUsuario }}</b></div>
                    <div><span class="lst-dados-rotulo">E-mail</span><b>{{ $u->emailUsuario }}</b></div>
                    <div>
                        <span class="lst-dados-rotulo">Data de nascimento</span>
                        <b>{{ $u->dataNascimentoUsuario ? $u->dataNascimentoUsuario->format('d/m/Y') : '—' }}</b>
                    </div>
                    <div><span class="lst-dados-rotulo">Idade</span><b>{{ $u->idade !== null ? $u->idade . ' anos' : '—' }}</b></div>
                    <div><span class="lst-dados-rotulo">Código</span><b class="lst-mono">#{{ $u->codUsuario }}</b></div>
                    <div><span class="lst-dados-rotulo">Situação</span><b>{{ $u->status_rotulo }}</b></div>
                </div>
            </div>

            {{-- Denúncias --}}
            <div class="lst-card">
                <h3 class="lst-card-titulo">Denúncias</h3>

                <div class="lst-dados">
                    <div><span class="lst-dados-rotulo">Denúncias</span><b>{{ $totalDenuncias }}</b></div>
                </div>

                <p class="lst-dados-rotulo lst-espaco">Denúncias mais recentes</p>
                <div class="lst-tabela-wrap">
                    <table class="lst-tabela">
                        <thead>
                            <tr><th>Data</th><th>Assunto</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($denuncias as $d)
                                <tr>
                                    <td>{{ $d->dataDenuncia->format('d/m/Y') }}</td>
                                    <td>{{ $d->motivoDenuncia }}</td>
                                    <td><span class="lst-status {{ $corItem($d->statusDenuncia) }}">{{ $d->statusDenuncia }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="lst-vazio">Nenhuma denúncia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Histórico --}}
            <div class="lst-card">
                <h3 class="lst-card-titulo">Histórico</h3>
                <div class="lst-linha-tempo">
                    <div>
                        <b>Conta criada</b>
                        <p class="lst-mute">{{ $u->dataCadastro->format('d/m/Y \à\s H:i') }}</p>
                    </div>

                    @if ($u->dataAtualizacao->ne($u->dataCadastro))
                        <div>
                            <b>Última alteração na conta</b>
                            <p class="lst-mute">{{ $u->dataAtualizacao->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Coluna lateral --}}
        <div class="lst-lateral">
            <div class="lst-card">
                <h3 class="lst-card-titulo">Situação da conta</h3>
                <span class="lst-status {{ $u->status_classe }}">{{ $u->status_rotulo }}</span>
                <p class="lst-mute lst-dica">{{ $explicacao }}</p>

                @if ($ativa || $suspensa)
                    <div class="lst-acoes lst-acoes--coluna lst-espaco">
                        @if ($ativa)
                            <button type="button" class="lst-btn lst-btn--perigo lst-btn--grande" onclick="abrirConfirmaConta('Suspensa')">
                                <i data-lucide="user-x"></i> Suspender conta
                            </button>
                        @else
                            <button type="button" class="lst-btn lst-btn--ok lst-btn--grande" onclick="abrirConfirmaConta('Ativa')">
                                <i data-lucide="user-check"></i> Reativar conta
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Formulário escondido: o pop-up só confirma e depois envia este formulário --}}
    <form method="POST" action="{{ route('usuarias.status', $u->codUsuario) }}" id="cu-form">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" id="cu-form-status">
    </form>

    {{-- Pop-up de confirmação --}}
    <div class="lst-modal" id="cu-modal" role="dialog" aria-modal="true" aria-labelledby="cu-modal-titulo">
        <div class="lst-modal-fundo" onclick="fecharConfirmaConta()"></div>

        <div class="lst-card lst-modal-caixa lst-modal-caixa--pergunta">
            <div class="lst-modal-cabeca">
                <span class="lst-icone" id="cu-modal-icone"></span>
                <h3 id="cu-modal-titulo"></h3>
                <p id="cu-modal-texto"></p>
            </div>

            <div class="lst-acoes lst-acoes--centro">
                <button type="button" class="lst-btn lst-btn--neutro" onclick="fecharConfirmaConta()">Cancelar</button>
                <button type="button" class="lst-btn lst-btn--primario" onclick="confirmarConta()">Confirmar</button>
            </div>
        </div>
    </div>

    {{-- Aviso rápido --}}
    <div class="lst-toast" id="cu-toast" role="status"><i data-lucide="check-circle-2"></i><span></span></div>
</div>
@endsection

@push('scripts')
    <script>var NOME_USUARIA = @json($u->nomeUsuario);</script>
    <script src="{{ asset('js/conta-usuaria.js') }}"></script>

    {{-- Mensagem que veio do controller (depois de suspender ou reativar) --}}
    @if (session('sucesso') || session('erro'))
        <script>
            mostrarToast(document.getElementById('cu-toast'), @json(session('sucesso') ?? session('erro')));
        </script>
    @endif
@endpush