<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tb_Cargo extends Model
{
    protected $table = 'tb_cargo';

    protected $fillable = [
        'nm_cargo',
    ];
}
