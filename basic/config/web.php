<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',    
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'  => 'ru-RU',
    'timeZone'  => 'Europe/Moscow',
    'name'      => 'АвтоТехСнаб',
    'modules'   => [
      'gridview' => [
          'class' => '\kartik\grid\Module'
       ],
    ],
    'components' => [        
        /*'assetManager' => [          
          'forceCopy'  => true,
        ],*/ 
        'request' => [            
            'cookieValidationKey' => 'nGL57l_yqQrai_FQAYuDEwZoYnfrJZdg',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [            
            'identityClass' => 'app\models\MongoUser',
            'enableAutoLogin' => true,
            'class' => 'app\models\SiteUser'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],        
        'mailer' => [
            'class'		=> 'yii\swiftmailer\Mailer',
            'transport' => [
			  'class'		=> 'Swift_SmtpTransport',
			  'host'		=> 'mail.atc58.ru',
			  'username'	=> 'robot@atc58.ru',
			  'password'	=> 'cFt32rT1',			  
			  'port'		=> '465',
			  'encryption'	=> 'tls',			  
			],
			//'useFileTransport'=>'false'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET','_POST'],
                ],
                [ 
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['balance'],
                    'except'  => ['application'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/balance.log',
                ],
                [ 
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['console'],
                    'except'  => ['application'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/console.log',
                ],
            ]
        ],
        'mongodb' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class'=>'yii\debug\Module','allowedIPs' => ['91.144.179.85','10.0.6.*','10.0.6.104', '::1']];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
