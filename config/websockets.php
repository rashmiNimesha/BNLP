<?php

return [
    'dashboard' => [
        'port' => env('WEBSOCKETS_PORT', 6001),
        'path' => env('WEBSOCKETS_PATH', 'laravel-websockets'),
        'middleware' => [
            'web',
            \BeyondCode\LaravelWebsockets\Dashboard\Http\Middleware\Authorize::class,
        ],
    ],
    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'path' => env('PUSHER_APP_PATH'),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
        ],
    ],
    'app_provider' => \BeyondCode\LaravelWebsockets\Apps\ArrayAppProvider::class,
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