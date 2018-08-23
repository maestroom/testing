<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Role Edit';
$this->params['breadcrumbs'][] = ['label' => 'Role', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="form_div"><?= $this->render('_rolesecuritygrid', [
	'model' => $model,
	'security_features' => $security_features,
	'role_security' => $role_security
]) ?></div>

		