<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-group">
	<select name="primary_table_fields_name" id="select-primary-table-field" class="form-control select-primary-table-field">
		<option value=""></option>
		<?php foreach($pr_table_fields[$pr_table] as $key => $vtable){ ?>
			<option value="<?= $vtable['field_name']; ?>"><?= $vtable['field_name']; ?></option>
		<?php } ?>
	</select>
</div>
<script>
	$(function(){
		$('#availabl-second-tables .select-primary-table-field').select2({
			placeholder:'Select First Table Field',
			dropdownParent: $('#availabl-second-tables .field-operator-id'),
			dropdownCssClass : 'select-primary-table-field',
		});
	});
</script>
<noscript></noscript>
