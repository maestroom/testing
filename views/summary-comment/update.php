<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SummaryComment */

$this->title = 'Update Summary Comment: ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => 'Summary Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="summary-comment-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
