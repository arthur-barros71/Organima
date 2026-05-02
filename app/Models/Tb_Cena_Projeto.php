<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tb_Cena_Projeto extends Model
{
    protected $table = 'tb_cena_projeto';

    public $timestamps = false;

    protected $fillable = [
        'nm_cena_projeto',
        'ds_cena_projeto',
        'nr_frame_inicial',
        'nr_frame_final',
        'qt_frame',
        'nm_cor',
        'dt_incial',
        'dt_final',
        'id_projeto',
        'id_cena_roteiro',
    ];

    public function projeto()
    {
        return $this->belongsTo(tb_projeto::class, 'id_projeto');
    }

    public function cena_roteiro()
    {
        return $this->belongsTo(tb_cena_roteiro::class, 'id_cena_roteiro');
    }
}
