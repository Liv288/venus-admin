<link rel="stylesheet" href="{{asset('css/sidebar-pesquisa.css')}}">

<aside id="side">
    <div class="logo">
        <span class="lb">Vênus</span>
        <span class="vm">V.</span>
        <small class="lb">Painel administrativo</small>
    </div>

    <div class="nl">Painel</div>

    <a class="nav {{ request()->routeIs('painel.dashboard') ? 'on' : '' }}" href="{{ route('painel.dashboard') }}">
        <i data-lucide="layout-grid"></i>
        <span class="lb">Visão geral</span>
    </a>

    <div class="nl">Gestão</div>

    {{-- Grupo "Conta": já abre sozinho quando a página atual é uma das dele --}}
    @php
        $grupoContaAberto = request()->routeIs('cadastros.*', 'usuarias.*', 'denuncias.*');
        $cadastrosPendentes = \App\Models\Profissional::pendentes()->count();
        $denunciasPendentes = \App\Models\Denuncia::pendentes()->count();
    @endphp

    <button type="button" class="nav {{ $grupoContaAberto ? 'open' : '' }}" data-grp="g-conta">
        <i data-lucide="user-round"></i>
        <span class="lb">Conta</span>
        <i data-lucide="chevron-down" class="chev"></i>
    </button>
    <div class="sub {{ $grupoContaAberto ? 'open' : '' }}" id="g-conta">
        <a class="nav {{ request()->routeIs('usuarias.*') ? 'on' : '' }}" href="{{ route('usuarias.index') }}">Contas</a>
        <a class="nav {{ request()->routeIs('cadastros.*') ? 'on' : '' }}" href="{{ route('cadastros.index') }}">
            Aprovação de cadastros
            @if ($cadastrosPendentes > 0)
                <span class="bd">{{ $cadastrosPendentes }}</span>
            @endif
        </a>
        <a class="nav {{ request()->routeIs('denuncias.*') ? 'on' : '' }}" href="{{ route('denuncias.index') }}">
            Denúncias
            @if ($denunciasPendentes > 0)
                <span class="bd">{{ $denunciasPendentes }}</span>
            @endif
        </a>
    </div>

    <div class="nl">Perfil</div>

    <a class="nav {{ request()->routeIs('perfil*') ? 'on' : '' }}" href="{{ route('perfil') }}">
        <i data-lucide="user-round"></i>
        <span class="lb">Meu perfil</span>
    </a>

    <div class="spacer"></div>

    <div class="out">
        {{-- O formulário continua existindo: o pop-up só confirma e depois envia ele --}}
        <form method="POST" action="{{ route('auth.logout') }}" id="form-logout">
            @csrf
            <button type="button" class="nav" id="btn-logout">
                <i data-lucide="log-out"></i>
                <span class="lb">Sair</span>
            </button>
        </form>
    </div>
</aside>

{{-- Pop-up de confirmação do logout --}}
<div class="logout-modal" id="logout-modal" aria-hidden="true">
    <div class="logout-caixa" role="dialog" aria-modal="true" aria-labelledby="logout-titulo">
        <span class="logout-icone"><i data-lucide="log-out"></i></span>
        <h3 id="logout-titulo">Deseja sair da conta?</h3>
        <p>Você precisará entrar novamente para acessar o painel.</p>

        <div class="logout-acoes">
            <button type="button" class="logout-btn logout-btn--neutro" id="logout-cancelar">Cancelar</button>
            <button type="button" class="logout-btn logout-btn--perigo" id="logout-confirmar">Sair</button>
        </div>
    </div>
</div>