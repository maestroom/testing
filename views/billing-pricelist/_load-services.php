<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

if(!empty($serviceList)) {
	$options = '<fieldset><legend class="sr-only">Service Tasks</legend>';
	foreach($serviceList as $key => $service) {
	 	//$options.='<option value="'.$key.'">'.$service.'</option>';
	 	$options.='<div class="col-sm-12">
                    <div class="custom-checkbox">
                        <input type="checkbox" value="'.$key.'" name="Pricing[service_task][]" id="Pricing[service_task][]-'.$key.'" aria-label="'.$service.'">
                        <label class="form_label" for="Pricing[service_task][]-'.$key.'">'.$service.'</label>
                    </div>
	 	</div>';
	 	//<div class="col-sm-12"><div class="custom-checkbox"><input type="checkbox" value="9" name="Pricing[service_task][]" checked="1" id="Pricing[service_task][]-9"><label class="form_label checked" for="Pricing[service_task][]-9">Forensics - Collect Evidence</label></div></div>
	}
        $options.='</fieldset>';
	echo $options;
}
?>
<script>
$('input').customInput();
</script>
<noscript></noscript>