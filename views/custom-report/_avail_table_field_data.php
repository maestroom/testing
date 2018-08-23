<?php
// helper html
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
?>
<?php if(isset($avail_table_fields)){ ?>
	<?php $i=1; foreach($avail_table_fields as $key => $value){ ?>
		<div class="myheader">
			<a href="javascript:void(0);"><?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?$table_display_names[$key]:$key; ?></a>
			<div class="pull-right header-checkbox">
				<input type="checkbox" id="table_name_<?= $i ?>" name="table_name[]" value="<?= $key ?>" class="table_name_<?= $i ?> maintableinput" onClick="inner_checkall(<?= $i ?>);" aria-label="Select table, <?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?$table_display_names[$key]:$key; ?>" /> 
				<label for="table_name_<?= $i ?>" class="table_name_<?= $i ?>"><span class="not-set"><?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?$table_display_names[$key]:$key; ?></span></label> 
			</div>
		</div>
		<div class="content">
			<fieldset>
			<legend class="sr-only">Available Fields <?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?$table_display_names[$key]:$key; ?></legend>
			<ul>
			<?php foreach($value as $k => $val){ ?>
					<li><span data-table-display-name="<?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?$table_display_names[$key]:'Calc'; ?>"><?= (isset($newdisplay_name[$key][$k]) && $newdisplay_name[$key][$k]!="")?Html::encode($newdisplay_name[$key][$k]):$val; ?></span>
						<div class="pull-right"> 
							<input type="checkbox" name="field_name[]" id="field_name_<?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?'':'calc_'; ?><?= $k ?>" value="<?= Html::encode($val); ?>" data-fieldid="<?php echo $k; ?>" data-table="<?php echo $key ?>" class="table_field field_name_<?= $i ?>" aria-label="Field Name, <?= (isset($newdisplay_name[$key][$k]) && $newdisplay_name[$key][$k]!="")?Html::encode($newdisplay_name[$key][$k]):$val; ?>" />
							<label for="field_name_<?= (isset($table_display_names[$key]) && $table_display_names[$key]!="")?'':'calc_'; ?><?= $k ?>" class="field_name_<?= $i ?>" ><span class="not-set"><?= (isset($newdisplay_name[$key][$k]) && $newdisplay_name[$key][$k]!="")?Html::encode($newdisplay_name[$key][$k]):$val; ?></span></label>
						</div>
					</li>
			<?php } ?>	
			</ul>
			</fieldset>
		</div>  
	<?php  $i++; } ?>	
<?php } ?>
<?php if(!empty($reportTypeFields)){?>
<label for="report_field_types" style="display:none"><span class="not-set">Field types</span></label>
<select id="report_field_types" style="display:none;">
<?php foreach($reportTypeFields as $id=>$ftype){?>
	<option value="<?=$id?>" data-con="<?=trim($reportTypeFields_conditions[$id])?>"><?=$ftype?></option>
<?php }?>
</select>
<?php }?>
<script>
$(function() {
	$('input').customInput();
});
function inner_checkall(loop){
	if($('#table_name_'+loop).is(':checked')){
		$('.field_name_'+loop).prop('checked',true);
		$('.field_name_'+loop).addClass('checked');
	}else{
		$('.field_name_'+loop).prop('checked',false);
		$('.field_name_'+loop).removeClass('checked');
	}
}

$('#avail_field_data .myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$("#avail_field_data .myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
			// sorting text
			//console.log();
        });
    });
});
</script>
<noscript></noscript>
