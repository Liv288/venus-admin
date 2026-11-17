<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Vênus</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="direita">
    </div>

    <div class="esquerda">
        <form action="{{ route('auth.login') }}" method="post" novalidate>
        @csrf
            <div class="titulo-subtitulo">
                <h1>Login ADM</h1>
                <p>Entre no site para gerenciar e administrar o nosso aplicativo e o site de profissional.</p>
            </div>

                @if ($errors->any())
                    <div class="erro-login" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li><p>{{ $error }}</p></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('erro'))
                    <div class="erro-login" role="alert">
                        <p>{{ session('erro') }}</p>
                    </div>
                @endif

                @if (session('sucesso'))
                    <div class="sucesso-login" role="status">
                        <p>{{ session('sucesso') }}</p>
                    </div>
                @endif

            <div class="input-label">
                <label for="emailAdmin">E-mail</label>
                <div class="input-com-icone">
                    <img src="{{ asset('images/gmail.png') }}" alt="" class="icone-input">
                    <input type="email" id="emailAdmin" name="email" value="{{ old('email') }}" placeholder="email@endereço" autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="input-label">
                <label for="senhaAdmin">Senha</label>
                <div class="input-com-icone">
                    <img src="{{ asset('images/senha.png') }}" alt="" class="icone-input">
                    <input type="password" id="senhaAdmin" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
                    <img src="{{ asset('images/visibilidade.png') }}" alt="Mostrar ou ocultar senha" class="icone-input-direita" id="olho" role="button" tabindex="0">
                </div>
            </div>

            <div class="colocar-ladoAlado">
                <a href="#">Esqueci minha senha</a>
            </div>

            <button type="submit" class="button-submit">Entrar</button>
        </form>
    </div>

    <script>
        const olho = document.getElementById('olho');
        const campoSenha = document.getElementById('senhaAdmin');

        function alternarSenha() {
            const mostrando = campoSenha.type === 'text';
            campoSenha.type = mostrando ? 'password' : 'text';
            olho.classList.toggle('ativo', !mostrando);
        }

        olho.addEventListener('click', alternarSenha);
        olho.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                alternarSenha();
            }
        });
    </script>
</body>
</html>