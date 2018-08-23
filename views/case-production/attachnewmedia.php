<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Media */

$this->title = 'Attach Media to Production';
$this->params['breadcrumbs'][] = ['label' => 'Evidences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-filestyle.js');
$js = <<<JS
// get the form id and set the event
	$(function() {
	 
	 $('#T271').filestyle({
			icon : false
		});
	$('#T71').MultiFile({ 
	  list: '#T71-list'
	 });	
	});
JS;
	$this->registerJs($js);
	$curr_date=date('Ymd',time());
?>
    <div class="sub-heading" class="two-cols-fieldset"><?= Html::encode($this->title) ?></div>
    <div id="form_div"  class="two-cols-fieldset">
        <?= $this->render('_formattachnewmedia', [
    'model' => $model,
    'listEvidenceType' => $listEvidenceType,
    'params' => $params,
    'listEvidenceCategory'=>$listEvidenceCategory,
    'listUnit'=>$listUnit,
    'listEvidenceEncrypt'=>$listEvidenceEncrypt,
    'listEvidenceLoc'=>$listEvidenceLoc,
    'case_arr'=>array(),
    'rec_time'=>$rec_time
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
			"datereturned":[changeflagmedia],
		},
        rangeHigh:'<?php echo $curr_date;?>'
    });
 });
 function changeflagmedia(){
	 $("#Evidence #is_change_form").val('1');
 }
</script>
<noscript></noscript>
