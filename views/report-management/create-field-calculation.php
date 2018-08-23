<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */

$this->title = 'Add Field Calculation';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-field-calculation']];
$this->params['breadcrumbs'][] = $this->title;  
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>    
    <?= $this->render('_form-field-calculation', [
        'model' => $model,
        'tableList'=>$tableList,
		'functions'=>$functions,
        'model_field_length'=>$model_field_length
	]) ?>
</div>
