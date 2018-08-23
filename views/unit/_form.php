<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\Unit */
/* @var $form yii\widgets\ActiveForm */
$model->default_unit = $model->isNewRecord?0:$model->default_unit;
?>

<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">	
		<?= $form->field($model,'default_unit')->hiddenInput()->label(false); ?>
    	<?= $form->field($model, 'unit_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$unit_length['unit_name']])->label($model->getAttributeLabel('unit_name'), ['class'=>'form_label']); ?>
    </div>	
</fieldset>
<div class=" button-set text-right">
	<?= Html::button('Cancel', ['title'=>'Cancel', 'class' => 'btn btn-primary', 'onclick'=>'SelectManageDropdown("MediaDataUnits");']) ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"MediaDataUnits");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>	
	/* input change */
	$('input').bind('input', function(){
		$('#Unit #is_change_form').val('1'); 
		$('#Unit #is_change_form_main').val('1');
	}); 
	$('document').ready(function(){ $("#active_form_name").val('Unit'); });
</script>
