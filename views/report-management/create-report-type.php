<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */

$this->title = 'Add Report Type';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-report-type']];
$this->params['breadcrumbs'][] = $this->title;  
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>    
    <?= $this->render('_form-report-type', [
        'model' => $model,
        'model_field_length'=>$model_field_length
	]) ?>
</div>
