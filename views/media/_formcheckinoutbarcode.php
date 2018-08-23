<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
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
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours">
	<?php 	$transType=array(1=>'Check in',2=>'Check out',3=>'Destroy',4=>'Move',5=>'Return'); ?>
	<?= $form->field($model, 'trans_type',['template' => "<div class='row input-field'><div class='col-md-3'>Transaction Type</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($transType,['prompt'=>'-Select Transaction Type-']);?>
    <?= $form->field($model, 'trans_requested_by',['template' => "<div class='row input-field'><div class='col-md-3'>Transaction Requested By</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($listUser,['prompt'=>'-Select Transaction Requested-']);?>
    <?= $form->field($model, 'Trans_to',['template' => "<div class='row input-field' id='trans_to'><div class='col-md-3'>Transaction To</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($listEvidenceTo,['prompt'=>'-Select Transaction To-']);?>
    <?= $form->field($model, 'trans_reason',['template' => "<div class='row input-field' ><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(); ?>        
    <?= $form->field($model, 'moved_to',['template' => "<div class='row input-field' id='move_to'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($listEvidenceLoc,['prompt'=>'-Select Move To Stored Location-']);?>
    <?= $form->field($model, 'is_duplicate',['template' => "<div class='row input-field' id='spn_Apply_Transaction_to_both'><div class='col-md-3' id='evidencetransactionIsDuplicate'> Apply Transaction to both Original and associated Duplicate Evidence </div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(array('label'=>null,'aria-labelledby'=>'evidencetransactionIsDuplicate')); ?>
    <div class="form-group">
		<div class='row input-field' id='	'>
			<div class='col-md-3'><label class="form_label">Search Barcode</label></div>
			<div class='col-md-7'>
				 <input type="text" id="scanned_barcode" name="scanned_barcode" autocomplete="off" onmouseover="this.focus();" class="form-control" onkeypress="return disableEnterKey(event);" aria-label='Barcode' />
				 <img alt="Search Barcode" title="Search Barcode" src="<?php //echo $tUrl; ?>/images/barcode-search.png" style="margin: 0 0 0 -57px;" onclick="searchIt();">
				 <input id="scanned_mids" name="scanned_mids" type="hidden" />
			</div>
		</div>
    </div>
    <div id="media_scanned" class="form-group">
		<div class='row input-field' id=''>
			  <div class='col-md-3'><label class="form_label">Scanned Media</label></div>
			  <div class='col-md-7'></div>
		</div>
	</div>	
</fieldset>
<div class="button-set text-right">
        <input type="hidden" name="Evidence[deleted_img]" id="Evidence_deleted_img" />
        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
        <?= Html::button('Clear', ['class' =>  'btn btn-primary','onclick'=>'location.href="index.php?r=media/index";']) ?>
        <?= Html::button('Apply', ['class' =>  'btn btn-primary','onclick'=>'apply_check_outin_barcode();'])  ?>
</div>
    <?php ActiveForm::end(); ?>
