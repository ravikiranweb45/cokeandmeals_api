<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=coke_and_meals',
    //'dsn' => 'pgsql:host=localhost;dbname=ks_6_4_2022',
    'username' => 'postgres',
    'password' => 'bigcity123',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public' //specify your schema here, public is the default schema
        ]
    ], // PostgreSQL
];