@extends('layouts.app')

@section('titulo', 'Analisar cadastro - Vênus')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@php
    $p = $profissional;
    $pendente = $p->status_codigo === 'p';
    $inicial = mb_strtoupper(mb_substr(preg_replace('/^(Dra?\.)\s*/u', '', $p->nomeProfissionalSaude), 0, 1));

    $endereco = $p->ruaProfissional
    ? $p->ruaProfissional . ($p->numeroProfissional ? ', ' . $p->numeroProfissional : '')
    : '';

    if ($endereco && $p->complementoProfissional) {
        $endereco .= ' - ' . $p->complementoProfissional;
    }

    $ruacep = $p->paisProfissional
    ? $p->paisProfissional . ($p->cepProfissional ? ', ' . $p->cepProfissional : '')
    : '';

    // Profissional que não informou conselho de classe (nada para consultar)
    $semConselho = empty(trim((string) $p->conselhoClasseProfissionalSaude));

    // Os 3 documentos do visualizador (o terceiro é a foto de perfil do cadastro)
    $documentos = [
        ['Documento complementar', $p->documentoComplementarProfissional],
        ['Comprovante do conselho', $p->comprovanteConselhoClasseProfissional],
        ['Foto de perfil', $p->fotoPerfilProfissional],
    ];

    // Itens que a pessoa que analisa precisa conferir
    $checklist = [
        ['O nome no documento é o mesmo do cadastro', 'Compare com o nome informado.'],
        ['O documento complementar está legível e válido', 'Sem cortes, reflexos ou data vencida.'],
        ['O comprovante do conselho está legível e válido', 'Veja o comprovante do conselho de classe.'],
        ['O registro está ativo na consulta pública', 'Confirme no site oficial do conselho.'],
        ['A categoria informada combina com o registro', 'Ex.: psicóloga com registro no CRP.'],
    ];

    $localizacao = trim(($p->cidadeProfissional ?? '') . ($p->ufProfissional ? ' - ' . $p->ufProfissional : ''), ' -');
@endphp

@section('content')
<div class="lst-page">

    {{-- Topo: voltar + navegação da fila --}}
    <div class="lst-cabeca">
        <a href="{{ route('cadastros.index') }}" class="lst-btn lst-btn--neutro lst-btn--pequeno">
            <i data-lucide="arrow-left"></i> Voltar para a lista
        </a>

        @if ($pendente && $posicao !== false)
            <div class="lst-fila">
                <a class="lst-nav-btn" aria-label="Cadastro anterior"
                   href="{{ $anterior ? route('cadastros.show', $anterior) : '#' }}"
                   @if (!$anterior) aria-disabled="true" @endif><i data-lucide="chevron-left"></i></a>
                <span class="lst-mute">{{ $posicao + 1 }} de {{ count($fila) }} pendentes</span>
                <a class="lst-nav-btn" aria-label="Próximo cadastro"
                   href="{{ $proximo ? route('cadastros.show', $proximo) : '#' }}"
                   @if (!$proximo) aria-disabled="true" @endif><i data-lucide="chevron-right"></i></a>
            </div>
        @endif
    </div>

    {{-- Passo a passo --}}
    <ol class="lst-etapas">
        <li class="lst-etapa" id="dc-passo-0"><span class="lst-etapa-num" id="dc-passo-num-0">1</span> Ver documentos</li>
        <li class="lst-etapa" id="dc-passo-1"><span class="lst-etapa-num" id="dc-passo-num-1">2</span> Consultar o conselho</li>
        <li class="lst-etapa" id="dc-passo-2"><span class="lst-etapa-num" id="dc-passo-num-2">3</span> Conferir o checklist</li>
        <li class="lst-etapa" id="dc-passo-3"><span class="lst-etapa-num" id="dc-passo-num-3">4</span> Decidir</li>
    </ol>

    {{-- Resumo da profissional --}}
    <div class="lst-hero">
        <div class="lst-hero-topo">
            <span class="lst-hero-avatar">{{ $inicial }}</span>
            <div class="lst-hero-info">
                <h1 id="dc-nome">{{ $p->nomeProfissionalSaude }}</h1>
                <p class="lst-hero-sub">{{ $p->categoriaProfissional }}</p>
                <div class="lst-pilulas">
                    <span class="lst-pilula lst-pilula--branca">{{ $p->conselho ?? $p->nrFiscalProfissional}}</span>
                    <span class="lst-pilula"><i data-lucide="mail"></i> {{ $p->emailProfissionalSaude }}</span>
                    <span class="lst-pilula"><i data-lucide="calendar"></i> Cadastro em {{ $p->dataCadastro->format('d/m/Y') }}</span>
                </div>
            </div>
            <span class="lst-status lst-pilula--grande
                {{ $p->status_codigo === 'p' ? 'lst-status--pendente' : ($p->status_codigo === 'r' ? 'lst-status--reenvio' : ($p->status_codigo === 'a' ? 'lst-status--ok' : '')) }}">
                {{ $p->status_rotulo }}
            </span>
        </div>
    </div>

    <div class="lst-detalhe lst-espaco">

        {{-- Coluna principal --}}
        <div class="lst-pilha">

            {{-- Documentos --}}
            <div class="lst-card">
                <div class="lst-card-topo">
                    <h3 class="lst-card-titulo">Documentos enviados</h3>
                    <div class="lst-abas" role="group" aria-label="Documentos">
                        @foreach ($documentos as $n => $doc)
                            <button type="button" class="lst-aba {{ $n === 0 ? 'is-active' : '' }}" id="dc-aba-{{ $n }}"
                                    onclick="abrirDocumento({{ $n }})">{{ $doc[0] }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="lst-palco lst-palco--real" id="dc-palco">
                    @foreach ($documentos as $n => $doc)
                        <div class="lst-arquivo-real" id="dc-doc-{{ $n }}" @if ($n !== 0) hidden @endif>
                            @php $url = \App\Models\Profissional::urlArquivo($doc[1]); @endphp
                            @if (!$url)
                                <p class="lst-mute">Documento não enviado ou indisponível.</p>
                            @elseif (str_ends_with(strtolower($doc[1]), '.pdf'))
                                <iframe src="{{ $url }}" title="{{ $doc[0] }}"></iframe>
                            @else
                                <img src="{{ $url }}" alt="{{ $doc[0] }} de {{ $p->nomeProfissionalSaude }}">
                            @endif
                        </div>
                    @endforeach

                    <div class="lst-ferramentas">
                        <button type="button" class="lst-ferr-btn" onclick="diminuirZoom()" aria-label="Diminuir zoom"><i data-lucide="zoom-out"></i></button>
                        <span id="dc-zoom-texto">100%</span>
                        <button type="button" class="lst-ferr-btn" onclick="aumentarZoom()" aria-label="Aumentar zoom"><i data-lucide="zoom-in"></i></button>
                        <button type="button" class="lst-ferr-btn" onclick="girarDocumento()" aria-label="Girar documento"><i data-lucide="rotate-cw"></i></button>
                    </div>
                </div>
            </div>

            {{-- Dados do cadastro --}}
            <div class="lst-card">
                <h3 class="lst-card-titulo">Dados do cadastro</h3>
                <div class="lst-dados">
                    <div><span class="lst-dados-rotulo">Nº fiscal</span><b>{{ $p->nrFiscalProfissional ?: '—' }}</b></div>
                    <div><span class="lst-dados-rotulo">Telefone</span><b>{{ $p->telProfissionalSaude ?? '-'}}</b></div>
                    <div><span class="lst-dados-rotulo">E-mail</span><b>{{ $p->emailProfissionalSaude ?? '-'}}</b></div>
                    <div><span class="lst-dados-rotulo">Categoria</span><b>{{ $p->categoriaProfissional }}</b></div>
                    <div><span class="lst-dados-rotulo">Especialidade</span><b>{{ $p->especialidadeProfissionalSaude ?? '—' }}</b></div>
                    <div><span class="lst-dados-rotulo">Conselho de classe</span><b>{{ $p->conselhoClasseProfissionalSaude ?: '—' }}</b></div>
                    <div><span class="lst-dados-rotulo">País</span><b>{{ $ruacep ?: '—' }}</b></div>
                    <div><span class="lst-dados-rotulo">Cidade</span><b>{{ $localizacao ?: '—' }}</b></div>
                    <div><span class="lst-dados-rotulo">Endereço</span><b>{{ $endereco ?: '—' }}</b></div>
                </div>

                <p class="lst-dados-rotulo lst-espaco">Apresentação</p>
                <p>{{ $p->apresentacaoProfissional ?? '-'}}</p>
            </div>

            {{-- Histórico --}}
            <div class="lst-card">
                <h3 class="lst-card-titulo">Histórico</h3>
                <div class="lst-linha-tempo">
                    <div>
                        <b>Cadastro enviado</b>
                        <p class="lst-mute">{{ $p->dataCadastro->format('d/m/Y \à\s H:i') }}</p>
                    </div>

                    @if (!$pendente)
                        <div>
                            <b>{{ $p->status_rotulo }}</b>
                            <p class="lst-mute">{{ $p->dataAtualizacao->format('d/m/Y \à\s H:i') }}</p>
                            @if ($p->motivoVerificacao)
                                <p>{{ $p->motivoVerificacao }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Coluna lateral --}}
        <div class="lst-lateral">

            {{-- Verificação externa --}}
            <div class="lst-card">
                <h3 class="lst-card-titulo">Consulta no conselho</h3>

                <div class="lst-pessoa lst-pessoa--espaco">
                    <span class="lst-conselho">{{ $p->conselho }}</span>
                    <div>
                        <b id="dc-numero">{{ $p->nrFiscalProfissional ?: '—' }}</b>
                        <span class="lst-mute">Número fiscal</span>
                    </div>
                    <button type="button" class="lst-icone-btn lst-pessoa-fim" onclick="copiarNumero()" aria-label="Copiar número">
                        <i data-lucide="copy"></i>
                    </button>
                </div>

                <a href="{{ $p->consulta_url }}" target="_blank" rel="noopener" onclick="abrirConsulta()"
                   class="lst-btn lst-btn--neutro lst-btn--grande">
                    <i data-lucide="external-link"></i> Abrir consulta pública
                </a>

                {{-- Só aparece para quem não informou conselho: segue para a próxima etapa sem consultar --}}
                @if ($semConselho)
                    <button type="button" class="lst-btn lst-btn--primario lst-btn--grande lst-espaco"
                            id="dc-sem-conselho" onclick="seguirSemConsulta()">
                        <i data-lucide="arrow-right"></i> Não possui conselho: continuar
                    </button>
                @endif

                <p class="lst-dica lst-dica--centro lst-mute" id="dc-consulta-dica"></p>
            </div>

            {{-- Checklist --}}
            <div class="lst-card">
                <div class="lst-checklist-topo">
                    <div class="lst-anel" id="dc-anel">
                        <svg viewBox="0 0 84 84" aria-hidden="true">
                            <circle class="lst-anel-trilha" cx="42" cy="42" r="36" fill="none" stroke-width="8"></circle>
                            <circle class="lst-anel-progresso" id="dc-anel-progresso" cx="42" cy="42" r="36" fill="none" stroke-width="8"
                                    stroke-linecap="round" stroke-dasharray="0 226.2"></circle>
                        </svg>
                        <b id="dc-anel-texto">0/5</b>
                    </div>
                    <div>
                        <h3 class="lst-card-titulo">Checklist</h3>
                        <p class="lst-mute">Marque tudo para liberar a aprovação.</p>
                    </div>
                </div>

                @foreach ($checklist as $n => $item)
                    <label class="lst-item-check" id="dc-item-{{ $n }}">
                        <input type="checkbox" id="dc-check-{{ $n }}" onchange="atualizarTela()">
                        <span class="lst-item-check-caixa"><i data-lucide="check"></i></span>
                        <span>
                            {{ $item[0] }}
                            <small>{{ $item[1] }}</small>
                        </span>
                    </label>
                @endforeach
            </div>

            {{-- Decisão (só para cadastro pendente) --}}
            <div class="lst-card">
                @unless ($pendente)
                    <h3 class="lst-card-titulo">Cadastro já analisado</h3>
                    <p class="lst-mute">Situação: {{ $p->status_rotulo }}.</p>
                    @if ($p->motivoVerificacao)
                        <p class="lst-dica">{{ $p->motivoVerificacao }}</p>
                    @endif
                @endunless

                <div class="lst-botoes-decisao lst-decisao--solta" @unless ($pendente) hidden @endunless>
                    <button type="button" class="lst-btn lst-btn--ok lst-btn--grande" id="dc-aprovar" onclick="abrirModalDecisao('aprovar')">
                        <i data-lucide="badge-check"></i> Aprovar cadastro
                    </button>
                    <p class="lst-mute lst-dica--centro" id="dc-faltam"></p>
                    <button type="button" class="lst-btn lst-btn--primario lst-btn--grande" onclick="abrirModalDecisao('reenvio')">
                        <i data-lucide="rotate-ccw"></i> Solicitar reenvio
                    </button>
                    <button type="button" class="lst-btn lst-btn--perigo lst-btn--grande" onclick="abrirModalDecisao('reprovar')">
                        <i data-lucide="x-circle"></i> Reprovar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulário escondido: o pop-up só confirma e depois envia este formulário --}}
    <form method="POST" action="{{ route('cadastros.decidir', $p->codProfissionalSaude) }}" id="dc-form">
        @csrf
        <input type="hidden" name="decisao" id="dc-form-decisao">
        <input type="hidden" name="motivo" id="dc-form-motivo">
        <div id="dc-form-documentos"></div>
    </form>

    {{-- Pop-up de decisão --}}
    <div class="lst-modal" id="dc-modal" role="dialog" aria-modal="true" aria-labelledby="dc-modal-titulo">
        <div class="lst-modal-fundo" onclick="fecharModalDecisao()"></div>

        <div class="lst-card lst-modal-caixa lst-modal-caixa--acima">
            <div class="lst-modal-cabeca">
                <span class="lst-icone lst-icone--verde" id="dc-icone-aprovar"><i data-lucide="badge-check"></i></span>
                <span class="lst-icone" id="dc-icone-reenvio"><i data-lucide="rotate-ccw"></i></span>
                <span class="lst-icone" id="dc-icone-reprovar"><i data-lucide="x-circle"></i></span>
                <h3 id="dc-modal-titulo"></h3>
                <p id="dc-modal-texto"></p>
            </div>

            {{-- Quais documentos a profissional deve reenviar (só no pedido de reenvio) --}}
            <div class="lst-docs" id="dc-docs-bloco" hidden>
                <p class="lst-docs-titulo">Documentos para reenviar</p>
                <label class="lst-doc-opcao"><input type="checkbox" class="dc-pedir-doc" value="foto"> Foto de perfil</label>
                <label class="lst-doc-opcao"><input type="checkbox" class="dc-pedir-doc" value="complementar"> Documento complementar</label>
                <label class="lst-doc-opcao"><input type="checkbox" class="dc-pedir-doc" value="comprovante"> Comprovante do conselho</label>
                <p class="lst-erro-msg" id="dc-docs-erro">Escolha pelo menos um documento.</p>
            </div>

            <div id="dc-motivo-bloco" hidden>
                <div class="lst-rapidas" id="dc-motivos-reenvio">
                    <button type="button" onclick="usarMotivo('Foto do documento ilegível')">Foto ilegível</button>
                    <button type="button" onclick="usarMotivo('Documento fora da validade')">Documento vencido</button>
                    <button type="button" onclick="usarMotivo('Nome do documento diferente do cadastro')">Nome diferente</button>
                    <button type="button" onclick="usarMotivo('Número do conselho não confere')">Número não confere</button>
                </div>

                <div class="lst-rapidas" id="dc-motivos-reprovar">
                    <button type="button" onclick="usarMotivo('Registro não localizado na consulta pública')">Registro não localizado</button>
                    <button type="button" onclick="usarMotivo('Registro inativo ou com restrição')">Registro inativo</button>
                    <button type="button" onclick="usarMotivo('Documentos com indício de irregularidade')">Irregularidade</button>
                </div>

                <div class="lst-escrever" id="dc-motivo-caixa">
                    <textarea id="dc-motivo" oninput="limparErroMotivo()" placeholder="Escreva o motivo..."></textarea>
                </div>
                <p class="lst-erro-msg">Escreva o motivo (pelo menos 10 letras).</p>
            </div>

            <div class="lst-acoes">
                <button type="button" class="lst-btn lst-btn--neutro" onclick="fecharModalDecisao()">Cancelar</button>
                <button type="button" class="lst-btn" id="dc-confirmar" onclick="confirmarDecisao()">Confirmar</button>
            </div>
        </div>
    </div>

    <div class="lst-toast" id="dc-toast" role="status"><i data-lucide="check-circle-2"></i><span></span></div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/aprovacao-cadastro.js') }}"></script>
@endpush