<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mycontainer">
	<table id="add-field-map" class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0" >
		<thead>
			<tr>
				<th style="min-width: inherit;width:30%;"><a href="javascript:void(0);" title="Field ID Value">Field ID Value</a></th>
				<th style="min-width: inherit;width:60%;"><a href="javascript:void(0);" title="Field Lookup Value">Field Lookup Value</a></th>
				<th style="min-width: inherit;width:10%;">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="filter_lookup_popup">
			<tr>
				<td style="min-width: inherit;width:30%;">
					<input type="text" name="ReportsLookupValues[field_value][]" class="form-control field_value">
				</td>
				<td style="min-width: inherit;width:60%;">
					<input type="text" name="ReportsLookupValues[lookup_value][]" class="form-control lookup_value" style="width:100%">
				</td>
				<td style="min-width: inherit;width:10%;">&nbsp;</td>
			</tr>
		</tbody>	
	</table>
	<br/>
	<?= Html::button('Add More' , [ 'title' =>'Add More', 'class' => 'btn btn-primary','onclick'=>'addMoreTr()']) ?>
	<br/><br/>
	<div><?php if($description!=""){ echo "<strong>Hint : {$description}</strong>"; }?></div>
</div>
<script>
function addMoreTr(){
	$('#filed-map-pop-up #filter_lookup_popup').append('<tr><td style="min-width: inherit;width:30%;"><input type="text" name="ReportsLookupValues[field_value][]" class="form-control field_value"></td><td style="min-width: inherit;width:60%;"><input type="text" name="ReportsLookupValues[lookup_value][]" class="form-control lookup_value" style="width:100%"></td><td style="min-width: inherit;width:10%;"><a title="Delete" href="javascript:void(0);" aria-label="Delete" onclick="$(this).parent().parent().remove();"><em class="fa fa-close text-primary"></em></a></td></tr>');
}
</script>
<noscript></noscript>
