<?php
use yii\helpers\Html;

if(!empty($current_table)){?>
	<option value=""></option>
	<?php 
		foreach ($current_table as $key => $tablefields){ 
			$display_name = $tablefields['field_display_name'];
			$table_name=$tablefields['table_name'];
			$field_name=$tablefields['field_name']; 
	?>
	<option value="<?= $tablefields['field_id'] ?>"><?= $field_name; ?></option>
<?php  } }?>
