<?php

return array(
    'databases' => array(
        'primary' => array(
            'adapter' => 'pgsql',
            'driver' => "pgsql:host=localhost;port=5432;dbname=demo;",
            'database' => "demo",
            'host' => 'localhost',
            'username' => "root",
            'password' => 'root',
            'charset' => 'utf8',
            'collate' => 'utf8_polish_ci',
            'schema' => '../_sql/schema.sql',
            'many-to-many' => '../_sql/many-to-many.php',
        )
    ),
    'runtime' => [
        'defaultConnection' => 'primary',
        'connections' => ['primary']
    ],
    'generator' => [
        'defaultConnection' => 'primary',
        'connections' => ['primary']
    ]
);
