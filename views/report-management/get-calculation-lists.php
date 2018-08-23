<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mycontainer">
	<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th><a href="javascript:void(0);" title="Calculation Fields" class="tag-header-black">Calculation Fields</a></th>
			<th class="tbl-selectall-option-th"><a href="javascript:void(0);" title="Select All" class="tag-header-black">Select All</a></th>
		</tr>
		<tr>
			<?php if(!empty($calculation)){ ?>
			<td>&nbsp;</td>	
			<td class="tbl-selectall-option text-center">
				<input type="checkbox" id="table_select_all" class="table-select-all check-all" name="table_lists" value="all" />
				<label for="table_select_all" class="table-select-all"><span class="sr-only">Select All Calculation Fields</span></label>
			</td>
			<?php }?> 
		</tr>
	</table>
	
	<!-- table fields -->
	<div class="table-fields-lists">
	
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0">
			<tbody>
			<?php
			if(!empty($calculation)){
				foreach($calculation as $ckey=>$cval){
			?>
				<tr>
					<td class="field-operator-id"><?=$cval?></td>
					<td class="tbl-selectall-option text-center">
					<input name="cal_field_name[]" data-tbl_name="<?=$cval?>" data-tbl_og_name="<?=$cval?>" data-tbl_display_name="<?=$cval?>" id="cal_field_name_<?=$ckey?>" value="<?=$ckey?>" class="cal-fields check-individual" type="checkbox">
					<label for="cal_field_name_<?=$ckey?>" class="column-fields"><span class="sr-only"><?=$cval?></span></label>
					</td>
				</tr>
		<?php } }else{?>
			<tr><td colspan="2" align="center">No Calculation Fields Found</td></tr>
		<?php }?>
			</tbody>
		</table>
	
	</div>
</div>
<script>
	$('input').customInput();
	$('.check-all').on('click',function(){
		$('.cal-fields').prop('checked',$(this).prop('checked'));
		$('.cal-fields').attr('checked',$(this).attr('checked'));
		if($(this).is(':checked')){
			$('.cal-fields').next('label').addClass('checked');
		}else{
			$('.cal-fields').next('label').removeClass('checked');
		}
	});
</script>
<noscript></noscript>
