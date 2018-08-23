<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
?>
		<div class="rows panel-body" title="Tip: Simply select the function and data from the tabular report that you need to summarize to generate the chart report.  For example, ‘I want to see the COUNT of PROJECT ID by CLIENT NAME.’">
		<div class="form-group col-sm-5 required">
					<div class="row input-field">
						<div class="col-md-4">
							<label class="form_label font13" for="item_function">I want to see the</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[item_fn]',
    'data' => ['proportion'=>'Proportion'],
    'options' => ['placeholder' => 'Select Item Function', "id"=>"item_function",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>		
		<div class="form-group col-sm-3 required">
					<div class="row input-field">
						<div class="col-md-2">
							<label class="form_label font13" for="y_data">of</label>
						</div>
						<div class="col-md-10">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[y_data]',
    'data' => $field_disply_with_table,
    'options' => ['placeholder' => 'Select Item', "id"=>"y_data",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>	
		<div class="form-group col-sm-3 required">
					<div class="row input-field">
						<div class="col-md-2">
							<label class="form_label font13" for="series">by</label>
						</div>
						<div class="col-md-10">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[series]',
    'data' => $field_disply_with_table,
    'options' => ['placeholder' => 'Select Series', "id"=>"series",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>		
		</div>	
		<h6 class='font14'><b>Display By</b></h6>
		<div class="rows  panel-body"  title="Tip: The application will automatically default an appropriate ‘Display by’ option, but you can adjust if needed.">
			<div class="col-sm-5">
				<div class="form-group  required">
					<div class="row input-field">
						<div class="col-md-4">
							<label class="form_label font13" for="item_function_display_by">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[item_fn_display]',
    'data' => ["Number"=>"Number","Percentage"=>"Percentage","Number_Percentage"=>"Number & Percentage"],
    'options' => ['placeholder' => 'Select Item Function Display By', "id"=>"item_function_display_by",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>	
			</div>
			<div class="col-sm-3">
				<div class="form-group  required">
					<div class="row input-field">
						<div class="col-md-4">
						</div>
						<div class="col-md-8">
						</div>
					</div>
				</div>		
			</div>
			<div class="col-sm-3">
					<div class="form-group  required">
					<div class="row input-field">
						<div class="col-md-2">
							<label class="form_label" for="series1_display_by">&nbsp;&nbsp;&nbsp;&nbsp;</label>
						</div>
						<div class="col-md-10">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[series1_display]',
    'data' => [],
    'options' => ['placeholder' => 'Select Series Display', "id"=>"series1_display_by",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?>
<?php if(!empty($reportTypeFields)){?>
<select id="field_types" style="display:none;">
<?php foreach($reportTypeFields as $id=>$ftype){?>
	<option value="<?=$id?>"><?=$ftype?></option>
<?php }?>
</select>
<?php }?>
<div class="help-block"></div>
						</div>
					</div>
				</div>	
			
			</div>
		</div>
		
<script>
<?php if(isset($post_data['ReportsUserSaved']['y_data'])){?>	
<?php if(isset($post_data['ReportsUserSaved']['y_data'])){?>$('#y_data').val('<?=$post_data['ReportsUserSaved']['y_data']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['item_fn_display'])){?> $('#item_function_display_by').val('<?=$post_data['ReportsUserSaved']['item_fn_display']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['series'])){?>$('#series').val('<?=$post_data['ReportsUserSaved']['series']?>').change();
var ftype=$('#field_types option[value="<?=$post_data['ReportsUserSaved']['series']?>"]').text();
			$("#series1_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#series1_display_by").append('<option value="Text">Text</option>');
			}
			if(ftype == 'INT'){
				$("#series1_display_by").append('<option value="Number">Text/Number</option>');
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#series1_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
			}
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['series1_display'])){?>$('#series1_display_by').val('<?=$post_data['ReportsUserSaved']['series1_display']?>').change();<?php }?>
<?php } else {?>
<?php if(isset($model->y_data)){?>$('#y_data').val('<?=$model->y_data?>').change();<?php }?>
<?php if(isset($model->item_fn_display)){?> $('#item_function_display_by').val('<?=$model->item_fn_display?>').change();<?php }?>
<?php if(isset($model->series)){?>$('#series').val('<?=$model->series?>').change();
var ftype=$('#field_types option[value="<?=$model->series?>"]').text();
			$("#series1_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#series1_display_by").append('<option value="Text">Text</option>');
			}
			if(ftype == 'INT'){
				$("#series1_display_by").append('<option value="Number">Text/Number</option>');
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#series1_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
			}
<?php }?>
<?php if(isset($model->series1_display)){?>$('#series1_display_by').val('<?=$model->series1_display?>').change();<?php }?>
<?php }?>
$(document).ready(function(){
		$("#item_function").val("proportion").change();
		<?php if(isset($post_data['ReportsUserSaved']['item_fn_display'])){?>
			$('#item_function_display_by').val('<?=$post_data['ReportsUserSaved']['item_fn_display']?>').change();
		<?php }else{?>	
			$("#item_function_display_by").val("Number").change();
		<?php }?>
		$('#series').on('change',function(){
			var ftype=$('#field_types option[value="'+this.value+'"]').text();
			$("#series1_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#series1_display_by").append('<option value="Text">Text</option>');
				$("#series1_display_by").val('Text').change();
			}
			if(ftype == 'INT'){
				$("#series1_display_by").append('<option value="Number">Text/Number</option>');
				$("#series1_display_by").val('Number').change();
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#series1_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
				$("#series1_display_by").val('Weeks').change();
			}
		});
	});
</script>
<noscript></noscript>
