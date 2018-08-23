<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */

$this->title = 'Update Calculation Stored Procedure';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-calculation-sp']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>
    <?= $this->render('_form-calculation-sp', [
    'model' => $model,
    'tables'=>$tables,
    'tableList'=>$tableList,
    'spparams'=>$spparams
	]) ?>
</div>
