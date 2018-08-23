<?php
use yii\helpers\Html;
?>
<div id='reportform_div'>  
	<input type="hidden" name="fn_name" id="fn_name" value="<?php echo $function_data->function_name?>"/>  
	<table class="table table-striped" style="width: 100%;" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($fnparams_data)){
				foreach($fnparams_data as $fnparams){
				?>
			<tr>
				<th><?php echo $fnparams->params?></th>
				<th><?php echo $fnparams->type?></th>
				<th>
					<select id="<?php echo $fnparams->params?>" class="fnparams_options" onchange="if(this.value=='custom') { $('#<?php echo $fnparams->params?>_val_other').show(); }else{ $('#<?php echo $fnparams->params?>_val_other').hide(); }">
						<?php if(!empty($fields)) { foreach($fields as $field){?>
							
						<option value="<?php echo $field->reportsTables->table_name.'.'.$field->field_name; ?>"><?php echo $field->reportsTables->table_name.'.'.$field->field_name; ?></option>
						<?php }}?>
						<option value="custom">Custom Option</option>
					</select>
					<input type="text" id="<?php echo $fnparams->params?>_val_other" style="display:none;" class="form-control" />
				</th>
			</tr>
			<?php } }?>
		</tbody>
	</table>
</div>
<script>
    jQuery(document).ready(function () {
		$('.fnparams_options').select2({
			dropdownParent: $('#get-fnparams')
		});
		
	});	
</script>
<noscript></noscript>		
