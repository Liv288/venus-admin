<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Link de reenvio de documentos: criado pelo admin, usado na página do profissional.
class ReenvioDocumentos extends Model
{
    protected $table = 'tbReenvioDocumentos';

    protected $fillable = [
        'codProfissionalSaude',
        'chave',
        'documentos',
    ];

    // Nome de cada documento que o admin pode pedir
    public const DOCUMENTOS = [
        'foto' => 'Foto de perfil',
        'complementar' => 'Documento complementar',
        'comprovante' => 'Comprovante do conselho de classe',
    ];

    // Lista de chaves pedidas (ex.: ["foto", "comprovante"])
    public function listaDocumentos(): array
    {
        return array_values(array_intersect(
            array_keys(self::DOCUMENTOS),
            explode(',', (string) $this->documentos)
        ));
    }
}
