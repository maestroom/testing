<?php 
date_default_timezone_set('UTC');

//date_default_timezone_set('America/New_York');
//echo date('Y-m-d H:i A');
ini_set("display_errors","On");
error_reporting(E_ALL ^ E_NOTICE);

// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'dev');
$dbconfig = require(__DIR__ . '/config/db.php');
try {
    $conn = new PDO($dbconfig['dsn'], $dbconfig['username'], $dbconfig['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM tbl_settings where field='yii_debug'");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $data=$stmt->fetch();
    if(isset($data['id']) && $data['id']!== null) {
        if($data['fieldvalue'] == 1) {
            define('YII_DEBUG', true);
            define('YII_ENV', 'dev');
        } else {
            define('YII_DEBUG', false);
            define('YII_ENV', 'prod');
        }
    } else {
        defined('YII_ENV') or define('YII_ENV', 'prod');
        define('YII_DEBUG', false);
        
    }
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();die;
}

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/config/web.php');

(new yii\web\Application($config))->run();
