<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Unit */

$this->title = 'Edit Unit: '.$model->unit_name;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
<div id="form_div"><?= $this->render('_form', [
	'model' => $model,
	 'unit_length'=>$unit_length
]) ?></div>

