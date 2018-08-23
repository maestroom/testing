<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Role Edit';
$this->params['breadcrumbs'][] = ['label' => 'Role', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="sub-heading"><?= Html::encode($this->title) ?></div>
<div id="form_div"><?php echo $this->render('_userForm', [
		'model' => $model,
		'role_details' => $role_details,
		'role_types'=>$role_types,
		'changePassAfter' => $changePassAfter,
		'teamLocation' => $teamLocation,
		'password' => $password,
		'security_features'=>$security_features,
		'role_security'=>$role_security,
		'actLog_user'=>$actLog_user,
		'model_field_length' => $model_field_length
]) ?></div>

		
