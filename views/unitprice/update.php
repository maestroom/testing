<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */

$this->title = 'Edit Unit Price: '.$model->unit_price_name;
$this->params['breadcrumbs'][] = ['label' => 'Unit Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
         'up_length' => $up_length
    ]) ?></div>

