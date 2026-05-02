<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tb_Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'tb_usuario';

    protected $fillable = [
        'nm_usuario',
        'ds_email',
        'cd_senha',
        'nr_telefone',
    ];

    protected $hidden = [
        'cd_senha',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'cd_senha' => 'hashed',
        ];
    }
}