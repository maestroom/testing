<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
//echo "<pre>",print_r($filter_data),print_r($reporttypefield),"</pre>";
if(isset($filter_data['operator_field_value']) && $field_theme_name!='Date'){
	if(count($filter_data['operator_field_value']) > 1){
		$filter_data['operator_value']=explode(",",$filter_data['operator_value'][0]);
	}
}
?>
<div class="row filter-parent">
	<!-- Form -->
	<form name="filter-popup-option" id="filter-popup-option" autocomplete="off">
		<input type="hidden" name="field_theme_name" id="field_theme_name" value="<?php echo $field_theme_name; ?>" />
		<input type="hidden" name="id" id="id" value="<?php echo $id ?>" />
		<input type="hidden" name="count" id="count" value="<?php if($filter_data['count']>0){ echo $filter_data['count'];} else{ echo 1;}?>" />
		<input type="hidden" id="has_lookup" value="<?php echo $has_lookup;?>" />
		<table class="table table-stripped" id="filter-option-popup">
			<tr>
				<td><a href="javascript:void(0);" title="Operator" class="tag-header-black">Operator</a></td>
				<?php if($field_theme_name!='Date'){ ?>
					<td id="filter_val_1"><a href="#" title="Value(s)" class="tag-header-black">Value(s)</a></td>
				<?php } ?>
				<?php if($has_lookup==true){ } else { ?>
						<td id="filter_val_2" class="operator-val" <?php if(!empty($filter_data['operator_value_new'])){ echo 'style="display:show;"';} else { echo 'style="display:none;"';} ?>><a href="#" title="Value(s)" class="tag-header-black">Value(s)</a></td>
						<td>
						<?php if($field_theme_name!='Date'){ ?>
							<a href="javascript:void(0);" id="add_operator"><em class="fa fa-plus text-primary" title="Add"></em></a>
						<?php } ?>
					</td>
				<?php } ?>
			</tr>
			<?php  
				if(!empty($filter_data)){ $i=0; ?>
					<?php foreach($filter_data['operator_field_value'] as $key => $vals){ ?>
						<tr <?= ($i==0)?'class="field-operator-id"':''; ?> id="operator_field_val_<?= $i ?>">
							<?php if($field_theme_name != 'Date'){ ?>
								<td>
									<select id="select-operator-value" name="operator_field_value[]" class="select-filter-operator" title="Select Operator">
										<option value=""></option>
											<?php foreach($filterdata as $val) { ?>
												<option value="<?= $val['id']; ?>" <?= ($vals==$val['id'])?"Selected":"";?> data-operator="<?= $val['field_operator']; ?>"><?= $val['field_operator']; ?></option>
											<?php } ?>
									</select>
								</td>
							<?php } ?>
							<?php if($field_theme_name == 'Date') { ?>
								<?php 
									$operation_key='';
									foreach($filterdata as $val) {
										if($val['field_operator'] == 'Between')
											$operation_key = $val['id'];	
									}
								?>
								<input type="hidden" id="select-operator-value" name="operator_field_value[]" value="<?php echo $operation_key; ?>" />
							<?php } ?>
							<td>
								<!-- field theme name flags -->
								<?php if($field_theme_name=='Flags') { ?>
									<select name="operator_value[]" id="flags" class="operator_value select-filter-operator">
										<option value=""></option>
										<option value="1" <?= $filter_data['operator_value'][$key]==1?'selected':''; ?>>True</option>
										<option value="0" <?= $filter_data['operator_value'][$key]==0?'selected':''; ?>>False</option>
									</select>
								<?php } ?>
								<!-- End -->
								
								<!-- field theme name Date -->
								<?php if($field_theme_name=='Date') { 
										$datekey="";
										if(trim(str_replace("-"," ",$filter_data['operator_value'][$key]))=='T'){
											$datekey="T";	
										}else if(trim(str_replace("-"," ",$filter_data['operator_value'][$key]))=='Y'){
											$datekey="Y";	
										}else if(trim(str_replace("-"," ",$filter_data['operator_value'][$key]))=='W'){
											$datekey="W";	
										}else if(trim(str_replace("-"," ",$filter_data['operator_value'][$key]))=='M'){
											$datekey="M";
										}else{
											$datekey="C";
										}
								?>
								<div class="input-group calender-group">
									<select name="operator_value[]" id="flags" class="operator_value select-filter-operator" onchange="if(this.value=='C'){$('#divrange_date_custom_1').show();}else{$('#divrange_date_custom_1').hide();}">
										<option value=""></option>
										<option value="T" <?php if($datekey=='T'){?>selected<?php }?>>Today</option>
										<option value="Y" <?php if($datekey=='Y'){?>selected<?php }?>>Yesterday</option>
										<option value="W" <?php if($datekey=='W'){?>selected<?php }?>>Last Week</option>
										<option value="M" <?php if($datekey=='M'){?>selected<?php }?>>Last Month</option>
										<option value="C" <?php if($datekey=='C'){?>selected<?php }?>>Custom Range</option>
									</select>
									<div id="divrange_date_custom_1" style="display:<?php if($datekey=='C'){?>block<?php }else{?>none<?php }?>;">
									<br>
										<?php 
											$dateval="";
											if($datekey=='C'){
												$dateval=$filter_data['operator_value'][$key]." - ".$filter_data['operator_value_new'][$key];
											}else{
												$filter_data['operator_value'][$key]="";
											}
											echo DateRangePicker::widget([
										    	'name' => 'operator_value_custom[]',
										    	'id' => 'range_date_'.($i+1),
										    	'options' => ['class' => 'date_pickers start_date form-control operator_value'],
										    	'presetDropdown' => false,
										    	'value' => $filter_data['operator_value'][$key],
										    	'pluginOptions'=>[
											        'locale'=>[
											            'format'=>'MM/DD/YYYY'
											        ]
											    ],
										    	'hideInput' => true
											]); 
										?>
										</div>
									</div>	
								<?php } ?>
								<!-- End -->
								
								<!-- field theme name -->
								<?php if($field_theme_name=='String' && $has_lookup == false){ ?>
									<input type="text" class="form-control operator_value" name="operator_value[]" id="operator_value" value="<?= $filter_data['operator_value'][$key]; ?>" />
								<?php } ?>
								<!-- End -->
								
								<!-- field theme name -->
								<?php if($field_theme_name=='Integer' && $has_lookup == false){ ?>
									<input type="text" class="form-control operator_value" name="operator_value[]" id="operator_value" value="<?= $filter_data['operator_value'][$key]; ?>" />
								<?php } else if($has_lookup == true && $has_lookupsql == false) { $atr="multiple";?>
									<select id="select-field-value" <?=$atr?>  name="operator_value[]" class="select-filter-operator">
										<option value=""></option>
										<?php 
											if(!empty($lookupfinal_data)){
												foreach($lookupfinal_data as $key1 => $text) {
											?>
											<option value="<?= $key1 ?>" <?php if(count(explode(",",$filter_data['operator_value'][$key])) > 1) { if(in_array($key1,explode(",",$filter_data['operator_value'][$key]))) { echo 'selected';}} else { if($filter_data['operator_value'][$key]==$key1){echo 'selected="selected"';}} ?>><?= $text ?></option>
											<?php 
											}
										} ?>
									</select>
								<?php } else if($has_lookup == true && $has_lookupsql == true) {
									$url = \yii\helpers\Url::to(['custom-report/selectloadmore','report_type_id'=>$report_type_id,'field_id'=>$field_id]);
									
									//select 2 with ajax and enter 1 or more
									echo Select2::widget([
										'name'=>'operator_value[]',
										'value' => array_keys($filtervalue_selected),
										'initValueText' => array_values($filtervalue_selected), // set the initial display text
										'options' => ['id'=>'select-field-value','multiple'=>true,'class'=>"select-filter-operator"],
										'showToggleAll' => false,
										'pluginOptions' => [
											//'tags' => $filtervalue_selected,
											'allowClear' => true,
											'minimumInputLength' =>1,
											'language' => [
												'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
											],
											'ajax' => [
												'url' => $url,
												'dataType' => 'json',
												'data' => new JsExpression('function(params) { return {q:params.term}; }')
											],
											'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
											'templateResult' => new JsExpression('function(city) { return city.text; }'),
											'templateSelection' => new JsExpression('function (city) { return city.text; }'),
										],
									]);
								?>
								<?php }?>
								<!-- End -->
							</td>
							<?php if($has_lookup==true){ }else{ ?>
							<td class="operator-val" <?php if(isset($filter_data['operator_value_new'][$key]) && $filter_data['operator_value_new'][$key]!=""){ echo 'style="display:show;"';} else { echo 'style="display:none;"';} ?>>
								<!-- field theme name Date -->
								<?php if($field_theme_name=='Date'){ ?>
									<div class="input-group calender-group">
										<?php /* echo DateRangePicker::widget([
									    	'name'=>'operator_value_new[]',
									    	'id' => 'range_date_'.($i+1),
									    	'options' => ['class' => 'date_pickers start_date form-control operator_value'],
									    	'presetDropdown'=>true,
									    	'value'=>$filter_data['operator_value_new'][$key],
									    	'hideInput'=>true
										]); */ ?>
									</div>	
								<?php } ?>
							<!-- End -->
						
							<!-- Field Theme Name -->
							<?php if($field_theme_name=='String' && $has_lookup == false){ ?>
								<input type="text" class="form-control operator_value" name="operator_value_new[]" id="operator_value_new" value="<?= $filter_data['operator_value_new'][$key]; ?>" />
							<?php } ?>
							<!-- End -->
							
							<!-- field theme name -->
							<?php if($field_theme_name=='Integer' && $has_lookup == false){ ?>
								<input type="text" class="form-control operator_value" name="operator_value_new[]" id="operator_value_new" value="<?= $filter_data['operator_value_new'][$key]; ?>" />
							<?php } else if($has_lookup == true && $has_lookupsql == false){ $atr="multiple"; ?>
								<select id="select-field-value-new" <?=$atr?> name="operator_value_new[]" class="select-filter-operator">
									<option value=""></option>
									<?php 
										if(!empty($lookupfinal_data)){
											foreach($lookupfinal_data as $key1 => $text) {
									?>
									<option value="<?= $key1 ?>" <?php if(count(explode(",",$filter_data['operator_value_new'][$key])) > 1) { if(in_array($key1,explode(",",$filter_data['operator_value_new'][$key]))) { echo 'selected';}} else { if($filter_data['operator_value_new'][$key]==$key1){echo 'selected="selected"';}} ?>><?= $text ?></option>
									<?php 
										}
									} ?>
								</select>
							<?php } else if($has_lookup == true && $has_lookupsql == true){
								//echo "herfgfage";
								$url = \yii\helpers\Url::to(['custom-report/selectloadmore','report_type_id'=>$report_type_id,'field_id'=>$field_id]);
								//select 2 with ajax and enter 1 or more
								echo Select2::widget([
									'name'=>'operator_value_new[]',
									'value' => array_keys($filtervalue_selected),
									'initValueText' => array_values($filtervalue_selected), // set the initial display text
									'options' => ['id'=>'select-field-value-new','multiple'=>true,'class'=>"select-filter-operator"],
									'pluginOptions' => [
										//'tags' => $filtervalue_selected,
										'allowClear' => true,
										'minimumInputLength' =>1,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],
										'ajax' => [
											'url' => $url,
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {q:params.term}; }')
										],
										'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
										'templateResult' => new JsExpression('function(city) { return city.text; }'),
										'templateSelection' => new JsExpression('function (city) { return city.text; }'),
									],
								]);
							?>	
							<?php }?>
							<!-- End -->
							</td>
						<?php if($i > 0){ // count value must be more than 1 ?>
							<td>
								<a href="javascript:void(0);" id="delete_operator" class="delete_operator" onClick="remove_filter(<?= $i; ?>);"><em class="fa fa-remove text-primary" title="Remove"></em></a>
							</td>
						<?php } ?>
						<?php }?>
						</tr>
					<?php $i++; } ?>
				<?php } else { ?>
					<tr class="field-operator-id">	
						<?php 
							$operation_key='';
							foreach($filterdata as $val){
								if($val['field_operator'] =='Between')
									$operation_key = $val['id'];	
							}
						?>
						<?php if($field_theme_name!='Date'){ ?>
							<td>
								<select id="select-operator-value" name="operator_field_value[]" class="select-filter-operator" title="Select Operator">
									<option value=""></option>
									<?php if(!empty($filterdata)) { foreach($filterdata as $val){ ?>
										<option value="<?= $val['id']; ?>" data-operator="<?= $val['field_operator']; ?>"><?= $val['field_operator']; ?></option>
									<?php } } ?>
								</select>
							</td>
						<?php } if($field_theme_name=='Date'){ ?>
							<input type="hidden" value="<?php echo $operation_key; ?>" id="select-operator-value" name="operator_field_value[]" />
						<?php } ?>

						<td>
							<!-- Field theme name flags -->
							<?php if($field_theme_name=='Flags'){ ?>
								<select name="operator_value[]" id="flags" class="operator_value select-filter-operator">
									<option value=""></option>
									<option value="1">True</option>
									<option value="0">False</option>
								</select>
							<?php } ?>
							<!-- End Field -->
							
							<!-- Date -->
							<?php if($field_theme_name=='Date'){ ?>
								<div class="input-group calender-group">
								<select name="operator_value[]" id="flags" class="operator_value select-filter-operator" onchange="if(this.value=='C'){$('#divrange_date_custom_1').show();}else{$('#divrange_date_custom_1').hide();}">
									<option value=""></option>
									<option value="T">Today</option>
									<option value="Y">Yesterday</option>
									<option value="W">Last Week</option>
									<option value="M">Last Month</option>
									<option value="C">Custom Range</option>
								</select>
								
								<div id="divrange_date_custom_1" style="display:none;">
								<br><br>
									<?php 
										echo DateRangePicker::widget([
									    	'name'=>'operator_value_custom[]',
									    	'id' => 'range_date_custom_1',
									    	'options' => ['class' => 'date_pickers start_date form-control operator_value'],
									    	'presetDropdown'=>false,
									    	'pluginOptions'=>[
										        'locale'=>[
										            'format'=>'MM/DD/YYYY'
										        ]
										    ],
									    	'hideInput'=>true
										]); 
									?>
								</div>
								</div> 
							<?php } ?>
							<!-- End Date -->
							
							<!-- Field Theme Name -->
							<?php if($field_theme_name=='String' && $has_lookup == false){ ?>
								<input type="text" class="form-control operator_value" name="operator_value[]" id="operator_value" />
							<?php } ?>
							<!-- End -->
							
							<!-- field theme name -->
							<?php if($field_theme_name=='Integer' && $has_lookup == false){ ?>
								<input type="text" class="form-control operator_value" name="operator_value[]" id="operator_value" value="<?= $filter_data['operator_value'][$key]; ?>" />
							<?php } else if($has_lookup == true &&  $has_lookupsql == false) { $atr="multiple";?>
								<select id="select-field-value" <?=$atr?>  name="operator_value[]" class="select-filter-operator">
									<option value=""></option>
									<?php 
									if(!empty($lookupfinal_data)){
										foreach($lookupfinal_data as $key1 => $text) {
									?>
									<option value="<?= $key1 ?>"><?= $text ?></option>
									<?php 
										}
									} ?>
								</select>
							<?php } else if($has_lookup == true &&  $has_lookupsql == true) { 
								$url = \yii\helpers\Url::to(['custom-report/selectloadmore','report_type_id'=>$report_type_id,'field_id'=>$field_id]);
								echo Select2::widget([
									'name'=>'operator_value[]',
									'options' => ['id'=>'select-field-value','multiple'=>true,'class'=>"select-filter-operator"],
									'showToggleAll' => false,
									'pluginOptions' => [
										'allowClear' => true,
										'minimumInputLength' =>1,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],
										'ajax' => [
											'url' => $url,
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {q:params.term}; }')
										],
										'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
										'templateResult' => new JsExpression('function(city) { return city.text; }'),
										'templateSelection' => new JsExpression('function (city) { return city.text; }'),
									],
								]);}?>
							<!-- End -->
						</td>
						<?php if($has_lookup==true){ }else{ ?>
						<td class="operator-val" style="display:none;">
							<!-- Date -->
							<?php if($field_theme_name=='Date'){ ?>
								<div class="input-group calender-group">
								<?php 
									echo DateRangePicker::widget([
								    	'name' => 'operator_value_new[]',
								    	'id' => 'range_date_new1',
								    	'options' => ['class' => 'date_pickers start_date1 form-control operator_value'],
								    	'presetDropdown' => true,
								    	'pluginOptions'=>[
									        'locale'=>[
									            'format'=>'MM/DD/YYYY'
									        ]
									    ],
								    	'hideInput'=>true
									]); 
								?>
								</div>	
							<?php } ?>
							<!-- End Date -->
							
							<!-- Field Theme Name -->
							<?php if($field_theme_name=='String' && $has_lookup == false){ ?>
								<input type="text" class="form-control operator_value" name="operator_value_new[]" id="operator_value_new" />
							<?php } ?>
							<!-- End -->
							
							<!-- field theme name -->
							<?php if($field_theme_name=='Integer' && $has_lookup == false){ ?>
								<input type="text" class="form-control operator_value" name="operator_value_new[]" id="operator_value_new" value="<?= $filter_data['operator_value_new'][$key]; ?>" />
							<?php } else if($has_lookup == true &&  $has_lookupsql == false){ $atr="multiple";?>
								<select id="select-field-value-new" <?=$atr?> name="operator_value_new[]" class="select-filter-operator">
									<option value=""></option>
									<?php 
										if(!empty($lookupfinal_data)){
											foreach($lookupfinal_data as $key1 => $text) { ?>
											<option value="<?= $key1 ?>" <?php if(count(explode(",",$filter_data['operator_value_new'][$key])) > 1) { if(in_array($key1,explode(",",$filter_data['operator_value_new'][$key]))) { echo 'selected';}} else { if($filter_data['operator_value_new'][$key]==$key1){echo 'selected="selected"';}} ?>><?= $text ?></option>
									<?php 
										}
									} ?>
								</select>
							<?php } else if($has_lookup == true &&  $has_lookupsql == true){
								$url = \yii\helpers\Url::to(['custom-report/selectloadmore','report_type_id'=>$report_type_id,'field_id'=>$field_id]);
								echo Select2::widget([
									'name'=>'operator_value_new[]',
									'options' => ['id'=>'select-field-value-new','multiple'=>true,'class'=>"select-filter-operator"],
									'showToggleAll' => false,
									'pluginOptions' => [
										'allowClear' => true,
										'minimumInputLength' =>1,
										'language' => [
											'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
										],
										'ajax' => [
											'url' => $url,
											'dataType' => 'json',
											'data' => new JsExpression('function(params) { return {q:params.term}; }')
										],
										'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
										'templateResult' => new JsExpression('function(city) { return city.text; }'),
										'templateSelection' => new JsExpression('function (city) { return city.text; }'),
									],
								]);
							}?>	
							<!-- End -->
							
						</td>
						<td>&nbsp;</td>
						<?php }?>
					</tr>
				<?php } ?>
		</table>
	</form>
</div>
<?php if(!empty($filter_data['operator_value_new'])){
	foreach($filter_data['operator_value_new'] as $k=>$opt_val_new){
		if(trim($opt_val_new)==""){unset($filter_data['operator_value_new'][$k]);}
	}
}?>
<?php if(trim($reporttypefield->report_condition)!=""){?>
Default Condition => <b><?=trim($reporttypefield->report_condition)?></b>
<?php }?>
<script>
/** Add Operator **/
$(function(){
	<?php if(empty($filter_data['operator_value_new'])){?>
	$('.operator-val').hide();
	$("td#filter_val_1").html('<a href="#" title="Value(s)" class="tag-header-black">Value(s)</a>');
	<?php }?>

	// start date 
	<?php  if(!empty($filter_data)){ $i=0; ?>
	<?php foreach($filter_data['operator_field_value'] as $key => $vals){ ?>
		var start_date = datePickerController.createDatePicker({             
			formElements: { 
				"start_date_<?=($i+1)?>": "%m/%d/%Y",
			},         
		});
		var start_date = datePickerController.createDatePicker({             
			formElements: { 
				"start_date_new<?=($i+1)?>": "%m/%d/%Y",
			},         
		});
	<?php $i++;} } else { ?>					
		var start_date = datePickerController.createDatePicker({             
			formElements: { 
				"start_date_1": "%m/%d/%Y",
			},         
		});
		var start_date = datePickerController.createDatePicker({             
			formElements: { 
				"start_date_new1": "%m/%d/%Y",
			},         
		});
	<?php } ?>
	
	// start date new
	var start_date_new = datePickerController.createDatePicker({             
		formElements: { "start_date_new": "%m/%d/%Y" },         
	});

	// select2 field operator
	$('.select-filter-operator').select2({
		//allowClear: false,
		dropdownParent: $('#availabl-price-points')
	});
	
	// assign count
	//var count = parseInt($('#count').val());	
	$('#add_operator').click(function() {
		var count = parseInt($('#count').val());		
		if(count < 3) {
			if($('#filter-option-popup tr').find('.date_pickers').length > 0) 
			{
				var testcount = count+1; // test count for datepicker
				var selectorParantObj = "<div class='input-group calender-group'><div class='drp-container input-group custom-date-container' id='range_date_1-container'><span class='input-group-addon'><em class='glyphicon glyphicon-calendar' title='Select Date'></em></span><span class='form-control text-right'><span class='pull-left'><span class='range-value'></span></span><strong class='caret date-caret'></strong><input type='text' class='date_pickers start_date form-control operator_value date-caret-input' name='operator_value[]' id='range_date_"+testcount+"' /></div></div>";
				
				/* Filter Option Tbody */
				$('#filter-option-popup tbody').append('<tr id="operator_field_val_'+count+'" class="field-operator-id"><td>'+selectorParantObj+'</td><td><a href="javascript:void(0);" id="delete_operator" class="delete_operator" onClick="remove_filter('+count+');"><em class="fa fa-remove text-primary" title="Remove"></em></a></td></tr>');
				$('#operator_field_val_'+count).find('select#select-operator-value').val(null);
				$('#operator_field_val_'+count).find('input').val(null);

				/* Set Time Out */
				setTimeout(function(){ 
					$('#range_date_'+testcount).daterangepicker({
						ranges: {
				           'Today': [moment(), moment()],
				           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				           'This Month': [moment().startOf('month'), moment().endOf('month')],
				           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				        }
				    });
				}, 500);

			} else {
				$('.select-filter-operator').select2('destroy'); // destroy select2
				var selectorParantObj = $('.field-operator-id').clone();
				
				var testcount = count+1; // test count for datepicker
				//$(selectorParantObj).find('input.start_date').prop('id','range_date_'+testcount);
				//$(selectorParantObj).find('input.start_date1').prop('id','range_date_new'+testcount);
				var selectorParant = selectorParantObj.html();

				$('#filter-option-popup').append('<tr id="operator_field_val_'+count+'" class="field-operator-id">'+selectorParant+'<td><a href="javascript:void(0);" id="delete_operator" class="delete_operator" onClick="remove_filter('+count+');"><em class="fa fa-remove text-primary" title="Remove"></em></a></td></tr>');
				$('#operator_field_val_'+count).find('select#select-operator-value').val(null);
				$('#operator_field_val_'+count).find('input').val(null);
			}

			$('.select-filter-operator').select2({
				//allowClear: false,
				dropdownParent: $('#availabl-price-points')
			});
			count++;
			$('#count').val(count); // Add count
		}
	});	
});

// delete operator
function remove_filter(loop){
	$('#operator_field_val_'+loop).closest('tr').remove();
	var count = parseInt($('#count').val());
	count--; 
	$('#count').val(count); // Decrease count value
	$('.select-filter-operator').select2('destroy'); // destroy select2
	$("#filter-option-popup tr").each(function(){
		$(this).find('.select-filter-operator').select2({
			allowClear: false,
			dropdownParent: $('#availabl-price-points')
		});
	});
	
}
/** End **/

$(document).on('change','.select-filter-operator',function(){
	var data = $(this).val();
	var data_type = $(this).find('option:selected').attr('data-operator');
	var tr_obj = $(this).closest('tr');
	// Between Dropdown with two text box and/or date picker
	if(data_type=='Between'){
		$(tr_obj).find('.operator-val').show();
		$("td#filter_val_1").html('Value1(s)');
		$("td#filter_val_2").html('Value2(s)');
	} else {
		$(tr_obj).find('.operator-val').hide();
		$("td#filter_val_1").html('Value(s)');
	}
});
</script>
<noscript></noscript>
