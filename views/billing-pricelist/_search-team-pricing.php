<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\SearchPricing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pricing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'team_id') ?>

    <?= $form->field($model, 'pricing_type') ?>

    <?= $form->field($model, 'price_point') ?>

    <?= $form->field($model, 'utbms_code_id') ?>

    <?php // echo $form->field($model, 'unit_price_id') ?>

    <?php // echo $form->field($model, 'pricing_range') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'cust_desc_template') ?>

    <?php // echo $form->field($model, 'is_custom') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'modified') ?>

    <?php // echo $form->field($model, 'modified_by') ?>

    <?php // echo $form->field($model, 'accum_cost') ?>

    <?php // echo $form->field($model, 'remove') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
