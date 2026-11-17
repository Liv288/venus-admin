@extends('layouts.app')

@section('titulo', 'Contas de usuárias - Vênus')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/listas.css') }}">
@endpush

@section('content')
<div class="lst-page">

    {{-- Título --}}
    <div class="lst-topo">
        <div>
            <h1>Contas de usuárias</h1>
            <p class="lst-mute">Consulte os dados e a situação da conta de cada usuária do aplicativo.</p>
        </div>

        <div>
            <button type="button" class="lst-btn lst-btn--neutro lst-btn--pequeno" onclick="baixarPdfUsuarias()">
                <i data-lucide="download"></i> Baixar PDF
            </button>
        </div>
    </div>

    <div class="lst-card lst-espaco">

        {{-- Busca + filtro de status (os dois recarregam a página com ?busca= e ?status=) --}}
        <div class="lst-barra">
            <h3>Usuárias</h3>

            <form method="GET" action="{{ route('usuarias.index') }}" class="lst-busca">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <button type="submit" class="lst-icone-btn" aria-label="Pesquisar"><i data-lucide="search"></i></button>
                <input type="search" name="busca" value="{{ $busca }}" placeholder="Buscar por nome ou e-mail" autocomplete="off">
            </form>

            <div class="lst-filtro lst-filtro--fim">
                <button type="button" class="lst-btn lst-btn--primario" id="lu-filtro-btn" aria-expanded="false">
                    <i data-lucide="sliders-horizontal"></i><span>{{ $status ? $statusLista[$status] : 'Filtro' }}</span>
                </button>

                <div class="lst-menu" id="lu-menu">
                    <a href="{{ route('usuarias.index', array_filter(['busca' => $busca])) }}"
                       @if (!$status) aria-pressed="true" @endif>
                        <span>Todas</span><i data-lucide="check"></i>
                    </a>
                    @foreach ($statusLista as $chave => $rotulo)
                        <a href="{{ route('usuarias.index', array_filter(['busca' => $busca, 'status' => $chave])) }}"
                           @if ($status === $chave) aria-pressed="true" @endif>
                            <span>{{ $rotulo }}</span><i data-lucide="check"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="lst-tabela-wrap">
            <table class="lst-tabela lst-tabela--larga">
                <thead>
                    <tr>
                        <th>Usuária</th>
                        <th>Idade</th>
                        <th>Cadastro em</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($usuarias as $u)
                        <tr>
                            <td>
                                <div class="lst-pessoa">
                                    <span class="lst-avatar">{{ $u->inicial }}</span>
                                    <div>
                                        <b>{{ $u->nomeUsuario }}</b>
                                        <span class="lst-mute">{{ $u->emailUsuario }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $u->idade !== null ? $u->idade . ' anos' : '—' }}</td>
                            <td>{{ $u->dataCadastro->format('d/m/Y') }}</td>
                            <td><span class="lst-status {{ $u->status_classe }}">{{ $u->status_rotulo }}</span></td>
                            <td>
                                <a href="{{ route('usuarias.show', $u->codUsuario) }}" class="lst-btn lst-btn--pequeno lst-btn--neutro" style="background-color: #9B1B2A;">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="lst-vazio">Nenhuma usuária encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="lst-mute lst-dica">
            @if ($usuarias->total())
                Mostrando {{ $usuarias->firstItem() }}–{{ $usuarias->lastItem() }} de {{ $usuarias->total() }}
            @endif
        </p>

        @if ($usuarias->hasPages())
            <div class="lst-paginacao">{{ $usuarias->links('vendor.pagination.listas') }}</div>
        @endif
    </div>

    {{-- Aviso rápido --}}
    <div class="lst-toast" id="lu-toast" role="status"><i data-lucide="check-circle-2"></i><span></span></div>
</div>
@endsection

@push('scripts')
    {{-- Gerador de PDF (só esta tela usa) --}}
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>

    {{-- Linhas do PDF: todas as usuárias da busca/filtro atual --}}
    <script>var LINHAS_PDF = @json($linhasPdf);</script>
    <script src="{{ asset('js/conta-usuaria.js') }}"></script>
@endpush