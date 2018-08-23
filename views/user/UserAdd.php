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
<div id="form_div">
<?= 
	$this->render('_userForm', [
		'model' => $model,
		'role_details' => $role_details,
		'role_types'=>$role_types,
		'changePassAfter' => $changePassAfter,
		'teamLocation' => $teamLocation,
		'is_ad' => $is_ad,
	//	'mycases'=>$mycases,
	//	'myteams'=>$myteams,
		'security_features'=>$security_features,
		'model_field_length' => $model_field_length
	]) 
?>
</div>	

		
