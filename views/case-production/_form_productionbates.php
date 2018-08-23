<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Typeahead;
use kartik\widgets\TypeaheadBasic;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="evidence-production-form">
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return true'],]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours">
        <?= $form->field($model, 'prod_bbates',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
        <?= $form->field($model, 'prod_ebates',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
        <?= $form->field($model, 'prod_vol',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
        <?= $form->field($model, 'prod_date_loaded',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['id'=>'prod_date_loaded','maxlength'=>'10','readonly'=>'readonly']); ?>            
   </div>
 </fieldset>
    <?php ActiveForm::end(); ?>
</div>
<script>
	/* Change Flag */
	$('input').bind('input', function(){
		$("#EvidenceProductionBates #is_change_form").val('1'); // change flag to 1
		$("#EvidenceProductionBates #is_change_form_main").val('1'); // change flag to 1
	});
	$('document').ready(function(){
		$("#active_form_name").val('EvidenceProductionBates'); // change flag to 1
	});
</script>
