<?php
use kartik\mpdf\Pdf;
use yii\web\Request;
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
	'name'=>'IS-A-TASK',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','HttpHeaders'],

	'components' => [
		'HttpHeaders'=>[
       	 'class'=>'app\components\HttpHeaders'
     	],
    	'pdf' => [
			'class' => Pdf::classname(),
			'format' => Pdf::FORMAT_A4,
			'orientation' => Pdf::ORIENT_PORTRAIT,
			'destination' => Pdf::DEST_BROWSER,
			'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
			// refer settings section for all configuration options
    	 ],
    	'formatter' => [
			'class' => 'yii\i18n\Formatter',
			'dateFormat' => 'm-d-Y',
			'datetimeFormat' => 'm-d-Y H:i:s',
			'timeFormat' => 'H:i:s', 
    	],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'abcd123456',
			'enableCookieValidation'=>true,
			'csrfCookie' => [
				'secure' => true,
			],
        ],
        'cache' => [
            //'class' => 'yii\caching\FileCache',
  	   'class' => 'yii\caching\ApcCache',
            //'class' => 'yii\caching\WinCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'authTimeout' => 31557600,
			'identityCookie' => [         
                    'name' => '_'.dirname(__DIR__), // unique for frontend         
                    'path'=>'/'.dirname(__DIR__),  // correct path for the frontend app.     
					'secure' => true
            ],
        ],
		'session' => [
                'name' => '_'.dirname(__DIR__).'SessionId', // unique for frontend
                'savePath' => __DIR__ . '/../runtime/session', // a temporary folder on frontend
				'cookieParams' => [
						'secure' => true,
				],
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
                    //'levels' => ['error', 'warning','info'],
                    'levels' => ['error'],
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
		'dynagrid'=> [
			'class'=>'\kartik\dynagrid\Module',
			'defaultPageSize'=>25,
			'minPageSize'=>25,
			'maxPageSize'=>25,
			 // other settings (refer to documentation)
			'dbSettings'=> [
				'tableName'=> 'tbl_dynagrid',
				'idAttr' => 'id',
				'filterAttr'=> 'filter_id',
				'sortAttr'=>'sort_id',
				'dataAttr'=>'data'
			],
			'dbSettingsDtl' => [
				'tableName' => 'tbl_dynagrid_dtl',
				'idAttr' => 'id',
				'categoryAttr' => 'category',
				'nameAttr' => 'name',
				'dataAttr' => 'data',
				'dynaGridId' => 'dynagrid_id'
			],
			
			// other module settings
		],
		'gridview' =>  [
			'class' => '\kartik\grid\Module'
			// enter optional module parameters below - only if you need to
			// use your own export download action or custom translation
			// message source
			// 'downloadAction' => 'gridview/export/download',
			// 'i18n' => []
		],
       /*'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            'treeStructure'=> [
                'treeAttribute' => 'p_id',
                'leftAttribute' => 'reference_id',
                'rightAttribute' => 'reference_id',
                'depthAttribute' => 'p_id',
			],
           'dataStructure'=>[
                'keyAttribute' => 'id',
                'nameAttribute' => 'fname',
                'iconAttribute' => 'doc_type',
                'iconTypeAttribute' => 'doc_type'
           ],
            // other module settings, refer detailed documentation
        ]*/
	],
	'as beforeRequest' => [
		'class' => 'yii\filters\AccessControl',
		'rules' => [
			[
				'allow' => true,
				'actions' => ['login','change-force-password','error','chk-passwords','cronemail','cronpastdueemail','cronpastdue','zip-blob'],
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
if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)) {
	$config['components']['request']['csrfCookie'] = ['secure' => true];
	$config['components']['user']['identityCookie']['secure'] = true;
	$config['components']['session']['cookieParams']['secure'] = true;
}
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    	'allowedIPs' => ['*'],
    ];
}

return $config;
