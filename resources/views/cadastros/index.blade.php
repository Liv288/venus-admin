@extends('layouts.app')

@section('titulo', 'Aprovar cadastros - Vênus')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="lst-page" data-url-lote="{{ route('cadastros.lote') }}" data-token="{{ csrf_token() }}">

    {{-- Título --}}
    <div class="lst-topo">
        <div>
            <h1>Aprovar cadastros</h1>
            <p class="lst-mute">Analise os cadastros de profissionais antes de liberar o acesso ao portal.</p>
        </div>

        <div>
            <button type="button" class="lst-btn lst-btn--neutro lst-btn--pequeno" data-pdf>
                <i data-lucide="download"></i> Baixar PDF
            </button>
        </div>
    </div>

    {{-- Cards de resumo (clicar em um deles abre a aba do status) --}}
    <div class="lst-kpis">
        <button type="button" class="lst-card lst-kpi" data-aba="p">
            <span class="lst-icone lst-icone--amarelo"><i data-lucide="clock"></i></span>
            <span class="lst-kpi-texto">
                <b id="ap-total-p">0</b>
                <span class="lst-kpi-rotulo">Pendentes</span>
            </span>
        </button>

        <button type="button" class="lst-card lst-kpi" data-aba="r">
            <span class="lst-icone"><i data-lucide="rotate-ccw"></i></span>
            <span class="lst-kpi-texto">
                <b id="ap-total-r">0</b>
                <span class="lst-kpi-rotulo">Reenvio solicitado</span>
            </span>
        </button>

        <button type="button" class="lst-card lst-kpi" data-aba="a">
            <span class="lst-icone lst-icone--verde"><i data-lucide="badge-check"></i></span>
            <span class="lst-kpi-texto">
                <b id="ap-total-a">0</b>
                <span class="lst-kpi-rotulo">Aprovados</span>
            </span>
        </button>
    </div>

    <div class="lst-card lst-espaco">

        {{-- Barra de ações em lote (aparece ao marcar alguém) --}}
        <div class="lst-lote" id="ap-lote">
            <span class="lst-lote-texto" id="ap-lote-n">0 selecionados</span>
            <button type="button" class="lst-btn lst-btn--pequeno" data-lote="aprovar">Aprovar selecionados</button>
            <button type="button" class="lst-btn lst-btn--pequeno lst-btn--contorno" data-lote="reenvio">Solicitar reenvio</button>
        </div>

        {{-- Título + abas + filtro de conselho + busca --}}
        <div class="lst-barra">
            <h3>Cadastros de profissionais</h3>

            <div class="lst-abas" role="group" aria-label="Status do cadastro">
                <button type="button" class="lst-aba" data-aba="p">
                    Pendentes <span class="lst-aba-badge" id="ap-badge-p">0</span>
                </button>
                <button type="button" class="lst-aba" data-aba="r">Reenvio</button>
                <button type="button" class="lst-aba" data-aba="a">Aprovados</button>
                <button type="button" class="lst-aba" data-aba="x">Reprovados</button>
            </div>

            <div class="lst-abas lst-abas--fim" role="group" aria-label="Conselho">
                <button type="button" class="lst-aba" data-conselho-filtro="">Todos</button>
                <button type="button" class="lst-aba" data-conselho-filtro="CRM">CRM</button>
                <button type="button" class="lst-aba" data-conselho-filtro="CRP">CRP</button>
                <button type="button" class="lst-aba" data-conselho-filtro="CRN">CRN</button>
                <button type="button" class="lst-aba" data-conselho-filtro="COREN">COREN</button>
                <button type="button" class="lst-aba" data-conselho-filtro="CREFITO">CREFITO</button>
            </div>

            <label class="lst-busca">
                <i data-lucide="search"></i>
                <input type="search" id="ap-busca" placeholder="Buscar por nome ou registro" autocomplete="off">
            </label>
        </div>

        {{-- Tabela --}}
        <div class="lst-tabela-wrap">
            <table class="lst-tabela lst-tabela--larga">
                <thead>
                    <tr>
                        <th class="lst-col-chk"><input type="checkbox" class="lst-chk" id="ap-todos" aria-label="Selecionar todos"></th>
                        <th>Profissional</th>
                        <th>Conselho</th>
                        <th>Nº fiscal</th>
                        <th>Cadastro em</th>
                        <th>Espera</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody id="ap-corpo">
                    @foreach ($profissionais as $p)
                        @php
                            $codigo = $p->status_codigo;
                            $pendente = $codigo === 'p';
                            $dias = (int) $p->dataCadastro->diffInDays(now());
                            $classeStatus = match ($codigo) {
                                'p' => 'lst-status--pendente',
                                'r' => 'lst-status--reenvio',
                                'a' => 'lst-status--ok',
                                default => '',
                            };
                        @endphp

                        <tr data-id="{{ $p->codProfissionalSaude }}"
                            data-status="{{ $codigo }}"
                            data-nome="{{ $p->nomeProfissionalSaude }}"
                            data-numero="{{ $p->nrFiscalProfissional }}"
                            data-conselho="{{ $p->conselho }}">

                            <td>
                                <input type="checkbox" class="lst-chk" data-selecionar="{{ $p->codProfissionalSaude }}"
                                       aria-label="Selecionar {{ $p->nomeProfissionalSaude }}" @disabled(!$pendente)>
                            </td>

                            <td>
                                <div class="lst-pessoa">
                                    <span class="lst-avatar">{{ mb_strtoupper(mb_substr(preg_replace('/^(Dra?\.)\s*/u', '', $p->nomeProfissionalSaude), 0, 1)) }}</span>
                                    <div>
                                        <b>{{ $p->nomeProfissionalSaude }}</b>
                                        <span class="lst-mute">{{ $p->emailProfissionalSaude }}</span>
                                    </div>
                                </div>
                            </td>

                            <td><span class="lst-conselho">{{ $p->conselho }}</span></td>
                            <td><span class="lst-mono">{{ $p->nrFiscalProfissional ?: '—' }}</span></td>
                            <td>{{ $p->dataCadastro->format('d/m/Y') }}</td>

                            <td>
                                <span data-espera class="{{ $pendente && $dias >= 7 ? 'lst-atraso' : '' }}">
                                    @if ($pendente)
                                        {{ $dias === 0 ? 'hoje' : $dias . ($dias === 1 ? ' dia' : ' dias') }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </td>

                            <td><span class="lst-status {{ $classeStatus }}">{{ $p->status_rotulo }}</span></td>

                            <td>
                                <a href="{{ route('cadastros.show', $p->codProfissionalSaude) }}"
                                   class="lst-btn lst-btn--pequeno {{ $pendente ? 'lst-btn--primario' : 'lst-btn--neutro' }}" style="background-color: #9B1B2A;">{{ $pendente ? 'Analisar' : 'Ver' }}</a>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="ap-vazio" hidden>
                        <td colspan="8" class="lst-vazio"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pop-up de decisão em lote (aprovar ou pedir reenvio) --}}
    <div class="lst-modal" id="ap-modal" role="dialog" aria-modal="true" aria-labelledby="ap-modal-titulo">
        <div class="lst-card lst-modal-caixa">
            <div class="lst-modal-cabeca">
                <span class="lst-icone" id="ap-modal-icone"></span>
                <h3 id="ap-modal-titulo"></h3>
                <p id="ap-modal-texto"></p>
            </div>

            <div id="ap-motivo-bloco" hidden>
                <div class="lst-docs">
                    <p class="lst-docs-titulo">Documentos para reenviar</p>
                    <label class="lst-doc-opcao"><input type="checkbox" class="ap-pedir-doc" value="foto"> Foto de perfil</label>
                    <label class="lst-doc-opcao"><input type="checkbox" class="ap-pedir-doc" value="complementar"> Documento complementar</label>
                    <label class="lst-doc-opcao"><input type="checkbox" class="ap-pedir-doc" value="comprovante"> Comprovante do conselho</label>
                    <p class="lst-erro-msg" id="ap-docs-erro">Escolha pelo menos um documento.</p>
                </div>

                <div class="lst-rapidas">
                    <button type="button" data-motivo="Foto do documento ilegível">Foto ilegível</button>
                    <button type="button" data-motivo="Documento fora da validade">Documento vencido</button>
                    <button type="button" data-motivo="Nome do documento diferente do cadastro">Nome diferente</button>
                    <button type="button" data-motivo="Número do conselho não confere">Número não confere</button>
                </div>

                <div class="lst-escrever" id="ap-motivo-caixa">
                    <textarea id="ap-motivo" placeholder="Explique o que precisa ser corrigido..."></textarea>
                </div>
                <p class="lst-erro-msg">Escreva o motivo (pelo menos 10 letras).</p>
            </div>

            <div class="lst-acoes">
                <button type="button" class="lst-btn lst-btn--neutro" data-fechar>Cancelar</button>
                <button type="button" class="lst-btn" id="ap-confirmar">Confirmar</button>
            </div>
        </div>
    </div>

    {{-- Aviso rápido --}}
    <div class="lst-toast" id="ap-toast" role="status"><i data-lucide="check-circle-2"></i><span></span></div>
</div>
@endsection

@push('scripts')
    {{-- Gerador de PDF (só esta tela usa) --}}
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>

    {{-- Mensagem que veio do controller (depois de decidir um cadastro) --}}
    @if (session('sucesso') || session('erro'))
        <script>
            mostrarToast(document.getElementById('ap-toast'), @json(session('sucesso') ?? session('erro')));
        </script>
    @endif
@endpush