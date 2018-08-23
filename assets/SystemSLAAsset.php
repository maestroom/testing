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
class SystemSLAAsset extends AppAsset
{
	public $sourcePath = '@webroot';
    public $js = [
    		'js/jQuery.customInput.js',
    		'js/datepicker.js',
    ];
    public $jsOptions = array(
    		'position' => \yii\web\View::POS_HEAD
    );
    public $css = ['css/datepicker.css'];
    public $depends = [
    		'yii\web\JqueryAsset',
    		'yii\web\YiiAsset',
    ];
}
