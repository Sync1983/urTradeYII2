<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [ 
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['consloe'],
                    'except'  => ['application'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/console.log',
                ],
            ],
        ],
        'mongodb' => $db,
    ],
    'params' => $params,
];
