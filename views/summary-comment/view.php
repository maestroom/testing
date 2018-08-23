<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SummaryComment */

$this->title = $model->Id;
$this->params['breadcrumbs'][] = ['label' => 'Summary Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="summary-comment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'parent_id',
            'case_id',
            'team_id',
            'team_loc',
            'comment:ntext',
            'created',
            'created_by',
            'modified',
            'modified_by',
        ],
    ]) ?>

</div>
