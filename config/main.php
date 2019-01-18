<?php
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

'components' => array(
    'currency_formator' => array(
        'class' => 'ext.yii-extension-INRCurrencyFormator.INRCurrencyFormator',
        'params' => array(
            'postfix'  => 'only',
            'currency' => '₹'
        )
    ),
    'db' => $db,
    'request' => array(
            'baseUrl' => 'http://localhost/primarc_new_data/primarc_pecan/web/'
    ),
    'html2pdf' => array(
            'class' => 'yii2tech\html2pdf\Manager',
            'viewPath' => '@app/pdf',
            'converter' => 'wkhtmltopdf',
        ),
    'mailer' => array(
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                // 'host' => 'mail.eatanytime.in',
                // 'username' => 'vaibhav.desai@eatanytime.in',
                // 'password' => 'Vaibhav@12345',
                // 'port' => '587',

                'host' => 'smtp.googlemail.com',
                'username' => 'accounts@primarcpecan.com',
                'password' => 'Primarcaccounts',
                'port' => '465',
                'encryption' => 'ssl',
                
               // 'encryption' => 'tls',
            ],
        ),
    // 'modules' => array(
    //     'user' => array(
    //         'class' => 'dektrium\user\Module',
    //         // 'enableUnconfirmedLogin' => true,
    //         // 'confirmWithin' => 21600,
    //         // 'cost' => 12,
    //         // 'admins' => ['admin']
    //     ),
    // ),
);