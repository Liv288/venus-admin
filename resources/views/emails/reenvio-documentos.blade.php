@extends('emails.layout')

@section('titulo', 'Reenvio de documentos')

@section('conteudo')
    <h2>Olá, {{ $profissional->nomeProfissionalSaude }}!</h2>

    <p>
        Analisamos o seu cadastro no <strong>Vênus</strong> e precisamos que você
        envie novamente alguns documentos.
    </p>

    <div class="box">
        <strong>Motivo</strong>
        <p>{{ $motivo }}</p>
    </div>

    <div class="box">
        <strong>Documentos que devem ser reenviados</strong>
        <ul>
            @foreach ($nomesDocumentos as $nome)
                <li>{{ $nome }}</li>
            @endforeach
        </ul>
    </div>

    <p>Para reenviar, clique no botão abaixo:</p>

    <div class="button-container">
        <a href="{{ $link }}" class="button">Reenviar documentos</a>
    </div>

    <p class="warning">
        Este link ficará disponível por <strong>{{ $dias }} {{ $dias === 1 ? 'dia' : 'dias' }}</strong>.
        Depois que você enviar, o cadastro volta para análise.
    </p>

    <p>Se você não fez esse cadastro, pode ignorar este e-mail com segurança.</p>
@endsection
