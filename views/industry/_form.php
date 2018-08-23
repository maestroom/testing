<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\Industry */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'industry_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$industry_length['industry_name']])->label('Client Industry', ['class'=>'form_label']); ?>
    </div>	
</fieldset>
<div class="button-set text-right">
        <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("Industries");']) //'onclick'=>'Industries();' ?>
        <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','id'=>'submitIndustry','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"Industries");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
	$('input').bind('input', function(){
		$('#Industry #is_change_form').val('1'); 
		$('#Industry #is_change_form_main').val('1');
	}); 
	$('document').ready(function(){ $("#active_form_name").val('Industry'); });
</script>
