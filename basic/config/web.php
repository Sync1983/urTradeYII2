<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',    
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'  => 'ru-RU',
    'components' => [
        'request' => [            
            'cookieValidationKey' => 'nGL57l_yqQrai_FQAYuDEwZoYnfrJZdg',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [            
            'identityClass' => 'app\models\MongoUser',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mongodb' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class'=>'yii\debug\Module','allowedIPs' => ['10.0.6.101','10.0.6.104', '127.0.0.1', '::1']];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
