<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\models\Options;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\ClientCase */
/* @var $form yii\widgets\ActiveForm */
$model->case_name = Html::decode($model->case_name);
?>
<div id='Caseform_div'>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?php $form->field($model, 'client_id')->label(false)->textInput(['value'=>$model->isNewRecord?$client_id:$model->client_id]); ?>
    	<?= $form->field($model, 'case_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['case_name']]); ?>
    	<?= $form->field($model, 'description',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['description']]); ?>
    	
    	<?= $form->field($model, 'case_type_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => $listCaseType,
			'options'=>['prompt'=>'Select Case Type', 'title' => 'Case Type' ,'nolabel' => true],
			'pluginOptions' => [
				'allowClear' => true
			],]);
		?>
    	
    	<?= $form->field($model, 'case_matter_no',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['case_matter_no']]); ?>
    	<?= $form->field($model, 'counsel_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['counsel_name']]); ?>
    	<?= $form->field($model, 'internal_ref_no',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['internal_ref_no']]); ?>
    	<?= $form->field($model, 'case_manager',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['case_manager']]); ?>
    	<?= $form->field($model, 'sales_user_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $listSalesRepo,
					'options'=>['prompt'=>'Select Sales Representative', 'title' => 'Sales Representative' ,'nolabel' => true],
					'pluginOptions' => [
						'allowClear' => true
					],]);?>
    	
		<?php if(!$model->isNewRecord) { ?>
	    	<?= $form->field($model, 'is_close',['template' => "<div class='row input-field'><div class='col-md-3'></div><div class='col-md-7'>{input}<label for='clientcase-is_close'>Case Closed</label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'custom-full-width']])->checkbox(['label' => '','labelOptions'=>['class'=>'custom-full-width','aria-label'=>'Case Closed','for'=>'clientcase-is_close']])->label(false); ?>
	    	<?= $form->field($model, 'case_close_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $listCaseCloseType,
					'options'=>['prompt'=>'Select Case Close Type', 'title' => 'Case Close Type' ,'nolabel' => true],
					'pluginOptions' => [
						'allowClear' => true
				],]); ?>
	    	<?= $form->field($model, 'close_reason',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(['maxlength'=>$model_field_length['close_reason']]); ?>
	    	<?php 
	    		if(!empty($actLog_case)){
					$caselog_date='<div class="form-group"><div class="row input-field"><div class="col-md-3"></div><div class="col-md-7">';
					foreach ($actLog_case as $caselog)
					{
						$sval = explode(":",$caselog->activity_name); // get different close type by Id
						if($caselog->activity_type == 'Closed')
							$caselog_date .='<em>('.($caselog->activity_type).'-'.($model->getCaseCloseTypeByID($sval[1])).' '.(new Options)->ConvertOneTzToAnotherTz($caselog->date_time,'UTC',$_SESSION['usrTZ'],"MDYHI").')</em> <br>'; //  /*date('m/d/Y h:i A',strtotime($caselog->date_time))*/ 
						else
							$caselog_date .='<em>('.($caselog->activity_type).' '.(new Options)->ConvertOneTzToAnotherTz($caselog->date_time,'UTC',$_SESSION['usrTZ'],"MDYHI").')</em> <br>'; ///*date('m/d/Y h:i A',strtotime($caselog->date_time))*/
					}
					echo $caselog_date.="</div></div></div>";
	    		}
	    	?>	
    	<?php } ?>
    </div>	
</fieldset>
<div class="button-set text-right">
	<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ?'Cancel':'Delete','class' => 'btn btn-primary', 'onclick'=> $model->isNewRecord ? 'addCase();' : 'removeCase('.$model->id.');']); ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord?'Add':'Update','class' =>  'btn btn-primary','onclick'=>$model->isNewRecord ? 'SubmitAjaxForm("'.$model->formName().'",this,"loadCasesByClient('.$client_id.')","Caseform_div")' : 'updateCaseForm('.$model->id.',"'.$model->formName().'",this,"loadCasesByClient('.$model->client_id.')");']) ?>
</div>
<?php ActiveForm::end(); ?>
</div>
<?php if(!$model->isNewRecord) { ?>
	<?php if($model->is_close == 1){ ?>
		<script>
			jQuery('.field-clientcase-case_close_id').show();
			jQuery('.field-clientcase-case_close_id').addClass('required');
			jQuery('.field-clientcase-close_reason').show();
		</script>
<noscript></noscript>	
	<?php } else { ?>
		<script>
			jQuery('.field-clientcase-case_close_id').hide();
			jQuery('.field-clientcase-close_reason').hide();
			jQuery('.field-clientcase-case_close_id').removeClass('required');
			jQuery('.field-clientcase-case_close_id select').val('');
			jQuery('.field-clientcase-close_reason textarea').text('');
		</script>
<noscript></noscript>
	<?php } ?>
	<script>
	$('#clientcase-is_close').customInput();
	jQuery('#clientcase-is_close').on('change',function(){
		if(jQuery(this).prop('checked') == true){
			jQuery('.field-clientcase-case_close_id').show();
			jQuery('.field-clientcase-case_close_id').addClass('required');
			jQuery('.field-clientcase-close_reason').show();
		} else {
			jQuery('.field-clientcase-case_close_id').hide();
			jQuery('.field-clientcase-close_reason').hide();
			jQuery('.field-clientcase-case_close_id').removeClass('required');
			jQuery('.field-clientcase-case_close_id select').val('');
			jQuery('.field-clientcase-close_reason textarea').text('');
		}
	});
	</script>
<noscript></noscript>
<?php } ?>
<script>
	/* Case Change Form Flag */
	jQuery('input').bind("input", function(){ 
		jQuery('#ClientCase #is_change_form').val('1'); 
		jQuery('#ClientCase #is_change_form_main').val('1');
	}); 
	jQuery('textarea').bind("input", function(){ 
		jQuery('#ClientCase #is_change_form').val('1'); 
		jQuery('#ClientCase #is_change_form_main').val('1');
	}); 
	jQuery('#ClientCase select').on('change', function() {
		jQuery('#ClientCase #is_change_form').val('1');
		jQuery('#ClientCase #is_change_form_main').val('1'); 
	});
	jQuery(':checkbox').change(function() {
		jQuery('#ClientCase #is_change_form').val('1');
		jQuery('#ClientCase #is_change_form_main').val('1'); 
	});
	/* jQuery document */
	jQuery('document').ready(function(){
		jQuery('#active_form_name').val('ClientCase'); // change form name
	});
</script>
