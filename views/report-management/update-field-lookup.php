<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldType */

$this->title = 'Edit Field Lookup';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-field-lookup']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><?= Html::encode($this->title) ?></div>
<div id='reportform_div'>
    <?= $this->render('_form-lookup', [
    'model' => $model,
    'current_table'=>$current_table,
    'filter_table_data'=>$filter_table_data,
    'lookup_table_data'=>$lookup_table_data
    ]) ?>
</div>
