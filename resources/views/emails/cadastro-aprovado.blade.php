@extends('emails.layout')

@section('titulo', 'Conta aprovada')

@section('conteudo')
    <h2>Parabéns, {{ $profissional->nomeProfissionalSaude }}!</h2>

    <p>
        O seu cadastro no <strong>Vênus</strong> foi analisado e <strong>aceito</strong>.
    </p>

    <p>
        Você já pode acessar a sua conta no portal do profissional usando o
        e-mail e a senha que cadastrou.
    </p>

    <p>Seja bem-vinda(o) ao Vênus!</p>
@endsection
