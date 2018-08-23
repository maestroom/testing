<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectRequestType */

$this->title = 'Edit Project Request Type: '.$model->request_type;
$this->params['breadcrumbs'][] = ['label' => 'Project Request Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
		'prt_length' => $prt_length
    ]) ?></div>
