<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldType */

$this->title = 'Add Field Operator';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-field-operator']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>
    <?= $this->render('_form-operator', [
        'model' => $model,
        'model_field_length'=>$model_field_length
	]) ?>
</div>
