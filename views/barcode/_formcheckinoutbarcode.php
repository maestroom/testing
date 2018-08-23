<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
//use kartik\widgets\FileInput;
//use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Evidence */
/* @var $form yii\widgets\ActiveForm */

$timings = Yii::$app->params['timing_arr']; 
$new_timings = array();
foreach ($timings as $k => $v) {
    $new_timings[$v] = $v;
}



?>
<?php $form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['media/create']):Url::to(['media/update-evidence-process']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset one-cols-fieldset-report">
    <div class="email-confrigration-table sla-bus-hours">
	<?php 	$transType=array(1=>'Check in',2=>'Check out',3=>'Destroy',4=>'Move',5=>'Return'); ?>
	<?= $form->field($model, 'trans_type',['template' => "<div class='row input-field'><div class='col-md-3'><label style='font-size: 12px;font-weight: normal;line-height: 34px;margin: 0px;' for='evidencetransaction-trans_type'>Transaction Type</label><span class='require-asterisk-again'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $transType,
    'options' => ['prompt' => 'Select Transaction Type', 'title' => 'Select Transaction', 'id' => 'evidencetransaction-trans_type','nolabel'=>true],
   /* 'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>
    <?= $form->field($model, 'trans_requested_by',['template' => "<div class='row input-field'><div class='col-md-3'><label style='font-size: 12px;font-weight: normal;line-height: 34px;margin: 0px;' for='evidencetransaction-trans_requested_by'>Transaction Requested By</label><span class='require-asterisk-again'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listUser,
    'options' => ['prompt' => 'Select Transaction Requested By', 'title' => 'Select Transaction Requested', 'id' => 'evidencetransaction-trans_requested_by','nolabel'=>true,'aria-required'=>'true'],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>
    <?= $form->field($model, 'Trans_to',['template' => "<div class='row input-field' id='trans_to'><div class='col-md-3'><label for='evidencetransaction-trans_to'>Transaction To</label></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceTo,
    'options' => ['prompt' => 'Select Transaction To', 'title' => 'Select Transaction To', 'id' => 'evidencetransaction-trans_to','nolabel'=>true],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/]);
    /*print_r($evidences_tr);
    die;*/
    ?>
    <?= $form->field($model, 'trans_reason',['template' => "<div class='row input-field' ><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(['maxlength'=>$evidences_tr["trans_reason"]]); ?>        
   
    <?= $form->field($model, 'moved_to',['template' => "<div class='row input-field' id='move_to'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceLoc,
    'options' => ['prompt' => 'Select Moved To', 'title' => 'Select Moved To', 'id' => 'evidencetransaction-moved_to','nolabel'=>true],
   /* 'pluginOptions' => [
        'allowClear' => true
    ],*/]);?>
   
    <?= $form->field($model, 'is_duplicate',['template' => "<div class='row input-field' id='spn_Apply_Transaction_to_both'><div class='col-md-3' id='evidencetransactionIsDuplicate'> Apply Transaction to both Original and associated Duplicate Evidence </div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(array('label'=>null,'aria-labelledby'=>'evidencetransactionIsDuplicate')); ?>
    <div class="form-group">
		<div class='row input-field' id='	'>
			<div class='col-md-3'>
				<label class="form_label" for="scanned_barcode">Search Barcode</label>
				<span class='require-asterisk-again'>*</span>
			</div>
			<div class='col-md-7'>
				 <input type="text" placeholder="Place Cursor Here & Then Scan Barcode"  id="scanned_barcode" name="scanned_barcode" autocomplete="off" onmouseover="this.focus();" class="form-control" onkeypress="return disableEnterKey(event);" aria-label='Search Barcode' aria-required="true" />
				 <input id="scanned_mids" name="scanned_mids" type="hidden"/>
			</div>
			<div class="col-md-2">
				<?= Html::button('Add', ['class' =>  'btn btn-primary','onclick'=>'searchIt();','title'=>'Add']) ?>
			</div>
		</div>
    </div>
    <div id="media_scanned" class="form-group ">
		<div class='row input-field' id=''>
			  <div class='col-md-3'><label class="form_label" id="scannedMedia">Scanned Media</label></div>
			  <div class='col-md-7'>
<!--                                <div class="" style="color:#000;background-color:#E9E7E8;border:1px solid #E9E7E8;padding: 5px 8px;font-size: 14px;"><a href="javascript:void(0);" title="Scanned Media: read only edit Barcode" class="tag-header-black" aria-label="Scanned Media: read only edit Barcode">Barcode</a></div>-->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" aria-describedby="scannedMedia">
						<thead>
                                                <th aria-label="Scanned Media: read only edit Barcode"><a href="javascript:void(0);" title="Barcode" class="tag-header-black" >Barcode</a></th>
							<th width="60"><a href="javascript:void(0);" title="Action" class="tag-header-black">Action</a></th>
						</thead>
						<tbody></tbody>
					</table>
			  </div>
		</div>
	</div>	
	
</fieldset>
<div class="button-set text-right">
	<input type="hidden" name="Evidence[deleted_img]" id="Evidence_deleted_img" />
	<input type="hidden" name="EvidenceTransaction[id]" class="form-control" id="evidencetransaction-id">
	<?php // $form->field($model, 'id')->hiddenInput()->label(false); ?>
	<?= Html::button('Cancel', ['class' =>  'btn btn-primary', 'id' => 'barcode-cancel-btn', 'title'=>'Cancel']) ?>
	<?= Html::button('Update', ['class' =>  'btn btn-primary','onclick'=>'apply_check_outin_barcode();','title'=>'Update'])  ?>
	<div class="pull-left div-note"><strong>Note:</strong> A Barcode Scanner is required to use this Feature </div>        
</div>
<?php ActiveForm::end(); ?>
<script>
	/** Edit change Event **/
	$('input').bind("input", function(){ $('#EvidenceTransaction #is_change_form').val('1'); $('#EvidenceTransaction #is_change_form_main').val('1'); }); 
	$('textarea').bind('input', function() {
		$('#EvidenceTransaction #is_change_form').val('1');
		$('#EvidenceTransaction #is_change_form_main').val('1');
	});
	$('select').on('change', function() {
		$('#EvidenceTransaction #is_change_form').val('1');
		$('#EvidenceTransaction #is_change_form_main').val('1');
	});
	$('document').ready(function(){
		$('#active_form_name').val('EvidenceTransaction'); // Form name evidence transaction
	});
	/** barcode cancel btn **/
	$('#barcode-cancel-btn').click(function(event){
		location.href = "index.php?r=media/index";
	});
</script>
