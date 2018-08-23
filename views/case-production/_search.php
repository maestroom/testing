<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\EvidenceProductionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="evidence-production-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'client_case_id') ?>

    <?= $form->field($model, 'staff_assigned') ?>

    <?= $form->field($model, 'prod_date') ?>

    <?php // echo $form->field($model, 'prod_rec_date') ?>

    <?php // echo $form->field($model, 'prod_party') ?>

    <?php // echo $form->field($model, 'production_desc') ?>

    <?php // echo $form->field($model, 'production_type') ?>

    <?php // echo $form->field($model, 'cover_let_link') ?>

    <?php // echo $form->field($model, 'prod_orig') ?>

    <?php // echo $form->field($model, 'prod_return') ?>

    <?php // echo $form->field($model, 'attorney_notes') ?>

    <?php // echo $form->field($model, 'prod_disclose') ?>

    <?php // echo $form->field($model, 'prod_agencies') ?>

    <?php // echo $form->field($model, 'prod_access_req') ?>

    <?php // echo $form->field($model, 'has_media') ?>

    <?php // echo $form->field($model, 'has_hold') ?>

    <?php // echo $form->field($model, 'has_projects') ?>

    <?php // echo $form->field($model, 'prod_misc1') ?>

    <?php // echo $form->field($model, 'prod_misc2') ?>

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
