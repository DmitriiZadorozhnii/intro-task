<?php
return [
    'class'    => \yii\db\Connection::class,
    'dsn'      => sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        env('DB_HOST', 'db'),
        env('DB_PORT', '3306'),
        env('DB_DATABASE', 'yii2')
    ),
    'username' => env('DB_USERNAME', 'yii2'),
    'password' => env('DB_PASSWORD', 'secret'),
    'charset'  => 'utf8mb4',

    #'enableSchemaCache'   => true,
    #'schemaCacheDuration' => 3600,
    #'schemaCache'         => 'cache',
];
