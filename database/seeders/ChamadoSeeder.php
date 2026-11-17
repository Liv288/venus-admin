<?php

namespace Database\Seeders;

use App\Models\Profissional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChamadoSeeder extends Seeder
{
    public function run(): void
    {
        // assunto, status, dias desde o envio
        $chamados = [
            ['Não consigo redefinir minha senha',      'Aberto',    1],
            ['Erro ao cadastrar horário na agenda',    'Aberto',    2],
            ['Dúvida sobre exclusão da conta',         'Resolvido', 7],
            ['Notificações não chegam no celular',     'Aberto',    5],
            ['Como alterar meu e-mail?',               'Resolvido', 14],
            ['A página de publicações não carrega',    'Resolvido', 22],
        ];

        $ids = Profissional::orderBy('codProfissionalSaude')->pluck('codProfissionalSaude')->all();
        if (count($ids) === 0) {
            return;
        }

        DB::table('tbChamadoSuporte')->delete();

        foreach ($chamados as $i => [$assunto, $status, $dias]) {
            $data = now()->subDays($dias)->setTime(13 + $i, 40);

            DB::table('tbChamadoSuporte')->insert([
                'codProfissionalSaude' => $ids[($i * 7 + 2) % count($ids)],
                'assuntoChamado' => $assunto,
                'descricaoChamado' => 'Olá, preciso de ajuda: ' . mb_strtolower($assunto) . '.',
                'anexoChamado' => '', // a coluna não aceita NULL
                'statusChamado' => $status,
                'dataAbertura' => $data,
            ]);
        }
    }
}
