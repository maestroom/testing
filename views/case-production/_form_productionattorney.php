<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Typeahead;
use kartik\widgets\TypeaheadBasic;
use app\models\Options;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */
/* @var $form yii\widgets\ActiveForm */

if (isset($model->prod_agencies) && !in_array(date('Y', strtotime($model->prod_agencies)), array('1970', '-0001'))) {
	$model->prod_agencies =(new Options)->ConvertOneTzToAnotherTz($model->prod_agencies, 'UTC', $_SESSION['usrTZ'],'date');
	//$statusImg =date('m/d/Y', strtotime($status));
}
if (isset($model->prod_access_req) &&  !in_array(date('Y', strtotime($model->prod_access_req)), array('1970', '-0001'))) {
	$model->prod_access_req =(new Options)->ConvertOneTzToAnotherTz($model->prod_access_req, 'UTC', $_SESSION['usrTZ'],'date');
	// $statusImg =date('m/d/Y', strtotime($status));
}
?>
<div class="evidence-production-form">
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return true'],]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours">
		<?php 
		$misc1Inputconfig=['maxlength'=>$model_field_length['prod_misc1']];
		$misc2Inputconfig=['maxlength'=>$model_field_length['prod_misc2']];
		$attorney_notesInputconfig=['rows' => '6'];
		$cover_let_linkInputconfig=['maxlength'=>$model_field_length['cover_let_link']];
		$prod_discloseInputconfig=['maxlength'=>$model_field_length['prod_disclose']];
		$prod_agenciesInputconfig=['id'=>'prod_agencies','maxlength'=>'10','readonly'=>'readonly','placeholder'=>'Select Date'];
		$prod_agencies_reqInputconfig=['id'=>'prod_access_req','maxlength'=>'10','readonly'=>'readonly','placeholder'=>'Select Date'];
		if($model->isAttributeRequired('attorney_notes')) 
			$attorney_notesInputconfig=['aria-required'=>"true",'rows'=>'6'];
		if($model->isAttributeRequired('cover_let_link')) 
			$cover_let_linkInputconfig=['aria-required'=>"true",'maxlength'=>$model_field_length['cover_let_link']];
		if($model->isAttributeRequired('prod_disclose')) 
			$prod_discloseInputconfig=['aria-required'=>"true",'maxlength'=>$model_field_length['prod_disclose']];		
		if($model->isAttributeRequired('prod_agencies')) 
			$prod_agenciesInputconfig=['aria-required'=>"true",'id'=>'prod_agencies','maxlength'=>'10','readonly'=>'readonly','placeholder'=>'Select Date'];
		if($model->isAttributeRequired('prod_access_req')) 
			$prod_agencies_reqInputconfig=['aria-required'=>"true",'id'=>'prod_access_req','maxlength'=>'10','readonly'=>'readonly','placeholder'=>'Select Date'];
		if($model->isAttributeRequired('prod_misc1')) 
			$misc1Inputconfig=['aria-required'=>"true",'maxlength'=>$model_field_length['prod_misc1']];
		if($model->isAttributeRequired('prod_misc2')) 
			$misc2Inputconfig=['aria-required'=>"true",'maxlength'=>$model_field_length['prod_misc2']];
		?>
		<?= $form->field($model, 'attorney_notes',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea($attorney_notesInputconfig); ?>           
        <?= $form->field($model, 'cover_let_link',['template' => "<div class='row input-field'><div class='col-md-3'><label class='form_label' for='evidenceproduction-cover_let_link'>Cover Letter UNC Link</label></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput($cover_let_linkInputconfig); ?>
        <?= $form->field($model, 'prod_disclose',['template' => "<div class='row input-field'><div class='col-md-3'><label class='form_label' for='evidenceproduction-prod_disclose'>Produced in Initial Disclosures</label></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput($prod_discloseInputconfig); ?>
        <?= $form->field($model, 'prod_agencies',['template' => "<div class='row input-field'><div class='col-md-3'><label class='form_label' for='prod_agencies'>Produced to Other Agencies</label></div><div class='col-md-7 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput($prod_agenciesInputconfig); ?>            
        <?= $form->field($model, 'prod_access_req',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput($prod_agencies_reqInputconfig); ?>            
        <?= $form->field($model, 'prod_misc1',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput($misc1Inputconfig); ?>
        <?= $form->field($model, 'prod_misc2',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput($misc2Inputconfig); ?>
    </div>
    </fieldset>
    <?php ActiveForm::end(); ?>
</div>
<script>
	/* change flag event */
	$('input').bind("input", function(){ 
		$('#EvidenceProduction #is_change_form').val('1'); // change flag to 1
		$("#EvidenceProduction #is_change_form_main").val('1'); // change flag to 1
	});
	$('textarea').bind("input",function(){
		$('#EvidenceProduction #is_change_form').val('1'); // change flag to 1
		$("#EvidenceProduction #is_change_form_main").val('1'); // change flag to 1
	});
	$('document').ready(function(){
		$("#active_form_name").val('EvidenceProduction'); // change flag to 1
	});
</script>
