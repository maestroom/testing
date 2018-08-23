<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mycontainer">
	<table id="add-field-from-primary-table" class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0">
		<input type="hidden" name="is_change_form" id="is_change_form" value="0" />
		<tr>
			<th></th>
			<th class="tbl-selectall-option-th"><a href="javascript:void(0);" title="Select All" aria-label="Select All" class="tag-header-black">Select All</a></th>
		</tr>
		<tr>
			<?php if(!empty($tables_list)){ ?>
			<td class="field-operator-id">
					<select id="my_primaryheader" name="selected_table_name" class="custom-table-list" title="Select Table" onChange="get_table_field_list();">
						<option value=""></option>
							<?php 
							$selected = ($flag == 'addfields')?'selected="selected"':"";
							foreach($tables_list as $table_name) { ?>
							<option value="<?= $table_name['id'] ?>" <?= $selected ?> ><?= $table_name['table_name']; ?></option>						<?php } ?>
					</select>
				
			</td>
			<td class="tbl-selectall-option text-center" style="display:none;">
				<input type="checkbox" id="table_select_all" class="table-select-all check-all" name="table_lists" value="all" />
				<label for="table_select_all" class="table-select-all"></label>
			</td>
			<?php } else if($flag == 'relationship') { ?>
				<td colspan="2" align="center">No Related Tables Found</td>
			<?php } else { ?>
				<td colspan="2" align="center">No records found</td>
			<?php } ?>
		</tr>
	</table>
	
	<!-- table fields -->
	<div class="table-fields-lists"></div>
</div>
<script>
	/** change select event **/
	$('select').on('change',function(){
		$('#add-field-from-primary-table #is_change_form').val('1');
		$('#add-field-from-primary-table #is_change_form_main').val('1');
	});
	$(':checkbox').change(function(){
		$('#add-field-from-primary-table #is_change_form').val('1');
		$('#add-field-from-primary-table #is_change_form_main').val('1');
	});
	<?php if($flag == 'addfields'){ ?>
		get_table_field_list();
	<?php } ?>
	// get the table fields list 
	function get_table_field_list(){
		var table_name = $('#my_primaryheader').val();
		var table_name_text = $('#my_primaryheader option:selected').text();
		var primary_table_name = '<?= $primary_table_name; ?>';
		$.ajax({
			url:baseUrl+'report-management/get-table-field-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:$('#ReportsReportType').serialize()+'&selected_table='+table_name+'&primary_table_name='+primary_table_name,
			success:function(response){
				$('.table-fields-lists').html(response);	
				$('.tbl_chkbox').show();
				$('.tbl-selectall-option').show();
				$('.table-select-all').prop('checked',false);
				$('.table-select-all').removeClass('checked');
				$('.table-select-all').attr('aria-label','Select All Fields for '+table_name_text);
				hideLoader();	
			}	
		});
	}
	
	/**
	 * table select all checkbox list
	 */
	 $('.table-select-all').click(function(){
		 if($('.table-select-all').is(':checked')){
			 $('.column-fields').prop('checked',true);
			 $('.column-fields').addClass('checked');
			 $('.tbl-selectall-option').show();
		 }else{
			 $('.column-fields').prop('checked',false);
			 $('.column-fields').removeClass('checked');
		 }
	 });
</script>
<noscript></noscript>
