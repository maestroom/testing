<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mycontainer">
	<div class="rows">
		
			<div class="col-sm-4 table-field-operator-id">
				<!-- filter_data -->
				<input type="hidden" name="primary_table" id="primary_table" value='<?= $data['primary_table'] ?>' />
				<input type="hidden" name="filter_data" id="filter_data" value='<?= $filter_data ?>' />
				<input type="hidden" name="filter_relation" id="filter_relation" value='<?= $filter_relation ?>' />
				
				<!-- Primary Tables -->
				<div class="form-group">
					<select name="select_primary_table" id="select-primary-table" class="form-control select-primary-table" onChange="get_primarytable_fields();">
						<option value=""></option>
						<?php foreach($table_fields as $key => $vtable){ ?>
							<?php $tablename=ucwords(str_replace("_",' ',str_replace('tbl_','',$key))); ?>
								<option value="<?= $key ?>" <?= $data['primary_table']==$key?'selected="selected"':''; ?> ><?= $tablename ?></option>
						<?php } ?>
					</select>
				</div>
				<!-- Secondary Fields -->
				<div class="pr-field-table field-operator-id"></div>
			</div>
			
			<!-- Field join type -->
			<div class="col-sm-4 field-join-type" style="display:none;">
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
			<div class="col-sm-4 secondary-field-operator-id"></div>
			<!-- end -->
		
	</div>
	<div class="ui-dialog-note">
	<div class="col-sm-12 text-left">(INNER) JOIN: Select records that have matching values in both tables.</div>
	<div class="col-sm-12 text-left">LEFT (OUTER) JOIN: Select records from the first (left-most) table with matching right table records.</div>
	<div class="col-sm-12 text-left">RIGHT (OUTER) JOIN: Select records from the second (right-most) table with matching left table records.</div>
	</div>
</div>
<style>
/*	.select-primary-table{width: 250px !important;}
	.join-type{width: 250px !important;}
	.select-primary-table-field{width: 250px !important;}
	.select-primary-table-field-list{width: 250px !important;}
	.select-secondary-table-list{width: 250px !important;}
	.select-secondary-table-field-list{width: 250px !important;}
*/
</style>
<!-- Select Primary tables -->
<script>
	get_primarytable_fields();
	function get_primarytable_fields(){
		var pr_table = $('#select-primary-table').val();
		var filter_data = $('#filter_data').val();
		$.ajax({
			url:baseUrl+'report-management/get-relationships-table-fields',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{pr_table:pr_table, filter_data:filter_data},
			dataType: "json", 
			success:function(response){
				hideLoader();
				$('.pr-field-table').html(response.primary_fields);
				$('.secondary-field-operator-id').html(response.secondary_fields);
				$('.field-join-type').show();
			}
		});
	}
	
	
	$(function(){
		
		$('#availabl-second-tables .select-primary-table').select2({
			placeholder:'Select First Table',
			dropdownParent: $('#availabl-second-tables .table-field-operator-id'),
			dropdownCssClass : 'select-primary-table',
		});
		
		
		$('#availabl-second-tables .join-type').select2({
			placeholder:'Select Join Type',
			dropdownParent: $('#availabl-second-tables .field-join-type'),
			dropdownCssClass : 'join-type',
		});
		
		
		$('#availabl-second-tables .select-secondary-table').select2({
			placeholder:'Select Second Table',
			dropdownParent: $('#availabl-second-tables .field-operator-id'),
		});
		$('#availabl-second-tables .select-secondary-table-field').select2({
			placeholder:'Select Second Field',
			dropdownParent: $('#availabl-second-tables .field-operator-id'),
		});
	});
</script>
<noscript></noscript>
