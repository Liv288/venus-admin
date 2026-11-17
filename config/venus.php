<?php

return [
    // O link do e-mail de reenvio de documentos aponta para o site do profissional.
    // Rodando local, o admin monta o endereço sozinho: usa o mesmo host em que ele está
    // aberto (localhost ou 127.0.0.1) e a porta abaixo.
    // 8001 é a porta que o "php artisan serve" escolhe sozinho para o segundo site,
    // quando o primeiro já está usando a 8000.
    // Se rodar o profissional em outra porta: php artisan serve --port=XXXX e coloque
    // PROFISSIONAL_PORTA=XXXX no .env do admin.
    'profissional_porta' => (int) env('PROFISSIONAL_PORTA', 8001),

    // Só se um dia o site tiver um endereço fixo (ex.: https://profissional.seusite.com.br).
    // Quando preenchido, tem prioridade sobre o host + porta acima.
    'profissional_url' => env('PROFISSIONAL_URL'),

    // Quantos dias o link de reenvio de documentos continua válido.
    'reenvio_dias' => (int) env('REENVIO_DOCUMENTOS_DIAS', 7),
];
