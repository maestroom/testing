<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\EvidenceStoredLoc */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    
    <div class="create-form">
	    <?= $form->field($model, 'stored_loc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=> $esl_length['stored_loc']])->label('Stored Location', ['class'=>'form_label']); ?>
    </div>
</fieldset>
<div class=" button-set text-right">
    <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("MediaLocation");']) ?>
    <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"MediaLocation");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>	
	$('input').bind('input', function(){
		$('#EvidenceStoredLoc #is_change_form').val('1'); 
		$('#EvidenceStoredLoc #is_change_form_main').val('1');
	}); 
	$('select').on('change', function() {
		$('#EvidenceStoredLoc #is_change_form').val('1');
		$('#EvidenceStoredLoc #is_change_form_main').val('1'); 
	});
	$('document').ready(function(){ $("#active_form_name").val('EvidenceStoredLoc'); });
</script>
