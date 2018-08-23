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
class CustomInputAsset extends AppAsset
{
	public $sourcePath = '@webroot';
    public $js = [
    		'js/jQuery.customInput.js',
    		
    ];
    public $jsOptions = array(
    		'position' => \yii\web\View::POS_HEAD
    );
    public $depends = [
    		'yii\web\JqueryAsset',
    ];
}
