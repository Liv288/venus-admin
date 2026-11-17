<link rel="stylesheet" href="{{ asset('css/sidebar-pesquisa.css') }}">

<div class="top">
    <button class="ib" id="menuBtn" aria-label="Menu" onclick="compactarSidebar()">
        <i data-lucide="menu"></i>
    </button>

    <form class="search" onsubmit="return false">
    </form>

    <a href="{{ route('perfil') }}" class="av" aria-label="Meu perfil">
        <span>{{ mb_strtoupper(mb_substr(session('nome', 'A'), 0, 1)) }}</span>
    </a>
</div>
