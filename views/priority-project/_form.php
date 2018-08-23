<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\PriorityProject */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
<div class="create-form">
<?= $form->field($model, 'priority',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$pp_length['priority']])->label($model->getAttributeLabel('priority'), ['class'=>'form_label']); ?>
	<?php
    	if($model->isNewRecord == 'Add'){
    	   $maxPriorityProjectOrder = $maxPriorityProjectOrder;
    	}
	?>
<?= $form->field($model, 'project_priority_order',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$pp_length['project_priority_order'],'value'=>$maxPriorityProjectOrder])->label($model->getAttributeLabel('project_priority_order'), ['class'=>'form_label']); ?>
</div>
</fieldset>
<div class="button-set text-right">
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("ProjectPriority");']) ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitHook("'.$model->formName().'",this,"ProjectPriority","'.($model->isNewRecord ? 'add' : 'update').'","'.($model->isNewRecord ? '0' : $model->id).'");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>	
	$('input').bind('input', function(){
		$('#PriorityProject #is_change_form').val('1'); 
		$('#PriorityProject #is_change_form_main').val('1'); 
	});
	$('document').ready(function(){ $("#active_form_name").val('PriorityProject'); });       
</script>

