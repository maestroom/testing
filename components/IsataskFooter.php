<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;


class IsataskFooter extends Widget{
	public $active='';
	public function init(){
		parent::init();
	}
	public function run()
	{
		return $this->render('footer',['active'=>$this->active]);
	}
	
}
