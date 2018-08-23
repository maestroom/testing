<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
$addon = <<< HTML
<span class="input-group-addon">
    <em class="glyphicon glyphicon-calendar"></em>
</span>
HTML;
?>
<style>
.font13{
	font-size:13px!important;
}
.font14{
	font-size:14px!important;
}
</style>
<fieldset class="one-cols-fieldset-report overflow-auto panel-body">
	<div class="administration-form format_and_properties">
		<h6 class='font14'><b>Summary Statement</b></h6>
		<?php if(isset($modeReportsChartFormat->chart_format) && strtolower($modeReportsChartFormat->chart_format)=='column basic'){?>
		<?=$this->renderAjax('column_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
		<?php if(isset($modeReportsChartFormat->chart_format) && strtolower($modeReportsChartFormat->chart_format)=='column clustered'){?>
			<?=$this->renderAjax('columnclusteredstack_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
		<?php if(isset($modeReportsChartFormat->chart_format) && strtolower($modeReportsChartFormat->chart_format)=='bar basic'){?>
			<?=$this->renderAjax('bar_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
		<?php if(isset($modeReportsChartFormat->chart_format) && strtolower($modeReportsChartFormat->chart_format)=='line clustered'){?>
			<?=$this->renderAjax('lineclusteredstack_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
		<?php if(isset($modeReportsChartFormat->chart_format) && strtolower($modeReportsChartFormat->chart_format)=='bar clustered'){?>
			<?=$this->renderAjax('barclusteredstack_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
		<?php if(isset($modeReportsChartFormat->chart_format) && strtolower($modeReportsChartFormat->chart_format)=='line basic'){?>
			<?=$this->renderAjax('line_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
		<?php if(isset($modeReportsChartFormat->chart_format) && (strtolower($modeReportsChartFormat->chart_format)=='circle pie' || strtolower($modeReportsChartFormat->chart_format)=='circle donut')){?>
			<?=$this->renderAjax('pie_sammary',['model'=>$model,'post_data'=>$post_data,'field_disply_with_table'=>$field_disply_with_table,'reportTypeFields'=>$reportTypeFields]);?>
		<?php }?>
	</div>
	<div style="display:none">
		<select id="field_type">
			<?php if(!empty($fieldNameType)){ foreach($fieldNameType as $field=>$type){?>
			<option value="<?=$field?>"><?=$type?></option>
			<?php }}?>
		</select>
		<select id="field_val">
			<?php if(!empty($post_data['fieldval'])){ foreach($post_data['fieldval'] as $id=>$fname){?>
			<option value="<?=$id?>"><?=str_replace('.','_',$fname)?></option>
			<?php }}?>
		</select>
	</div>
</fieldset>
<div class="button-set text-right">
	<?= Html::button('Previous', ['title'=>'Previous','class' =>  'btn btn-primary','onclick'=>'$( "#tabs-step-2" ).hide(); $( "#tabs-step-3" ).hide();$( "#tabs-step-5" ).hide();$( "#tabs-step-4" ).show(); changeheader4();']) ?>
	<?php $allReports_url = Url::toRoute(['saved-report/index']); ?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
	<?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','onclick'=>'next_step_view();']) ?>
</div>
<script>
$(function(){
	if($('#old_type').val() == $('#current_type').val()){}else{
		if($('#y_function')){$('#y_function').val(null).change();}
		if($('#x_function')){$('#x_function').val(null).change();}
		if($('#y_function_display_by')){$('#y_function_display_by').val(null).change();}
		if($('#x_function_display_by')){$('#x_function_display_by').val(null).change();}
		if($('#y_data')){$('#y_data').val(null).change();}
		if($('#x_data')){$('#x_data').val(null).change();}
		if($('#x_data_display_by')){$('#x_data_display_by').val(null).change();}
		if($('#y_data_display_by')){$('#y_data_display_by').val(null).change();}
		if($('#series')){$('#series').val(null).change();}
		$('#old_type').val($('#current_type').val());
	}
});
function changeheader4(){
	$(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 4: Select Format & Properties'>Step 4: Select Format & Properties</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
}
function next_step_view(){
	var err=false;
	if($('#x_function').val()==""){
		$('#x_function').parent().parent().parent().addClass('has-error');
		err=true;
	}
	if($('#x_data').val()==""){
		$('#x_data').parent().parent().parent().addClass('has-error');
		err=true;
	}
	if($('#y_data').val()==""){
		$('#y_data').parent().parent().parent().addClass('has-error');
		err=true;
	}
	if($('#x_function_display_by').val()==""){
		$('#x_function_display_by').parent().parent().parent().addClass('has-error');
		err=true;
	}
	if($('#y_data_display_by').val()==""){
		$('#y_data_display_by').parent().parent().parent().addClass('has-error');
		err=true;
	}
	if(err==true){
		return false;
	}
	$('#x_function').parent().parent().parent().removeClass('has-error');
	$('#x_data').parent().parent().parent().removeClass('has-error');
	$('#y_data').parent().parent().parent().removeClass('has-error');
	$('#x_function_display_by').parent().parent().parent().removeClass('has-error');
	$('#y_data_display_by').parent().parent().parent().removeClass('has-error');
	
	$( "#tabs-step-1" ).hide();
	$( "#tabs-step-2" ).hide();
	$( "#tabs-step-3" ).hide();
	$( "#tabs-step-4" ).hide();
	$( "#tabs-step-5" ).hide();
	$( "#tabs-step-6" ).show(); 
	$(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 6: View Chart Report'>Step 6: View Chart Report</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
	var type=$('.chart_format:checked').val();
	var form = $('#report-type-format-dates').serialize();
	$.ajax({
			type: 'post',
			url:baseUrl+'saved-report/view-chart',
			data: form,
			beforeSend:function (data) {showLoader();},
			success:function(response){
				hideLoader();
				$("#form_div_step_6").html(response);
			},
			error :function(response){
				hideLoader();
				alert('There is an Error in the Report Query Execution. Click on the OK button to view the Error Details.');
				alert(response.responseText);
				return false;
				
			}
	});
}
</script>
<noscript></noscript>
