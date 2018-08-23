<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\EvidenceEncryptType */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
<div class="create-form">
	<?= $form->field($model, 'encrypt',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$eet_length['encrypt']])->label('Encryption Type', ['class'=>'form_label']); ?>
</div>
</fieldset>	
<div class=" button-set text-right">
	<?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("MediaEncrypt");']) ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord?'Add':'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"MediaEncrypt");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>	
	$('input').bind('input', function(){
		$('#EvidenceEncryptType #is_change_form').val('1'); 
		$('#EvidenceEncryptType #is_change_form_main').val('1');
	}); 
	$('document').ready(function(){ $("#active_form_name").val('EvidenceEncryptType'); });
</script>
