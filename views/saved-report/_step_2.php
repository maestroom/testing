<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\daterange\DateRangePicker;
use app\components\IsataskFormFlag;

//echo "<pre>",print_r($grouping_value),"</pre>"; die;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
$addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
?>
<div style="display:none;">
<?= $form->field($model, 'report_type_id',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
	    	'data' => $modeReportType,
	    	'options' => ['prompt' => false, 'id' => 'reportsusersaved-report_type_id'],
	    	/*'pluginOptions' => [
	    	    'allowClear' => true
	    	],*/
		])->label(false);?>
<?= IsataskFormFlag::widget(); // change flag ?>		
</div>		
<?= $form->field($model, 'report_format_id')->hiddenInput()->label(false); ?>
<fieldset class="one-cols-fieldset-report">
	<div class="col-sm-12 add-custom-report-stap-two">
		<div class="col-sm-5">
			<h4 class="form_label"><a href="javascript:void(0);" title="Available Fields" class="tag-header-black">Available Fields</a> <div class="pull-right header-checkbox">
			Select All
			<div class="pull-right">
			<input id="select_all_fields" name="select_all_fields" onclick="check_allfields();" type="checkbox">
			<label for="select_all_fields">&nbsp;</label>
			</div>
			</div></h4>
			<div class="mycontainer" id="avail_field_data"></div>
		</div>
		<div class="col-sm-1">
			<div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="select_report_field" title="Move Available Fields To Report Fields"><em class="glyphicon glyphicon-chevron-right text-primary fa-2x" title="Select Report Fields"></em></a></div>
		</div>
		<div class="col-sm-6">
			<h4 class="form_label"><a href="javascript:void(0);" title="Selected Report Fields" class="tag-header-black">Selected Report Fields</a> </h4>
			<ul class="ui-sortable select_report_fields" id="table_field_container">
				<?php 
				$grouping_value=array();
				$filter_values = array();
				$sorting_values = array(); 
				if(!empty($fields)){
						foreach($fields as $field){
							if($field->field_calculation_id!=0) {
								$calculation_field=$field->fieldCalculation;
								$logic = $field->reportsUserSavedFieldsLogic;
								if(!empty($logic)){
									foreach($logic  as $lgfilter){
										if($lgfilter->report_field_operator_id > 0){
										}
										/* Sorting */
										if($lgfilter->sort_type != 0 && $lgfilter->sort_order != 0){
											$sorting_values[$field->field_calculation_id]=array('id'=>"{$field->field_calculation_id}",'sort-type'=>"{$lgfilter->sort_type}",'sort-order'=>"{$lgfilter->sort_order}");
										}
										/* Grouping */
										if($lgfilter->format_total_type > 0) { 
											$grouping_value[$field->field_calculation_id]=array('id'=>"{$field->field_calculation_id}",'group-type'=>"{$lgfilter->format_total_type}","group-display-by"=>"","group-display-number-dp"=>"2","group-display-currency-dp"=>"2","display_by_currency_smb"=>"$","group-display-per-dp"=>"2");
										}
										if($lgfilter->format_display_type > 0){
											if($lgfilter->format_display_type ==2 ){ //number
												$grouping_value[$field->field_calculation_id]=array(
												'id'=>"{$field->field_calculation_id}",
												'group-type'=>"",
												'group-display-by'=>"{$lgfilter->format_display_type}",
												'group-display-number-dp'=>"{$lgfilter->format_display_decimal}",
												'group-display-number-sp'=>"{$lgfilter->format_display_separator}",
												'group-display-currency-dp'=>"2",
												'display_by_currency_smb'=>"$",
												'group-display-per-dp'=>"2"
												);	
											}
											if($lgfilter->format_display_type ==3 ){ //currency
												$grouping_value[$field->field_calculation_id]=array('id'=>"{$field->field_calculation_id}",
												'group-type'=>"",
												'group-display-by'=>"{$lgfilter->format_display_type}",
												'group-display-number-dp'=>"2",
												'group-display-number-sp'=>"",
												'group-display-currency-dp'=>"{$lgfilter->format_display_decimal}",
												'display_by_currency_smb'=>"{$lgfilter->format_display_symbol}",
												'group-display-per-dp'=>"2"
												);	
											}
											if($lgfilter->format_display_type ==4 ){ //percentages
												$grouping_value[$field->field_calculation_id]=array('id'=>"{$field->field_calculation_id}",
												'group-type'=>"",
												'group-display-by'=>"{$lgfilter->format_display_type}",
												'group-display-number-dp'=>"2",
												'group-display-number-sp'=>"",
												'group-display-currency-dp'=>"2",
												'display_by_currency_smb'=>"$",
												'group-display-per-dp'=>"{$lgfilter->format_display_decimal}"
												);	
											}
										}
									}
								}
							?>
							<li data-field_name="<?= $calculation_field->calculation_name ?>" data-table_name="Calc" class="report_<?=$field->field_calculation_id?> li_<?=$field->field_calculation_id?>" id="<?=$field->field_calculation_id?>">
								Calc => <?=$calculation_field->calculation_name?>
								<input name="fielddisp[<?=$field->field_calculation_id?>]" id="fielddisp_<?=$field->field_calculation_id?>" value="<?=$calculation_field->calculation_name?>" type="hidden">
								<input name="fieldval[<?=$field->field_calculation_id?>]" id="fieldval_<?=$field->field_calculation_id?>" value="Calc.<?=$calculation_field->calculation_name?>" type="hidden">
								<a href="javascript:void(0);" class="icon-set" onclick="remove_field(<?=$field->field_calculation_id?>)" aria-label="Remove"><span class="fa fa-close text-primary pull-right" title="Remove"></span></a>
								<a class="icon-set handel_sort" href="javascript:void(0);"><span class="fa fa-arrows text-primary pull-right" title="Move" aria-label="Remove"></span></a>
								<a href="javascript:void(0);" id="sorting-category<?=$field->field_calculation_id?>" class="icon-set" onclick="sorting_popup(<?=$field->field_calculation_id?>);" title="Add Sorting"><?php if(!empty($sorting_values[$field->field_calculation_id])){ if($sorting_values[$field->field_calculation_id]['sort-type'] ==1) { ?><i class="text-danger pull-left">1</i><?php }?><?php if($sorting_values[$field->field_calculation_id]['sort-type'] ==2) { ?><i class="text-danger pull-left">2</i><?php }?><?php if($sorting_values[$field->field_calculation_id]['sort-type'] ==3) { ?><i class="text-danger pull-left">3</i><?php }}?><i class="glyphicon <?php if(!empty($sorting_values[$field->field_calculation_id])){ if($sorting_values[$field->field_calculation_id]['sort-order'] == 1){?>glyphicon-arrow-up<?php } if($sorting_values[$field->field_calculation_id]['sort-order']== 2){?>glyphicon-arrow-down<?php }?> text-danger<?php }else{?>glyphicon-chevron-up text-primary<?php }?> pull-right" title="Sorting"></i></a>
								<a href="javascript:void(0);" class="icon-set" onclick="" id="filter_icon_<?=$field->field_calculation_id?>"><span class="glyphicon glyphicon-filter text-gray pull-right" title="Filter"></span></a>
								<!-- IRT 564 -->
								<a href="javascript:void(0);" class="icon-set" onClick="group_pop_up('<?= $reportfield->id?>');" id="grouping-category<?= $reportfield->id ?>"> <span class="glyphicon glyphicon-plus-sign text-primary pull-right" title="Format"><span class="not-set">Format</span> </span> </a>
								<!-- End -->
							</li>
							<?php
							}else{
							$reportfield=$field->reportsReportTypeFields;
							$field_info = $reportfield->reportsField;
							$logic = $field->reportsUserSavedFieldsLogic;

							if(!empty($logic)){
									foreach($logic  as $lgfilter){
										if($lgfilter->report_field_operator_id > 0){
											$val1="";$val2=""; if(isset($lgfilter->value1) && $lgfilter->value1!=""){$val1=$lgfilter->value1;}if(isset($lgfilter->value2) && $lgfilter->value2!=""){ $val2=$lgfilter->value2;} 
												if(isset($filter_values[$reportfield->id])){
													if(!empty($filter_values[$reportfield->id]['operator_field_value']))
														array_push($filter_values[$reportfield->id]['operator_field_value'],$lgfilter->report_field_operator_id);
													if(isset($filter_values[$reportfield->id]['operator_value_new'][0]) && $filter_values[$reportfield->id]['operator_value_new'][0]!="")
														$filter_values[$reportfield->id]['operator_value_new']=array($filter_values[$reportfield->id]['operator_value_new'][0].','.$val2);
													if(isset($filter_values[$reportfield->id]['operator_value'][0])  && $filter_values[$reportfield->id]['operator_value'][0]!="")
														$filter_values[$reportfield->id]['operator_value']=array($filter_values[$reportfield->id]['operator_value'][0].','.$val1);
													
												} else {
													$filter_values[$reportfield->id]=array('id' => "{$reportfield->id}",'count' =>"1",'operator_field_value' => array ($lgfilter->report_field_operator_id),'operator_value_new' => array($val2), 'operator_value' => array($val1));
												}
										}
										if($lgfilter->sort_type != 0 && $lgfilter->sort_order != 0){
											$sorting_values[$reportfield->id]=array('id'=>"{$reportfield->id}",'sort-type'=>"{$lgfilter->sort_type}",'sort-order'=>"{$lgfilter->sort_order}");
										}
										if($lgfilter->format_total_type > 0) { 
											$grouping_value[$reportfield->id]=array('id'=>"{$reportfield->id}",'group-type'=>"{$lgfilter->format_total_type}","group-display-by"=>"","group-display-number-dp"=>"2","group-display-currency-dp"=>"2","display_by_currency_smb"=>"$","group-display-per-dp"=>"2");
										}
										if($lgfilter->format_display_type > 0){
											if($lgfilter->format_display_type ==2 ){ //number
												$grouping_value[$reportfield->id]=array('id'=>"{$reportfield->id}",
												'group-type'=>"",
												'group-display-by'=>"{$lgfilter->format_display_type}",
												'group-display-number-dp'=>"{$lgfilter->format_display_decimal}",
												'group-display-number-sp'=>"{$lgfilter->format_display_separator}",
												'group-display-currency-dp'=>"2",
												'display_by_currency_smb'=>"$",
												'group-display-per-dp'=>"2"
												);	
											}
											if($lgfilter->format_display_type ==3 ){ //currency
												$grouping_value[$reportfield->id]=array('id'=>"{$reportfield->id}",
												'group-type'=>"",
												'group-display-by'=>"{$lgfilter->format_display_type}",
												'group-display-number-dp'=>"2",
												'group-display-number-sp'=>"",
												'group-display-currency-dp'=>"{$lgfilter->format_display_decimal}",
												'display_by_currency_smb'=>"{$lgfilter->format_display_symbol}",
												'group-display-per-dp'=>"2"
												);	
											}
											if($lgfilter->format_display_type ==4 ){ //percentages
												$grouping_value[$reportfield->id]=array('id'=>"{$reportfield->id}",
												'group-type'=>"",
												'group-display-by'=>"{$lgfilter->format_display_type}",
												'group-display-number-dp'=>"2",
												'group-display-number-sp'=>"",
												'group-display-currency-dp'=>"2",
												'display_by_currency_smb'=>"$",
												'group-display-per-dp'=>"{$lgfilter->format_display_decimal}"
												);	
											}
										}
									}
							} ?>
						<li data-field_name="<?=$field_info->field_name?>" data-table_name="<?=$field_info->reportsTables->table_name?>" class="report_<?= $reportfield->id ?> li_<?=$reportfield->id?>" id="<?=$reportfield->id ?>">
							<?=$field_info->reportsTables->table_display_name.' => '.$field_info->field_display_name?>
							<input name="fielddisp[<?=$reportfield->id?>]" id="fielddisp_<?=$reportfield->id?>" value="<?=$field_info->field_display_name?>" type="hidden">
							<input name="fieldval[<?=$reportfield->id?>]" id="fieldval_<?=$reportfield->id?>" value="<?=$field_info->reportsTables->table_name.'.'.$field_info->field_name?>" type="hidden">
							<a href="javascript:void(0);" class="icon-set" aria-label="Remove" onclick="remove_field(<?=$reportfield->id?>)"><span class="fa fa-close text-primary pull-right" title="Remove"></span></a>
							<a class="icon-set handel_sort" href="javascript:void(0);" aria-label="move"><span class="fa fa-arrows text-primary pull-right" title="Move"></span></a>
							<a href="javascript:void(0);" id="sorting-category<?=$reportfield->id?>" class="icon-set" onclick="sorting_popup(<?=$reportfield->id?>);" title="Add Sorting" aria-label="Add Sorting"><?php if(!empty($sorting_values[$reportfield->id])){ if($sorting_values[$reportfield->id]['sort-type'] ==1) { ?><i class="text-danger pull-left">1</i><?php }?><?php if($sorting_values[$reportfield->id]['sort-type'] ==2) { ?><i class="text-danger pull-left">2</i><?php }?><?php if($sorting_values[$reportfield->id]['sort-type'] ==3) { ?><i class="text-danger pull-left">3</i><?php }}?><i class="glyphicon <?php if(!empty($sorting_values[$reportfield->id])){ if($sorting_values[$reportfield->id]['sort-order'] == 1){?>glyphicon-arrow-up<?php } if($sorting_values[$reportfield->id]['sort-order']== 2){?>glyphicon-arrow-down<?php }?> text-danger<?php }else{?>glyphicon-chevron-up text-primary<?php }?> pull-right" title="Sorting"></i></a>
							<a href="javascript:void(0);" class="icon-set" onclick="filter_pop_up(<?=$reportfield->id?>);" id="filter_icon_<?=$reportfield->id?>" aria-label="Filter"><span class="glyphicon glyphicon-filter text-<?php if(!empty($filter_values[$reportfield->id])) { ?>danger<?php }else{ ?>primary<?php } ?> pull-right" title="Filter"></span></a>

						<!-- IRT 564 -->
						<?php if($field_type_name[$field_info->reports_field_type_id]=='INT') { ?>
							<a href="javascript:void(0);" class="icon-set" onClick="group_pop_up('<?= $reportfield->id ?>');" id="grouping-category<?= $reportfield->id ?>"> <span class="glyphicon glyphicon-plus-sign text-<?php if(!empty($grouping_value[$reportfield->id])) { ?>danger<?php }else{ ?>primary<?php } ?> pull-right" title="Format"><span class="not-set">Format</span></span></a>
						<?php } ?>
						<!-- END -->
						</li>
				<?php }
					}
				}?>
			</ul>
		</div>
	</div>
	<?php //echo "<pre>",print_r($fields),"</pre>"; ?>
	<!-- Popup values hidden -->
	<div id="filter_custom_values" class="filter_custom_values">
	<?php if(!empty($filter_values)) {
			foreach($filter_values as $fkey=>$fval) {
			?>
				<input data-id="<?=$fkey?>" id="filter_value_<?=$fkey?>" name="filter_value[]" class="filter_value" value='<?php echo json_encode($fval,true);?>' type="hidden">
			<?php }
	}?>
	</div>
	<div id="sorting_custom_values" class="sorting_custom_values"><?php 
		if(!empty($sorting_values)) {
			foreach($sorting_values as $skey=>$sval){?>
				<input class="sorting-values " data-id="<?=$skey?>" name="sorting_value[]" id="sorting_value_<?=$skey?>" value='<?php echo json_encode($sval,true);?>' type="hidden">
			<?php }
		}
	?></div>
	<div id="grouping_custom_values" class="grouping_custom_values">
	<?php if(!empty($grouping_value)) { foreach($grouping_value as $gk=>$gv) {?>
			<input class="grouping-values " data-id="<?=$gk?>" name="grouping_value[]" id="grouping_value_<?=$gk?>" value='<?php echo json_encode($gv,true);?>' type="hidden">
	<?php } } ?>
	</div>
	<div id="chart_custom_values" class="chart_custom_values"></div>
	<input type="hidden" id="chart_legend_values" name="chart_legend" value="" />
	<input type="hidden" id="has_manipulation_by" value="N" />
	<!-- End popup -->
</fieldset>

<div class="button-set text-right">
	<?php $allReports_url = Url::toRoute(['saved-report/index']); ?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"']) ?>
	<?= Html::button('Clear', ['title'=>'Clear','class' => 'btn btn-primary','onclick'=>'$("#table_field_container").html(null);$("#filter_custom_values").html(null);$("#sorting_custom_values").html(null);$("#grouping_custom_values").html(null);$("#chart_custom_values").html(null);']) ?>
	<?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep1','onclick'=>'post_step();']) ?>
</div>
<script>
jQuery(document).ready(function() {
$("#active_form_name").val('report-type-format-dates'); // change active flag form
$.ajax({
	url:baseUrl+'custom-report/get-table-field-details&step=0',
	type:'post',
	data:$("#report-type-format-dates").serialize()+'&flag='+'step0',
	beforeSend:function (data) {showLoader();},
	success:function(resp){
		hideLoader();
		$('#avail_field_data').html(resp);
		var objselect2=$("#reportsusersaved-report_type_id option:selected").text();
		$(".sub-heading").html("<a href='javascript:void(0);' title='Step 2: Select Fields, Filters & Sort Orders' class='tag-header-black'>Step 2: Select Fields, Filters & Sort Orders</a><div style=float:right><?=$model->custom_report_name?></div>");
	},complete:function(){
		<?php if($flag=='run'){?>
			post_step();
		<?php }?>
	}
});
});

	// Selected Report Fields Add
	$('#select_report_field').click(function(){
		var report_typeId = $('#reportsusersaved-report_format_id').val(); // report type (chart or tabular)
		//alert(report_typeId);
		//var step_1_by_filter = JSON.parse($('#tabs-step-1').find('input#prev_reportsusersaved-byFilter').val());
		var i=1;
		var cntselected=$('.table_field:checked').length;
		$('.table_field:checked').each(function(){
			//var val = $(this).data('table');
			var table_name=$(this).data('table');
			if(table_name=='Calculation'){
				table_name='Calc';
			}
			var field_vals=htmlEncode($(this).val());
			var val = table_name+'.'+field_vals;
			var display_field = $(this).closest('li').find('span');
			var display_name = display_field.html();
			
			var id = $(this).data('fieldid');
			var fieldtype=$('#report_field_types option[value="'+id+'"]').text();
			var filter_condition=$('#report_field_types option[value="'+id+'"]').data('con');
			var filter_class="text-primary";
			if(filter_condition != ""){
				var filter_class="text-danger";
			}
			var filterTag = '<a href="javascript:void(0);" class="icon-set" onClick="filter_pop_up('+id+');" id="filter_icon_'+id+'"><span class="glyphicon glyphicon-filter '+filter_class+' pull-right" title="Filter"></span></a>';
			var groupTag = '';//'<a href="javascript:void(0);" class="icon-set" onClick="group_pop_up('+id+');" id="grouping-category'+id+'"><span class="glyphicon glyphicon-plus-sign text-primary pull-right" title="Format"></span></a>';
			

			if(table_name=='Calc'){
				var filterTag = '<a href="javascript:void(0);" class="icon-set" onClick="" id="filter_icon_'+id+'"><span class="glyphicon glyphicon-filter text-gray pull-right" title="Filter"><span class="not-set">Filter</span</span></a>';
				var groupTag = '<a href="javascript:void(0);" class="icon-set" onClick="group_pop_up('+id+');" id="grouping-category'+id+'"><span class="glyphicon glyphicon-plus-sign text-primary pull-right" title="Format"><span class="not-set">Format</span</span></a>';
			}else if(fieldtype != undefined  && fieldtype == 'INT'){
				var groupTag = '<a href="javascript:void(0);" class="icon-set" onClick="group_pop_up('+id+');" id="grouping-category'+id+'"><span class="glyphicon glyphicon-plus-sign text-primary pull-right" title="Format"><span class="not-set">Format</span</span></a>';
			}

			if($('.select_report_fields').find('.report_'+id).length == 0){ // condition check
				
				/*
				No Need Client/case teamservice selection any more
				
				if(((field_vals == 'client_id' || field_vals == 'client_case_id' || table_name == 'tbl_client') && step_1_by_filter[0].byClientCase == 1) || ((field_vals == 'teamservice_id' || field_vals == 'team_loc' || table_name == 'tbl_teamservice' || table_name == 'tbl_teamlocation_master') && step_1_by_filter[1].byTeamservice == 1)){
					filterTag = '<a href="javascript:void(0);" class="icon-set hide" onClick="filter_pop_up('+id+');" id="filter_icon_'+id+'"><span class="glyphicon glyphicon-filter text-primary pull-right" title="Filter"></span></a>';
				}
				*/
				if(report_typeId==1){
					$( ".select_report_fields" ).append('<li data-field_name="'+field_vals+'" data-table_name="'+table_name+'" class="report_'+id+' li_'+id+'" id='+id+'>'+display_field.attr('data-table-display-name')+" => "+display_name+'<input type="hidden" name="fielddisp['+id+']" id="fielddisp_'+id+'" value="'+display_name+'" /><input type="hidden" name="fieldval['+id+']" id="fieldval_'+id+'" value="'+val+'" /><a href="javascript:void(0);" class="icon-set" aria-label="remove field" onClick="remove_field('+id+')"><span class="fa fa-close text-primary pull-right" title="Remove"></span></a><a class="icon-set handel_sort" href="javascript:void(0);"><span class="fa fa-arrows text-primary pull-right" title="Move" aria-label="Move"></span></a><a href="javascript:void(0);" id="sorting-category'+id+'" class="icon-set" onClick="sorting_popup('+id+');" title="Add Sorting"><i class="glyphicon glyphicon-chevron-up text-primary pull-right" title="Sorting"></i></a>'+filterTag+groupTag+'</li>');
				}if(report_typeId==2){
					$( ".select_report_fields" ).append('<li data-field_name="'+field_vals+'" data-table_name="'+table_name+'" class="report_'+id+' li_'+id+'" id='+id+'>'+display_field.attr('data-table-display-name')+" => "+display_name+'<input type="hidden" name="fielddisp['+id+']" id="fielddisp_'+id+'" value="'+display_name+'" /><input type="hidden" name="fieldval['+id+']" id="fieldval_'+id+'" value="'+val+'" /><a href="javascript:void(0);" aria-label="remove field" class="icon-set" onClick="remove_field('+id+')"><span class="fa fa-close text-primary pull-right" title="Remove"></span></a><a class="icon-set handel_sort" href="javascript:void(0);"><span class="fa fa-arrows text-primary pull-right" title="Move"></span></a><a href="javascript:void(0);" aria-label="Sorting" id="sorting-category'+id+'" class="icon-set" onClick="sorting_popup('+id+');" title="Add Sorting"><i class="glyphicon glyphicon-chevron-up text-primary pull-right" title="Sorting"></i></a>'+filterTag+'</li>');
					/*<a href="javascript:void(0);" class="icon-set" id="chart-legend'+id+'" onClick="make_legend('+id+');"><span class="glyphicon glyphicon-stop text-primary pull-right" title="Lagend"></span></a></li>*/
				}
			}
			$(this).prop('checked',false);
			$(this).next('label').removeClass('checked');
			if(i==cntselected){
				//$("input[value='"+table_name+"']").prop('checked',false);
				//$("input[value='"+table_name+"']").next('label').removeClass('checked');	
			}
			i++;
		});
		$(".maintableinput:checked").each(function(){
			//alert($(this).attr('id'));
			$(this).prop('checked',false);
			$(this).attr('checked',false);
			$(this).next('label').removeClass('checked');	
		});
		$("#select_all_fields").prop('checked',false);
		$("#select_all_fields").attr('checked',false);
		$("#select_all_fields").next('label').removeClass('checked');	
	});
	function htmlEncode(value){
		if (value) {
		return jQuery('<div />').text(value).html();
		} else {
		return '';
		}
		}

	function htmlDecode(value) {
		if (value) {
		return $('<div />').html(value).text();
		} else {
		return '';
		}
	}
	// Remove Field	from selected report fields
	function remove_field(loop)
	{
		var fieldval = $('#fieldval_'+loop).val(); // field value
		//var display_name = $('#field_name_'+loop).closest('li').find('span').html();
		var display_field = $('#field_name_'+loop).closest('li').find('span');
		var display_name = display_field.attr('data-table-display-name')+" => "+display_field.html();
			// if(confirm("Are you sure you want to delete "+display_name+" field?")){
			var rs = $('.select_report_fields').find('.report_'+loop);
			rs.remove(); // remove selected record field
			
			// change flag to 1
			$("#report-type-format-dates #is_change_form").val('1');
			$("#is_change_form_main").val('1');
			
			$('#sorting_value_'+loop).remove(); // remove sorting option 
			$('#filter_value_'+loop).remove(); // remove filter
			$('#chart_values_'+loop).remove();
			if($('#chart_legend_values').val()==loop){
				$('#chart_legend_values').val('');
			}
		//}
		
	}
	
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			//$(this).width(($(this).width()+5));
		});
		return ui;
	};
  
	$("#table_field_container").sortable({
		handle:'.handel_sort',
		helper: fixHelper,
		stop: function(e,ui) { 
			
		},
		change: function(e, ui){
			$("#report-type-format-dates #is_change_form").val('1'); // change flag to 1
			$("#is_change_form_main").val('1'); // change flag to 1
		}
	}).disableSelection(); 
	
	function  make_legend(id)
	{
		var display_field = $('#field_name_'+id).closest('li').find('span').html();
		var chart_legend = $("#chart_legend_values").val();
		$("#chart_legend_values").val(id);
		$('.glyphicon-stop').removeClass('text-danger');
		$('#chart-legend'+id).find('.glyphicon-stop').addClass('text-danger');
		
	}
	/**
	 * Display By popup
	 */
	 function display_by_report_field(id)
	 {
		 //var fieldval = $('#fieldval_'+id).val(); // field value
		 var display_field = $('#field_name_'+id).closest('li').find('span');
		 var fieldval = display_field.attr('data-table-display-name')+" => "+display_field.html();
		 if($('#chart-category'+id).find('span.glyphicon-tags').hasClass('text-gray')){
			 return false;
		 }
		 var chart_id = $('#reportsusersaved-chart_format_id').val();
		 var chart_type = $('select[name="ReportsUserSaved[chart_format_id]"] option:selected').text();
		 var obj = {};
		 var str = $('.chart-values').each(function(){
			obj[$(this).attr('data-id')] = $(this).val();
		//	postdata.push(obj);
		 });
		  var selected_chart_format = $('#reportsusersaved-chart_format_id option:selected').text().toLowerCase();		
		  obj['selected_chart_format'] = selected_chart_format;
		  if($('#chart_legend_values').val()!=""){
			obj[$('#chart_legend_values').val()] = '{"id":"'+$('#chart_legend_values').val()+'","axis":"l","display_by":"","manipulation_by":""}';
		  }
		  obj['has_manipulation_by']=$('#has_manipulation_by').val();
		 $.ajax({
			type: 'post',
			url:baseUrl+'custom-report/displayby-pop-up-chart&id='+id+'&chartId='+chart_id,
			data: obj,
			beforeSend:function (data) {showLoader();},
			success:function(response){
			hideLoader();
			if($('body').find('#availabl-price-points').length == 0){
				$('body').append('<div class="dialog" id="availabl-price-points" title="'+chart_type+' : '+fieldval+'"></div>');
			}
			$('#availabl-price-points').html('').html(response);		
			$('#availabl-price-points').dialog({ 
				modal: true,
				width:'50em',
				height:456,
				create: function(event, ui){
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close:function(){
					$(this).dialog('destroy').remove();
				},
				buttons: [
					{ 
						  text: "Clear", 
						  "class": 'btn btn-primary',
						  "title": 'Clear',
						  click: function () { 
							 if(confirm("Are you sure you want to clear selected Axis for field "+fieldval+"")){
									$('#chart_values_'+id).remove();
									$('#chart-category'+id).html('<span class="glyphicon glyphicon-tags pull-right" title="Axis"></span>');	
									$(this).dialog('destroy').remove();
									$('.glyphicon-tags').not('.text-danger').removeClass('text-gray');
									$('#has_manipulation_by').val("");
									if($("#chart_legend_values").val()==id){
										$("#chart_legend_values").val('');
									}
							  }
						  } 
					   },
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
							text: "Update", 
							"class": 'btn btn-primary',
							"title": 'Update',
							click: function () 
							{ 
								// select sort order  
								var chart_axis = '';
								if($('#chart-option-axis').length){
									chart_axis =$('#chart-option-axis').val();
								}
								
								if(chart_axis=='l'){
									//alert(chart_axis);return false;
									var chart_legend = $("#chart_legend_values").val();
									$("#chart_legend_values").val(id);
									$('#chart-category'+id).html('<span class="text-danger">'+chart_axis+'</span><span class="glyphicon glyphicon-tags pull-right text-danger" title="Legend"></span>');	
									$(this).dialog('destroy').remove(); // remove dialog box 
									hideLoader();
									return false;
								}
								var chart_display_by = $('#chart-option-display-by option:selected').text().toLowerCase();
								var chart_manipulation_by = $('#chart-option-manipulation-by').val();
								var chart_interval_range = $('#chart-option-interval_range').val();
								var chart_view_display = $('#chart-option-view_display').val();
								var number_decimal_places = $('#chart-option-number_decimal_places').val();
								var currency_decimal_places = $('#chart-option-currency_decimal_places').val();
								var currency_symbol = $('#chart-option-currency_symbol').val();
								//var axis = chart_axis.split('-');
								
								/*if((chart_axis == '' && selected_chart_format!='pie') || ($('#chart-option-display-by').prop('disabled')==false && chart_display_by=='')){
									alert("Please select value for Chart Axis / Diplay By.");
									return false;
								} else {*/
								console.log(chart_display_by);
								if(chart_axis.toLowerCase() == ''){
									alert("Please select Axis.");
									return false;
								}
																
								if(selected_chart_format.toLowerCase() == 'column'){
									if(chart_axis.toLowerCase() == 'x' && chart_manipulation_by==''){
										alert("Please select value for Total Type.");
										return false;	
									} else if (chart_axis.toLowerCase() == 'y'){
										if(chart_display_by==''){
											alert("Please select value for Display By.");
											return false;	
										} /*else if (chart_view_display == ''){
											alert("Please select value for Interval Range.");
											return false;	
										} else if (chart_interval_range == ''){
											alert("Please select value for View Display.");
											return false;	
										}*/
										if(chart_display_by=='date' && (chart_view_display == '' || chart_interval_range == '')){
											alert("Please select value for  Interval Range / View Display.");
											return false;	
										} else if(chart_display_by=='number' && (number_decimal_places == '')){
											alert("Please select value for Number Decimal Places.");
											return false;	
										}
										else if(chart_display_by=='currency' && (currency_decimal_places == '' || currency_symbol == '')){
											alert("Please select value for currency Decimal Places.");
											return false;	
										}
									}
									
								}else if(selected_chart_format.toLowerCase() == 'bar'){
									if(chart_axis.toLowerCase() == 'y' && chart_manipulation_by==''){
										alert("Please select value for Total Type.");
										return false;	
									} else if (chart_axis.toLowerCase() == 'x'){
										if(chart_display_by==''){
											alert("Please select value for Display By.");
											return false;	
										} /*else if (chart_view_display == ''){
											alert("Please select value for Interval Range.");
											return false;	
										} else if (chart_interval_range == ''){
											alert("Please select value for View Display.");
											return false;	
										}*/
										if(chart_display_by=='date' && (chart_view_display == '' || chart_interval_range == '')){
											alert("Please select value for  Interval Range / View Display.");
											return false;	
										} else if(chart_display_by=='number' && (number_decimal_places == '')){
											alert("Please select value for Number Decimal Places.");
											return false;	
										}
										else if(chart_display_by=='currency' && (currency_decimal_places == '' || currency_symbol == '')){
											alert("Please select value for currency Decimal Places.");
											return false;	
										}
									}
									
								}
									// chart filter popup option
									var form = $('#chart-filter-popup-option').serialize();
									
									// Ajax
									$.ajax({
										type : 'post',
										url:baseUrl+'custom-report/pop-up-option-select&id='+id+'&flag=chart',
										data: form,
										beforeSend:function (data) {showLoader();},
										success:function(response){
											$("#chart_values_"+id).remove();
											// chart custom values	
											$('#chart_custom_values').append("<input class='chart-values' data-id='"+id+"' type='hidden' name='chart_values_"+id+"' id='chart_values_"+id+"' value="+response+" />");
										},
										complete:function(){
											if($(".chart-values").length == 3 && chart_type.toLowerCase() != 'pie'){
												$('.glyphicon-tags').not('.text-danger').addClass('text-gray');
											}
											if($(".chart-values").length == 1 && chart_type.toLowerCase() == 'pie'){
												$('.glyphicon-tags').not('.text-danger').addClass('text-gray');
											}
											$('#has_manipulation_by').val(chart_manipulation_by)
										}
									});
									
									if(chart_axis != '' && chart_axis.toLowerCase()=='x'){
										$('#chart-category'+id).html('<span class="text-danger">'+chart_axis+'</span><span class="glyphicon glyphicon-tags pull-right text-danger" title="'+chart_axis+' Axis"></span>');	
									} else if(chart_axis != '' && chart_axis.toLowerCase()=='y'){
										$('#chart-category'+id).html('<span class="text-danger">'+chart_axis+'</span><span class="glyphicon glyphicon-tags pull-right text-danger" title="'+chart_axis+' Axis"></span>');	
									}else if(chart_axis == '' && selected_chart_format=='pie'){
										$('#chart-category'+id).html('<span class="text-danger"></span><span class="glyphicon glyphicon-tags pull-right text-danger" title="Pie '+$('#chart-option-display-by option:selected').text()+' "></span>');
									}else {
										$('#chart-category'+id).html('<span class="glyphicon glyphicon-tags text-primary pull-right" title="Axis"></span>');	
									}
									
									// remove dialog box	
									$(this).dialog('destroy').remove(); // remove dialog box 
									hideLoader();
								//}
							}
						}
					]
				});	
			}
		});
	 }
	
	/**
	 * Sorting Popup
	 */
	function sorting_popup(id)
	{
		var fieldval = $('#fieldval_'+id).val(); // field value
		//var display_name = $('#field_name_'+id).closest('li').find('span').html();
		var display_field = $('#field_name_'+id).closest('li').find('span');
		var display_name = display_field.attr('data-table-display-name')+" => "+display_field.html();
		var obj = {}; var sort_cnt = 0;
		var str = $('.sorting-values').each(function(){
			obj[$(this).attr('data-id')] = $(this).val();
			sort_cnt++;
		});
		
		// validation sort cnt more than 3
		if(sort_cnt > 2){
			// alert("You have already selected 3 sort type."); return false;
		}
		
		// custom -sorting- popup
		$.ajax({
			type: 'post',
			url: baseUrl+'custom-report/sort-pop-up-option&id='+id,
			data: obj,
			beforeSend: function (data) {showLoader();},
			success: function(response){
			hideLoader();
			if($('body').find('#availabl-price-points').length == 0){
				$('body').append('<div class="dialog" id="availabl-price-points" title="Apply Sort Order to '+display_name+'"></div>');
			}
			$('#availabl-price-points').html('').html(response);		
			$('#availabl-price-points').dialog({ 
				modal: true,
				width:'50em',
				height: 456,
				create: function(event, ui){ 
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close:function(){
					$(this).dialog('destroy').remove();
				},
				buttons: [
						{
						  text: "Clear", 
						  "class": 'btn btn-primary',
						  "title": 'Clear',
						  click: function () { 
							  var fieldval = $('#fieldval_'+id).val(); // field value
							  if(confirm("Are you sure you want to clear sort order for field "+display_name+"")){
									$("#sorting_value_"+id).remove();
									$('#sorting-category'+id).html('<i class="glyphicon glyphicon-chevron-up pull-right" title="Sorting"></i>');	
									$('#sorting-category'+id).attr("onclick","sorting_popup("+id+");");
									$(this).dialog('destroy').remove();
							  }
							  
						  }
						},
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
						  text: "Update", 
						  "class": 'btn btn-primary',
							"title": 'Update',
							  click: function () 
							  { 
								// select sort order  
								var sort_type = $('#select-sort-type-field').val();
								var sort_order = $('#select-sort-order').val();
								var form = $('#sort-popup-option').serialize();
								
								// sort validation
								if(sort_type==''){
									alert("Please Select Sort Type");
									return false;
								}
								
								// sort order
								if(sort_order==''){
									alert("Please Select Sort Order");
									return false;
								}
								
								// Ajax
								$.ajax({
									type : 'post',
									url:baseUrl+'custom-report/pop-up-option-select&id='+id+'&flag=sort',
									data: form,
									beforeSend:function (data) {showLoader();},
									success:function(response)
									{
										// change flag to 1
										$("#report-type-format-dates #is_change_form").val('1');
										$("#is_change_form_main").val('1');
										
										// sorting custom values
										$('#sorting_value_'+id).remove(); 
										$('#sort_type_'+id).remove(); 
										
										var append_class="";
										if($('#sorting-category'+id).closest('li').hasClass('main_date_field')){
											append_class="main_date_field";
										}
										// sort type & sort order
										if(sort_type!='' && sort_order!=''){
											$('#sorting_custom_values').append("<input class='sorting-values "+append_class+"' data-id='"+id+"' type='hidden' name='sorting_value[]' id='sorting_value_"+id+"'  value='"+response+"' />");
										}
										// End
									}
								});
								// end
								var hover = "";
								if(sort_type==1) {
									hover = "Primary ";
								} else if(sort_type==2) {
									hover = "Secondary ";
								} else if(sort_type==3) {
									hover = "Tertiary ";
								}
								// sort order
								if(sort_order==1) {
									hover=hover+'Ascending'; 
									$('#sorting-category'+id).html('<i class="text-danger pull-left">'+sort_type+'</i><i class="glyphicon glyphicon-arrow-up text-danger pull-right" title="'+hover+'"></i>');
									//$('#sorting-category'+id).attr("onclick","remove_sorting_popup("+id+");");
									$('#sorting-category'+id).attr("title",hover);
								} else if(sort_order==2) {
									hover=hover+'Descending';
									$('#sorting-category'+id).html('<i class="text-danger pull-left">'+sort_type+'</i><i class="glyphicon glyphicon-arrow-down text-danger pull-right" title="'+hover+'"></i>');
									//$('#sorting-category'+id).attr("onclick","remove_sorting_popup("+id+");");
									$('#sorting-category'+id).attr("title",hover);
								} else {
									$('#sorting-category'+id).html('<i class="glyphicon glyphicon-chevron-up pull-right" title="Sorting"></i>');	
									//$('#sorting-category'+id).attr("onclick","sorting_popup("+id+");");
									$('#sorting-category'+id).attr("title","Add Sorting");
								}
								
								// remove dialog box	
								$(this).dialog('destroy').remove(); // remove dialog box 
								hideLoader();
							}
						}
					]
				});	
			}
		});
	}
	/**
	*Group By Order
	*/
	function group_pop_up(id){
		var fieldval = $('#fieldval_'+id).val(); // field value
		var display_field = $('#field_name_'+id).closest('li').find('span');
		var display_name = display_field.attr('data-table-display-name')+" => "+display_field.html();
		var type = 'field';
		if($('#field_name_calc_'+id).length > 0){
			var display_field = $('#field_name_calc_'+id).closest('li').find('span');
			var display_name = display_field.attr('data-table-display-name')+" => "+display_field.html();
			type = 'calculation';
		}
		var obj = {}; var sort_cnt = 0;
		var str = $('.grouping-values').each(function(){
			obj[$(this).attr('data-id')] = $(this).val();
			sort_cnt++;
		});
		
		// validation sort cnt more than 3
		if(sort_cnt > 3){
			// alert("You have already selected 3 sort type."); return false;
		}
		
		// custom -sorting- popup
		$.ajax({
			type: 'post',
			url: baseUrl+'custom-report/group-pop-up-option&id='+id+'&type='+type,
			data: obj,
			beforeSend: function (data) {showLoader();},
			success: function(response){
			hideLoader();
			if($('body').find('#availabl-group-type').length == 0){
				$('body').append('<div class="dialog" id="availabl-group-type" title="Format '+display_name+'"></div>');
			}
			$('#availabl-group-type').html('').html(response);		
			$('#availabl-group-type').dialog({ 
				modal: true,
				width:'50em',
				height: 456,
				create: function(event, ui){ 
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close:function(){
					$(this).dialog('destroy').remove();
				},
				buttons: [
						{
						  text: "Clear", 
						  "class": 'btn btn-primary',
						  "title": 'Clear',
						  click: function () { 
							  var fieldval = $('#fieldval_'+id).val(); // field value
							  if(confirm("Are you sure you want to clear grouping for field "+display_name+"")){
									$("#grouping_value_"+id).remove();
									$('#grouping-category'+id).html('<span class="glyphicon glyphicon-plus-sign text-primary pull-right" title="Format"></span>');	
									$('#grouping-category'+id).attr("onclick","group_pop_up("+id+");");
									$(this).dialog('destroy').remove();
							  }
						  }
						},
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
						  text: "Update", 
						  "class": 'btn btn-primary',
							"title": 'Update',
							  click: function () 
							  { 
								// select sort order  
								var sort_type  = $('#select-group-type-field').val();
								var display_by = $('#select-group-display-by').val();
								var form = $('#group-popup-option').serialize();
								
								// sort validation
								/*if(sort_type==''){
									alert("Please Select Total Type");
									return false;
								}*/
								if(sort_type == '' && display_by == ''){
									alert("Please Select Total Type Or Display By");
									return false;
								}
								
								// Ajax
								$.ajax({
									type : 'post',
									url:baseUrl+'custom-report/pop-up-option-select&id='+id+'&flag=group',
									data: form,
									beforeSend:function (data) {showLoader();},
									success:function(response)
									{
										// change flag to 1
										$('#report-type-format-dates #is_change_form').val('1');
										$('#is_change_form_main').val('1');
										
										// sorting custom values
										$('#grouping_value_'+id).remove(); 
										var append_class="";
										if($('#grouping-category'+id).closest('li').hasClass('main_date_field')){
											append_class="main_date_field";
										}
										// sort type & sort order
										if(sort_type!='' ||  display_by != ''){
											$('#grouping_custom_values').append("<input class='grouping-values "+append_class+"' data-id='"+id+"' type='hidden' name='grouping_value[]' id='grouping_value_"+id+"'  value='"+response+"' />");
										}
										// End
									}
								});
								// end
								var hover = "";
								if(sort_type!='' ||  display_by != ''){
									hover = "Format";
								}
								/*if(sort_type==1) {
									hover = "Format ";
								} else if(sort_type==2) {
									hover = "Format ";
								} else if(sort_type==3) {
									hover = "Format ";
								}*/
								// sort order
								if(hover!=""){
									$('#grouping-category'+id).html('<span class="text-danger pull-left"></span><span class="glyphicon glyphicon-plus-sign text-danger" title="'+hover+'"></span>');
									$('#sorting-category'+id).attr("title",hover);
								} else {
									$('#grouping-category'+id).html('<span class="glyphicon glyphicon-plus-sign" title="Format"></span>');	
									$('#grouping-category'+id).attr("title","Format");
								}
								
								// remove dialog box	
								$(this).dialog('destroy').remove(); // remove dialog box 
								hideLoader();
							}
						}
					]
				});	
			}
		});
	}
	/**
	*Remove Sorting Order
	*/
	function remove_sorting_popup(id){
		var fieldval = $('#fieldval_'+id).val(); // field value
		if(confirm("Are you sure you want to Delete sort order : "+fieldval+"")){
			$("#sorting_value_"+id).remove();
			$('#sorting-category'+id).html('<i class="glyphicon glyphicon-chevron-up pull-right" title="Sorting"></i>');	
			$('#sorting-category'+id).attr("onclick","sorting_popup("+id+");");
		}
	}
	
	/**
	 * Filter popup
	 */
	function filter_pop_up(id)
	{
		var fieldval = $('#fieldval_'+id).val(); // field value
		var display_field = $('#field_name_'+id).closest('li').find('span');
		var display_name = display_field.attr('data-table-display-name')+" => "+display_field.html();
		var form = $('#report-type-format-dates').serialize();
		$.ajax({
			type : 'post',
			url:baseUrl+'custom-report/filter-pop-up-option&id='+id+'&report_type_id='+$('#reportsusersaved-report_type_id').val(),
			data: form,
			beforeSend:function (data) {showLoader();},
			success:function(response){
				hideLoader();
				if($('body').find('#availabl-price-points').length == 0){
					$('body').append('<div class="dialog" id="availabl-price-points" title="Apply Filter to '+display_name+'"></div>');
				}
				$('#availabl-price-points').html('').html(response);		
				$('#availabl-price-points').dialog({ 
					modal: true,
					width:'50em',
					height: 456,
					create: function(event, ui){ 
						$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					},
					close:function(){
						$(this).dialog('destroy').remove();
					},
					buttons: [
						{
						  text: "Clear", 
						  "class": 'btn btn-primary',
						  "title": 'Clear',
						  click: function () { 
							  var fieldval = $('#fieldval_'+id).val(); // field value
							  if(confirm("Are you sure you want to clear filter for field "+display_name+"?")){
									$("#filter_value_"+id).remove();
									$("#filter_icon_"+id).find('span').removeClass('text-danger');
									$('.delete_operator').trigger('click');
									$('#filter-option-popup tr.field-operator-id').each(function(){
										$('.select-filter-operator').val('').change();
										//$(".select-filter-operator").select2("");
										if($(this).find('input').length){
											opt_val=$(this).find('input').val(null);
										}else{
											opt_val=$(this).find('select#flags').val(null);
										}
										
									});
									$('.operator-val').hide();
									$("td#filter_val_1").html('Value(s)');
									$('#count').val(1);
									//$(this).dialog('destroy').remove();
							  }
							  
						  	}
						},
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('destroy').remove();
						  } 
					    },
					    { 
							text: "Update", 
							"class": 'btn btn-primary',
							"title": 'Update',
							click: function () 
							{ 
								value_existoperator_val=true;
								var has_lookup=$("#has_lookup").val();
								var field_theme_name = $('#field_theme_name').val();
								
								/** IRT 564 **/
								var start_date = new Array();
								$('.start_date').each(function(){
									start_date.push($(this).val());
								});
								if(field_theme_name == 'Date'){ //&& start_date == ''){
									if($('.operator_value').val() == 'C' && start_date == ''){
										alert("Please select Date.");
										return false;
									}
								}
								/* End */

								$('#filter-option-popup tr.field-operator-id').each(function(){
									opt=$(this).find('select#select-operator-value').val();
									if(has_lookup==1){
										opt_val=$('select[name="operator_value[]"').map(function() {return $( this ).val();}).get();
										
										//.val();
										//opt_val=opt_val.toString();
										//alert(opt_val);
									}
									else if($(this).find('input').not('[type="hidden"]').length){
										//console.log("inb11");
										opt_val=$(this).find('input').val();
									} else if($(this).find('select#flags').length){ 
										//console.log("inb");
										opt_val=$(this).find('select#flags').val();
									} else if($(this).find('select#select-field-value').length){
										opt_val=$(this).find('select#select-field-value').val();
										//console.log($(this).find('select#select-field-value option:selected').text());
									}
									/*console.log(opt+ ' '+opt_val);
									
									
									if( ($.trim(opt)!="" && $.trim(opt_val)=="") || ($.trim(opt)=="" && $.trim(opt_val)!="")){
									value_existoperator_val=false;
									return false;
									}*/
								});
								console.log(value_existoperator_val); //return false;
								if(!value_existoperator_val){
									alert("Please select operator and it's value.");
									return false;
								}
								// filter popup option
								var form = $('#filter-popup-option').serialize();
								
								// Ajax Post
								$.ajax({
									type : 'post',
									url:baseUrl+'custom-report/pop-up-option-select&id='+id,
									data: form,
									beforeSend: function (data) {showLoader();},
									success: function(response){
										// change flag to 1
										// change flag to 1
										$("#report-type-format-dates #is_change_form").val('1');
										$("#is_change_form_main").val('1');
										
										$("#filter_value_"+id).remove();
										if(response!=""){	
											$("#filter_icon_"+id).find('span').addClass('text-danger');
											// Append filter custom values
											$('#filter_custom_values').append("<input type='hidden' data-id='"+id+"' id='filter_value_"+id+"' name='filter_value[]' class='filter_value' value='"+response+"' />");
										}else{
											$("#filter_icon_"+id).find('span').removeClass('text-danger');
										}
									}
								});
								
								// remove dialog box 
								$(this).dialog('destroy').remove(); 
								hideLoader();	 
						    }
						}
					]
				});	
			}
		});
	}
	function post_step(){
		if($( "ul#table_field_container li" ).length > 0 ){
			if(($('#reportsusersaved-report_format_id option:selected').text().toLowerCase() == 'chart' && (($('#reportsusersaved-chart_format_id option:selected').text().toLowerCase() == 'pie' && $('.chart-values').length!=1) || ($('#reportsusersaved-chart_format_id option:selected').text().toLowerCase() != 'pie' && $('.chart-values').length!=2)))){
				alert("Please adjust process, Chart Axis is not added properly.");
				return false;
			} else {
				var URL = baseUrl+'custom-report/preview-report';
				//if($('#reportsusersaved-report_format_id').val()==2){
					//var URL = baseUrl+'custom-report/preview-chart-report';
				//}
				var form = $('#report-type-format-dates').serialize();
				// Ajax Post
				$.ajax({
					type : 'post',
					url:URL,
					data: form,
					beforeSend: function (data) {showLoader();$("#preview-save-run").html(null);$("#totalrecordcnt").html(0);},
					success: function(response){
						$( "#btn_add_chart" ).attr("disabled",true);
						hideLoader();
						//$( "#tabs-step-1" ).hide();
						$( "#tabs-step-2" ).hide();
						$( "#tabs-step-3" ).show();
						$("#preview-save-run").html(response);
						var objselect2=$("#reportsusersaved-report_type_id option:selected").text();
						//$(".sub-heading").html('<a href="javascript:void(0);" title="Step 3: Preview Tabular Report" class="tag-header-black">Step 3: Preview Tabular Report<div style="float: right"><?=$model->custom_report_name?></div></a>');
						$(".sub-heading").html('<a href="javascript:void(0);" class="tag-header-black" title="Step 3: Preview Tabular Report">Step 3: Preview Tabular Report</a><div style="float: right">'+objselect2+'</div>');
						<?php if($flag=='run'){?>
							$(".sub-heading").html('<?=$model->custom_report_name?>');
						<?php }?>
						<?php if($model->chart_format_id!=0 && $flag=='run') {?>
							$( "#tabs-step-2" ).hide();
							$( "#tabs-step-1" ).hide();
							$( "#tabs-step-3" ).hide(); 
							addchart();
						<?php }?>
						
					},
					complete: function(response){
						if(response.status==200){
							createView();
						}else{
							location.href='<?php echo $allReports_url?>';
						}
					},
					error :function(){
						hideLoader();
						alert('There is an Error in the Report Query Execution. Click on the OK button to view the Error Details.');
						alert(response.responseText);
						return false;
					}
				});
			}
		} else {
			alert("Please add fields to display on Report");
			return false;
		}
	}
	function createView(){
				var URL = baseUrl+'custom-report/preview-report&createview=true';
				var form = $('#report-type-format-dates').serialize();
				$.ajax({
					type : 'post',
					url:URL,
					data: form,
					success: function(response){
						$("#totalrecordcnt").html(response);
						$( "#btn_add_chart" ).removeAttr("disabled");
					},
					error :function(){
					}
				});
	}
</script>
