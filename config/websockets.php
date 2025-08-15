<?php

return [
    'dashboard' => [
        'port' => env('WEBSOCKETS_PORT', 6001),
        'path' => env('WEBSOCKETS_PATH', 'laravel-websockets'),
        'middleware' => [
            'web',
            \BeyondCode\LaravelWebSockets\Dashboard\Http\Middleware\Authorize::class,
        ],
    ],
    'apps' => [
        [
            'id' => env('PUSHER_APP_ID', 'bnlp-app'),
            'name' => env('APP_NAME', 'BNLP'),
            'key' => env('PUSHER_APP_KEY'), // Remove fallback to ensure it uses .env
            'secret' => env('PUSHER_APP_SECRET'), // Remove fallback
            'path' => env('PUSHER_APP_PATH', null),
            'capacity' => null,
            'enable_client_messages' => true,
            'enable_statistics' => false,
        ],
    ],
    'ssl' => [
        'local_cert' => env('WEBSOCKETS_SSL_LOCAL_CERT', null),
        'local_pk' => env('WEBSOCKETS_SSL_LOCAL_PK', null),
        'passphrase' => env('WEBSOCKETS_SSL_PASSPHRASE', null),
    ],
    'max_request_size_in_kb' => 250,
    'statistics' => [
        'model' => \BeyondCode\LaravelWebsockets\Statistics\Models\WebSocketsStatisticsEntry::class,
        'interval_in_seconds' => 60,
        'delete_statistics_older_than_days' => 60,
    ],
];