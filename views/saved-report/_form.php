<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsUserSaved */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reports-user-saved-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'custom_report_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'custom_report_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'report_save_to')->textInput() ?>

    <?= $form->field($model, 'share_report_by')->textInput() ?>

    <?= $form->field($model, 'report_type_id')->textInput() ?>

    <?= $form->field($model, 'report_format_id')->textInput() ?>

    <?= $form->field($model, 'chart_format_id')->textInput() ?>

    <?= $form->field($model, 'date_type_field_id')->textInput() ?>

    <?= $form->field($model, 'date_range')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'modified')->textInput() ?>

    <?= $form->field($model, 'modified_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
