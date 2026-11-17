<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil - Vênus</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/sidebar-pesquisa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfil-config.css') }}">
</head>
<body>

    @include('layouts.sidebar')

    <div class="direita">
        @include('layouts.barra-pesquisa')

        @php
            $localizacao = collect([$admin->cidadeAdmin, $admin->ufAdmin, $admin->paisAdmin])->filter()->implode(', ');
            $endereco = collect([
                $admin->ruaAdmin,
                $admin->numeroAdmin,
                $admin->complementoAdmin,
            ])->filter()->implode(', ');
        @endphp

        <div class="wrap">

            @if (session('sucesso'))
                <div class="alerta ok" role="status">{{ session('sucesso') }}</div>
            @endif

            <div class="card">
                <div class="pcover"></div>

                <div class="top2">
                    <div class="pav">
                        <span>{{ mb_strtoupper(mb_substr($admin->nomeAdmin, 0, 1)) }}</span>
                    </div>

                    <div class="grow">
                        <h1>{{ $admin->nomeAdmin }}</h1>
                        <p class="sm">{{ $localizacao ?: 'Localização não informada' }}</p>
                    </div>
                    
                    <a href="{{ route('perfil.editar') }}" class="btn p">
                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
                        fill="currentColor" viewBox="0 0 24 24" >
                        <!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
                        <path d="M6 22h12c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2h-1V7c0-2.76-2.24-5-5-5S7 4.24 7 7v2H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2M9 7c0-1.65 1.35-3 3-3s3 1.35 3 3v2H9zm-3 4h12v9h-5v-2.28c.59-.35 1-.99 1-1.72 0-1.1-.9-2-2-2s-2 .9-2 2a2 2 0 0 0 1 1.72V20H6z"></path>
                        </svg> Mudar senha
                    </a>

                    <a href="{{ route('perfil.editar') }}" class="btn p">
                        <i data-lucide="pencil"></i> Editar perfil
                    </a>
                </div>
            </div>

            <div class="pg">
                <div class="stack">
                    <div class="card">
                        <h3>Contato</h3>

                        <div class="ct">
                            <div class="ico"><i data-lucide="mail"></i></div>
                            <div><label>E-mail</label><b>{{ $admin->emailAdmin }}</b></div>
                        </div>

                        <div class="ct">
                            <div class="ico"><i data-lucide="phone"></i></div>
                            <div><label>Telefone</label><b>{{ $admin->telAdmin ?: '—' }}</b></div>
                        </div>

                        <div class="ct">
                            <div class="ico"><i data-lucide="map-pin"></i></div>
                            <div><label>Localização</label><b>{{ $localizacao ?: '—' }}</b></div>
                        </div>
                    </div>

                    <div class="card zona-perigo">
                        <h3>Excluir conta</h3>
                        <p class="sm">Ao excluir, você perde o acesso ao painel e não poderá mais entrar com esta conta.</p>

                        <button type="button" class="btn d" id="btn-excluir-conta">
                            <i data-lucide="trash-2"></i> Excluir minha conta
                        </button>
                    </div>
                </div>

                <div class="stack">
                    <div class="card">
                        <h3>Informações pessoais</h3>
                        <div class="dl">
                            <div><label>Nome</label><b>{{ $admin->nomeAdmin }}</b></div>
                            <div><label>Nº fiscal</label><b>{{ $admin->nrFiscalAdmin ?: '—' }}</b></div>
                            <div><label>País</label><b>{{ $admin->paisAdmin ?: '—' }}</b></div>
                            <div><label>Cidade</label><b>{{ $admin->cidadeAdmin ?: '—' }}</b></div>
                            <div><label>Estado</label><b>{{ $admin->ufAdmin ?: '—' }}</b></div>
                            <div><label>CEP</label><b>{{ $admin->cepAdmin ?: '—' }}</b></div>
                            <div><label>Endereço</label><b>{{ $endereco ?: '—' }}</b></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Pop-up de confirmação da exclusão (o formulário só é enviado depois de confirmar) --}}
        <form method="POST" action="{{ route('perfil.excluir') }}" id="form-excluir-conta">
            @csrf
            @method('PUT')
        </form>

        <div class="logout-modal" id="excluir-modal" aria-hidden="true">
            <div class="logout-caixa" role="dialog" aria-modal="true" aria-labelledby="excluir-titulo">
                <span class="logout-icone"><i data-lucide="trash-2"></i></span>
                <h3 id="excluir-titulo">Deseja excluir sua conta?</h3>
                <p>Você será desconectado e não poderá mais acessar o painel com esta conta.</p>

                <div class="logout-acoes">
                    <button type="button" class="logout-btn logout-btn--neutro" id="excluir-cancelar">Cancelar</button>
                    <button type="button" class="logout-btn logout-btn--perigo" id="excluir-confirmar">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/adm.js') }}"></script>
<script>
    (function () {
        var modal = document.getElementById('excluir-modal');
        var abrir = document.getElementById('btn-excluir-conta');

        function mostrar(sim) {
            modal.classList.toggle('is-open', sim);
            modal.setAttribute('aria-hidden', sim ? 'false' : 'true');
            (sim ? document.getElementById('excluir-cancelar') : abrir).focus();
        }

        abrir.addEventListener('click', function () { mostrar(true); });
        document.getElementById('excluir-cancelar').addEventListener('click', function () { mostrar(false); });
        document.getElementById('excluir-confirmar').addEventListener('click', function () {
            document.getElementById('form-excluir-conta').submit();
        });
        modal.addEventListener('click', function (e) { if (e.target === modal) { mostrar(false); } });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) { mostrar(false); }
        });
    })();
</script>
    <script>lucide.createIcons();</script>

</body>
</html>
