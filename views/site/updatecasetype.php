<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CaseType */

$this->title = 'Edit Case Type: '.$model->case_type_name;
$this->params['breadcrumbs'][] = ['label' => 'Case Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
    <?= $this->render('_form', [
        'model' => $model,
        'case_type_length' =>$case_type_length
    ]) ?>
