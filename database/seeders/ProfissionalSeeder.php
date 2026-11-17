<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfissionalSeeder extends Seeder
{
    public function run(): void
    {
        // nome, categoria, status da verificação (Em análise = acabou de se cadastrar), dias desde o cadastro
        $profissionais = [
            ['Dra. Helena Prado',      'Médico',            'Aprovado',           105],
            ['Dra. Renata Figueiredo', 'Assistente Social', 'Aprovado',            98],
            ['Marina Albuquerque',     'Psicologo',         'Aprovado',            84],
            ['Dra. Sílvia Tavares',    'Médico',            'Aprovado',            71],
            ['Paula Guimarães',        'Outro',             'Aprovado',            63],
            ['Cláudia Menezes',        'Enfermeiro',        'Aprovado',            52],
            ['Dra. Tereza Bastos',     'Médico',            'Aprovado',            40],
            ['Vanessa Siqueira',       'Enfermeiro',        'Aprovado',            29],
            ['Dra. Aline Cordeiro',    'Médico',            'Em análise',          11],
            ['Roberta Faria',          'Psicologo',         'Em análise',           8],
            ['Dra. Lúcia Honorato',    'Assistente Social', 'Pendência',            5],
            ['Simone Andrade',         'Outro',             'Aprovado',             2],
            ['Priscila Veloso',        'Enfermeiro',        'Em análise',          15],
            ['Cíntia Rangel',          'Psicologo',         'Pendência',           33],
        ];

        // Conselho de classe de cada categoria
        $conselhos = [
            'Médico' => 'CRM',
            'Enfermeiro' => 'COREN',
            'Psicologo' => 'CRP',
            'Assistente Social' => 'CRESS',
            'Outro' => 'CREFITO',
        ];

        $senha = Hash::make('Senha@123');

        foreach ($profissionais as $i => [$nome, $categoria, $status, $dias]) {
            $data = now()->subDays($dias)->setTime(8 + ($i % 10), ($i * 13) % 60);
            $email = strtolower(preg_replace('/[^a-z]/i', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nome))) . '@exemplo.com';

            DB::table('tbprofissionalsaude')->updateOrInsert(
                ['emailProfissionalSaude' => $email],
                [
                    'nomeProfissionalSaude' => $nome,
                    'telProfissionalSaude' => sprintf('11 9%04d-%04d', 1000 + $i * 37, 2000 + $i * 53),
                    'senhaProfissionalSaude' => $senha,
                    'duasEtapasAtiva' => false,
                    'categoriaProfissional' => $categoria,
                    'especialidadeProfissionalSaude' => $categoria,
                    'conselhoClasseProfissionalSaude' => $conselhos[$categoria] ?? null,
                    'apresentacaoProfissional' => "Profissional de saúde da mulher ({$categoria}), com foco em atendimento acolhedor e informação de qualidade.",
                    'paisProfissional' => 'Brasil',
                    'cidadeProfissional' => 'São Paulo',
                    'ufProfissional' => 'SP',
                    'nrFiscalProfissional' => sprintf('%03d.%03d.%03d-%02d', 100 + $i * 7, 200 + $i * 11, 300 + $i * 13, 10 + $i),
                    'fotoPerfilProfissional' => 'perfil-' . (($i % 5) + 1) . '.png',
                    'statusVerificacao' => $status,
                    'motivoVerificacao' => null,
                    'statusConta' => 'Ativa',
                    'comprovanteConselhoClasseProfissional' => 'carteira-conselho.png',
                    'documentoComplementarProfissional' => 'documento-foto.png',
                    'dataCadastro' => $data,
                    'dataAtualizacao' => $data,
                ]
            );
        }
    }
}