<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\DataType */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => 'frm-updatetempfield','enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'display',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$data_type_length['data_type']])->label('Display', ['class'=>'form_label']); ?>

    	<?= $form->field($model, 'origination',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$data_type_length['data_type']])->label($model->getAttributeLabel('origination'), ['class'=>'form_label']); ?>
    	
    	<?= $form->field($model, 'field_value',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$data_type_length['data_type']])->label($model->getAttributeLabel('field_value'), ['class'=>'form_label']); ?>

    	<?= $form->field($model, 'preview_display',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$data_type_length['data_type']])->label($model->getAttributeLabel('preview_display'), ['class'=>'form_label']); ?>
    		<input type="hidden" id="template_id" value="<?=$data->email_sort?>" />
    </div>	
</fieldset>
<?php ActiveForm::end();?>
<script>
$('input').bind('input', function(){
	$('#DataType #is_change_form').val('1'); 
	$('#is_change_form_main').val('1');
}); 
</script>
