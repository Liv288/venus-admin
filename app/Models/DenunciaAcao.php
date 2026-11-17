<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenunciaAcao extends Model
{
    protected $table = 'tbDenunciaAcao';

    protected $fillable = [
        'idDenuncia',
        'idAdmin',
        'acaoTomada',
        'justificativa',
    ];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class, 'idDenuncia');
    }
}
