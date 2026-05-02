<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Roteiro extends Model
{
    use HasFactory;

    // Definindo explicitamente o nome da tabela
    protected $table = 'tb_roteiro';

    protected $fillable = [
        'nm_roteiro',
        'ds_roteiro',
        'qt_cena',
        'id_usuario',
        'content',
    ];

    public function usuario()
    {
        return $this->belongsTo(tb_usuario::class, 'id_usuario');
    }
}
