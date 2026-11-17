<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * A ordem importa: denúncias dependem das usuárias e chamados dependem dos profissionais.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UsuariaSeeder::class,
            ProfissionalSeeder::class,
            DenunciaSeeder::class,
            ChamadoSeeder::class,
            ConteudoBaseSeeder::class,
        ]);
    }
}
