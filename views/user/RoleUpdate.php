<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Role Edit';
$this->params['breadcrumbs'][] = ['label' => 'Role', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="form_div"><?= $this->render('_roleForm', [
		'model' => $model,
		'security_features' => $security_features,
		'model_field_length' => $model_field_length
	]) ?>
</div>

		
 
			
