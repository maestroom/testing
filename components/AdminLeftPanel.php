<?php
namespace app\components;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\Session;
use app\models\Role;
use app\models\User;

class AdminLeftPanel extends Widget{
	public $active='';
	public function init(){
		parent::init();
	}
	public function run()
	{
		$roleId = Yii::$app->user->identity->role_id;
		$roleInfo = Role::findOne($roleId);
		$roleTypes = explode(',', $roleInfo->role_type);
		$User_Role = explode(',', $roleInfo->role_type);
		$has_access_4=0;
		$session = new Session;
        $session->open();
		if(!isset($session['has_access_4'])){
			$session['has_access_4']=(new User)->checkAccess(4);
		}
		$has_access_4=$session['has_access_4'];
		return $this->render('adminleftpanel',['active'=>$this->active, 'roleTypes' => $roleTypes, 'roleId' => $roleId, 'has_access_4' => $has_access_4]);
	}
	
}
