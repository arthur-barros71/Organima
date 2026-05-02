<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by your
    | application. The available options are "pusher", "redis", "log", and
    | "null". You may set this value to any of these drivers as needed.
    |
    */

    'default' => env('BROADCAST_DRIVER', 'pusher'),

    /*
    |--------------------------------------------------------------------------
    | Pusher Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure your pusher connections. The "key", "secret", and
    | "app_id" should be available to you from your Pusher account. You can
    | use the pusher service to broadcast events to your app with ease.
    |
    */

    'connections' => [
        
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,                
            ],
            'client_options' => [
                'verify' => false, // Desabilitando SSL para o Guzzle
            ],
            'auth' => [
                'route' => 'broadcasting.auth',
                'middleware' => ['auth'], // Use o middleware de autenticação
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],

];