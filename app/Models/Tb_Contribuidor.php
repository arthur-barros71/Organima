<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Contribuidor extends Model
{
    use HasFactory;

    // Definindo explicitamente o nome da tabela
    protected $table = 'tb_contribuidor';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_projeto',
        'id_roteiro',
        'id_cargo',
    ];

    public function usuario()
    {
        return $this->belongsTo(tb_usuario::class, 'id_usuario');
    }

    public function projeto()
    {
        return $this->belongsTo(tb_projeto::class, 'id_projeto');
    }

    public function roteiro()
    {
        return $this->belongsTo(tb_roteiro::class, 'id_roteiro');
    }
}
