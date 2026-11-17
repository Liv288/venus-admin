<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoConteudo extends Model
{
    protected $table = 'tbTipoConteudo';

    protected $primaryKey = 'codTipoConteudo';

    protected $fillable = [
        'descricaoTipoConteudo',
    ];
}
