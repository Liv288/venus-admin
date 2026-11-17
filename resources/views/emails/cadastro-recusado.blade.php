@extends('emails.layout')

@section('titulo', 'Cadastro recusado')

@section('conteudo')
    <h2>Olá, {{ $profissional->nomeProfissionalSaude }}.</h2>

    <p>
        Analisamos o seu cadastro no <strong>Vênus</strong> e, infelizmente,
        ele <strong>não pôde ser aceito</strong>.
    </p>

    <div class="box">
        <strong>Motivo</strong>
        <p>{{ $motivo }}</p>
    </div>

    <p>
        Se você acredita que houve algum engano, entre em contato com o
        suporte do Vênus.
    </p>
@endsection
