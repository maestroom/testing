<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
?>
<div class="rows panel-body" title="Tip: Simply select the function and data from the tabular report that you need to summarize to generate the chart report.  For example, ‘I want to see the COUNT of PROJECT ID by CLIENT NAME.’">
		<div class="form-group col-sm-5 required">
					<div class="row input-field">
						<div class="col-md-4">
							<label class="form_label font13" for="x_function">I want to see the</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[x_fn]',
    'data' => ['sum'=>'Sum','count'=>'Count'],
    'options' => ['placeholder' => 'Select X Function', "id"=>"x_function",'aria-required' => 'true','nolabel' => true],
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
							<label class="form_label font13" for="x_data">of</label>
						</div>
						<div class="col-md-10">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[x_data]',
    'data' => $field_disply_with_table,
    'options' => ['placeholder' => 'Select X Data', "id"=>"x_data",'aria-required' => 'true','nolabel' => true],
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
							<label class="form_label font13" for="y_data">by</label>
						</div>
						<div class="col-md-10">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[y_data]',
    'data' => $field_disply_with_table,
    'options' => ['placeholder' => 'Select Y Data', "id"=>"y_data",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>		
		</div>	
		<h6 class='font14'><b>Display By</b></h6>
		<div class="rows panel-body" title="Tip: The application will automatically default an appropriate ‘Display by’ option, but you can adjust if needed.">
			<div class="col-sm-5">
				<div class="form-group  required">
					<div class="row input-field">
						<div class="col-md-4">
							<label class="form_label font13" for="x_function_display_by">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[x_fn_display]',
    'data' => [],
    'options' => ['placeholder' => 'Select X Function Display By', "id"=>"x_function_display_by",'aria-required' => 'true','nolabel' => true],
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
							<label class="form_label" for="y_data_display_by">&nbsp;&nbsp;&nbsp;&nbsp;</label>
						</div>
						<div class="col-md-10">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[y_data_display]',
    'data' => [],
    'options' => ['placeholder' => 'Select Y Data Display By', "id"=>"y_data_display_by",'aria-required' => 'true','nolabel' => true],
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
<?php if(isset($post_data['ReportsUserSaved']['x_data'])) {?>
<?php if(isset($post_data['ReportsUserSaved']['x_data'])){?>$('#x_data').val('<?=$post_data['ReportsUserSaved']['x_data']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['x_fn'])){?> $('#x_function').val('<?=$post_data['ReportsUserSaved']['x_fn']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['x_fn']) && $post_data['ReportsUserSaved']['x_fn']=='sum'){?>
$("#x_function_display_by").empty();
$("#x_function_display_by").append('<option value="Number">Number</option><option value="Currency">Currency</option>');
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['x_fn']) && $post_data['ReportsUserSaved']['x_fn']=='count'){?>
	$("#x_function_display_by").empty();
	$("#x_function_display_by").append('<option value="Number">Number</option>');
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['y_data'])){?>$('#y_data').val('<?=$post_data['ReportsUserSaved']['y_data']?>').change();

var ftype=$('#field_types option[value="<?=$post_data['ReportsUserSaved']['y_data']?>"]').text();
			$("#y_data_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#y_data_display_by").append('<option value="Text">Text</option>');
			}
			if(ftype == 'INT'){
				$("#y_data_display_by").append('<option value="Number">Number</option>');
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#y_data_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
			}
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['x_fn_display'])){?>$('#x_function_display_by').val('<?=$post_data['ReportsUserSaved']['x_fn_display']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['y_data_display'])){?>$('#y_data_display_by').val('<?=$post_data['ReportsUserSaved']['y_data_display']?>').change();<?php }?>
<?php }else{ ?>
<?php if(isset($model->x_data)){?>$('#x_data').val('<?=$model->x_data?>').change();<?php }?>
<?php if(isset($model->x_fn)){?> $('#x_function').val('<?=$model->x_fn?>').change();<?php }?>
<?php if(isset($model->x_fn) && $model->x_fn=='sum'){?>
$("#x_function_display_by").empty();
$("#x_function_display_by").append('<option value="Number">Number</option><option value="Currency">Currency</option>');
<?php }?>
<?php if(isset($model->x_fn) && $model->x_fn=='count'){?>
	$("#x_function_display_by").empty();
	$("#x_function_display_by").append('<option value="Number">Number</option>');
<?php }?>
<?php if(isset($model->y_data)){?>$('#y_data').val('<?=$model->y_data?>').change();

var ftype=$('#field_types option[value="<?=$model->y_data?>"]').text();
			$("#y_data_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#y_data_display_by").append('<option value="Text">Text</option>');
			}
			if(ftype == 'INT'){
				$("#y_data_display_by").append('<option value="Number">Text/Number</option>');
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#y_data_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
			}
<?php }?>
<?php if(isset($model->x_fn_display)){?>$('#x_function_display_by').val('<?=$model->x_fn_display?>').change();<?php }?>
<?php if(isset($model->y_data_display)){?>$('#y_data_display_by').val('<?=$model->y_data_display?>').change();<?php }?>

<?php }?>
	
	
	$(document).ready(function(){
		$('#x_function').on('change',function(){
			var val=this.value;
			if(val=='sum'){
				$("#x_function_display_by").empty();
				$("#x_function_display_by").append('<option value="Number">Number</option><option value="Currency">Currency</option>');
			}
			if(val=='count'){
				$("#x_function_display_by").empty();
				$("#x_function_display_by").append('<option value="Number">Number</option>');
			}
			$("#x_function_display_by").val('Number').change();
		});
		$('#y_data').on('change',function(){
			var ftype=$('#field_types option[value="'+this.value+'"]').text();
			$("#y_data_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#y_data_display_by").append('<option value="Text">Text</option>');
				$("#y_data_display_by").val('Text').change();
			}
			if(ftype == 'INT'){
				$("#y_data_display_by").append('<option value="Number">Text/Number</option>');
				$("#y_data_display_by").val('Number').change();
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#y_data_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
				$("#y_data_display_by").val('Weeks').change();
			}
		});
		$("#x_data").on('change',function(){
			if($('#x_function').val()=='sum'){
			id=this.value;
				if(id !=""){
					next_id = $("#field_val option[value='"+id+"']").text();
					type=$("#field_type option[value='"+next_id+"']").text();
					if (typeof type  !== "undefined"){
						if(type == 'varchar' || type == 'datetime' || type == 'text'){
							alert('You can not select a non-numeric field when the Sum option is selected.');
							$("#x_data").val(null).change();
							return false;
						}
					}
				}
			}
		});
	});
</script>
<noscript></noscript>
