<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\ClientCase */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
.field-reportsusersaved-custom_report_name. help-block{
	height:2px;
}
</style>
<div id='Caseform_div'>
<?php $form = ActiveForm::begin(['id' => 'frm_popup_'.$model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="">
		<?= $form->field($model, 'flag')->hiddenInput()->label(false); ?>
		<?= $form->field($model, 'custom_report_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
		<?= $form->field($model, 'custom_report_description',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(['maxlength'=>255]); ?>
		<?php $model->report_save_to = $model->isNewRecord?1:$model->report_save_to; ?>
		<?= $form->field($model, 'report_save_to',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => array(1=>'Private',2=>'Shared',3=>'Public'),
					'options'=>['prompt'=>'Select Save To'],
					'pluginOptions' => [
						'allowClear' => false,
						'dropdownParent'=>new JsExpression("$('#save-report-access-popup')")
					],
					'pluginEvents'=>[
					"change" => "function() { 
							if(this.value==1 || this.value==3){ 
								$('#share_report').hide();
								$('#show_by_content').hide();
							}else{
								$('#share_report').show();
							}
						}",
					]
					]);?>
		<div id="share_report" style="display:none;">
		<?= $form->field($model, 'share_report_by',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => array(1=>'By Role', 2=>'By Client/Case', 3=>'By Team/Location',4=>'By User'),
					'options'=>['prompt'=>'Select Share Type'],
					'pluginOptions' => [
						'allowClear' => false
					],
					'pluginEvents'=>[
					"change" => "function() { 
							ShowBYData(this.value);
						}",
					]]);
		?>
		</div>	
		<div id="show_by_data" style="display:none">
			<div class="form-group field-reportsusersaved-show_by has-success">
				<div class="row input-field">
						<div class="col-md-3">
								<label for="reportsusersaved-show_by" class="form_label">&nbsp;</label>
						</div>
						<div class="col-md-7">
						</div>
						<div class="help-block">
						</div>
				</div>
				</div>
			</div>
		</div>
											
	</div>
</fieldset>
<?php ActiveForm::end(); ?>
<script>
	
	/* TextArea */
	$('select').on("change",function(){
		$('#frm_popup_ReportsUserSaved #is_change_form').val('1');
		$('#frm_popup_ReportsUserSaved #is_change_form_main').val('1');
	});
	$('textarea').bind("input",function(){
		$('#frm_popup_ReportsUserSaved #is_change_form').val('1');
		$('#frm_popup_ReportsUserSaved #is_change_form_main').val('1');
	});
	$('input').bind("input",function(){
		$('#frm_popup_ReportsUserSaved #is_change_form').val('1');
		$('#frm_popup_ReportsUserSaved #is_change_form_main').val('1');
	});
	$(':checkbox').change(function(){
		$('#frm_popup_ReportsUserSaved #is_change_form').val('1');
		$('#frm_popup_ReportsUserSaved #is_change_form_main').val('1');
	});
	$('document').ready(function(){
		$('#active_form_name').val('frm_popup_ReportsUserSaved');
	});
	/* End */
	
	$('#save-report-access-popup').parent().find('.ui-dialog-titlebar').focus();
	function ShowBYData(val){
		$.ajax({
			type : 'post',
			url:baseUrl+'custom-report/getshowby',
			data: {'show_by':val},
			beforeSend:function (data) {showLoader();},
			success:function(response){
				hideLoader();
				$('#show_by_data').show();
				$('#show_by_data .col-md-7').html(response);
			}
		});
	}
</script>
<noscript></noscript>
