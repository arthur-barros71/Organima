<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tb_Tipos_De_Projeto extends Model
{
    protected $table = 'tb_tipo';

    protected $fillable = [
        'nm_tipo',
    ];
}
