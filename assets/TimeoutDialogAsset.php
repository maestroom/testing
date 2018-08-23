<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TimeoutDialogAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/timeout-dialog/timeout-dialog.css',
    ];
    public $js = [
			//'js/timeout-dialog/store.js',
    		//'js/timeout-dialog/timeout-dialog.js',
    		'js/timeout-dialog/timeout-dialog-cookie.js'
    		
    ];
    public $jsOptions = array(
    	'position' => \yii\web\View::POS_HEAD
	);
    public $depends = [
        'yii\web\YiiAsset',
       // 'yii\jui\JuiAsset',
    ];
}
