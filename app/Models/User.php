<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'tbadmin';

    protected $primaryKey = 'codAdmin';

    const CREATED_AT = 'dataCadastro';
    const UPDATED_AT = 'dataAtualizacao';

    protected $fillable = [
        'nomeAdmin',
        'emailAdmin',
        'telAdmin',
        'senhaAdmin',
        'paisAdmin',
        'cidadeAdmin',
        'ufAdmin',
        'cepAdmin',
        'ruaAdmin',
        'numeroAdmin',
        'complementoAdmin',
        'nrFiscalAdmin',
        'statusConta',
    ];

    protected $hidden = [
        'senhaAdmin',
    ];
}
