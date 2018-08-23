<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TeamlocationMaster */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<input type="hidden" id="is_change_form" name="is_change_form" class="is_change_form" value="0" />
<input type="hidden" id="is_change_form_main" name="is_change_form_main" class="is_change_form_main" value="0" />
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'team_location_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$tlm_length['team_location_name']])->label($model->getAttributeLabel('team_location_name'), ['class'=>'form_label']); ?>
    </div>
</fieldset>
<div class=" button-set text-right">
 <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("TeamLocations");']) ?>
 <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"TeamLocations");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
	$('input').bind('input', function(){
		$('#TeamlocationMaster #is_change_form').val('1'); 
		$('#TeamlocationMaster #is_change_form_main').val('1');
	});
	$('document').ready(function(){ $("#active_form_name").val('TeamlocationMaster'); });
</script>
