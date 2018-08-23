<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;


class MyTeamLeftPanel extends Widget{
	public $active='';
	public function init(){
		parent::init();
	}
	public function run()
	{
		return $this->render('myteamleftpanel',['active'=>$this->active]);
	}
	
}
