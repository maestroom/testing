<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Media */

$this->title = 'Edit Media #'.$evidNum;
$this->params['breadcrumbs'][] = ['label' => 'Evidences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
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
    'case_arr'=>$case_arr,
    'evid_docs'=>$evid_docs,
    'evidencecontents_data'=>$evidencecontents_data,
    'CC_data'=>$CC_data,
    'evidence_case_id'=>$evidence_case_id ,
	'evidences_length' => $evidences_length,
    'action'=>'edit',
    'client_id'=>$client_id
]) ?>
    </div>
    <div id="evidContentFrm2" style="display: none;"></div>
   <script>
$(function() {
/**/
if($('#evidence-dup_evid').is(':checked'))
    $('#evidence-org_link').parent('.col-md-12').show();
else
    $('#evidence-org_link').parent('.col-md-12').hide();

$('#evidence-dup_evid').change(function(){
		if($(this).is(':checked'))
			$('#evidence-org_link').parent('.col-md-12').show();
		else
			$('#evidence-org_link').parent('.col-md-12').hide();
	});
$( "#evidence-case_id" ).change(function(){
         if($("#evidence-case_id option:selected" ).length > 0)
             $('#btn_add_content').show();
         else
             $('#btn_add_content').hide();
        
    });
 
    /*$( "#evidence-client_id" ).change(function(){
        loadCaselistByClient($(this).val(),'evidence-case_id');
    });*/
    
    $('input').customInput();
    datePickerController.createDatePicker({	                     
        formElements: { "evidence-received_date": "%m/%d/%Y" },
        callbackFunctions:{
			"datereturned" : [changeflag],
		},
    });
 });
function remove_image(name, obj,file_name)
{
    if (confirm("Are you sure you want to Delete "+file_name+"?")) {
        $(obj).parent().html('');
        if ($('#Evidence_deleted_img').val() != '') {
            $('#Evidence_deleted_img').val($('#Evidence_deleted_img').val() + "," + name);
        } else {
            $('#Evidence_deleted_img').val(name);
        }
    }
    return false;
}
</script>
<noscript></noscript>
