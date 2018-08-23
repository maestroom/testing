<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;
//echo "<pre>",print_r($field_list),"</pre>";
?>
<div class="mycontainer">
	<div class="rows">
		
			<div class="col-sm-4 table-field-operator-id">
				<!-- filter_data -->
				<!-- Primary Tables -->
				<div class="form-group">
					<select name="select_primary_table" id="select-primary-table" class="form-control select-primary-table">
						<option value="<?= $table_name ?>" selected="selected" ><?= $table_name ?></option>
					</select>
				</div>
				<!-- Secondary Fields -->
				<div class="pr-field-table field-operator-id">
					<select class="form-control select-primary-table-field" id="select-primary-table-field" name="primary_table_fields_name">
						<option value=""></option>
					<?php 
					if(!empty($field_list)) {
						foreach($field_list as $field) { 
						?>
							<option value="<?= $field ?>"><?= $field ?></option>
						<?php 
						}
					} ?>
					</select>
				</div>
			</div>
			
			<!-- Field join type -->
			<div class="col-sm-4 field-join-type">
				<div class="form-group">
					<select name="join-type" id="join-type" class="form-control join-type">
						<option value="">Select Join Type</option>
						<option value="1">LEFT JOIN</option>
						<option value="2">INNER JOIN</option>
						<option value="3">RIGHT JOIN</option>
					</select>
				</div>
			</div>
			
			<!-- secondary field operator -->	
			<div class="col-sm-4 secondary-field-operator-id">
				<div class="form-group">
					<select name="select_secondary_table" id="select-secondary-table-list" class="form-control select-secondary-table" onChange="get_secondary_field_lists();">
						<option value=""></option>
						<?php 
						if(!empty($othertableList)){
							foreach($othertableList as $table){ 
						?>
							<option value="<?= $table ?>" ><?= $table ?></option>
						<?php }
						} ?>
					</select>
				</div>
				<!-- Secondary Fields -->
				<div class="pr-field-table field-operator-id" id="div-secondary-table-fields">
					
				</div>
			</div>
			<!-- end -->
		
	</div>
	<div class="ui-dialog-note">
	<div class="col-sm-12 text-left">LEFT (OUTER) JOIN: returns all rows from the LEFT-hand table1 and only those rows from table2 where the joined fields are equal.</div>
	<div class="col-sm-12 text-left">RIGHT (OUTER) JOIN: returns all rows from the RIGHT-hand table2 and only those rows from table1 where the joined fields are equal.</div>
	<div class="col-sm-12 text-left">(INNER) JOIN: returns all rows from both table1 and table2 where the joined fields are equal.</div>
	</div>
</div>
<!-- Select Primary tables -->
<script>
	function get_secondary_field_lists(){
		var scr_table = $('#select-secondary-table-list').val();
		$.ajax({
			url:baseUrl+'report-management/manage-secondary-table-field-lists',
			beforeSend:function () {showLoader();},
			type:'post',
			data:{scr_table:scr_table},
			success:function(response){
				$('#div-secondary-table-fields').html(response);
				hideLoader();		
			}
		});
		//$('#secondary_table_alias').val(scr_table);
	}
	$(function(){
		$('#manage-relationships .select-primary-table').select2({
			placeholder:'Select First Table',
			dropdownParent: $('#manage-relationships .table-field-operator-id'),
			dropdownCssClass : 'select-primary-table',
		});
		
		$('#manage-relationships .select-primary-table-field').select2({
			placeholder:'Select First Table Field',
			dropdownParent: $('#manage-relationships .table-field-operator-id'),
			dropdownCssClass : 'select-primary-table-field',
		});
		
		$('#manage-relationships .join-type').select2({
			placeholder:'Select Join Type',
			dropdownParent: $('#manage-relationships .field-join-type'),
			dropdownCssClass : 'join-type',
		});
		
		
		$('#manage-relationships .select-secondary-table').select2({
			placeholder:'Select Second Table',
			dropdownCssClass : 'select-primary-table',
			dropdownParent: $('#manage-relationships .secondary-field-operator-id'),
		});
		
	});
</script>
<noscript></noscript>
