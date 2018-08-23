<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasks-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'client_case_id') ?>

    <?= $form->field($model, 'sales_user_id') ?>

    <?= $form->field($model, 'task_status') ?>

    <?php // echo $form->field($model, 'task_complete_date') ?>

    <?php // echo $form->field($model, 'task_closed') ?>

    <?php // echo $form->field($model, 'task_cancel') ?>

    <?php // echo $form->field($model, 'task_cancel_reason') ?>

    <?php // echo $form->field($model, 'team_priority') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'modified') ?>

    <?php // echo $form->field($model, 'modified_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
