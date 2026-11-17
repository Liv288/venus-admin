<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariaSeeder extends Seeder
{
    public function run(): void
    {
        $nomes = [
            'Ana Beatriz Lima', 'Camila Rocha', 'Juliana Martins', 'Fernanda Souza', 'Larissa Alves',
            'Mariana Costa', 'Beatriz Ferreira', 'Gabriela Santos', 'Isabela Ribeiro', 'Letícia Carvalho',
            'Amanda Gomes', 'Bruna Pereira', 'Carolina Araújo', 'Daniela Barbosa', 'Eduarda Nunes',
            'Fabiana Teixeira', 'Helena Moreira', 'Joana Cardoso', 'Kelly Dias', 'Luana Mendes',
            'Marcela Freitas', 'Natália Pinto', 'Olívia Campos', 'Patrícia Duarte', 'Rafaela Monteiro',
            'Sabrina Vieira', 'Tainá Correia', 'Valentina Rezende', 'Yasmin Batista', 'Bianca Lopes',
            'Clara Azevedo', 'Débora Farias', 'Elisa Nogueira', 'Flávia Castro', 'Giovana Melo',
            'Heloísa Ramos',
        ];

        $senha = Hash::make('Senha@123');

        foreach ($nomes as $i => $nome) {
            // Espalha os cadastros pelos últimos 120 dias (mais perto de hoje = mais cadastros)
            $diasAtras = (int) floor(120 * (1 - sqrt(($i + 1) / count($nomes))));
            $data = now()->subDays($diasAtras)->setTime(9 + ($i % 9), ($i * 7) % 60);

            // A maioria está ativa; algumas contas foram desativadas ou suspensas
            $status = 'ativa';
            if ($i % 12 === 7) {
                $status = 'excluida';
            } elseif ($i % 17 === 11) {
                $status = 'suspensa';
            }

            // statusUsuario só aceita Ativa, Suspensa ou Excluída (conta desativada = Excluída)
            $statusUsuario = $status === 'suspensa' ? 'excluida' : $status;

            $email = strtolower(preg_replace('/[^a-z]/i', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nome))) . '@exemplo.com';

            DB::table('tbUsuario')->updateOrInsert(
                ['emailUsuario' => $email],
                [
                    'nomeUsuario' => $nome,
                    'dataNascimentoUsuario' => now()->subYears(16 + ($i * 3) % 24)->subDays($i * 11)->toDateString(),
                    'senhaUsuario' => $senha,
                    'statusUsuario' => $status,
                    'dataCadastro' => $data,
                    'dataAtualizacao' => $data,
                ]
            );
        }
    }
}
