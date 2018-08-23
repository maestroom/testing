<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\ReportsChartFormatDisplayBy */

$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
        <?= $form->field($model, 'chart_display_by', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['chart_display_by']]); ?>    	        
        <div class='row input-field'>
            <div class="form-group clearfix required">
                    <div class='col-md-3'>
                        <label class='form_label required'>
                            Chart Formats 
                        </label>
                    </div>
                    <div class='col-md-7'>
                            <?php $displayby_id = $model->isNewRecord?0:$model->id; ?>
                            <?= Html::Button('Add Chart Formats', ['title' => 'Associate Chart Formats to this Chart Display By','class' => 'btn btn-primary', 'id' => 'chart-format-list', 'onClick' => 'getallchartformats('.$displayby_id.');']) ?>
                    </div>
            </div>
        </div>
        <div class="row input-field">
            <div class="form-group clearfix">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                            <!-- table stripped -->
                                    <table class="table table-striped sm-table-report" id="form-fieldtype-report" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <thead>
                                                    <tr>
                                                            <th title="Associated Chart Format"><a href="javascript:void(0);" class="tag-header-black" title="Associated Chart Format"><strong>Associated Chart Format</strong></a></th>
                                                            <th title="Action"><a href="javascript:void(0);" class="tag-header-black" title="Action"><strong>Action</strong></a></th>
                                                    </tr>	
                                            </thead>
                                            <tbody>	
                                                    <?php                                                   
                                                    if(isset($chartdisplayList) && !empty($chartdisplayList)){
                                                        ?>
                                                            <?php foreach($chartdisplayList as $single){ ?>
                                                                    <tr class="chart_format_<?php echo $single['id']; ?>">
                                                                            <input type="hidden" name="chart_format[]" class="report_chart_format" value="<?php echo $single['id']; ?>" />
                                                                            <td><?php echo $single['chart_format']; ?></td>
                                                                            <td><a href="javascript:void(0);" onClick="remove_dialog_single_data('ReportsChartFormatDisplayBy','chart_format','<?php echo $single['id']; ?>');" aria-label="Remove"><em class="fa fa-close text-primary" title="Delete"></em></a></td>
                                                                    </tr>
                                                            <?php }?>
                                                    <?php } ?>    
                                                   <input type="hidden" value="<?=$chart_ids?>" class="total_chart_ids" >                                                                     
                                            </tbody>
                                    </table>
                            <!-- End table -->
                    <div id="report-fieldtypes" class="has-error help-block"></div>
                    </div>
                    
            </div>
        </div>
    </div>    
</fieldset>
<div class="button-set text-right">
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportChartDisplayByCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->chart_display_by . '","chart-display-by");']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary','onclick'=>'savechart_displayby("'.$model->formName().'");']) ?>
</div>   
<div class="dialog" id="availabl-chart-format" title="Available Chart Formats"></div>
<?php ActiveForm::end(); ?>
<script>
	/* Reports chart format */
	jQuery('input').bind('input', function(){
		$('#ReportsChartFormatDisplayBy #is_change_form').val('1');
		$('#ReportsChartFormatDisplayBy #is_change_form_main').val('1');
	});
	jQuery(':checkbox').change(function(event){
		$('#ReportsChartFormatDisplayBy #is_change_form').val('1');
		$('#ReportsChartFormatDisplayBy #is_change_form_main').val('1');
	});
	/* End */
    jQuery(document).ready(function () {
		$('#active_form_name').val('ReportsChartFormatDisplayBy'); // form name
        $('#<?= $model->formName() ?>').submit(function (e) {
            savechart_displayby('<?= $model->formName() ?>');
        });
    });
    function savechart_displayby(form_id)
    {
        if($('#reportschartformatdisplayby-chart_display_by').val()==''){
                $("#reportschartformatdisplayby-chart_display_by").next().html('Please Enter Chart Display.');
                $("#reportschartformatdisplayby-chart_display_by").parent().parent().parent().addClass('has-error');
                return false;
        }                  
        if($('.report_chart_format').length==0 || $('.report_chart_format').length==''){             
                $("#report-fieldtypes").html('Please Add Chart Format.');                    
                $("#report-fieldtypes").parent().parent().parent().addClass('has-error');
                return false;
        }
        SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportChartDisplayBy()", "reportform_div");
    }
</script>
<noscript></noscript>
