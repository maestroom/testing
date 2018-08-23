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
class SystemAsset extends AppAsset
{
	public $sourcePath = '@webroot';
    public $js = [
    		'js/autosize.min.js',
    		'js/datepicker.js',
    		'js/bootstrap-wysiwyg.js',
    		'js/jquery.hotkeys.js',
    		'js/prettify.js',
    		'js/form-builder/admin.formbuilder.js',
    		'js/common.js',
    ];
    public $css = [
    		'css/datepicker.css',
    		'css/prettify.css',
    		'css/font-awesome.css',
    		'css/index.css'
    ];
}
