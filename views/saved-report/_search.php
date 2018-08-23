<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ReportsUserSavedSSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reports-user-saved-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'custom_report_name') ?>

    <?= $form->field($model, 'custom_report_description') ?>

    <?= $form->field($model, 'report_save_to') ?>

    <?= $form->field($model, 'share_report_by') ?>

    <?php // echo $form->field($model, 'report_type_id') ?>

    <?php // echo $form->field($model, 'report_format_id') ?>

    <?php // echo $form->field($model, 'chart_format_id') ?>

    <?php // echo $form->field($model, 'date_type_field_id') ?>

    <?php // echo $form->field($model, 'date_range') ?>

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
