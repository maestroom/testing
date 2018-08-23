<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Change Force Password';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
$("#user-old_password").val(null);
$("#user-usr_pass").val(null);
$("#user-confirm_password").val(null);
$('#change-force-password').find('#user-old_password').on('keyup',function(){
		var old_password = $(this).val();
		$.ajax({
			url : 'index.php?r=/site/chk-passwords',
			type : 'post',
			data : {
			'id':'{$id}',
			'old_password' : old_password	
			},
			success: function(response){
				if(response == 0){
					$('#user-old_password').parent().parent().parent().addClass("has-error");
					$('#user-old_password').siblings().html("Current Password does not match existing Password.");
				}else{
					$('#user-old_password').parent().parent().parent().removeClass("has-error");
					$('#user-old_password').siblings().html("");
				}
			},
			});
	});
$("#change-password-button").on('click',function(){
	if($("#user-old_password").val()!="" && $("#user-usr_pass").val()!="" && $("#user-confirm_password").val()!=""){
		if(!$('#change-force-password').find('.has-error').length){
			$('#change-force-password').submit();
		}	
	}else{
		$("#user-old_password").blur();
		if($("#user-old_password").val()==""){
					$('#user-old_password').parent().parent().parent().addClass("has-error");
					$('#user-old_password').siblings().html("Current Password cannot be blank.");
		}
		$("#user-usr_pass").blur();
		$("#user-confirm_password").blur();
	}
});
JS;
$this->registerJs($js);
?>

<div class="login-container">
  <div class="col-sm-11 login-lt-box">
   <div class="login-box">
	<?php  if(isset($Settingdata->fieldtext) && $Settingdata->fieldtext!="") { echo html_entity_decode(Html::encode($Settingdata->fieldtext));} else {?>Welcome To IS-A-TASK<?php }?>
   </div>
      <div class="login-box">
	<?php  if(isset($Settingdatabottom->fieldtext) && $Settingdatabottom->fieldtext!="") { echo html_entity_decode(Html::encode($Settingdatabottom->fieldtext));} else {?>Welcome To IS-A-TASK<?php }?>
   </div>
  </div>
  <div class="login-box login-rt-box" style="width: 490px;">
	 <?php $form = ActiveForm::begin([
			        'id' => 'change-force-password',
			 		'options' => ['class' => 'form1','novalidate'=>'novalidate'],
			        'fieldConfig' => [
			            'template' => "<div class=\"col-sm-12\">{label}\n{input}</div>\n{error}",
			            'labelOptions' => ['class' => 'form_label'],
			        ],
			    ]); ?>
	  <div class="row">
	   		<?= $form->field($model, 'usr_username',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['disabled'=>'disabled']); ?>
	  </div>
	  <div class="row">
	  		<?= $form->field($model, 'old_password',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->passwordInput()->label('Current Password'); ?>
	  </div>
	  <div class="row">
	  		<?= $form->field($model, 'usr_pass',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->passwordInput()->label('New Password'); ?>
	  </div>
	  <div class="row">
	  		<?= $form->field($model, 'confirm_password',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->passwordInput(); ?>
	  </div>
	  <div class="button-set text-right">
		<?= Html::button('Change Password', ['class' => 'btn btn-primary','title'=>'Change Password', 'name' => 'change-password-button', 'id' => 'change-password-button']) ?>
	  </div>
	  <?php ActiveForm::end(); ?>
    </div>
