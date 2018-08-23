<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldOperator */

$this->title = 'Update Chart Display By';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-chart-display-by']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>
    <?= $this->render('_form-chart-display-by', [
    'model' => $model ,
    'chart_ids'=>$chart_ids,
    'chartdisplayList'=>$chartdisplayList,
    'model_field_length'=>$model_field_length
	]) ?>
</div>
