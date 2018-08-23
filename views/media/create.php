<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Media */

$this->title = 'Add Media';
$this->params['breadcrumbs'][] = ['label' => 'Evidences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
//$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-filestyle.js');
$js = <<<JS
// get the form id and set the event
$(function() {
 $('#T7').MultiFile({ 
        list: '#T7-list',
        STRING: {
            remove: '<em class="fa fa-close text-danger" title="Remove"></em>'
         },
         maxsize:102400
 });	
});

JS;
$this->registerJs($js);
$curr_date=date('Ymd',time());
?>
    <div class="sub-heading" class="two-cols-fieldset-report"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"  class="two-cols-fieldset-report">
        <?= $this->render('_form', [
    'model' => $model,
    'listEvidenceType' => $listEvidenceType,
    'clientList' => $clientList,
    'listEvidenceCategory'=>$listEvidenceCategory,
    'listUnit'=>$listUnit,
    'listEvidenceEncrypt'=>$listEvidenceEncrypt,
    'listEvidenceLoc'=>$listEvidenceLoc,
    'evidences_length'		=>	$evidences_length,
    'case_arr'=>array()
]) ?>
    </div>
    <div id="evidContentFrm2" style="display: none;"></div>
   <script>
$(function() {
	$('#evidence-org_link').parent('.col-md-12').hide();
	$('#evidence-dup_evid').change(function(){
		if($(this).is(':checked'))
			$('#evidence-org_link').parent('.col-md-12').show();
		else
			$('#evidence-org_link').parent('.col-md-12').hide();
	});
/**/
$( "#evidence-case_id" ).change(function(){
	 if($("#evidence-case_id option:selected" ).length > 0)
		 $('#btn_add_content').show();
	 else
		 $('#btn_add_content').hide();
    });
 
    $( "#evidence-client_id" ).change(function(){
		// loadCaselistByClient($(this).val(),'evidence-case_id');
    });
    $( "#evidence-received_date" ).click(function(){
		$(this).next('span').find('a').trigger('click');
	});
	//alert(current_date);
	
    $('input').customInput();
		datePickerController.createDatePicker({	                     
        formElements: { "evidence-received_date": "%m/%d/%Y"},
        rangeLow:"19700313",
        callbackFunctions:{
			"datereturned" : [changeflag],
		},
        rangeHigh:'<?php echo $curr_date;?>'
    });
});
</script>
<noscript></noscript>
