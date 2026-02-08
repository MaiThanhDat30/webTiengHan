<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    | PostgreSQL (Render) là DB chính
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [

        /*
        |--------------------------------------------------------------------------
        | SQLite (giữ mặc định)
        |--------------------------------------------------------------------------
        */
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        /*
        |--------------------------------------------------------------------------
        | MySQL – SOURCE (LOCAL)
        |--------------------------------------------------------------------------
        */
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        /*
        |--------------------------------------------------------------------------
        | PostgreSQL – RENDER (DB ĐÍCH)
        |--------------------------------------------------------------------------
        */
        // 'pgsql_render' => [
        //     'driver' => 'pgsql',
        //     'host' => env('DB_HOST'),
        //     'port' => env('DB_PORT', 5432),
        //     'database' => env('DB_DATABASE'),
        //     'username' => env('DB_USERNAME'),
        //     'password' => env('DB_PASSWORD'),
        //     'charset' => 'utf8',
        //     'prefix' => '',
        //     'schema' => 'public',
        //     'sslmode' => 'require',
        // ],
        'pgsql_old' => [
            'driver' => 'pgsql',
            'host' => env('OLD_DB_HOST'),
            'port' => env('OLD_DB_PORT', 5432),
            'database' => env('OLD_DB_DATABASE'),
            'username' => env('OLD_DB_USERNAME'),
            'password' => env('OLD_DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'require',
        ],
        /*
        |--------------------------------------------------------------------------
        | PostgreSQL – DEFAULT (trùng render, để Laravel dùng mặc định)
        |--------------------------------------------------------------------------
        */
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT', 5432),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'require',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */
    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    */
    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_',
        ],

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],
    ],
];
