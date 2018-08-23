<?php 

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'abcd123456',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
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
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info'],
                    'logFile' => '@runtime/logs/app.log'
                ],
            ],
        ],
    	'urlManager' => ['class' => 'yii\web\UrlManager',
    		// Disable index.php
    		'showScriptName' => true,
    		// Disable r= routes
    		'enablePrettyUrl' => false,
    		'rules' => array(
    				'system/emailtemplatedata/<id:\d+>' => 'system/emailtemplatedata',
    		),
    	],
        'db' => require(__DIR__ . '/db.php'),
		'ldap' => [
    		'class'=>'Edvlerblog\Ldap',
    		'options'=> [
    			'ad_port'      => 389,
    			'domain_controllers'    => array('ldap.forumsys.com'),
    			'account_suffix' =>  '@forumsys.com',
    			'base_dn' => "cn=read-only-admin,dc=example,dc=com",
    			'admin_username' => 'cn=read-only-admin,dc=example,dc=com',
    			'admin_password' => 'password'
			]
    	]
	],
		'modules' => [
				'gridview' =>  [
						'class' => '\kartik\grid\Module'
						// enter optional module parameters below - only if you need to
						// use your own export download action or custom translation
						// message source
						// 'downloadAction' => 'gridview/export/download',
						// 'i18n' => []
				]
		],
		'as beforeRequest' => [
				'class' => 'yii\filters\AccessControl',
				'rules' => [
						[
								'allow' => true,
								'actions' => ['login'],
						],
						[
								'allow' => true,
								'roles' => ['@'],
						],
				],
				'denyCallback' => function () {
					return Yii::$app->response->redirect(['site/login']);
				},
				],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    	'allowedIPs' => ['*'],
    ];
}

return $config;
