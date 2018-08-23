<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\FormBuilder;
?>
<div class="create-form add-instruction-form" id="add-instruction-form">
<?php

if(!empty($formbuilder_data)) {
	foreach($formbuilder_data as $key => $data) {
?>
<input type="hidden" class="servicetask_ids" value="<?php echo $key; ?>" />
<div id="form_builder_panel<?php echo $key; ?>">
    <ol class="form-builder-ol"></ol>
</div>
<div id="formbuilder-edit-<?php echo $key; ?>">
<?php 
	foreach ($data as $ele_id=>$fdata) {
		// echo "<pre>",print_r($formValues[$fdata['form_builder_id']]),"</pre>";
		if(($fdata['remove'] == 1 && $formValues[$fdata['form_builder_id']] != '') || $fdata['remove'] == 0) {
	?>	
            <input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
            <input type="hidden" name="<?=$ele_id?>[formref_id]" value="<?= $key ?>" />
            <input type="hidden" name="<?=$ele_id?>[type]" value="<?= $fdata['type'] ?>"> 
            <input type="hidden" name="<?=$ele_id?>[label]" value="<?= Html::encode($fdata['label'])?>">
            <input type="hidden" name="<?=$ele_id?>[value]"	value="<?php if(isset($formValues[$fdata['form_builder_id']])){ echo Html::encode($formValues[$fdata['form_builder_id']]); } else { echo Html::encode($fdata['value']); }?>">
            <input type="hidden" name="<?=$ele_id?>[values]" value="<?= Html::encode($fdata['values'])?>">
            <input type="hidden" name="<?=$ele_id?>[values_ids]" value="<?php echo Html::encode($fdata['values_option_ids']);?>"> 
            <?php  if($flag != 'Edit' && $flag != 'Saved'){ ?> 
                    <input type="hidden" name="<?=$ele_id?>[load_prev]" value="<?php echo $fdata['no_load_prev']; ?>"> 
            <?php } ?>
            <input type="hidden" name="<?=$ele_id?>[description]" value="<?=Html::encode($fdata['description']) ?>"> 
            <input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>"> 
            <input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
            <input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=Html::encode($fdata['text_val'])?>"> 
            <input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?=$fdata['sync_prod'] ?>"> 
            <!--<input type="hidden" name="<?php //$ele_id ?>[field_type]" value="<?php //$fdata['field_type'] ?>">-->
            <input type="hidden" name="<?=$ele_id?>[wfloadprevoius]" value="<?= ($loadprevoius>0) ? '1':'0' ?>">
            <input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?= (($loadprevoius>0 && $fdata['no_load_prev']==1) || $loadprevoius==0 || in_array($key,$new_servicetask_id))? Html::encode($fdata['default_answer']):'' ?>">
            <input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?php if(isset($unitValues[$fdata['form_builder_id']])){echo Html::encode($unitValues[$fdata['form_builder_id']]);} else {echo (($flag != 'Edit' && $flag != 'Saved') || (($flag == 'Edit' || $flag == 'Saved') && in_array($key,$new_servicetask_id)) && $fdata['default_unit'] > 0)?$fdata['default_unit']:'';} ?>">
            <input type="hidden" name="<?=$ele_id?>[is_new_st_addded]" value="<?php echo in_array($key,$new_servicetask_id)?1:''; ?>">
            <?php 
                    if(($fdata['type'] == 'checkbox' || $fdata['type'] == 'radio' || $fdata['type'] == 'dropdown')){
                            $selected_value=array();
                            $all_values =  explode(";",$fdata['values_option_ids']);
                            if(isset($formValues[$fdata['form_builder_id']])) {
                                    $selected_options = (new FormBuilder)->getSelectedOption($activeinstruction_id,$fdata['form_builder_id'],1);
                            } else {
                                    $selected_options = (new FormBuilder)->getDefaultElementOption($fdata['form_builder_id']);
                            }
                    ?>
            <?php if($fdata['no_load_prev'] != '1'){ ?>
                    <input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=implode(",",$selected_options);?>">
            <?php } elseif($flag == 'Edit') { ?>
                    <input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=implode(",",$selected_options);?>">	
                    <?php } else {  ?>
                    <input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=implode(",",$selected_options); ?>">
            <?php } ?>
            <?php 
            } else { ?>
            <input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
            <?php } ?>
            <input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>"> 

            <input type="hidden" name="<?=$ele_id?>[formtype]" value="<?= ($flag != 'Edit' && $flag != 'Saved' && $project_id==0 || ($loadprevoius>0 && $project_id>0))?'projectadd':'projectedit' ?>"> 
            <input type="hidden" name="<?=$ele_id?>[edit]" value="1">
	<?php }} ?>
</div>
<?php } } exit;?>
</div>
<script>
showLoader();
</script>
<noscript></noscript>
