<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DataType */

$this->title = 'Edit Service Task - '.$model->service_task;
$this->params['breadcrumbs'][] = ['label' => 'Service Task', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_servicetaskform', [
        'model' => $model,
        'teamId'=>$teamId,
    	'teamLocation'=>$teamLocation,
    	'model_field_length' => $model_field_length
    ]) ?></div>

