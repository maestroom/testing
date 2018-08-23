<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
\app\assets\CustomInputAsset::register($this);
$js = <<<JS
// get the form id and set the event
$(function() {
	$('.auth').on('click',function(){
		if($(this).is(':checked')){
			if($(this).val() =='Authenticated'){
				$('#up_container').show();
			}else{
				$('#up_container').hide();
			}
		}
	});	
	 $("#submitbtncancel").click(function(){
	 	dailogConfirmed('Confirm','Are you Sure you want to remove Email Configuration?','cancelConfig',new Array());
   });
   $("#submitbtnconfig").click(function(){
		$('#buttonSubmit').val('config');
	   submitForm();
   });
	$("#testMail").click(function(){
		var form = $('#{$model->formName()}');
		$.ajax({
	        url    : baseUrl +'/system/testemailalerts',
	        type   : 'post',
	        data   : form.serialize(),
	        success: function (response){
	        	$('#testmessagestatus').html(response);
                        $('#testmessagestatus').focus();
	        },
	        error  : function (){
	            console.log('internal server error');
	        }
	    });
   });
   submitForm = function (){
	var form = $('#{$model->formName()}');
	$.ajax({
        url    : form.attr('action'),
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
        	$('.btn').attr('disabled','disabled');
        },
        success: function (response){
        	if(response == 'OK'){
        		commonAjax(baseUrl +'/system/emailsetting','admin_main_container');
        	}else{
            	$('.btn').removeAttr("disabled");
				$("#admin_main_container").html(response);
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
  }
});

JS;
$this->registerJs($js);
?>

<div class="right-main-container">			
     <div class="sub-heading"><a href="javascript:void(0);" title="Email Alerts Configuration"  class="tag-header-black">Email Alerts Configuration</a></div>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
		<fieldset class="one-cols-fieldset">
			<div class="email-confrigration-table">
			 	<?= $form->field($model, 'from_name',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-4">{input}{error}{hint}</div></div>'])->textInput(['maxlength'=>$esettings_length['from_name']])->label($model->getAttributeLabel('from_name'), ['class'=>'form_label']);?>
				<?= $form->field($model, 'from_email',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-4">{input}{error}{hint}</div></div>'])->textInput(['maxlength'=>$esettings_length['from_email']])->label($model->getAttributeLabel('from_email'), ['class'=>'form_label']);?>
				<?= $form->field($model, 'email_host',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-4">{input}{error}{hint}</div></div>'])->textInput(['maxlength'=>$esettings_length['email_host']])->label($model->getAttributeLabel('email_host'), ['class'=>'form_label']);?>
				<?php if(!isset($model->security)){ $model->security='none';} if(!isset($model->sendmailvia)){ $model->security='mail';}?>				
                                <?= $form->field($model, 'sendmailvia',['template' => '<div class="row custom-full-width"><fieldset><legend class="sr-only">Send Email via</legend><div class="col-md-2">{label}</div><div class="col-md-9">{input}<div class="row"><div class="col-md-12">{error}</div></div>{hint}</div></fieldset></div>'])->radioList(array('smtp'=>'Smtp','mail'=>'Mail'),['item' => function($index, $label, $name, $checked, $value) {
                                    $return = '<div class="col-md-2"><label for="Emailsettings-sendmailvia-'.$value.'">';
                                        if($checked)
                                            $return .= '<input id="Emailsettings-sendmailvia-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '" aria-label="'.ucwords($label).'" aria-setsize="2" aria-posinset="'.($index+1).'" title="This field is required" >';
                                        else
                                            $return .= '<input id="Emailsettings-sendmailvia-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '" aria-label="'.ucwords($label).'" aria-setsize="2" aria-posinset="'.($index+1).'" title="This field is required">';

                                        $return .= ucwords($label);
                                        $return .= '</label></div>';
                                    return $return;
                                }])->label($model->getAttributeLabel('sendmailvia'), ['class'=>'form_label']); ?>
                                
				<?= $form->field($model, 'security',['template' => '<div class="row custom-full-width"><fieldset><legend class="sr-only">Security</legend><div class="col-md-2">{label}</div><div class="col-md-9">{input}<div class="row"><div class="col-md-12">{error}</div></div>{hint}</div></fieldset></div>'])->radioList(array('tls'=>'TLS','ssl'=>'SSL','none'=>'NONE'),['item' => function($index, $label, $name, $checked, $value) {
							$return = '<div class="col-md-2"><label for="Emailsettings-security-'.$value.'">';
							if($checked)
								$return .= '<input aria-setsize="3" aria-posinset="'.($index+1).'" id="Emailsettings-security-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '" aria-label="'.ucwords($label).'" title="This field is required">';
							else
								$return .= '<input aria-setsize="3" aria-posinset="'.($index+1).'" id="Emailsettings-security-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '" aria-label="'.ucwords($label).'" title="This field is required">';
							
							$return .= ucwords($label);
							$return .= '</label></div>';
						
							return $return;
						}])->label($model->getAttributeLabel('security'), ['class'=>'form_label']); ?>
                                
				<?= $form->field($model, 'port',['template' => '<div class="row custom-full-width"><div class="col-md-2">{label}</div><div class="col-md-4">{input}{error}{hint}</div></div>'])->textInput(['maxlength'=>$esettings_length['port']])->label($model->getAttributeLabel('port'), ['class'=>'form_label']);?>
				<?php $model->auth= 'Anonymous'; if((isset($model->username) && $model->username!="")) { $model->auth='Authenticated';}?>
				
                                <?= $form->field($model, 'auth',['template' => '<div class="row custom-full-width"><fieldset><legend class="sr-only">Authentication</legend><div class="col-md-2">{label}</div><div class="col-md-4"><div class="row">{input}{error}{hint}</div></div></fieldset></div>'])->radioList(array('Anonymous'=>'Anonymous','Authenticated'=>'Authenticated'),['item' => function($index, $label, $name, $checked, $value) {
							$return = '<div class="col-md-5"><label for="'.$name.'-'.$value.'">';
							if($checked)
								$return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '" class="auth" aria-label="'.ucwords($label).'">';
							else
								$return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '" class="auth" aria-label="'.ucwords($label).'">';
							
							$return .= ucwords($label);
							$return .= '</label></div>';
						
							return $return;
						}])->label($model->getAttributeLabel('auth'), ['class'=>'form_label']); ?>
                                <div id="up_container" style="<?php if($model->auth== 'Anonymous'){?>display:none<?php }?>">
                                    <?= $form->field($model, 'username',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-4">{input}{error}{hint}</div></div>'])->textInput(['maxlength'=>$esettings_length['username']])->label($model->getAttributeLabel('username'), ['class'=>'form_label']);?>

                                    <?= $form->field($model, 'password',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-4">{input}{error}{hint}</div></div>'])->passwordInput(['aria-label'=>' ','maxlength'=>$esettings_length['password']])->label($model->getAttributeLabel('password'), ['class'=>'form_label']);?>
				 </div>
				 <div class="row">
				  <div class="form-group">
				  <div class="col-md-2"><label class="form_label required" for="toEmail">To Email [Testing]</label></div>
				  <div class="col-md-4">                                      
					  <input id="toEmail" type="text" name="toEmail" size="30" class="form-control" maxlength="<?php echo $esettings_length['sendmailvia'];?>">
				  </div>
				  <div class="col-md-4">
					<?php echo Html::button('Test Mail',['title'=>'Test Mail','class'=>'btn btn-primary','id'=>'testMail']) ?>
                                      <div class="left" id="testmessagestatus" tabindex="0"></div>
				  </div>
				  </div>
				 </div>
				 <?php $model->emailtype= 'external';echo $form->field($model, 'emailtype',['template' => '{input}'])->hiddenInput()->label(false);?>
			   
			</div>
			</fieldset>
			
			<div class="button-set text-right">
			 <input type="hidden" value="" name="buttonSubmit" id="buttonSubmit">
			 <?php 
				//if((isset($model->setting_id) && $model->setting_id!="")){  
			 		echo Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','id'=>'submitbtncancel']);
			 	//} 
			 ?>
			 <?= Html::button('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitbtnconfig']) ?>
			 </div>
			<?php ActiveForm::end(); ?>
		   </div>
<script>
$(function() {
  $('input').customInput();
  $('#active_form_name').val('Emailsettings'); // active form name
  $('input').change(function(){ 
	$('#Emailsettings #is_change_form').val('1'); 
	$('#Emailsettings #is_change_form_main').val('1');
  }); 
  $(':radio').change(function(){ 
	$('#Emailsettings #is_change_form').val('1'); 
	$('#Emailsettings #is_change_form_main').val('1');
  });
});
</script>
<noscript></noscript>
