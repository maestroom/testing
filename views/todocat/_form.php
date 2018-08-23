<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
\app\assets\CustomInputAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\TodoCats */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
<div class="create-form">
	<?= $form->field($model, 'todo_cat',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=> $tdc_length['todo_cat']])->label('ToDo Category', ['class'=>'form_label']) ?>
	<?= $form->field($model, 'todo_desc',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textArea(['rows'=>3,'maxlength'=> $tdc_length['todo_desc']])->label('ToDo Description', ['class'=>'form_label']) ?>
	<?= $form->field($model, 'notes',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textArea(['rows'=>3,'maxlength'=> $tdc_length['notes']])->label($model->getAttributeLabel('notes'), ['class'=>'form_label']) ?>
	<?= $form->field($model, 'stop',['template' => "<div class='row input-field'><div class='col-md-2' id='sla-stop-clock'>SLA Stop Clock</div><div class='col-md-10'>{input}<label for='todocats-stop'><span class='screenreader'>SLA Stop Clock</span>&nbsp;</label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(['label' => '','labelOptions'=>['class'=>'custom-full-width','aria-labelledby'=>'sla-stop-clock'],'title'=> 'SLA Stop Clock']); ?>
</div>
</fieldset>
<div class=" button-set text-right">
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("ToDoFollowupCategory");']) ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"ToDoFollowupCategory");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
$('input').customInput();
$('document').ready(function(){ $("#active_form_name").val('Todocats'); });
$('input').bind('input', function(){
	$('#Todocats #is_change_form').val('1'); 
	$('#Todocats #is_change_form_main').val('1');
});
$(':checkbox').change(function(){ 
	$('#Todocats #is_change_form').val('1'); 
	$('#Todocats #is_change_form_main').val('1');
});
$('textarea').bind('input', function(){ 
	$('#Todocats #is_change_form').val('1'); 
	$('#Todocats #is_change_form_main').val('1'); 
}); 
</script>
<noscript></noscript>
