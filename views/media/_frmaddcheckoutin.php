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
//print_r($UserName);die;
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours">
    <?php
    	echo $form->field($model, 'trans_type',['template' => "<div class='row input-field'><div class='col-md-3'>Transaction Type<span class='require-asterisk' style='display:inline-block'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
		    'data' => $transType,
		    'options' => ['prompt' => 'Select Transaction Type', 'id' => 'evidencetransaction-trans_type', 'title' => 'Select Transaction Type'],
			   	/*'pluginOptions' => [
			        'allowClear' => true
			    ],*/
    		]); 
    ?>
    <?php /*?>
    <?php // echo $form->field($model, 'trans_requested_by',['template' => "<div class='row input-field'><div class='col-md-3'>Transaction Requested By</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($UserName,['prompt'=>'Select Transaction Requested']);?>
        <div class="trans_requested_by_container">        
        <div class="row input-field">
            <div class="col-md-3">
                <label class="form_label">Transaction Requested By</label>
            </div>
				<div class="col-md-7">
                <?php  
					echo Select2::widget([
							'model' => $model,
							'attribute' => 'trans_requested_by',
							'data' => $UserName,
							'options' => ['prompt' => 'Select User', 'title' => 'Select User', 'id' => 'trans_requested_by', 'class' => 'form-control'],
	                    //'pluginOptions' => [
						//	'allowClear' => true
	                    //]
                    ]); ?>
            </div>
        </div>  
        </div>  
      
    <?= $form->field($model, 'Trans_to',['template' => "<div class='row input-field' id='trans_to'><div class=''>&nbsp;</div><div class='col-md-3'>Transaction To</div><div class='col-md-7'>{input}\n<label for='evidencetransaction-trans_to'>&nbsp;</label>{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceTo,
    'options' => ['prompt' => 'Select Transaction To', 'id' => 'evidencetransaction-trans_to'],
    //'pluginOptions' => [
      //  'allowClear' => true
    //],]);?>
    <?= $form->field($model, 'trans_reason',['template' => "<div class='row input-field' ><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(['maxlength'=>$model_field_length['trans_reason']]); ?>        
     <?= $form->field($model, 'moved_to',['template' => "<div class='row input-field' id='move_to'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listEvidenceLoc,
    'options' => ['prompt' => 'Select Move To Stored Location', 'id' => 'evidencetransaction-moved_to'],
    //'pluginOptions' => [
      //  'allowClear' => true
    //], ]); ?>
    
    <?= $form->field($model, 'is_duplicate',['template' => "<div class='row input-field' id='spn_Apply_Transaction_to_both'><div class='col-md-3'> Apply Transaction to both Original and associated Duplicate Evidence </div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(array('label'=>'')); ?>
    */?> 
</fieldset>
<div class="button-set text-right">
		<input type="hidden" value="<?php echo $evidNum; ?>" name="evid_nums" />
        <input type="hidden" value="<?php echo Yii::$app->user->identity->id; ?>" name="uid" /> 
        <?= Html::button('Cancel', ['class' =>  'btn btn-primary','onclick'=>'location.href="index.php?r=media/index";','title'=>'Cancel']) ?>
        <?php //echo Html::submitButton('Update', ['class' =>'btn btn-primary','title'=>'Update']); ?>
</div>
    <?php ActiveForm::end(); ?>

<script>
  /* Change Flag */
  $('document').ready(function(){ $('#active_form_name').val('EvidenceTransaction'); });
  $('select').on('change', function(){
	  $("#EvidenceTransaction #is_change_form").val('1');
	  $("#EvidenceTransaction #is_change_form_main").val('1');
  });
  $('textarea').bind('input', function(){
	  $("#EvidenceTransaction #is_change_form").val('1');
	  $("#EvidenceTransaction #is_change_form_main").val('1');
  });
</script>