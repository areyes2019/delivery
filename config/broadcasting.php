<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'pusher'),

    'connections' => [

        // 🚫 Reverb NO se usa (lo dejamos por compatibilidad futura)
        'reverb' => [
            'driver' => 'reverb',
            'key' => null,
            'secret' => null,
            'app_id' => null,
            'options' => [],
        ],

        // ✅ PUSHER (ÚNICO ACTIVO)
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],
];
