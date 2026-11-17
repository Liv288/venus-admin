<?php

namespace Database\Seeders;

use App\Models\Usuaria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DenunciaSeeder extends Seeder
{
    public function run(): void
    {
        // motivo, status, dias desde o envio
        $denuncias = [
            ['Mensagem ofensiva em conversa',          'Pendente',  1],
            ['Perfil falso de profissional',           'Pendente',  3],
            ['Conteúdo inadequado em comentário',      'Pendente',  4],
            ['Spam com propaganda de produtos',        'Resolvida', 9],
            ['Linguagem agressiva no chat',            'Resolvida', 17],
            ['Informação de saúde enganosa',           'Pendente',  6],
            ['Assédio durante atendimento',            'Resolvida', 26],
            ['Foto de perfil imprópria',               'Resolvida', 41],
        ];

        $tiposAlvo = ['Fórum publicação', 'Fórum resposta', 'Perfil profissional'];
        $gravidades = ['Baixa', 'Média', 'Alta'];

        $ids = Usuaria::orderBy('codUsuario')->pluck('codUsuario')->all();
        if (count($ids) === 0) {
            return;
        }

        DB::table('tbDenuncia')->delete();

        foreach ($denuncias as $i => [$motivo, $status, $dias]) {
            $data = now()->subDays($dias)->setTime(10 + $i, 15);

            DB::table('tbDenuncia')->insert([
                'tipoAlvo' => $tiposAlvo[$i % count($tiposAlvo)],
                'codAlvo' => (string) ($i + 1),
                'codUsuarioDenunciante' => $ids[($i * 5) % count($ids)],
                'motivoDenuncia' => $motivo,
                'comentarioDenuncia' => 'Descrição enviada pela usuária sobre: ' . mb_strtolower($motivo) . '.',
                'gravidade' => $gravidades[$i % count($gravidades)],
                'statusDenuncia' => $status,
                'dataDenuncia' => $data,
            ]);
        }
    }
}
