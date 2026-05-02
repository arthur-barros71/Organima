<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tb_Cena_Roteiro extends Model
{
    protected $table = 'tb_cena_roteiro';

    public $timestamps = false;

    protected $fillable = [
        'nm_cena_roteiro',
        'ds_cena_roteiro',
        'nm_cor',
        'dt_incial',
        'dt_final',
        'ds_texto',
        'id_roteiro',
    ];

    public function roteiro()
    {
        return $this->belongsTo(tb_roteiro::class, 'id_roteiro');
    }
}
