<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use app\assets\AppAsset;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SystemCustomWordingAsset extends AppAsset
{
	public $sourcePath = '@webroot';
    public $js = [
			
    		/*'js/bootstrap-wysiwyg.min.js',
    		'js/jquery.hotkeys.js',
    		'js/prettify.js',*/
			'js/jquery-te-1.4.0.min.js',
    		'js/jQuery.customInput.js',
    ];
    public $css = [
			'css/jquery-te-1.4.0.css',
    		/*'css/prettify.css',
    		'css/font-awesome.css',
    		'css/index.css'*/
    ];
}
