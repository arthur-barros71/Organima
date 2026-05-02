<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('chat_proj.{id}', function ($user, $id) {

    // Permitir se quiser sempre (teste)
    return true;
});

Broadcast::channel('chat_rot.{id}', function ($user, $id) {

    // Permitir se quiser sempre (teste)
    return true;
});
