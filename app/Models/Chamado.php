<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chamado extends Model
{
    protected $table = 'tbChamadoSuporte';

    protected $primaryKey = 'codChamado';

    // A tabela só tem a data de abertura, não tem "atualizado em"
    const CREATED_AT = 'dataAbertura';
    const UPDATED_AT = null;

    protected $fillable = [
        'codProfissionalSaude',
        'assuntoChamado',
        'descricaoChamado',
        'anexoChamado',
        'statusChamado',
        'dataAbertura',
    ];

    // Quem abriu o chamado: o profissional de saúde
    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'codProfissionalSaude', 'codProfissionalSaude');
    }

    public function scopeAbertos($query)
    {
        return $query->where('statusChamado', 'Aberto');
    }
}
