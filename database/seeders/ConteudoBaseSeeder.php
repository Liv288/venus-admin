<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConteudoBaseSeeder extends Seeder
{
    public function run(): void
    {
        // nome da categoria => descrição
        $categorias = [
            'Saúde mental' => 'Conteúdos sobre saúde mental e bem-estar emocional.',
            'Gravidez na adolescência' => 'Conteúdos sobre gravidez na adolescência e acompanhamento.',
            'ISTs e prevenção' => 'Conteúdos sobre infecções sexualmente transmissíveis e prevenção.',
            'Meus direitos' => 'Conteúdos sobre direitos das mulheres e das adolescentes.',
            'Métodos contraceptivos' => 'Conteúdos sobre métodos contraceptivos e planejamento.',
            'Corpo e puberdade' => 'Conteúdos sobre o corpo e as mudanças da puberdade.',
            'Rede de apoio' => 'Conteúdos sobre serviços e pessoas que oferecem apoio.',
        ];

        $formatos = [
            'Artigo',
            'Vídeo curto',
            'Vídeo animado',
            'Infográfico',
            'Cartilha',
            'Card comparativo',
        ];

        foreach ($categorias as $nome => $descricao) {
            DB::table('tbtipocategoria')->updateOrInsert(
                ['nomeTipoCategoria' => $nome],
                ['descricaoTipoCategoria' => $descricao, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach ($formatos as $descricao) {
            DB::table('tbTipoConteudo')->updateOrInsert(
                ['descricaoTipoConteudo' => $descricao],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
