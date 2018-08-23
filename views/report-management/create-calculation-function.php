<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */

$this->title = 'Add Calculation Function';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-calculation-function']];
$this->params['breadcrumbs'][] = $this->title;  
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>    
    <?= $this->render('_form-calculation-function', [
    'model' => $model,
    'tableList'=>$tableList,
    'model_field_length'=>$model_field_length
   ]) ?>
</div>
