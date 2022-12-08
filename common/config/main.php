<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'dbSqlite' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:'. realpath(__DIR__ . '/../db').'/sqlite.db',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'useFileTransport' => false,
            'viewPath' => '@common/mail',
            'transport' => [
//                'scheme' => 'smtp',
//                'host' => 'smtp.gmail.com',
//                'username' => 'max.hofmann125x@gmail.com',
//                'password' => 'lenqgutavebuqnzr',
//                'port' => 465,
//                'encryption' => 'ssl',
//                'auth_mode' => 'login',
//                'dsn' => 'native://default',
                'dsn' => 'gmail://max.hofmann125x@gmail.com:lenqgutavebuqnzr@default',
            ],
        ],
    ],
];
