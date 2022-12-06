<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'dbSqlite' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:'. realpath(__DIR__ . '/../db').'/sqlite.db',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
    ],
];
