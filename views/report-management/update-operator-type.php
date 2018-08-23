<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldOperator */

$this->title = 'Update Field Operator';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-field-operator']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>
    <?= $this->render('_form-operator', [
    'model' => $model ,
    'fieldtypesList'=>$fieldtypesList,
    'fieldoperatorList'=>$fieldoperatorList,
    'model_field_length'=>$model_field_length
	]) ?>
</div>
