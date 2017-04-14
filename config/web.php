<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'xNWti-kuzjHZcozK0We7OwddZad6bGYl',
            'baseUrl' => 'http://localhost/primarc_pecan/web/'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
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
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */

        'mycomponent' => [
            'class' => 'app\components\MyComponent'
        ],

        'html2pdf' => [
            'class' => 'yii2tech\html2pdf\Manager',
            'viewPath' => '@app/pdf',
            'converter' => 'wkhtmltopdf',
        ],

        // 'assetManager' => [
        //     'bundles' => [
        //         'yii\web\JqueryAsset' => false,
        //     ],
        // ],

        // 'assetManager' => [
        //     'bundles' => [
        //         'yii\web\JqueryAsset' => [
        //             'sourcePath' => null,   // do not publish the bundle
        //             'js' => [
        //                 // '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
        //                 'js/jquery/jquery.min.js',
        //             ]
        //         ],
        //     ],
        // ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
        ],
    ],

    // 'defaultRoute' => 'login/index',
    // 'controllerNamespace' => 'app\\backend\\controllers',
    // 'defaultRoute' => 'backend/controllers/user/security/login',
    // 'defaultRoute' => 'user/security/login',
    'defaultRoute' => 'security/login',
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    // $config['modules']['user'] = [
    //     'class' => 'dektrium\user\Module',
    //     // 'enableUnconfirmedLogin' => true,
    //     // 'confirmWithin' => 21600,
    //     // 'cost' => 12,
    //     // 'admins' => ['admin'],
    //     // 'controllerMap' => [
    //     //     'admin' => [
    //     //         'class'  => 'dektrium\user\controllers\AdminController',
    //     //         'layout' => false,
    //     //     ],
    //     // ],
    //     // uncomment the following to add your IP if you are not connecting from localhost.
    //     //'allowedIPs' => ['127.0.0.1', '::1'],
    // ];
}

return $config;
