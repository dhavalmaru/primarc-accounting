<?php

'components' => array(
    'currency_formator' => array(
        'class' => 'ext.yii-extension-INRCurrencyFormator.INRCurrencyFormator',
        'params' => array(
            'postfix'  => 'only',
            'currency' => '₹'
        )
    ),
    'request' => array(
            'baseUrl' => 'http://localhost:8081/primarc-pecan/web/'
    )
);