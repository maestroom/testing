<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;


class ReportLeftPanel extends Widget{
	public $active='';
	public function init(){
		parent::init();
	}
	public function run()
	{
		return $this->render('reportleftpanel',['active'=>$this->active]);
	}
	
}
