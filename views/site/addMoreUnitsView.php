<?php 
$unitConversionSize = Yii::$app->params['unit_conversion_size'];
$uniqID = uniqid();
?>
<tr>
	<td width="20%">
		<select name="UnitMaster[unit_id][]" class="form-control" onchange="$('#unit_convert_report-<?=$uniqID?>').val(this.value);" >
			<option value="">Select Unit</option>
			<?php 
			if(!empty($remainingUnits)){
				foreach($remainingUnits as $key => $units){
					echo '<option value="'.$key.'">'.$units.'</option>';
				}
			}
			?>
		</select>
	</td>
	<td width="25%"><input type="text" class="form-control" name="UnitMaster[unit_size][]" value="0" aria-label="Unit Size" /></td>
	<td width="30%"><?= $unitConversionSize[$type] ?></td>
	<td width="25%">
		<input type="radio" id="unit_convert_report-<?=$uniqID?>" name='unit_convert_report'>
		<label for="unit_convert_report-<?=$uniqID?>" class="">&nbsp;</label>
	</td>
</tr>
