<?php
use yii\helpers\Html;
?>
<div style="display:none" class="appended-copy-element">
	<?php if(!empty($formbuilder_data)){?>
	<form method="POST" id="copy-form-edit" autocomplete="off">
		<?php 
			foreach ($formbuilder_data as $ele_id=>$fdata) {
			if($fdata['remove'] == 0){
		?>
		<input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
		<input type="hidden" name="<?=$ele_id?>[type]" value="<?=$fdata['type'] ?>">
		<input type="hidden" name="<?=$ele_id?>[label]" value="<?= rawurlencode('Copy - '.$fdata['label'])?>">
		<input type="hidden" name="<?=$ele_id?>[value]" value="<?=Html::encode($fdata['value'])?>">
		<input type="hidden" name="<?=$ele_id?>[values]" value="<?=Html::encode($fdata['values'])?>">
		<input type="hidden" name="<?=$ele_id?>[description]" value="<?=Html::encode($fdata['description'])?>">
		<input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>">
		<input type="hidden" name="<?=$ele_id?>[element_view]" value="<?=$fdata['element_view'] ?>">
		<input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
		<input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=Html::encode($fdata['text_val'])?>">
		<input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?=$fdata['sync_prod'] ?>">
		<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
		<input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>">
		<input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?=$fdata['default_answer'] ?>">
		<input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?=$fdata['default_unit'] ?>">
		<input type="hidden" name="<?=$ele_id?>[form_type]" value="<?=$fdata['form_type'] ?>">
		<input type="hidden" name="<?=$ele_id?>[edit]" value="1">
		<?php }}?>
		
	</form>
	<?php }?>
</div>
