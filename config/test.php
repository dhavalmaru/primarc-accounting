<?php
$params = require(__DIR__ . '/params.php');
$dbParams = require(__DIR__ . '/test_db.php');

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),    
    'language' => 'en-US',
    'components' => [
        'db' => $dbParams,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [            
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],        
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
        'html2pdf' => [
            'class' => 'yii2tech\html2pdf\Manager',
            'viewPath' => '@app/pdf',
            'converter' => 'wkhtmltopdf',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',,
            'useFileTransport'=>'false',
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
        'modules' => [
            'user' => [
                'class' => 'dektrium\user\Module',
            ],
        ],
    ],
    'params' => $params,
];
