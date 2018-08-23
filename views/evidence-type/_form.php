<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\EvidenceType */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    <?= $form->field($model, 'evidence_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$evidence_type_length['evidence_name']])->label('Media Type', ['class'=>'form_label']); ?>
    <?= $form->field($model, 'est_size',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$evidence_type_length['est_size']])->label($model->getAttributeLabel('est_size'), ['class'=>'form_label']); ?>
    <?php //$form->field($model, 'media_unit_id')->textInput() ?>
    <?= $form->field($model, 'media_unit_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['label'=>'Default Unit','class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $units,
    'options' => ['prompt' => 'Default Unit','nolabel'=>true],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>
    </div>
</fieldset>
<div class=" button-set text-right">
        <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'SelectManageDropdown("MediaType");']) ?>
        <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'ManageDropdownSubmitAjaxForm("'.$model->formName().'",this,"MediaType");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>	
	$('input').bind('input', function(){
		$('#EvidenceType #is_change_form').val('1'); 
		$('#EvidenceType #is_change_form_main').val('1');
	});
	$('select').on('change', function() {
		$('#EvidenceType #is_change_form').val('1');
		$('#EvidenceType #is_change_form_main').val('1'); 
	});
	$('document').ready(function(){ $("#active_form_name").val('EvidenceType'); });
</script>
