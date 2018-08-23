<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\DataType */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'data_type',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$data_type_length['data_type']])->label($model->getAttributeLabel('data_type'), ['class'=>'form_label']); ?>
    </div>	
</fieldset>
<div class="button-set text-right">
    <?= Html::button('Cancel', ['title'=> 'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("MediaDataType");']) ?>
    <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"MediaDataType");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
	$('input').bind('input', function(){
		$('#DataType #is_change_form').val('1'); 
		$('#is_change_form_main').val('1');
	}); 
</script>
