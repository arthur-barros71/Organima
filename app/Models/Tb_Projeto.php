<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Projeto extends Model
{
    use HasFactory;

    protected $table = 'tb_projeto';

    protected $fillable = [
        'nm_projeto',
        'ds_projeto',
        'qt_frame',
        'id_usuario',
        'id_tipo',
        'nm_fps',
        'qt_volume',
        'dt_inicial',
        'updated_at',
        'nm_proporcao',
        'id_roteiro',
    ];

    public function usuario()
    {
        return $this->belongsTo(tb_usuario::class, 'id_usuario');
    }

    public function roteiro()
    {
        return $this->belongsTo(tb_roteiro::class, 'id_roteiro');
    }
}