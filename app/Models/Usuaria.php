<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuaria extends Model
{
    protected $table = 'tbUsuario';

    protected $primaryKey = 'codUsuario';

    const CREATED_AT = 'dataCadastro';
    const UPDATED_AT = 'dataAtualizacao';

    protected $fillable = [
        'nomeUsuario',
        'apelidoUsuario',
        'emailUsuario',
        'senhaUsuario',
        'dataNascimentoUsuario',
        'cidadeUsuario',
        'ufUsuario',
        'fotoPerfilUsuario',
        'temaUsuario',
        'idiomaUsuario',
        'anonimaPadraoUsuario',
        'statusUsuario',
        'dataCadastro',
        'dataAtualizacao',
    ];

    protected $hidden = [
        'senhaUsuario',
    ];

    protected function casts(): array
    {
        return [
            'dataNascimentoUsuario' => 'date',
        ];
    }

    public function scopeAtivas($query)
    {
        return $query->where('statusUsuario', 'ativa');
    }

    // ---------- Relações ----------

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'codUsuarioDenunciante', 'codUsuario');
    }

    // ---------- Apoio para as telas de contas de usuárias ----------

    // Primeira letra do nome, usada no avatar
    public function getInicialAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->nomeUsuario, 0, 1));
    }

    // Idade em anos (null se a data de nascimento não estiver preenchida)
    public function getIdadeAttribute(): ?int
    {
        return $this->dataNascimentoUsuario?->age;
    }

    // Texto do status para mostrar na tela (no banco fica em minúsculas, sem acento)
    public function getStatusRotuloAttribute(): string
    {
        return match ($this->statusUsuario) {
            'ativa' => 'Ativa',
            'suspensa' => 'Suspensa',
            'excluida' => 'Excluída',
            default => (string) $this->statusUsuario,
        };
    }

    // Cor do selo de status (as classes vêm do listas.css)
    public function getStatusClasseAttribute(): string
    {
        return match ($this->statusUsuario) {
            'ativa' => 'lst-status--ok',
            'suspensa' => 'lst-status--pendente',
            'excluida' => 'lst-status--recusado',
            default => '',
        };
    }
}