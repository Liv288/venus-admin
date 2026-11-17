<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatoConteudo extends Model
{
    protected $table = 'tbFormatoConteudo';

    protected $fillable = [
        'nomeFormatoConteudo',
    ];
}
