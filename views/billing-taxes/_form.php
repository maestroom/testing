<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TaxCode */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tax-code-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tax_class_id')->textInput() ?>

    <?= $form->field($model, 'tax_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tax_code_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tax_rate')->textInput() ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'client')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'modified')->textInput() ?>

    <?= $form->field($model, 'modified_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
