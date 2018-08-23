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
class HighchartAsset extends AppAsset
{
    public $sourcePath = '@webroot';
    
    public $js = [
    	'js/highcharts/GraphUp_jquery.js',
    	'js/highchart5/code/highcharts.js',
    	'js/highchart5/code/highcharts-more.js',
    	'js/highcharts/jspdf.js',
    	'js/highcharts/FileSaver.js',
    	'js/highcharts/rgbcolor.js',
    	'js/highcharts/canvg.js',
    	'js/highchart5/code/modules/data.js',
    	'js/highchart5/code/modules/drilldown.js',    
    	'js/highchart5/code/modules/exporting.js',
    	'js/highcharts/highchart-common.js',
    	'js/highchart5/code/highcharts-3d.js',
    	'js/highcharts/pattern-fill.js'
    ];

}
