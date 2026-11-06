<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'salon_booking'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_merge(
                array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]),
                // Force the driver to initiate an SSL connection when the server
                // requires secure transport (e.g. ProxySQL "SSL is required").
                // Only enabled when DB_SSL=true so local non-SSL dev is unaffected.
                env('DB_SSL', false) ? [
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => (bool) env('DB_SSL_VERIFY', false),
                ] : []
            ) : [],
        ],
    ],
    'migrations' => 'migrations',
];
