<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
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
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                // 'host' => 'mail.eatanytime.in',
                // 'username' => 'vaibhav.desai@eatanytime.in',
                // 'password' => 'Vaibhav@12345',
                // 'port' => '587',

                'host' => 'smtp.rediffmailpro.com',
                'username' => 'dhaval.maru@otbconsulting.co.in',
                'password' => '55921721dNM@',
                'port' => '465',
                'encryption' => 'ssl',

               // 'encryption' => 'tls',
            ],
        ],
    // 'modules' => [
    //     'user' => [
    //         'class' => 'dektrium\user\Module',
    //     ],
    // ],
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // $config['modules']['user'] = [
    //     'class' => 'dektrium\user\Module',
    //     // 'enableUnconfirmedLogin' => true,
    //     // 'confirmWithin' => 21600,
    //     // 'cost' => 12,
    //     // 'admins' => ['admin']
    // ];
}

return $config;
