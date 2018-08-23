<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
?>
<div class="rows" title="Tip: Simply select the function and data from the tabular report that you need to summarize to generate the chart report.  For example, ‘I want to see the COUNT of PROJECT ID by CLIENT NAME.’">
	<div class="form-group required">
		<div class="row input-field">
			<div class="col-md-2 step4_S1div1">
				<label for="y_function" class="form_label font13">I want to see the </label>
			</div>
			<div class="col-md-2">
				<?php echo Select2::widget(['name' => 'ReportsUserSaved[y_fn]','data' => ['sum'=>'Sum','count'=>'Count'],'options' => ['placeholder' => 'Select Y Function', "id"=>"y_function",'aria-required' => 'true','nolabel' => true],'pluginOptions' => ['allowClear' => true],]);?>
				<div class="help-block"></div>
			</div>
			<div class="col-md-2 step4_S1div2">
				<label for="y_data" class="form_label font13">of </label>
			</div>
			<div class="col-md-2">
				<?php echo Select2::widget(['name' => 'ReportsUserSaved[y_data]','data' => $field_disply_with_table,'options' => ['placeholder' => 'Select Y Data', "id"=>"y_data",'aria-required' => 'true','nolabel' => true],'pluginOptions' => ['allowClear' => true],]);?>
				<div class="help-block"></div>
			</div>
			<div class="col-md-1 step4_S1div3">
				<label for="series" class="form_label font13">grouped by </label>
			</div>
			<div class="col-md-2">
				<?php echo Select2::widget(['name' => 'ReportsUserSaved[series]','data' => $field_disply_with_table,'options' => ['placeholder' => 'Select Series', "id"=>"series",'aria-required' => 'true','nolabel' => true],'pluginOptions' => ['allowClear' => true],]);?>
				<div class="help-block"></div>
			</div>
			<div class="col-md-1 step4_S1div4">
				<label for="x_data" class="form_label font13">by </label>
			</div>
			<div class="col-md-2">
				<?php echo Select2::widget(['name' => 'ReportsUserSaved[x_data]','data' => $field_disply_with_table,'options' => ['placeholder' => 'Select X Data', "id"=>"x_data",'aria-required' => 'true','nolabel' => true],'pluginOptions' => ['allowClear' => true],]);?>
				<div class="help-block"></div>
			</div>
		</div>
	</div>
	<h6 class='font14'><b>Display By</b></h6>
	<div class="form-group required" title="Tip: The application will automatically default an appropriate ‘Display by’ option, but you can adjust if needed.">
		<div class="row input-field">
			<div class="col-md-2 step4_S2div1">
				<label for="y_function_display_by" class="form_label font13">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
			</div>
			<div class="col-md-2">
				<?php echo Select2::widget(['name' => 'ReportsUserSaved[y_fn_display]','data' => [],'options' => ['placeholder' => 'Select Y Function Display By', "id"=>"y_function_display_by",'aria-required' => 'true','nolabel' => true],'pluginOptions' => ['allowClear' => true],]);?>
				<div class="help-block"></div>
			</div>
			<!-- -->
			<div class="col-md-2 step4_S2div2">
			</div>
			<div class="col-md-2">
			</div>
			<div class="col-md-1 step4_S2div3">
			</div>
			<div class="col-md-2">
			</div>
			<!-- -->
			<div class="col-md-1 step4_S2div4">
					<label for="x_data_display_by" class="form_label font13">&nbsp;&nbsp;&nbsp;&nbsp;</label>
			</div>
			<div class="col-md-2">
				<?php echo Select2::widget(['name' => 'ReportsUserSaved[x_data_display]','data' => [],'options' => ['placeholder' => 'Select X Data Display By', "id"=>"x_data_display_by",'aria-required' => 'true','nolabel' => true],'pluginOptions' => ['allowClear' => true],]);?>
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
<?php /*?><div class="rows panel-body" title="Tip: Simply select the function and data from the tabular report that you need to summarize to generate the chart report.  For example, ‘I want to see the COUNT of PROJECT ID by CLIENT NAME.’">
		<div class="form-group col-sm-4 required">
					<div class="row input-field">
						<div class="col-md-5">
							<label class="form_label font13" for="y_function">I want to see the</label>
						</div>
						<div class="col-md-7">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[y_fn]',
    'data' => ['sum'=>'Sum','count'=>'Count'],
    'options' => ['placeholder' => 'Select Y Function', "id"=>"y_function"],
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
    'options' => ['placeholder' => 'Select Y Data', "id"=>"y_data"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
		<div class="form-group col-sm-3 required">
					<div class="row input-field">
						<div class="col-md-5">
							<label class="form_label font13" for="series">grouped by</label>
						</div>
						<div class="col-md-7">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[series]',
    'data' => $field_disply_with_table,
    'options' => ['placeholder' => 'Select Series', "id"=>"series"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>			
		<div class="form-group col-sm-2 required">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label font13" for="x_data">by</label>
						</div>
						<div class="col-md-9">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[x_data]',
    'data' => $field_disply_with_table,
    'options' => ['placeholder' => 'Select X Data', "id"=>"x_data"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>		
		</div>	
		<div class="rows">
			<div class="col-sm-12"></div>
		</div>		
		<h6 class='font14'><b>Display By</b></h6>
		<div class="rows panel-body"  title="Tip: The application will automatically default an appropriate ‘Display by’ option, but you can adjust if needed.">
			<div class="col-sm-4">
				<div class="form-group  required">
					<div class="row input-field">
						<div class="col-md-5">
							<label class="form_label font13" for="y_function_display_by">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						</div>
						<div class="col-md-7">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[y_fn_display]',
    'data' => [],
    'options' => ['placeholder' => 'Select Y Function Display By', "id"=>"y_function_display_by"],
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
						<div class="col-md-4">
						</div>
						<div class="col-md-8">
						</div>
					</div>
				</div>		
			</div>
			<div class="col-sm-2">
					<div class="form-group  required">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="x_data_display_by">&nbsp;&nbsp;&nbsp;&nbsp;</label>
						</div>
						<div class="col-md-9">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[x_data_display]',
    'data' => [],
    'options' => ['placeholder' => 'Select X Data Display By', "id"=>"x_data_display_by"],
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
<?php */?>
<script>
<?php if(isset($post_data['ReportsUserSaved']['y_data'])){?>
<?php if(isset($post_data['ReportsUserSaved']['y_data'])){?>$('#y_data').val('<?=$post_data['ReportsUserSaved']['y_data']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['series'])){?>$('#series').val('<?=$post_data['ReportsUserSaved']['series']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['y_fn'])){?> $('#y_function').val('<?=$post_data['ReportsUserSaved']['y_fn']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['y_fn']) && $post_data['ReportsUserSaved']['y_fn']=='sum'){?>
$("#y_function_display_by").empty();
$("#y_function_display_by").append('<option value="Number">Number</option><option value="Currency">Currency</option><option value="Percentage">Percentage</option>');
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['y_fn']) && $post_data['ReportsUserSaved']['y_fn']=='count'){?>
	$("#y_function_display_by").empty();
	$("#y_function_display_by").append('<option value="Number">Number</option><option value="Percentage">Percentage</option>');
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['x_data'])){?>$('#x_data').val('<?=$post_data['ReportsUserSaved']['x_data']?>').change();

var ftype=$('#field_types option[value="<?=$post_data['ReportsUserSaved']['x_data']?>"]').text();
			$("#x_data_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#x_data_display_by").append('<option value="Text">Text</option>');
			}
			if(ftype == 'INT'){
				$("#x_data_display_by").append('<option value="Number">Text/Number</option>');
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#x_data_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
			}
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['y_fn_display'])){?>$('#y_function_display_by').val('<?=$post_data['ReportsUserSaved']['y_fn_display']?>').change();<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['x_data_display'])){?>$('#x_data_display_by').val('<?=$post_data['ReportsUserSaved']['x_data_display']?>').change();<?php }?>
<?php }else{ ?>
<?php if(isset($model->y_data)){?>$('#y_data').val('<?=$model->y_data?>').change();<?php }?>
<?php if(isset($model->series)){?>$('#series').val('<?=$model->series?>').change();<?php }?>
<?php if(isset($model->y_fn)){?> $('#y_function').val('<?=$model->y_fn?>').change();<?php }?>
<?php if(isset($model->y_fn) && $model->y_fn=='sum'){?>
$("#y_function_display_by").empty();
$("#y_function_display_by").append('<option value="Number">Number</option><option value="Currency">Currency</option><option value="Percentage">Percentage</option>');
<?php }?>
<?php if(isset($model->y_fn) && $model->y_fn=='count'){?>
	$("#y_function_display_by").empty();
	$("#y_function_display_by").append('<option value="Number">Number</option><option value="Percentage">Percentage</option>');
<?php }?>
<?php if(isset($model->x_data)){?>$('#x_data').val('<?=$model->x_data?>').change();
var ftype=$('#field_types option[value="<?=$model->x_data?>"]').text();
			$("#x_data_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#x_data_display_by").append('<option value="Text">Text</option>');
			}
			if(ftype == 'INT'){
				$("#x_data_display_by").append('<option value="Number">Text/Number</option>');
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#x_data_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
			}
<?php }?>
<?php if(isset($model->y_fn_display)){?>$('#y_function_display_by').val('<?=$model->y_fn_display?>').change();<?php }?>
<?php if(isset($model->x_data_display)){?>$('#x_data_display_by').val('<?=$model->x_data_display?>').change();<?php }?>
<?php }?>
	$(document).ready(function(){
		$('#y_function').on('change',function(){
			var val=this.value;
			if(val=='sum'){
				$("#y_function_display_by").empty();
				$("#y_function_display_by").append('<option value="Number">Number</option><option value="Currency">Currency</option>');
			}
			if(val=='count'){
				$("#y_function_display_by").empty();
				$("#y_function_display_by").append('<option value="Number">Number</option>');
			}
			$("#y_function_display_by").val('Number').change();
		});
		$('#x_data').on('change',function(){
			var ftype=$('#field_types option[value="'+this.value+'"]').text();
			$("#x_data_display_by").empty();
			if(ftype == 'VARCHAR' || ftype == 'TINYINT'){
				$("#x_data_display_by").append('<option value="Text">Text</option>');
				$("#x_data_display_by").val('Text').change();
			}
			if(ftype == 'INT'){
				$("#x_data_display_by").append('<option value="Number">Text/Number</option>');
				$("#x_data_display_by").val('Number').change();
			}
			if(ftype == 'DATE' || ftype == 'DATETIME'){
				$("#x_data_display_by").append('<option value="Days">Days</option><option value="Weeks">Weeks</option><option value="Months">Months</option><option value="Years">Years</option>');
				$("#x_data_display_by").val('Weeks').change();
			}
		});
		$("#y_data").on('change',function(){
			if($('#y_function').val()=='sum'){
				id=this.value;
				if(id !=""){
					next_id = $("#field_val option[value='"+id+"']").text();
					type=$("#field_type option[value='"+next_id+"']").text();
					if (typeof type  !== "undefined"){
						if(type == 'varchar' || type == 'datetime' || type == 'text'){
							alert('You can not select a non-numeric field when the Sum option is selected.');
							$("#y_data").val(null).change();
							return false;
						}
					}
				}
			}
		});
	});
</script>
<noscript></noscript>