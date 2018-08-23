<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\FormBuilder;
\app\assets\SystemAsset::register($this);
?>
<ol class="form-builder-ol"></ol>
<div style="displa:none">
<?php if(!empty($formbuilder_data)){
    //echo '<pre>';print_r($formbuilder_data);die;
?>
<form method="POST" id="custodian-edit" autocomplete="off">
<?php 
	foreach ($formbuilder_data as $ele_id=>$fdata) {
		//echo "<pre>",print_r($fdata),"</pre>";
		if(($fdata['remove'] == 1 && $formValues[$fdata['form_builder_id']] != '') ||  $fdata['remove'] == 0){ 
?>
<input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
<input type="hidden" name="<?=$ele_id?>[type]" value="<?=$fdata['type'] ?>">
<input type="hidden" name="<?=$ele_id?>[label]" value="<?=Html::encode($fdata['label'])?>">
<input type="hidden" name="<?=$ele_id?>[value]" value="<?php if(isset($formValues[$fdata['form_builder_id']])){echo Html::encode($formValues[$fdata['form_builder_id']]);} else {echo Html::encode($fdata['value']);}?>">
<input type="hidden" name="<?=$ele_id?>[values]" value="<?php echo Html::encode($fdata['values']);?>">
<input type="hidden" name="<?=$ele_id?>[values_ids]" value="<?php echo Html::encode($fdata['values_option_ids']);?>">
<input type="hidden" name="<?=$ele_id?>[description]" value="<?=Html::encode($fdata['description'])?>">
<input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>">
<input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
<input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=Html::encode($fdata['text_val'])?>">
<input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?=$fdata['sync_prod'] ?>">
<!--<input type="hidden" name="<?php //$ele_id ?>[field_type]" value="<?php //$fdata['field_type'] ?>">-->
<?php if(($fdata['type'] == 'checkbox' || $fdata['type'] == 'radio' || $fdata['type']=='dropdown')) {
	if(isset($formValues[$fdata['form_builder_id']])){
		$selected_options = (new FormBuilder)->getSelectedOption($cust_id,$fdata['form_builder_id'],3);
	} else {
		$selected_options = (new FormBuilder)->getDefaultElementOption($fdata['form_builder_id']);
	}
//echo "<pre>t",print_r($selected_options),"</pre>";
?>
<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=implode(",",$selected_options);?>">
<?php }else{?>
<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
<?php }?>
<input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>">
<input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?=$fdata['default_answer'] ?>">
<input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?php if(isset($unitValues[$fdata['form_builder_id']])){echo Html::encode($unitValues[$fdata['form_builder_id']]);} else {echo ($fdata['default_unit'] > 0)?$fdata['default_unit']:'';} ?>">

<input type="hidden" name="<?=$ele_id?>[formtype]" value="<?=$formtype ?>">
<input type="hidden" name="<?=$ele_id?>[edit]" value="1">
<?php }}?>
</form>
<?php }?>

</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
   Admin.formbuilder.init();
    var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk';
	var into = $(".form-builder-ol");
	$.ajax({
		url: Url,
		type:"post",
		data:$('#custodian-edit').serialize(),
		cache: false,
		dataType:'json',
		success:function(result){
			$.each(result,function(key,val){
				$(into).prepend(val);
				var $newrow = $(into).find('li:first');
				Admin.formbuilder.properties($newrow);
				Admin.formbuilder.layout($newrow);
				Admin.formbuilder.attr.update($newrow);
				//show
				$newrow.hide().slideDown('slow');
			});
			delete result;
		},
		complete:function(){
			$('#custodian-edit').remove();
			 $('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					console.log(datepicker_id);
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements ,callbackFunctions: {
							"datereturned" : [changeflag],
						}
					});	
				});
			 $("input").customInput();
			 hideLoader();
		}
	});
});
</script>
<noscript></noscript>
