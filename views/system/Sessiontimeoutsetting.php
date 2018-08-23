<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
\app\assets\CustomInputAsset::register($this);
$js = <<<JS
// get the form id and set the event
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
	
	if($('#settings-fieldvalue input[type="radio"]:checked').val() == 2 && $('select#times').val()==''){
		$('#times').closest('div.form-group').removeClass('has-success').addClass('has-error');
		$('#times').parent().find('.help-block').html('Session Timeout cannot be blank.');
		return false;
	} else {
   		var form = $(this);
		$.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            beforeSend : function()    {
            	$('#submitUpdate').attr('disabled','disabled');
            },
            success: function (response){
            	if(response == 'OK'){
             		commonAjax(baseUrl +'/system/sessiontimeoutsetting','admin_main_container');
             		if($('#settings-fieldvalue input[type="radio"]:checked').val() == 2){
             			$.fn.idleTimeout().setCookie('idleTimeLimit', $('select[name="times"]').val());
             		} else {
             			$.fn.idleTimeout().setCookie('idleTimeLimit', 1200);
             		}
            	}else{
                	$('#submitUpdate').removeAttr("disabled");
            	}
            },
            error  : function (){
                console.log('internal server error');
            }
        });
   	}
	return false;
	// do whatever here, see the parameter \$form? is a jQuery Element to your form
}).on('submit', function(e){
    e.preventDefault();
});
$('select#times').change(function(){
	if($(this).val()!=''){
		$('#times').closest('div.form-group').removeClass('has-error').addClass('has-success');;
		$('#times').parent().find('.help-block').html('');
	}
});
JS;
$this->registerJs($js);
?>
<div class="right-main-container">			
	<div class="sub-heading"><a href="javascript:void(0);" title="Session Timeout" class="tag-header-black">Session Timeout</a></div>
	<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	
	<fieldset class="one-cols-fieldset">
	 <div class="administration-form custom-inline-block-width">
         <legend class="sr-only">Session Timeout</legend>
	 <?php 
            $model->field= 'session_timeout';
            echo $form->field($model, 'field')->hiddenInput()->label(false);
            $time="";
            if($model->fieldvalue === null || $model->fieldvalue == 1){
                   $model->fieldvalue = 1;
            } else {
                   $time=$model->fieldvalue; 
                   $model->fieldvalue = 2; 
            } 
            $list = [1 => 'User Options Configuration (Default)', 2 => 'System Configuration'];
            echo $form->field($model, 'fieldvalue')->radioList($list,['onchange'=>'ShowHideTime()',
                   'item' => function($index, $label, $name, $checked, $value) {
                   $return = '<label for="'.$name.'-'.$value.'">';
                   if($checked)
                           $return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'"  id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '">';
                   else
                           $return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '">';

                   $return .= ucwords($label);
                   $return .= '</label>';

                   return $return;
                }
           ])->label(false);
        ?>
 	<div class="form-group field-settings-times" style="<?= $model->fieldvalue == 1 ? 'display:none':''; ?>">
                <div class="col-md-3">
			 <?php
				echo Select2::widget([
						'name' => 'times',
	                    'attribute' => 'times',
	                    'value'=>$time,
	                    'data' => array('60'=>'1 Min','900'=>'15 Mins','1800'=>'30 Mins','3600'=>'1 hrs'),
	                    'options' => ['prompt' => 'Select Session Timeout','class' => 'form-control','id'=>'times','nolabel'=>'true','aria-label'=>'Select Session Timeout'],
	                   /* 'pluginOptions' => [
	                      'allowClear' => true
	                    ]*/
	                    ]);
	            	echo '<div class="help-block"></div>';
			?>
		</div>
	</div>
	 </div>
	</fieldset>
	<div class="button-set text-right">
		<?= Html::Button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','id'=>'CancelSessionTimeoutSetting']) ?>
		<?= Html::submitButton('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitUpdate']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
<script>
/* customInput */	
$(function() {
  $('input').customInput();
  $('#active_form_name').val('Settings'); // Add Form
  $(':radio').change(function(){ 
	$('#Settings #is_change_form').val('1'); 
	$('#Settings #is_change_form_main').val('1');
  });
  $('select').on('change', function() {
	$('#Settings #is_change_form').val('1');
	$('#Settings #is_change_form_main').val('1'); 
  });
});

$('#CancelSessionTimeoutSetting').click(function(event){
	var chk_status = checkformstatus(event);
	if(chk_status == true) commonAjax(baseUrl +'/system/sessiontimeoutsetting','admin_main_container');
});

function ShowHideTime(){ //alert($("input[name='Settings[fieldvalue]']:checked").val());
	if($("input[name='Settings[fieldvalue]']:checked").val()==2){
		$(".field-settings-times").show();
	}else{
		$(".field-settings-times").hide();
	}
}
</script>
<noscript></noscript>
