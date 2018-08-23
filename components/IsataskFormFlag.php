<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;


class IsataskFormFlag extends Widget{
//	public $active='';
	public function init(){
		parent::init();
	}
	public function run()
	{
		return $this->render('formflag',[]); //'active' => $this->active
	}
}
