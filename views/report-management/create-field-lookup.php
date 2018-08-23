<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldType */

$this->title = 'Add Field Lookup';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-field-lookup']];
$this->params['breadcrumbs'][] = $this->title;
//echo "<prE>",print_r($current_table),"</prE>";
?>
<div class="sub-heading"><?= Html::encode($this->title) ?></div>
<div id='reportform_div'>
    <?= $this->render('_form-lookup', [
    'model' => $model,
    'current_table'=>$current_table
    ]) ?>
</div>
