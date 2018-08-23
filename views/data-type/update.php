<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DataType */

$this->title = 'Edit Data Type: '.$model->data_type;
$this->params['breadcrumbs'][] = ['label' => 'Data Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
        'data_type_length' =>$data_type_length
    ]) ?></div>

