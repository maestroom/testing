<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-filestyle.js');
$js = <<<JS
// get the form id and set the event
$(function() {
	$('#settings-loginpage_logo').filestyle({
		icon : false
	});
	$('#settings-modulepage_logo').filestyle({
		icon : false
	});
	$('#formIDrebrand-system').ajaxForm({
	success:   function(response){
			if(response == 'OK'){
				commonAjax(baseUrl +'/system/rebrand-system','admin_main_container');
			}else{
				//alert('Error');
			}
	} 
	});
	$("#submitbtndefault").on('click',function(){
	 if (confirm('Are you sure you want to restore the system default settings?')) {	
		$('#submitbtn').val('default'); 
		$('#formIDrebrand-system').submit();
	}
	});
	$("#submitbtnupdate").on('click',function(){
                var iso_version = $('#settings-custom_version').val();
		$('#submitbtn').val('update');
		$('#formIDrebrand-system').submit();
                $('#isatask_version').text(iso_version);
	});
});

JS;
$this->registerJs($js);
?>
<style>.loginpage_logo{display:none;}</style>
		   <div class="right-main-container">			
			<div class="sub-heading"><a href="javascript:void(0);" title="Rebrand System" class="tag-header-black">Rebrand System</a></div>
			<?php $form = ActiveForm::begin(['id' => 'formIDrebrand-system','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
			<?= IsataskFormFlag::widget(); // change flag ?>
			<fieldset class="one-cols-fieldset">
			  <div class="create-form rebrand-system">

					<div class="row">
					 <div class="col-sm-2"><label class="form_label required" for="text1">Custom Login Logo</label></div>
					 <div class="col-sm-4">
					  <!--<input id="input04" type="file" name="File Input Box" class="form-control" disabled=""> <!--aria-describedby="upload_instructions"-->
					  <?= $form->field($model, 'loginpage_logo')->label(null,['class' => 'loginpage_logo'])->fileInput(); //->label(false); ?>
					  <!--<span id="upload_instructions">(File Size cannot exceed 100MB)</span>-->
					  <ul class="rebrand-list">
						<li><strong>Login Image Size: </strong>w 214 x h 64</li>
						<li><strong>Format: </strong>.png</li>
						<li><strong>Resolution: </strong>72 ppl, transparent</li>
					  </ul>
					 </div>
					 <div class="col-sm-6">
					 <?php if(isset($loginlogo_data->fieldvalue) && $loginlogo_data->fieldvalue!=""){
						$logo=utf8_decode($loginlogo_data->fieldimage);
						
					 ?>
					 <?= Html::img("data:image/jpeg;base64,". base64_encode( $logo ) ,['alt'=>$loginlogo_data->fieldvalue,'style'=>'height:37px']);?>
					 <?php }?>
					 </div>
					 </div>
					 
					 <div class="row">
					 <div class="col-sm-2"><label class="form_label required" for="Select Box">Custom Module Logo</label></div>
					 <div class="col-sm-4">
					  <?= $form->field($model, 'modulepage_logo')->fileInput()->label(null,['class' => 'loginpage_logo']); //->label(false); ?>
					  <ul class="rebrand-list">
						<li><strong>Module Image Size: </strong>w 125 x h 37</li>
						<li><strong>Format: </strong>.png</li>
						<li><strong>Resolution: </strong>72 ppl, transparent</li>
					  </ul>
					 </div>
					 <div class="col-sm-6">
					 <?php if(isset($modulelogo_data->fieldvalue) && $modulelogo_data->fieldvalue!=""){
						$module_logo=utf8_decode($modulelogo_data->fieldimage);
					 ?>
					 <?= Html::img("data:image/jpeg;base64,". base64_encode( $module_logo ),['alt'=>$modulelogo_data->fieldvalue,'style'=>'height:37px']);?>
					 <?php }?>
					 </div>
					 </div>
					 
					 <div class="row">
					 <div class="col-sm-2"><label class="form_label required" for="settings-custom_logo_name">Custom Logo Name</label></div>
					 <div class="col-sm-4">
					 	<?php 
						 //$model->custom_logo_name =Yii::$app->name;
						 if(isset($custom_logo_name_data->fieldvalue) && $custom_logo_name_data->fieldvalue!="") { $model->custom_logo_name =$custom_logo_name_data->fieldvalue; } ?>
					 	<?php echo $form->field($model, 'custom_logo_name', ['template' => '{input}{error}{hint}'])->textInput(['maxlength'=>$rbs_length['fieldvalue']])->label(false); ?>
					 </div>
					 </div>

					 <div class="row">
					 <div class="col-sm-2"><label class="form_label required" for="settings-custom_version">Custom Version</label></div>
					 <div class="col-sm-4">
					 	<?php if(isset($custom_version_data->fieldvalue) && $custom_version_data->fieldvalue!="") { $model->custom_version =$custom_version_data->fieldvalue; } ?>
					 	<?php //$form->field($model, 'custom_version')->textInput(['size'=>'30','onkeypress'=>"return noAlphabets(event);"])->label(false); ?>
					 	<?php echo $form->field($model, 'custom_version', ['template' => '{input}{error}{hint}'])->textInput(['maxlength'=>$rbs_length['fieldvalue']])->label(false); ?>
					 	<input type="hidden" name="submitbtn" id="submitbtn" />
					 </div>
					 </div>
					 
					 

				</div>
			</fieldset>
			
			<div class="button-set text-right">
			 <?= Html::button('Default', ['title'=>"Default",'class' => 'btn btn-primary pull-left','id'=>'submitbtndefault']) ?>
			 <?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','id'=>'cancelbtnrebrand', 'onclick' => "commonAjax(baseUrl + '/system/rebrand-system','admin_main_container')"]) ?>
			 <?= Html::button('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitbtnupdate']) ?>
			</div>
			 <?php ActiveForm::end(); ?>
		   </div>
<script>
/* 
$('#cancelbtnrebrand').click(function(event){
	var chk_status = checkformstatus(event,"formIDrebrand-system"); // check form edit status 
	if(chk_status == true) {
		commonAjax(baseUrl +'/system/rebrand-system','admin_main_container');
	}
}); 
*/
$('document').ready(function(){
	$('#active_form_name').val('formIDrebrand-system'); // form Id
});
$('input').bind('input', function(){
	$('#formIDrebrand-system #is_change_form').val('1'); 
	$('#formIDrebrand-system #is_change_form_main').val('1');
});
$('input[type=file]').change(function() {
	$('#formIDrebrand-system #is_change_form').val('1'); 
	$('#formIDrebrand-system #is_change_form_main').val('1');
});
</script>
<noscript></noscript>
