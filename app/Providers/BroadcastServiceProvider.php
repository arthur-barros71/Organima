<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Registra a rota POST /broadcasting/auth com middleware de autenticação
        Broadcast::routes(['middleware' => ['auth']]);

        // Carrega seus canais declarados em routes/channels.php
        require base_path('routes/channels.php');
    }
}
