<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TodoCats */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Todo Cats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todo-cats-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'todo_cat',
            'todo_desc',
            'notes',
            'stop',
            'remove',
        ],
    ]) ?>

</div>
