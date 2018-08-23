<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SummaryComment */

$this->title = 'Create Summary Comment';
$this->params['breadcrumbs'][] = ['label' => 'Summary Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summary-comment-create">
    <?= $this->render('_form', [
        'model' => $model,
        'flag' =>'reply'
    ]) ?>
</div>
