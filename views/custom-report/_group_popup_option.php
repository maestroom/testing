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
<div class="row group-parent">
	<form name="group-popup-option" id="group-popup-option" autocomplete="off">
		<input type="hidden" id="id" name="id" value="<?= $id ?>" />
		<?php /*?>
		<!-- Header -->
		<div class="rows">
			<div class="col-sm-12">
				<div class="col-sm-6"><strong>Total Type</strong></div>
			</div>
		</div>
		
		<div class="rows field-operator-id form-group clearfix">
			<div class="col-sm-12">
				<!-- Group type Dropdown -->	
				<div class="col-sm-6">
					<select id="select-group-type-field" name="group-type" title="Select Group Type">
						<option value=""></option>
						<?php foreach($group_type as $key => $val){ ?>
							<option value="<?= $key ?>" <?php if($type==$key){echo "Selected"; } ?>><?= $val ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		
		</div>
		<?php */?>
		<div class="rows">
			<div class="col-sm-12">
				<div class="col-sm-6"><strong>Display By</strong></div>
			</div>
		</div>
		<div class="rows field-operator-id form-group clearfix">
			<div class="col-sm-12">
				<!-- Group type Dropdown -->	
				<div class="col-sm-6">
					<select id="select-group-display-by" name="group-display-by" title="Select Display By">
						<option value=""></option>
						<?php foreach($display_by as $key => $val){ ?>
							<option value="<?= $key ?>" <?php if($selected_display_by==$key){echo "Selected"; } ?>><?= $val ?></option>
						<?php } ?>
					</select>
					
				</div>
				<div class="col-sm-6">
					<span id="sample" class="format_sample_span"><?php if(isset($selected_display_by)){if($selected_display_by==2){ echo "Sample: 5.00";}else if($selected_display_by==3){echo "Sample: $5.00";}else if($selected_display_by==4){echo "Sample: 5.00%";}}?></span>
				</div>
			</div>
		
		</div>
		<!--  NUMBER -->
		<div class="rows form-group clearfix" style="display:<?php if($selected_display_by==2){?>block<?php }else{?>none<?php }?>;" id="display_by_number">
			<div class="col-sm-12">
				<div class="col-sm-6">
				Decimal places: <input type="number" name="group-display-number-dp" value="<?php echo $selected_display_dp?>" class="form-control">
				</div>
			</div>
		</div>
		<div class="rows form-group clearfix" style="display:<?php if($selected_display_by==2){?>block<?php }else{?>none<?php }?>;" id="display_by_number_sp">
			<div class="col-sm-12">
				<div class="col-sm-6">
				<input type="checkbox" id="group-display-number-sp" value="1" name="group-display-number-sp" <?php if(isset($selected_display_sp) && $selected_display_sp!=''){?> checked="checked" <?php }?> aria-label="Use 1000 Separator"> Use 1000 Separator(,)
				</div>
			</div>
		</div>
		
		<!--  currency -->
		<div class="rows form-group clearfix" style="display:<?php if($selected_display_by==3){?>block<?php }else{?>none<?php }?>;" id="display_by_currency">
			<div class="col-sm-12 form-group">
				<div class="col-sm-6">
				Decimal places: <input type="number" name="group-display-currency-dp" value="<?php echo $selected_display_currency_dp?>" class="form-control">
				</div>
			</div>
		</div>
		<div class="rows form-group clearfix" style="display:<?php if($selected_display_by==3){?>block<?php }else{?>none<?php }?>;" id="display_by_currency_symbol">
			<div class="col-sm-12">
				<div class="col-sm-6 currency_select_sym">
				Symbol: <select id="display_by_currency_smb" name="display_by_currency_smb" class="form-control">
						<option value="">None</option>
						<option value="$" selected="selected">$&nbsp;&nbsp;&nbsp;</option>
						
				</select>
				</div>
			</div>
		</div>
		<!--  Percentage -->
		<div class="rows" style="display:<?php if($selected_display_by==4){?>block<?php }else{?>none<?php }?>;" id="display_by_per">
			<div class="col-sm-12">
				<div class="col-sm-6">
					Decimal places: <input type="number" name="group-display-per-dp" value="<?php echo $selected_display_per_dp?>" class="form-control">
				</div>
			</div>
		</div>
	
	
	
	</form>
</div>
<script>
	
/** Add Operator **/
$(function(){
	
	// select2 field operator
	$('#select-group-type-field').select2({
		allowClear: false,
		dropdownParent: $('#availabl-group-type')
	});	
	$('#select-group-display-by').select2({
		allowClear: false,
		dropdownParent: $('#availabl-group-type')
	});
	$('#display_by_currency_smb').select2({
		allowClear: false,
		dropdownParent: $('#availabl-group-type')
	});
	
	$("#select-group-display-by").on('change',function(){
		$("#sample").html('');
		if(this.value == 2){ //number
			$("#sample").html('Sample: 5.00');
			$('#display_by_number').show();
			$('#display_by_number_sp').show();
			$('#display_by_per').hide();
			$('#display_by_currency').hide();
			$('#display_by_currency_symbol').hide();
		}
		else if(this.value == 3){
			$("#sample").html('Sample: $5.00');
			$('#display_by_currency').show();
			$('#display_by_currency_symbol').show();
			$('#display_by_per').hide();
			$('#display_by_number').hide();
			$('#display_by_number_sp').hide();
		}
		else if(this.value == 4){
			$("#sample").html('Sample: 5.00%');
			$('#display_by_per').show();
			$('#display_by_number').hide();
			$('#display_by_number_sp').hide();
			$('#display_by_currency').hide();
			$('#display_by_currency_symbol').hide();
		}
	});
});
/** End **/
</script>
<noscript></noscript>
