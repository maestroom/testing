<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\daterange\DateRangePicker;

$roleId=Yii::$app->user->identity->role_id;

/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */

$addon = <<< HTML
<span class="input-group-addon">
    <em class="glyphicon glyphicon-calendar" title="Select Date"></em>
</span>
HTML;
?>

<fieldset class="one-cols-fieldset-report">
	<div class="col-sm-12 add-custom-report-stap-one">
		<?= $form->field($model, 'report_type_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
	    	'data' => $modeReportType,
	    	'options' => ['nolabel' => true, 'prompt' => 'Report Type', 'id' => 'reportsusersaved-report_type_id','aria-label'=>'Report Type','title' => 'Report Type','placeholder'=>'Report Type','aria-required' => 'true'],
	    ])->label('Report Type');//->textInput(); ?>
		<input type="hidden" name="report_format_id" id="reportsusersaved-report_format_id" value="T" />
		<?php /*= $form->field($model, 'report_format_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => $modeReportFormat,
	    	'options' => ['prompt' => '', 'id' => 'reportsusersaved-report_format_id'],
	    ])->label('Report Format');*/ ?>
		<?php /*== $form->field($model, 'chart_format_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => $modeReportsChartFormat,
	    	'options' => ['prompt' => '', 'id' => 'reportsusersaved-chart_format_id'],
	    ])->label('Chart Format');*/ ?>
		
</fieldset>
<div class=" button-set text-right">
	<input type="hidden" id="prev_reportsusersaved-chart_format_id" value="" />
	<input type="hidden" id="prev_reportsusersaved-report_type_id" value="<?php if(!$model->isNewRecord) { echo $model->report_type_id;}?>" />
	<input type="hidden" id="prev_reportsusersaved-report_format_id" value="<?php if(!$model->isNewRecord) { echo $model->report_format_id;}?>" />
	<input type="hidden" id="prev_reportsusersaved-chart_format_text" value="" />
	<input type="hidden" id="prev_reportsusersaved-byFilter" value="" />
	<input type="hidden" id="saved-report-flag" value="<?=$flag?>" />
	<input type="hidden" id="saved-report-id" value="<?=$id?>" />
	<?php /*= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'']) */?>
	<?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep1','onclick'=>'validateReportSteps(1);']) ?>
</div>
<script>
$('input').customInput();
$('.field-reportsusersaved-chart_format_id').hide();
$('#reportsusersaved-report_format_id').change(function(){
	if($(this).val() == '2')
		$('.field-reportsusersaved-chart_format_id').show();
	else
		$('.field-reportsusersaved-chart_format_id').hide();
});

$('#teamserviceListBox .myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')) {
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$('#teamserviceListBox .myheader a').click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
			//sorting text
			//console.log();
        });
    });
});

function validateReportSteps(step){
	$flag=false;
	var num = step-1;
	$('#reportsusersaved-flag').val('step'+num);
	var byfilter = [{'byClientCase':0},{'byTeamservice':0}];
	$.ajax({
		url:baseUrl+'custom-report/validate-steps&step='+step,
		type:'post',
		data:$("#report-type-format-dates").serialize()+'&flag='+'step'+num,
		success:function(response){
			if(response.length==0){
				/** get table & table fields details **/
				$.ajax({
					url:baseUrl+'custom-report/get-table-field-details&step='+step,
					type:'post',
					data:$("#report-type-format-dates").serialize()+'&flag='+'step'+num,
					success:function(resp){
						$('#avail_field_data').html(resp);
						var objselect2=$("#reportsusersaved-report_type_id option:selected").text();
						$('.sub-heading').html('<a href="javascript:void(0);" class="tag-header-black" title="Step 2: Select Fields, Filters & Sort Orders">Step 2: Select Fields, Filters & Sort Orders</a><div style="float: right">'+objselect2+'</div>');
						$('#tabs-step-1').hide(); $('#tabs-step-2').show();
					},complete:function(){
						
						var client_case_id_len = $('#client-list').find('input[type="checkbox"]:checked').length;
						var client_id_len = $('#client-case-list').find('input[type="checkbox"]:checked').length;
						if(client_case_id_len > 0 || client_id_len > 0){
							byfilter[0].byClientCase = 1;
						}
						var teamservice_len = $('.field-reportsusersavedfilterteamserviceloc-teamservice_id').find('.teamservice_chk:checked').length;
						if(teamservice_len > 0){
							byfilter[1].byTeamservice = 1;
						}
						
						var report_typeId = $('#reportsusersaved-report_format_id').val(); // report type (chart or tabular)
						var prev_report_type_id = $('#prev_reportsusersaved-report_type_id').val();
						var prev_reportsusersaved_report_format_id= $('#prev_reportsusersaved-report_format_id').val();
						if(prev_report_type_id!=$('#reportsusersaved-report_type_id').val()){
							$("#table_field_container").html(null);
							$("#filter_custom_values").html(null);
							$("#sorting_custom_values").html(null);
							$("#chart_custom_values").html(null);
							$("#chart_legend_values").val('');
						}
						if(prev_reportsusersaved_report_format_id!=$('#reportsusersaved-report_format_id').val()){
							$("#table_field_container").html(null);
							$("#filter_custom_values").html(null);
							$("#sorting_custom_values").html(null);
							$("#chart_custom_values").html(null);
							$("#chart_legend_values").val('');
						}
						var  prev_chart_format_id = $('#prev_reportsusersaved-chart_format_id').val();
						
						if(prev_chart_format_id!=$('#reportsusersaved-chart_format_id').val()){
							$("#table_field_container").html(null);
							$("#filter_custom_values").html(null);
							$("#sorting_custom_values").html(null);
							$("#chart_custom_values").html(null);
							$("#chart_legend_values").val('');
						}
						var  prev_chart_format_text = $('#prev_reportsusersaved-chart_format_text').val();
						$('.main_date_field').remove();
						//alert($('#table_field_container li').length);
						if($('#table_field_container li').length){
							$('#table_field_container li').each(function(){
								var id= $(this).attr('id');
								if(report_typeId==2 && $(this).find('span.glyphicon-tags').length==0){
									$(this).append('<a href="javascript:void(0);" class="icon-set" id="chart-category'+id+'" onClick="display_by_report_field('+id+');"><span class="glyphicon glyphicon-tags text-primary pull-right" title="Axis"></span></a>');
								}else{
									if(report_typeId==2 && prev_chart_format_id != "" && (prev_chart_format_id != $('#reportsusersaved-chart_format_id').val()) && ($('#reportsusersaved-chart_format_id option:selected').text().toLowerCase()=='pie'  || prev_chart_format_text=='pie')){
										$('#chart_values_'+id).remove();
										$(this).find('span.glyphicon-tags').closest('a').remove();
										$(this).append('<a href="javascript:void(0);" class="icon-set" id="chart-category'+id+'" onClick="display_by_report_field('+id+');"><span class="glyphicon glyphicon-tags text-primary pull-right" title="Axis"></span></a>');
									}
									if((report_typeId==1 && $(this).find('span.glyphicon-tags').length>0)){
										$('#chart_values_'+id).remove();
										$(this).find('span.glyphicon-tags').closest('a').remove();
									}
								}
								var field_vals = $(this).data('field_name');
								var table_vals = $(this).data('table_name');
								if(((field_vals == 'client_id' || field_vals == 'client_case_id' || table_vals == 'tbl_client') && byfilter[0].byClientCase == 0) || ((field_vals == 'teamservice_id' || field_vals == 'team_loc' || table_vals == 'tbl_teamservice' || table_vals == 'tbl_teamlocation_master') && byfilter[1].byTeamservice == 0)){
									$(this).find('span.glyphicon-filter').closest('a.hide').removeClass('hide');
								}
								if(((field_vals == 'client_id' || field_vals == 'client_case_id' || table_vals == 'tbl_client') && byfilter[0].byClientCase == 1) || ((field_vals == 'teamservice_id' || field_vals == 'team_loc' || table_vals == 'tbl_teamservice' || table_vals == 'tbl_teamlocation_master') && byfilter[1].byTeamservice == 1)){
									$(this).find('span.glyphicon-filter').closest('a').addClass('hide');
									$(this).find('span.glyphicon-filter').removeClass('text-danger').addClass('text-primary');
									$('div#filter_custom_values').find('input#filter_value_'+id).remove();
								}
							});
						}
						
						$('#prev_reportsusersaved-chart_format_id').val($('#reportsusersaved-chart_format_id').val());
						$('#prev_reportsusersaved-report_type_id').val($('#reportsusersaved-report_type_id').val());
						$('#prev_reportsusersaved-chart_format_text').val($('#reportsusersaved-chart_format_id option:selected').text().toLowerCase());
						$('#prev_reportsusersaved-byFilter').val(JSON.stringify(byfilter));
						$('#prev_reportsusersaved-report_format_id').val($('#reportsusersaved-report_format_id').val())
						
					}
				});
			} else {
				for (var key in response) {
					/*if(key == 'reportsusersaved-date_range_start'){
						$("#"+key).parent().parent().parent().find('.help-block').html(response[key]);
						$("#"+key).closest('div.form-group').addClass('has-error');
					} else {*/
						$("#"+key).parent().find('.help-block').html(response[key]);
						$("#"+key).closest('div.form-group').addClass('has-error');
					//}
				}
			}
		}
	});
	return $flag;
}

function inner_checkall(loop){
	if($('#teamservice_'+loop).is(':checked')){
		$('.teamservice_loc_'+loop).prop('checked',true);
		$('.teamservice_loc_'+loop).addClass('checked');
	}else{
		$('.teamservice_loc_'+loop).prop('checked',false);
		$('.teamservice_loc_'+loop).removeClass('checked');
	}
	//alert($('.chk:checked').length);
	if($('.teamservice_chk:checked').length==0){
		$('#teamservice_chkselectall').prop('checked',false);
		$('#teamservice_chkselectall').next('label').removeClass('checked');
	}
	if($('.teamservice_chk:checked').length==$('.chk').length){
		$('#teamservice_chkselectall').prop('checked',true);
		$('#teamservice_chkselectall').next('label').addClass('checked');
	}
}

function teamservice_checkall(stat) {
	if(stat){
		$('.teamservice_chk').each(function(){
			$(this).prop('checked',true);
			$(this).next('label').addClass('checked');
		});
	} else {
		$('.teamservice_chk').each(function(){
			$(this).prop('checked',false);
			$(this).next('label').removeClass('checked');
		});
	}
}

$(document).ready(function(){
	$('input[name="ReportsUserSaved[filter_by_client_case]"').on('change',function(){
		if($(this).val() == '2') {
			$('#client-list').hide();
			$('#client-case-list').show();
			$('#client-list').find('input[type="checkbox"]:checked').removeAttr('checked').next('label').removeClass('checked');
		}
		if($(this).val() == '1') {
			$('#client-case-list').hide();
			$('#client-list').show();
			$('#client-case-list').find('input[type="checkbox"]:checked').removeAttr('checked').next('label').removeClass('checked');
		}
	});
	
	$('.inner_chk').on('click',function(){
		id=$(this).data('id');
		if($('.teamservice_loc_'+id+':checked').length==0){
			$('#teamservice_'+id).prop('checked',false);
			$('#teamservice_'+id).next('label').removeClass('checked');
			return false;
		}
		if($('.teamservice_loc_'+id+':checked').length>0){
			$('#teamservice_'+id).prop('checked',true);
			$('#teamservice_'+id).next('label').addClass('checked');
		}
	});
	/*no need client case teamservice selection */
	/*
	$('#reportsusersaved-report_type_id').on('change',function(){
		var report_type_id = $(this).val();
		if(report_type_id!=''){
			$.ajax({
				url:baseUrl+'custom-report/chk-filter-field-exist&id='+report_type_id,
				type:'get',
				success:function(response){
					if(response.length > 0){
						
						var responsedata = JSON.parse(response);
						if(responsedata.byTeamservice == 0){
							$('.field-reportsusersavedfilterteamserviceloc-teamservice_id').hide();
							$('.field-reportsusersavedfilterteamserviceloc-teamservice_id').hide();
							$('.field-reportsusersavedfilterteamserviceloc-teamservice_id').find('.teamservice_chk:checked').prop('checked',false).next('label').removeClass('checked');
						} else {
							$('.field-reportsusersavedfilterteamserviceloc-teamservice_id').show();
						}
						if(responsedata.byClientCase == 0){
							$('.field-reportsusersavedfilterclientcase-client_case_id').hide();
							$('.field-reportsusersavedfilterclientcase-client_case_id').find('input[type="radio"][name="ReportsUserSaved[filter_by_client_case]"]:checked').prop('checked',false).next('label').removeClass('checked');
							$('#client-list').hide();
							$('#client-case-list').hide();
							$('#client-list').find('input[type="checkbox"]:checked').removeAttr('checked').next('label').removeClass('checked');
							$('#client-case-list').find('input[type="checkbox"]:checked').removeAttr('checked').next('label').removeClass('checked');
						} else {
							$('.field-reportsusersavedfilterclientcase-client_case_id').show();
						}
						
					}
				}
			});
		} else {
			$('.field-reportsusersavedfilterclientcase-client_case_id').hide();
			$('.field-reportsusersavedfilterclientcase-client_case_id').find('input[type="radio"][name="ReportsUserSaved[filter_by_client_case]"]:checked').prop('checked',false).next('label').removeClass('checked');
			$('#client-list').hide();
			$('#client-case-list').hide();
			$('#client-list').find('input[type="checkbox"]:checked').removeAttr('checked').next('label').removeClass('checked');
			$('#client-case-list').find('input[type="checkbox"]:checked').removeAttr('checked').next('label').removeClass('checked');
			
			$('.field-reportsusersavedfilterteamserviceloc-teamservice_id').hide();
			$('.field-reportsusersavedfilterteamserviceloc-teamservice_id').find('.teamservice_chk:checked').prop('checked',false).next('label').removeClass('checked');
		}
	});
	*/
});
function check_allfields(){
	var chk=$('#select_all_fields').prop('checked');
	$('#avail_field_data input[type="checkbox"]').each(function(){
		$(this).prop('checked',chk);
		if(chk){
			$(this).next('label').addClass('checked');
		}else{
			$(this).next('label').removeClass('checked');
		}
	});
}
</script>
<noscript></noscript>
