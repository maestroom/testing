<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\TaxCodeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tax-code-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tax_class_id') ?>

    <?= $form->field($model, 'tax_code') ?>

    <?= $form->field($model, 'tax_code_desc') ?>

    <?= $form->field($model, 'tax_rate') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'client') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'modified') ?>

    <?php // echo $form->field($model, 'modified_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
