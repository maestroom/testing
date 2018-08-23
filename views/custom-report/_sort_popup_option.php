<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="row sort-parent">
	<form name="sort-popup-option" id="sort-popup-option" autocomplete="off">
		<!-- Header -->
		<div class="rows">
			<div class="col-sm-12">
				<div class="col-sm-6"><strong>Sort Type</strong></div>
				<div class="col-sm-6"><strong>Sort Order</strong></div>
			</div>
		</div>
		<input type="hidden" id="id" name="id" value="<?= $id ?>" />
		<div class="rows field-operator-id">
			<div class="col-sm-12">
				<!-- Sort type Dropdown -->	
				<div class="col-sm-6">
					<select id="select-sort-type-field" name="sort-type" title="Select Sort Type">
						<option value=""></option>
						<?php foreach($sort_type as $key => $val){ ?>
							<option value="<?= $key ?>" <?php if($type==$key){echo "Selected"; } ?>><?= $val ?></option>
						<?php } ?>
					</select>
				</div>
				<!-- Sort Order Dropdown -->
				<div class="col-sm-6">
					<select id="select-sort-order" name="sort-order" title="Select Sort Order">
						<option value=""></option>
						<?php foreach($sort_order as $key => $val){ ?>
							<option value="<?= $key ?>" <?php if($order==$key){echo "Selected";} ?>><?= $val ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	
/** Add Operator **/
$(function(){
	// select2 field operator
	$('#select-sort-type-field').select2({
		allowClear: false,
		dropdownParent: $('.field-operator-id')
	});
	
	// select2 field operator
	$('#select-sort-order').select2({
		allowClear: false,
		dropdownParent: $('.field-operator-id')
	});
});
/** End **/
</script>
<noscript></noscript>
