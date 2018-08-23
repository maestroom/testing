<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var getTableSchemas */
?>
<select name="select_secondary_table_field_list" id="select-secondary-table-field-list" class="form-control select-secondary-table-field-list">
	<option value=""></option>
	<?php foreach($sr_table_fields as $vfield){ ?>
		<option value="<?= $vfield ?>"><?= $vfield ?></option>
	<?php } ?>
</select>

<script>
	$(function(){
		$('.select-secondary-table-field-list').select2({
			placeholder:'Select Second Table Field',
			dropdownParent: $('.secondary-field-operator-id'),
			dropdownCssClass : 'select-primary-table-field-list',
		});
	});
</script>
<noscript></noscript>
