<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsReportFormat */
/* @var $form yii\widgets\ActiveForm */

$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
<?= $form->field($model, 'report_format', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['report_format']]); ?>    	
    </div>	
</fieldset>
<div class="button-set text-right">
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportFormatCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->report_format . '","report-format");']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary','onclick'=>'SubmitAjaxForm("'.$model->formName().'",this,"loadReportFormat()","reportform_div");']) ?>
</div>    
<?php ActiveForm::end(); ?>
<script>
	/* change event input */
	$('input').bind('input', function(){
		$('#ReportsReportFormat #is_change_form').val('1');
		$('#ReportsReportFormat #is_change_form_main').val('1');
	});
    $(document).ready(function () {
		$('document').ready(function(){ $("#active_form_name").val('ReportsReportFormat'); });
        $('#<?= $model->formName() ?>').submit(function () {
		    SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportFormat()", "reportform_div");            
        });
    });
</script>
<noscript></noscript>
