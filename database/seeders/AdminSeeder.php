<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbadmin')->updateOrInsert(
            ['emailAdmin' => 'admin@gmail.com'],
            [
                'nomeAdmin' => 'Fernando de Oliveira',
                'telAdmin' => '11 99999-9999',
                'senhaAdmin' => Hash::make('Adm@123'),
                'paisAdmin' => 'Brasil',
                'cidadeAdmin' => 'São Paulo',
                'ufAdmin' => 'SP',
                'cepAdmin' => '01001-000',
                'ruaAdmin' => 'Praça da Sé',
                'numeroAdmin' => '01',
                'complementoAdmin' => 'Lado ímpar',
                'nrFiscalAdmin' => '12.345.427/0001-00',
                'statusConta' => 'Ativa',
                'dataCadastro' => now(),
                'dataAtualizacao' => now(),
            ]
        );
    }
}
