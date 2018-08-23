<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */

$this->title = 'Add Production Attorney';
?>
<div id="form_div"  class="two-cols-fieldset">
    <?= $this->render('_form_productionattorney', [
        'model' => $model,
        'model_field_length'=>$model_field_length
    ]) ?>
</div>
<script>
$( "#prod_agencies,#prod_access_req" ).click(function(){
    $(this).next('span').find('a').trigger('click');
});
datePickerController.createDatePicker({	                     
	formElements: { "prod_agencies": "%m/%d/%Y"},
	callbackFunctions: {
		"datereturned" : [changeflag],
	},
});
datePickerController.createDatePicker({	                     
	formElements: { "prod_access_req": "%m/%d/%Y"},
	callbackFunctions: {
		"datereturned" : [changeflag],
	},
});    
</script>
<noscript></noscript>
