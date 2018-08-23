<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Industry */

$this->title = 'Edit Client Industry: '.$model->industry_name;
$this->params['breadcrumbs'][] = ['label' => 'Industries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?><div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id="form_div">
    <?= $this->render('_form', [
        'model' => $model,
        'industry_length' => $industry_length
    ]) ?>
</div>
