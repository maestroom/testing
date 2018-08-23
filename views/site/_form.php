<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\CaseType */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id'=>'frm-casetype']); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	<fieldset class="one-cols-fieldset">
    <div class="create-form">
		<?= $form->field($model, 'case_type_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength' => $case_type_length['case_type_name']])->label($model->getAttributeLabel('case_type_name'), ['class'=>'form_label']); ?>
	</div>
</fieldset>
<div class="button-set text-right">
	<?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("CaseType");']) ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord?'Add':'Update', 'class' => 'btn btn-primary','onclick'=>$model->isNewRecord ? 'AddProcessCaseType();' : 'UpdateProcessCaseType();']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>	
	$('input').bind('input', function(){
		$('#frm-casetype #is_change_form').val('1'); 
		$('#frm-casetype #is_change_form_main').val('1');
	});
	$('document').ready(function(){ $('active_form_name').val('frm-casetype'); });  // active form name
</script>
