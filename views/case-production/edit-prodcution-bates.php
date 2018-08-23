<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */

$this->title = 'Edit Production Bates - ';
?>
    <div id="form_div"  class="two-cols-fieldset">
        <?= $this->render('_form_productionbates', [
            'model' => $model
        ]) ?>
    </div>
<script>
$( "#prod_date_loaded" ).click(function(){
            $(this).next('span').find('a').trigger('click');

    });
datePickerController.createDatePicker({	                     
        formElements: { "prod_date_loaded": "%m/%d/%Y"},
        callbackFunctions: {
			"datereturned" : [changeflag],
		},
    });
</script>
<noscript></noscript>
