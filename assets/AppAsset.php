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
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/bootstrap.css',
    	//'css/bootstrap-style.css',
        'css/isatask.css',
    	'css/font-awesome.min.css',	
    	'css/jquery-ui.css',
    	'css/datatables.css',
    	'css/enhanced.css',
    	'css/datepicker.css',
    	'css/theme.default.css'
    ];
    public $js = [
    		//'js/jquery.js',
    		'js/jquery-ui.js',
    		'js/bootstrap.js',
			'js/jquery.validate.min.js',
    		'js/jQuery.customInput.js',
    		'js/common.js',
    		'js/administration.js',
    		'js/client.js',
            'js/case.js',
    		'js/users.js',
    		'js/bootstrap-filestyle.js',
    		'js/datepicker.js',
    		'js/jquery.slidereveal.min.js',
			'js/media.js',
			'js/jquery.form.min.js',
			'js/caseproduction.js',
            'js/reportmanagment.js',
    		'js/jquery.tablesorter.js',
    ];
    
	// public $cssOptions = ['noscript' => true];
   
    public $jsOptions = array(
    	'position' => \yii\web\View::POS_HEAD,
    );
    public $depends = [
    	'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
