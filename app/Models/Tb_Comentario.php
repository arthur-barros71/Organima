<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tb_Comentario extends Model
{
    protected $table = 'tb_comentario';

    public $timestamps = false;

    protected $fillable = [
        'ds_comentario',
        'dt_comentario',
        'ic_tipo',
        'ic_conserto',
        'nr_frame',
        'id_usuario',
        'id_projeto',
        'id_roteiro',
    ];

    public function usuario()
    {
        return $this->belongsTo(tb_usuario::class, 'id_usuario');
    }

    public function project()
    {
        return $this->belongsTo(tb_projeto::class, 'id_projeto');
    }

    public function roteiro()
    {
        return $this->belongsTo(tb_roteiro::class, 'id_roteiro');
    }
}
