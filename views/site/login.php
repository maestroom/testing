<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-container">
  <div class="col-sm-12 login-lt-box">
   <div class="login-box">
	<?php  if(isset($Settingdata->fieldtext) && $Settingdata->fieldtext!="") { echo html_entity_decode(Html::encode($Settingdata->fieldtext));} else {?>Welcome To IS-A-TASK<?php }?>
   </div>
   <div class="login-box">
	<?php  if(isset($Settingdatabottom->fieldtext) && $Settingdatabottom->fieldtext!="") { echo html_entity_decode(Html::encode($Settingdatabottom->fieldtext));} else {?>Welcome To IS-A-TASK<?php }?>
   </div>
  </div>
  <div class="login-box login-rt-box">
	 
	 <?php $form = ActiveForm::begin([
			        'id' => 'form1',
			 		'options' => ['class' => 'form1','novalidate'=>'novalidate'],
			        /*'fieldConfig' => [
			            'template' => "<div class=\"col-sm-12\">{label}\n{input}</div>\n{error}",
			            'labelOptions' => ['class' => 'form_label'],
			        ],*/
			    ]); ?>
	  <div class="row">
	   <?= $form->field($model, 'usr_username')->textInput(['class'=>'form-control','size'=>"30",'maxlength'=>$users_length["usr_username"], 'title' => 'Username', 'autocomplete' => 'new-username' ])->label('Username',['class'=>'form_label']); ?>
	  </div>
	  <div class="row">
	   	<?= $form->field($model, 'password')->passwordInput(['class'=>'form-control','size'=>"30" ,'maxlength'=>$users_length["usr_pass"], 'title' => 'Password', 'autocomplete' => 'new-password'])->label('Password',['class'=>'form_label']); ?>
	  </div>
	  <?php if(isset($SettingLdap->id) && $SettingLdap->id>0){
		  $model->login_type='AD';
		  ?>
	  <div class="row">
			<?= $form->field($model, 'login_type')
			->widget(Select2::classname(), [			
	    	'data' => ['AD' => 'LDAP User', 'IAT' => 'IS-A-TASK User'],
	    	'options' => ['prompt' => false, 'nolabel' => true, 'id' => 'loginform-login_type','placeholder'=>''],
	    	'pluginOptions' => [
				'placeholder'=>'',
	    	    'allowClear' => false
	    	],
		])->label('Login Type',['class'=>'form_label']); ?>
	  </div>
	  <?php }?>
	  <div class="button-set text-right">
		<?= Html::submitButton('Login', ['class' => 'btn btn-primary','title'=>'Login to Isatask', 'name' => 'login-button']) ?>
	  </div>
	  <?php ActiveForm::end(); ?>
	</div>
