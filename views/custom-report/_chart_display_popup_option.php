<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
$interval_range=Yii::$app->params['interval_range'];
$view_display=Yii::$app->params['view_display'];
//echo "<pre>",print_r($filter_data[$id]),"</pre>";

?>
<div class="row chart-display-parent">
	<form name="chart-display-popup-option" id="chart-filter-popup-option" autocomplete="off">
		<!-- Display By option Select2 -->
		<input type="hidden" id="id" name="id" value="<?= $id ?>" />
		<div class="rows field-operator-id">
			<div class="col-sm-12">
				<div class="col-sm-7">
					<div class="col-sm-4">
						<strong>Axis</strong>
					</div>
					<select id="chart-option-axis" name="axis" title="Select Chart Axis" onchange="showhidedisplayandtotal(this.value,'<?php echo $selected_chart_format?>');">
						<option value=""></option>
						<?php foreach($chart_axis as $value){ ?>
							<option value="<?= $value; ?>" <?= json_decode($filter_data[$id])->axis == $value?'selected="selected"':'' ?>><?php if($value=='l'){?> Legend <?php }else{?> <?= ucwords($value); ?> axis<?php }?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-7 display_by">
					<div class="col-sm-12 display_by">
						<strong>Display By</strong>
					</div>
					<select id="chart-option-display-by" name="display_by" title="Select Display By" onchange="change_display_by();" >
					 <?php if($reportTypeFields[$id] == 'DATETIME' ||  $reportTypeFields[$id] == 'DATE') {} else {'disabled="disabled"';}?>
						<option value=""></option>
						<?php foreach($display_by as $val){ ?>
							<option value="<?= $val['id'] ?>" <?= json_decode($filter_data[$id])->display_by == $val['id']?'selected="selected"':'' ?>><?= $val['chart_display_by'] ?></option>
						<?php } ?>
					</select>
				</div>
				
					<div class="col-sm-7 interval_range">
						<div class="col-sm-12 interval_range">
							<strong>Interval Range</strong>
						</div>
						<select id="chart-option-interval_range" onchange="change_interval_range();" name="interval_range" title="Select Interval Range">
							<option value=""></option>
							<?php foreach($interval_range as $inkey=>$inval){?>
							<option value="<?=$inkey?>" <?= json_decode($filter_data[$id])->interval_range == $inkey?'selected="selected"':'' ?>><?=$inval?></option>
							<?php }?>
						</select>
					</div>
					<div class="col-sm-7 view_display">
						<div class="col-sm-12 view_display">
							<strong>View Display</strong>
						</div>
						<select id="chart-option-view_display" name="view_display" title="Select View Display">
							<option value=""></option>
							<?php foreach($view_display as $vikey=>$vival){?>
							<option value="<?=$vikey?>" <?= json_decode($filter_data[$id])->view_display == $vikey?'selected="selected"':'' ?>><?=$vival?></option>
							<?php }?>
						</select>
					</div>	
					<div class="col-sm-7 currency_decimal_places">
						<div class="col-sm-12 currency_decimal_places">
							<strong>Decimal Places</strong>
						</div>
						<input type="text" id="chart-option-currency_decimal_places" name="currency_decimal_places" value="<?= (isset(json_decode($filter_data[$id])->currency_decimal_places)?json_decode($filter_data[$id])->currency_decimal_places:'2') ?>" />
					</div>
					<div class="col-sm-7 currency_symbol">
						<div class="col-sm-12 currency_symbol">
							<strong>Symbol</strong>
						</div>
						<input type="text" id="chart-option-currency_symbol" name="currency_symbol" value="<?= (isset(json_decode($filter_data[$id])->currency_symbol)?json_decode($filter_data[$id])->currency_symbol:'$') ?>" />
					</div>	
					
					<div class="col-sm-7 number_decimal_places">
						<div class="col-sm-12 number_decimal_places">
							<strong>Decimal Places</strong>
						</div>
						<input type="text" id="chart-option-number_decimal_places" name="number_decimal_places" value="<?= (isset(json_decode($filter_data[$id])->number_decimal_places)?json_decode($filter_data[$id])->number_decimal_places:'2') ?>" />
					</div>
					
					<div class="col-sm-7 number_1000_separator">
						<div class="col-sm-12 number_1000_separator">
							<strong>Use 1000 Separator (,)</strong>
						</div>
						<input type="checkbox" id="chart-option-number_1000_separator" name="number_1000_separator" <?php if(isset(json_decode($filter_data[$id])->number_1000_separator)){echo 'checked="checked"';}?> aria-label="Use 1000 Separator" />
					</div>
					
					
					
				<div class="col-sm-7 total_type">
					<div class="col-sm-12 total_type">
						<strong>Total Type</strong>
					</div>
					<select id="chart-option-manipulation-by" name="manipulation_by" title="Select Manipulation By">
						<option value=""></option>
						<option value="COUNT" <?= json_decode($filter_data[$id])->manipulation_by == 'COUNT'?'selected="selected"':'' ?>>COUNT</option>
						<option value="SUM" <?= json_decode($filter_data[$id])->manipulation_by == 'SUM'?'selected="selected"':'' ?>>SUM</option>
						
					</select>
				</div>
			</div>
		</div>
		<!-- End -->
	</form>
</div>
<script>
function hideAll(){
	$('.display_by').hide();
	$('.total_type').hide();
	$('.interval_range').hide();
	$('.view_display').hide();
	$('.currency_decimal_places').hide();
	$('.currency_symbol').hide();
	$('.number_decimal_places').hide();
	$('.number_1000_separator').hide();
}
</script>
<?php if(!isset(json_decode($filter_data[$id])->axis)){?>
<script>hideAll();</script>
<?php }
if(isset(json_decode($filter_data[$id])->axis) && json_decode($filter_data[$id])->axis=='l'){?>
<script>hideAll();</script>
<?php
}
/*===============COLUMN======================*/
if(isset(json_decode($filter_data[$id])->axis) && json_decode($filter_data[$id])->axis=='x' && strtolower($selected_chart_format)=='column')
{?>
<script>hideAll();$('.total_type').show();</script>
<?php } if(isset(json_decode($filter_data[$id])->axis) && json_decode($filter_data[$id])->axis=='y' && strtolower($selected_chart_format)=='column'){
	if(isset(json_decode($filter_data[$id])->display_by)){
	$display_by_txt="";
	foreach($display_by as $val){ 
		if($val['id'] == json_decode($filter_data[$id])->display_by){
			$display_by_txt=$val["chart_display_by"];
		}
	}
	
	if(strtolower($display_by_txt)=='date'){?>
	<script>
		$('.total_type').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
		
		$('.interval_range').show();
		$('.view_display').show();
	</script>
	<?php }
	if(strtolower($display_by_txt)=='text'){?>
	<script>
		$('.total_type').hide();
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
	</script>	
	<?php } ?>	
	<?php if(strtolower($display_by_txt)=='currency'){?>
	<script>
		$('.total_type').hide();
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
		
		$('.currency_decimal_places').show();
		$('.currency_symbol').show();	
	</script>
	<?php }?>
	<?php if(strtolower($display_by_txt)=='number'){?>
	<script>
		$('.total_type').hide();
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		
		$('.number_decimal_places').show();
		$('.number_1000_separator').show();
	</script>	
	<?php }
	}?>
<?php }

/*===============BAR======================*/
if(isset(json_decode($filter_data[$id])->axis) && json_decode($filter_data[$id])->axis=='y' && strtolower($selected_chart_format)=='bar'){?>
<script>hideAll();$('.total_type').show();</script>
<?php } if(isset(json_decode($filter_data[$id])->axis) && json_decode($filter_data[$id])->axis=='x' && strtolower($selected_chart_format)=='bar'){
	?><script>$('.display_by').show();</script><?php
	if(isset(json_decode($filter_data[$id])->display_by)){
	$display_by_txt="";
	foreach($display_by as $val){ 
		if($val['id'] == json_decode($filter_data[$id])->display_by){
			$display_by_txt=$val["chart_display_by"];
		}
	}
	
	if(strtolower($display_by_txt)=='date'){?>
	<script>
		$('.total_type').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
		
		$('.interval_range').show();
		$('.view_display').show();
	</script>
	<?php }
	if(strtolower($display_by_txt)=='text'){?>
	<script>
		$('.total_type').hide();
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
	</script>	
	<?php } ?>	
	<?php if(strtolower($display_by_txt)=='currency'){?>
	<script>
		$('.total_type').hide();
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
		
		$('.currency_decimal_places').show();
		$('.currency_symbol').show();	
	</script>
	<?php }?>
	<?php if(strtolower($display_by_txt)=='number'){?>
	<script>
		$('.total_type').hide();
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		
		$('.number_decimal_places').show();
		$('.number_1000_separator').show();
	</script>	
	<?php }
	}?>
<?php } ?>
<script>
/** Add Operator **/
$(function(){
	$('#chart-option-axis').select2({
		dropdownParent: $('.field-operator-id')
	});
	$('#chart-option-display-by').select2({
		dropdownParent: $('.field-operator-id')
	});
	$('#chart-option-manipulation-by').select2({
		dropdownParent: $('.field-operator-id')
	});
	
	if($('.interval_range')){
		$('#chart-option-interval_range').select2({
			dropdownParent: $('.field-operator-id')
		});
	}
	if($('.view_display')){
		$('#chart-option-view_display').select2({
			dropdownParent: $('.field-operator-id')
		});
	}
	/*if('<?=strtolower($selected_chart_format)?>' == 'column'){
		$('.display_by').hide();
	}
	if('<?=strtolower($selected_chart_format)?>' == 'bar'){
		$('.display_by').hide();
	}*/	
});
function change_display_by(){
	var option_txt = $("#chart-option-display-by option:selected").text();
	$('.interval_range').hide();
	$('.view_display').hide();
	$('.currency_decimal_places').hide();
	$('.currency_symbol').hide();
	$('.number_decimal_places').hide();
	$('.number_1000_separator').hide();
	$("#chart-option-interval_range").val('').change();
	$("#chart-option-view_display").val('').change();
	$("#chart-option-number_decimal_places").val('');
	$("#chart-option-currency_decimal_places").val('');
	$("#chart-option-currency_symbol").val('');
	$("#chart-option-number_1000_separator").attr('checked',false);
	if(option_txt.toLowerCase() == 'date'){
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
		
		$('.interval_range').show();
		$('.view_display').show();
	}
	if(option_txt.toLowerCase() == 'text'){
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
	}
	if(option_txt.toLowerCase() == 'currency'){
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.number_decimal_places').hide();
		$('.number_1000_separator').hide();
		
		$('.currency_decimal_places').show();
		$('.currency_symbol').show();
		$('#chart-option-currency_decimal_places').val(2);
		$('#chart-option-currency_symbol').val('$');
	}
	if(option_txt.toLowerCase() == 'number'){
		$('.interval_range').hide();
		$('.view_display').hide();
		$('.currency_decimal_places').hide();
		$('.currency_symbol').hide();
		
		$('.number_decimal_places').show();
		$('.number_1000_separator').show();
		$('#chart-option-number_decimal_places').val(2);
	}
	 
}
function hideallYBarOption(){
	$('.interval_range').hide();
	$('.view_display').hide();
	$('.currency_decimal_places').hide();
	$('.currency_symbol').hide();
	$('.number_decimal_places').hide();
	$('.number_1000_separator').hide();
}
var $input = $('#chart-option-view_display');
function change_interval_range(){
	var select_val=$("#chart-option-interval_range").val();
	if(select_val=='day'){
		var data = [{id: 'MM/DD/YYYY',text: 'MM/DD/YYYY'}];
		refreshSelect($input, data);
	}
	if(select_val=='week'){
		var data = [{id: 'MM/DD/YYYY',text: 'MM/DD/YYYY'}];
		refreshSelect($input, data);
	}
	if(select_val=='month'){
		var data = [{id: 'MM',text: 'MM'},{id: 'MM/YY',text: 'MM/YY'}];
		refreshSelect($input, data);
	}
	if(select_val=='year'){
		var data = [{id: 'YYYY',text: 'YYYY'}];
		refreshSelect($input, data);
	}
}
function refreshSelect($input, data) {
    $input.select2('destroy').empty().select2({data: data});
}
function showhidedisplayandtotal(val,chart){
	$('.interval_range').hide();
	$('.view_display').hide();
	$('.display_by').hide();
	$('.total_type').hide();
	if(val=='l'){
		$("#chart-option-display-by").val('').change();
		$("#chart-option-interval_range").val('').change();
		$("#chart-option-view_display").val('').change();
		$("#chart-option-number_decimal_places").val('');
		$("#chart-option-currency_decimal_places").val('');
		$("#chart-option-currency_symbol").val('');
		$("#chart-option-number_1000_separator").attr('checked',false);
		$("#chart-option-manipulation-by").val('').change();
		hideallYBarOption();
	}
	if(val=='x' && chart=='column'){
		
		$("#chart-option-display-by").val('').change();
		$("#chart-option-interval_range").val('').change();
		$("#chart-option-view_display").val('').change();
		
		$("#chart-option-number_decimal_places").val('');
		$("#chart-option-currency_decimal_places").val('');
		$("#chart-option-currency_symbol").val('');
		$("#chart-option-number_1000_separator").attr('checked',false);
		hideallYBarOption();
		$('.total_type').show();
	}
	if(val=='y' && chart=='column'){
		$("#chart-option-manipulation-by").val('').change();
		hideallYBarOption();
		$('.display_by').show();
	}
	
	//BAR
	if(val=='y' && chart=='bar'){
		$("#chart-option-display-by").val('').change();
		$("#chart-option-interval_range").val('').change();
		$("#chart-option-view_display").val('').change();
		
		$("#chart-option-number_decimal_places").val('');
		$("#chart-option-currency_decimal_places").val('');
		$("#chart-option-currency_symbol").val('');
		$("#chart-option-number_1000_separator").attr('checked',false);
		hideallYBarOption();
		$('.total_type').show();
	}
	if(val=='x' && chart=='bar'){
		$("#chart-option-manipulation-by").val('').change();
		hideallYBarOption();
		$('.display_by').show();
	}
}
/** End **/
</script>
<noscript></noscript>
