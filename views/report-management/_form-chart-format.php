<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsChartFormat */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
$axisList = ['x'=>'X Axis','Y'=>'Y Axis'];
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
<?= $form->field($model, 'chart_format', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['chart_format']]); ?>    	
        <div class="form-group">
            <div class="row input-field">
                <div class="col-md-3">
                    <label class="form_label required">
                        Chart Format Axis 
                    </label>
                </div>
                <div class="col-md-9">
                <?php
                    $xchecked = $ychecked = $lchecked  = '';
                    if(isset($model->chart_axis) && $model->chart_axis != '') {
                        $chart_axis = explode(',',$model->chart_axis);
                        if(in_array("x", $chart_axis)) {
                            $xchecked = 'checked';
                        }
                        if(in_array("y", $chart_axis)) {
                            $ychecked = 'checked';
                        }
                        if(in_array("l", $chart_axis)) {
                            $lchecked = 'checked';
                        }                        
                    }
                ?>  
                    <fieldset>
                    <div class="custom-full-width">
                        <legend class="sr-only">Chart Format Axis</legend>
                        <div class="pull-left col-md-12 clearfix">
                            <input type="checkbox" aria-label="X Axis" id="chart_format_x" class="fieldtype-report" name="axis[]" value="x" <?=$xchecked;?>/><label for="chart_format_x" class="form_label">X Axis</label>                     
                        </div>
                        <div class="pull-left col-md-12 clearfix">
                            <input type="checkbox" id="chart_format_Y" aria-label="Y Axis" class="fieldtype-report" name="axis[]" value="y" <?=$ychecked;?> /><label for="chart_format_Y" class="form_label" >Y Axis</label>                     
                        </div>  
                        <div class="pull-left col-md-12 clearfix">
                            <input type="checkbox" id="chart_format_L" aria-label="Legend" class="fieldtype-report" name="axis[]" value="l" <?=$lchecked;?> /><label for="chart_format_L" class="form_label" >Legend</label>                     
                        </div>  
                    </div>
                    </fieldset>
                </div>   
            </div>
        </div>
    </div>	
</fieldset>
<div class="button-set text-right">
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportChartFormatCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->chart_format . '","chart-format");']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary submit-btn-handler','onclick'=>'SubmitAjaxForm("'.$model->formName().'",this,"loadReportChartFormat()","reportform_div");']) ?>
</div>    
<?php ActiveForm::end(); ?>
<script>
	/* Reports chart format */
	jQuery('input').bind('input', function(){
		$('#ReportsChartFormat #is_change_form').val('1');
		$('#ReportsChartFormat #is_change_form_main').val('1');
	});
	jQuery(':checkbox').change(function(){
		$('#ReportsChartFormat #is_change_form').val('1');
		$('#ReportsChartFormat #is_change_form_main').val('1');
	});
	/* End */
    jQuery(document).ready(function () {
		$('#active_form_name').val('ReportsChartFormat'); // form name
        $('#<?= $model->formName() ?>').submit(function () {            
            SubmitAjaxForm("<?= $model->formName() ?>",$(".submit-btn-handler"), "loadReportChartFormat()", "reportform_div");            
        });
        $('input').customInput();        
    });
</script>
<noscript></noscript>
<style>
.label_axis{margin-top: 9px;}
</style>
