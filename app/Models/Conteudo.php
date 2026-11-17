<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conteudo extends Model
{
    protected $table = 'tbConteudo';

    protected $primaryKey = 'codConteudo';

    const CREATED_AT = 'dataCriacao';
    const UPDATED_AT = 'dataAtualizacao';

    protected $fillable = [
        'codAdmin',
        'tituloConteudo',
        'corpoConteudo',
        'capaConteudo',
        'tempoLeituraMinutos',
        'codTipoCategoria',
        'codTipoConteudo',
        'codProfissionalSaude',
        'statusConteudo',
        'dataCriacao',
        'dataAtualizacao',
    ];

    protected function casts(): array
    {
        return [
            'dataCriacao' => 'datetime',
        ];
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaConteudo::class, 'codTipoCategoria', 'codTipoCategoria');
    }

    public function formato()
    {
        return $this->belongsTo(TipoConteudo::class, 'codTipoConteudo', 'codTipoConteudo');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'codAdmin', 'codAdmin');
    }

    // Tempo de leitura: 200 palavras por minuto, no mínimo 1 minuto
    public static function calcularTempoLeitura(?string $texto): int
    {
        preg_match_all('/[\p{L}\p{N}]+/u', strip_tags($texto ?? ''), $palavras);

        return max(1, (int) ceil(count($palavras[0]) / 200));
    }
}
