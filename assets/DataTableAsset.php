<?php
/**
 * @copyright Copyright (c) 2014 Serhiy Vinichuk
 * @license MIT
 * @author Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 */

namespace app\assets;


use yii\web\AssetBundle;

class DataTableAsset extends AssetBundle
{
    public $fontAwesome = false;
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();
       	$this->js = ['datatable/js/jquery.dataTables.js'];
       	$this->css =['datatable/css/datatables.css','datatable/css/font-awesome.min.css'];
       
    }

} 