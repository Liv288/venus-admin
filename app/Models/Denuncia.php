<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    protected $table = 'tbDenuncia';

    protected $primaryKey = 'codDenuncia';

    // A tabela só tem a data de criação (dataDenuncia), não tem "atualizado em"
    const CREATED_AT = 'dataDenuncia';
    const UPDATED_AT = null;

    // A tela só conhece dois grupos: em aberto e encerradas
    const ABERTAS = ['Pendente', 'Em análise'];
    const ENCERRADAS = ['Resolvida', 'Arquivada'];

    protected $fillable = [
        'codDenuncia',
        'tipoAlvo',
        'codAlvo',
        'codUsuarioDenunciante',
        'motivoDenuncia',
        'comentarioDenuncia',
        'gravidade',
        'statusDenuncia',
        'dataDenuncia',
    ];

    protected function casts(): array
    {
        return [
            'dataDenuncia' => 'datetime',
        ];
    }

    public function usuaria()
    {
        return $this->belongsTo(Usuaria::class, 'codUsuarioDenunciante', 'codUsuario');
    }

    public function scopePendentes($query)
    {
        return $query->whereIn('statusDenuncia', self::ABERTAS);
    }

    public function scopeResolvidas($query)
    {
        return $query->whereIn('statusDenuncia', self::ENCERRADAS);
    }
}
