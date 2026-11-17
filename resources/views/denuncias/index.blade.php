@extends('layouts.app')

@section('titulo', 'Denúncias - Vênus')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="lst-page" data-token="{{ csrf_token() }}">

    {{-- Título --}}
    <div class="lst-topo">
        <div>
            <h1>Lista de denúncias</h1>
            <p class="lst-mute">Analise as denúncias enviadas pelas usuárias e registre a decisão.</p>
        </div>
    </div>

    {{-- Cards de resumo (clicar em um deles filtra a tabela) --}}
    <div class="lst-kpis">
        <button type="button" class="lst-card lst-kpi is-active" data-filtro="todos" aria-pressed="true" title="Mostrar todas as denúncias">
            <span class="lst-icone"><i data-lucide="alert-circle"></i></span>
            <span class="lst-kpi-texto">
                <b id="dn-total">{{ number_format($total, 0, ',', '.') }}</b>
                <span class="lst-kpi-rotulo">Total de denúncias</span>
            </span>
        </button>

        <button type="button" class="lst-card lst-kpi" data-filtro="pendente" aria-pressed="false" title="Mostrar só as pendentes">
            <span class="lst-icone lst-icone--amarelo"><i data-lucide="hourglass"></i></span>
            <span class="lst-kpi-texto">
                <b id="dn-pendentes" data-valor="{{ $pendentes }}">{{ number_format($pendentes, 0, ',', '.') }}</b>
                <span class="lst-kpi-rotulo">Denúncias pendentes</span>
            </span>
        </button>

        <button type="button" class="lst-card lst-kpi" data-filtro="resolvida" aria-pressed="false" title="Mostrar só as resolvidas">
            <span class="lst-icone lst-icone--verde"><i data-lucide="check-circle-2"></i></span>
            <span class="lst-kpi-texto">
                <b id="dn-resolvidas" data-valor="{{ $resolvidas }}">{{ number_format($resolvidas, 0, ',', '.') }}</b>
                <span class="lst-kpi-rotulo">Denúncias resolvidas</span>
            </span>
        </button>
    </div>

    {{-- Tabela --}}
    <div class="lst-card lst-espaco">
        <div class="lst-barra">
            <h3>Denúncias</h3>

            <div class="lst-abas" role="group" aria-label="Status da denúncia">
                <button type="button" class="lst-aba is-active" data-filtro="todos" aria-pressed="true">Todas</button>
                <button type="button" class="lst-aba" data-filtro="pendente" aria-pressed="false">Pendentes</button>
                <button type="button" class="lst-aba" data-filtro="resolvida" aria-pressed="false">Resolvidas</button>
            </div>

            <button type="button" class="lst-btn lst-btn--neutro lst-btn--pequeno" data-pdf>
                <i data-lucide="download"></i> Baixar PDF
            </button>

            <label class="lst-busca">
                <i data-lucide="search"></i>
                <input type="search" id="dn-busca" placeholder="Pesquisar…" aria-label="Pesquisar denúncias" autocomplete="off">
            </label>
        </div>

        <div class="lst-tabela-wrap">
            <table class="lst-tabela">
                <thead>
                    <tr>
                        <th><button type="button" class="lst-ordenar" data-ordenar="id">ID</button></th>
                        <th><button type="button" class="lst-ordenar" data-ordenar="email">Solicitante</button></th>
                        <th><button type="button" class="lst-ordenar" data-ordenar="assunto">Assunto</button></th>
                        <th><button type="button" class="lst-ordenar" data-ordenar="data">Data</button></th>
                        <th><button type="button" class="lst-ordenar" data-ordenar="status">Status</button></th>
                    </tr>
                </thead>

                <tbody id="dn-corpo">
                    @foreach ($denuncias as $d)
                        @php
                            $codigo = 'DE' . str_pad($d->codDenuncia, 6, '0', STR_PAD_LEFT);
                            $email = $d->usuaria?->emailUsuario ?? '—';
                            $pendente = in_array($d->statusDenuncia, \App\Models\Denuncia::ABERTAS);
                        @endphp

                        {{-- O status vai em minúsculas porque é assim que o JavaScript da tela o conhece --}}
                        <tr data-id="{{ $codigo }}"
                            data-email="{{ $email }}"
                            data-data="{{ $d->dataDenuncia->format('Y-m-d') }}"
                            data-status="{{ $pendente ? 'pendente' : 'resolvida' }}"
                            data-descricao="{{ $d->comentarioDenuncia }}"
                            data-url="{{ route('denuncias.decidir', $d->codDenuncia) }}"
                            tabindex="0">

                            <td class="lst-mono">{{ $codigo }}</td>
                            <td>{{ $email }}</td>
                            <td>{{ $d->motivoDenuncia }}</td>
                            <td>{{ $d->dataDenuncia->format('d/m/Y') }}</td>
                            <td>
                                <span class="lst-status {{ $pendente ? 'lst-status--pendente' : 'lst-status--ok' }}">
                                    {{ $pendente ? 'Pendente' : 'Resolvida' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="dn-vazio" @if ($denuncias->isNotEmpty()) hidden @endif>
                        <td colspan="5" class="lst-vazio">Nenhuma denúncia encontrada.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="lst-mute lst-dica">Clique em uma linha para ver os detalhes e atualizar o status.</p>
    </div>

    {{-- Painel lateral: detalhes da denúncia (o JavaScript preenche) --}}
    <div class="lst-painel" id="dn-painel" role="dialog" aria-modal="true" aria-labelledby="dn-id">
        <div class="lst-painel-fundo" data-fechar></div>

        <div class="lst-painel-caixa">
            <div class="lst-painel-topo">
                <h3 id="dn-id"></h3>
                <button type="button" class="lst-icone-btn" data-fechar aria-label="Fechar"><i data-lucide="x"></i></button>
            </div>

            <span class="lst-status" id="dn-status"></span>

            <h3 class="lst-painel-titulo" id="dn-assunto"></h3>
            <p class="lst-mute" id="dn-meta"></p>
            <blockquote class="lst-citacao" id="dn-descricao"></blockquote>

            <div class="lst-acoes">
                <button type="button" class="lst-btn lst-btn--neutro" data-fechar>Fechar</button>
                <button type="button" class="lst-btn lst-btn--primario" id="dn-alternar"></button>
            </div>
        </div>
    </div>

    {{-- Pop-up de confirmação: a justificativa é obrigatória --}}
    <div class="lst-modal lst-modal--2" id="dn-pergunta-modal" role="dialog" aria-modal="true" aria-labelledby="dn-pergunta-texto">
        <div class="lst-card lst-modal-caixa lst-modal-caixa--pergunta">
            <h3 id="dn-pergunta-texto"></h3>

            <label class="lst-dados-rotulo lst-espaco" for="dn-justificativa">Justificativa da decisão *</label>
            <div class="lst-escrever" id="dn-justificativa-caixa">
                <textarea id="dn-justificativa" placeholder="Explique o motivo da sua decisão..." rows="4" maxlength="1000" required></textarea>
            </div>
            <p class="lst-erro-msg" id="dn-justificativa-erro">Informe a justificativa antes de confirmar.</p>

            <div class="lst-acoes lst-acoes--centro">
                <button type="button" class="lst-btn lst-btn--neutro" id="dn-pergunta-nao">Cancelar</button>
                <button type="button" class="lst-btn lst-btn--primario" id="dn-pergunta-sim">Confirmar</button>
            </div>
        </div>
    </div>

    {{-- Aviso rápido --}}
    <div class="lst-toast" id="dn-toast" role="status"><i data-lucide="check-circle-2"></i><span></span></div>
</div>
@endsection

@push('scripts')
    {{-- Gerador de PDF (só esta tela usa) --}}
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
@endpush
