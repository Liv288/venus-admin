<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaConteudo extends Model
{
    protected $table = 'tbtipocategoria';

    protected $primaryKey = 'codTipoCategoria';

    protected $fillable = [
        'nomeTipoCategoria',
        'descricaoTipoCategoria',
    ];
}
