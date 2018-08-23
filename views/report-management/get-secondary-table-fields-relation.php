<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var getTableSchemas */
?>
<input type="hidden" name="filter_data" id="filter_data" value="<?= $filter_data ?>" />
<!-- Primary Tables -->
<div class="form-group">
	<select name="select_secondary_table_list" id="select-secondary-table-list" class="select-secondary-table-list" onChange="get_secondary_field_lists();" placeholder="">
		<option value=""></option>
		<?php foreach($sr_table_fields as $key => $vtable){ ?>
			<?php $tablename=ucwords(str_replace("_",' ',str_replace('tbl_','',$key)));  ?>
				<option value="<?= $key ?>"><?= $tablename ?></option>
		<?php } ?>
	</select>
</div>
<div class="show-secondary-fields"></div>
<script>
	$(function(){
		
		$('.select-secondary-table-list').select2({
			placeholder:'Select Second Table',
			dropdownParent: $('.secondary-field-operator-id'),
			dropdownCssClass : 'select-primary-table-field-list',
		});
	});
	
	function get_secondary_field_lists(){
		var scr_table = $('#select-secondary-table-list').val();
		var filter_data = $('#filter_data').val();
		$.ajax({
			url:baseUrl+'report-management/get-secondary-table-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{scr_table:scr_table, filter_data:filter_data},
			success:function(response){
				$('.show-secondary-fields').html(response);
				hideLoader();		
			}
		});
	}
</script>
<noscript></noscript>
