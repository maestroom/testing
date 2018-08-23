<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
\app\assets\CustomInputAsset::register($this);
$js = <<<JS
$('#Settings :input').change(function(){
   $('#checkAd').val(0);
});
$('#settings-ldap_user_filter').bind('change',function(){
	$('#UserLdapFilterinput').val(null);
	if($(this).val()!=""){
		$('#UserLdapFilterinput').val($(this).val());
	}
});
$('#settings-ldap_group_filter').bind('change',function(){
	$('#GroupLdapFilterinput').val(null);
	if($(this).val()!=""){
		$('#GroupLdapFilterinput').val($(this).val());
	}
});
 $("#submitbtncancel").click(function(){
	 	dailogConfirmed('Confirm','Are you Sure you want to remove Ldap Configuration?','cancelConfig',new Array());
   });
   $("#submitbtnconfig").click(function(){
   $('#testmessagestatus').html(null);
    	var error4=false;
    	 var emailtype=$('input:radio[name="Settings[ldap_connection_type]"]:checked').val();
                var ldap_server=$("#settings-servers").val();
                var ldap_port=$("#settings-ldap_port").val();
                var ldap_domain=$("#settings-defaultdomain").val();
                var ldap_search=$("#settings-search").val();
                var ldap_admin=$("#settings-ldap_admin").val();
                var ldap_admin_pass=$("#settings-ldap_admin_pass").val();
    	if(!emailtype)
         	 error4="- Please Select Connection Type.<br>";
             if(ldap_server=='')
             {
             	$("#settings-servers").blur();
             	error4=true;
             }
             if(ldap_port==''){
             	$("#settings-ldap_port").blur();
             	error4=true;
             }
             if(ldap_domain==''){
             	$("#settings-defaultdomain").blur();
            	error4=true;
            }
            if(ldap_search==''){
            	$("#settings-search").blur();
            	error4=true;
            }
            if(ldap_admin==''){
            	$("#settings-ldap_admin").blur();
            	error4=true;
            }
            if(ldap_admin_pass==''){
            	$("#settings-ldap_admin_pass").blur();
            	error4=true;
            }
            if(error4){
            	return false;
            	$('#checkAd').val(0);
				$('#updatemessagestatus').html(null);
            }


		$('#buttonSubmit').val('config');

		testAdDirect();
		var checkAd = $('#checkAd').val();
		//if(checkAd == 1){
			/*$('#updatemessagestatus').removeClass('alert-success').addClass('alert-danger');
	   		$('#updatemessagestatus').html('Unable to connect Ad Server.');*/
	   	//}else{
	   		//$('#checkAd').val(0);
	       // $('#updatemessagestatus').removeClass('alert-success').addClass('alert-danger');
	       // $('#updatemessagestatus').html('Unable to connect Ad Server.');
	   	//}
   });
   function testAdDirect(){
   	$('#updatemessagestatus').html(null);
		var form = $('#{$model->formName()}');
                var error4=false;
                var emailtype=$('input:radio[name="Settings[ldap_connection_type]"]:checked').val();
                var ldap_server=$("#settings-servers").val();
                var ldap_port=$("#settings-ldap_port").val();
                var ldap_domain=$("#settings-defaultdomain").val();
                var ldap_search=$("#settings-search").val();
                var ldap_admin=$("#settings-ldap_admin").val();
                var ldap_admin_pass=$("#settings-ldap_admin_pass").val();



                if(!emailtype)
                        error4="- Please Select Connection Type.<br>";
                if(ldap_server=='')
                {
                        $("#settings-servers").blur();
                        error4=true;
                }
                if(ldap_port==''){
                        $("#settings-ldap_port").blur();
                        error4=true;
                }
                if(ldap_domain==''){
                         $("#settings-defaultdomain").blur();
                         error4=true;
                }
                if(ldap_search==''){
                         $("#settings-search").blur();
                         error4=true;
                }
                if(ldap_admin==''){
                         $("#settings-ldap_admin").blur();
                         error4=true;
                }
                if(ldap_admin_pass==''){
                         $("#settings-ldap_admin_pass").blur();
                         error4=true;
                }
                if(error4){
                        return false;
                }
                $('#checkAd').val(0);
                $('#testmessagestatus').html(null);
		$.ajax({
	        url    : baseUrl +'/system/testldapconfig',
	        type   : 'post',
	        data   : form.serialize(),
	         beforeSend : function()    {
        	},
	        success: function (response){
	        	if(response==1){
	        		$('#checkAd').val(1);
	        		$('#testmessagestatus').removeClass('alert-danger').addClass('alert-success');
	        		$('#testmessagestatus').html(' LDAP Connection Successful');
	        		submitForm();

	        	}else{
	        		$('#checkAd').val(0);
	        		$('#testmessagestatus').removeClass('alert-success').addClass('alert-danger');
	        		$('#testmessagestatus').html(' LDAP Connection Not Successful');
	        	}
	        },
	        error  : function (){
	            console.log('internal server error');
	        }
	    });
   }
	$("#testAd").click(function(){
	$('#updatemessagestatus').html(null);
		var form = $('#{$model->formName()}');
                var error4=false;
                var emailtype=$('input:radio[name="Settings[ldap_connection_type]"]:checked').val();
                var ldap_server=$("#settings-servers").val();
                var ldap_port=$("#settings-ldap_port").val();
                var ldap_domain=$("#settings-defaultdomain").val();
                var ldap_search=$("#settings-search").val();
                var ldap_admin=$("#settings-ldap_admin").val();
                var ldap_admin_pass=$("#settings-ldap_admin_pass").val();



                if(!emailtype)
                        error4="- Please Select Connection Type.<br>";
                if(ldap_server=='')
                {
                        $("#settings-servers").blur();
                        error4=true;
                }
                if(ldap_port==''){
                        $("#settings-ldap_port").blur();
                        error4=true;
                }
                if(ldap_domain==''){
                         $("#settings-defaultdomain").blur();
                         error4=true;
                }
                if(ldap_search==''){
                         $("#settings-search").blur();
                         error4=true;
                }
                if(ldap_admin==''){
                         $("#settings-ldap_admin").blur();
                         error4=true;
                }
                if(ldap_admin_pass==''){
                         $("#settings-ldap_admin_pass").blur();
                         error4=true;
                }
                if(error4){
                        return false;
                }
                $('#checkAd').val(0);
                $('#testmessagestatus').html(null);
		$.ajax({
	        url    : baseUrl +'/system/testldapconfig',
	        type   : 'post',
	        data   : form.serialize(),
	         beforeSend : function()    {
        		showLoader();
        	},
	        success: function (response){
	        	hideLoader();
	        	if(response==1){
	        		$('#checkAd').val(1);
	        		$('#testmessagestatus').removeClass('alert-danger').addClass('alert-success');
	        		$('#testmessagestatus').html(' LDAP Connection Successful');
	        	}else{
	        		$('#checkAd').val(0);
	        		$('#testmessagestatus').removeClass('alert-success').addClass('alert-danger');
	        		$('#testmessagestatus').html(' LDAP Connection Not Successful');
	        	}
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
        		commonAjax(baseUrl +'/system/ldapconfig','admin_main_container');
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
JS;
$this->registerJs($js);
if(!empty($json_data)){foreach ($json_data as $ele=>$eleval){$model->$ele=$eleval;}}
?>
		   <div class="right-main-container">
			<div class="sub-heading"><a href="javascript:void(0);" title="LDAP Configuration" class="tag-header-black">LDAP Configuration</a></div>
			<?php $radio_data =  Yii::$app->params['ladap_connection_type']; ?>
			<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true ]); ?>
				<?= IsataskFormFlag::widget(); // change flag ?>
				<fieldset class="one-cols-fieldset">
                                    <div class="email-confrigration-table"><?php if(!isset($model->ldap_connection_type)){$model->ldap_connection_type='AD'; }?>
                                        <?= $form->field($model, 'ldap_connection_type',['template' => '<div class="row custom-full-width"><fieldset><legend class="sr-only">Connection Type</legend><div class="col-md-2">{label}</div><div class="col-md-8"><div class="row">{input}{error}{hint}</div></div></fieldset></div>'])->radioList(['AD'=>'Microsoft Active Directory Server','LDAP'=>'Linux LDAP Server'],
                                            ['item' => function($index, $label, $name, $checked, $value) use($radio_data) {
                                                    $return = '<div class="col-sm-6"><label for="'.$name.'-'.$value.'">';
                                                        if($checked)
                                                            $return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" aria-label="'. $radio_data[$value].'" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '">';
                                                        else
                                                            $return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" aria-label="'. $radio_data[$value].'" id="'.$name.'-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '">';
                                                        $return .= ucwords($label);
                                                $return .= '</label></div>';
                                                return $return;
                                            }]
                                        )->label($model->getAttributeLabel('ldap_connection_type'), ['class'=>'form_label']); ?>
                                    	<?= $form->field($model, 'servers',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-6">{input}{error}{hint}</div></div>'])->textInput()->label($model->getAttributeLabel('servers'), ['class'=>'form_label']);?>
					<?= $form->field($model, 'ldap_port',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-6">{input}{error}{hint}</div></div>'])->textInput()->label($model->getAttributeLabel('ldap_port'), ['class'=>'form_label']);?>
					<?= $form->field($model, 'defaultDomain',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-6">{input}{error}{hint}</div></div>'])->textInput()->label($model->getAttributeLabel('defaultDomain'), ['class'=>'form_label']);?>
					<?= $form->field($model, 'search',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-6">{input}{error}{hint}</div></div>'])->textInput()->label($model->getAttributeLabel('search'), ['class'=>'form_label']);?>
					<?= $form->field($model, 'ldap_admin',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-6">{input}{error}{hint}</div></div>'])->textInput()->label($model->getAttributeLabel('ldap_admin'), ['class'=>'form_label']);?>
					<?= $form->field($model, 'ldap_admin_pass',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-6">{input}{error}{hint}</div></div>'])->passwordInput(['aria-label'=>' '])->label($model->getAttributeLabel('ldap_admin_pass'), ['class'=>'form_label']);?>
					<?php  /* = $form->field($model, 'ldap_user_filter',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-10">{input}{error}{hint}</div></div>','labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $user_filter,
					'options' => ['prompt' => 'Select User LDAP Filter','nolabel'=>true],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/
					/*]);
				?>
				<div class="row">
				 <div class="form-group">
				  <div class="col-md-2">&nbsp;</div>
				  <div class="col-md-10"><input id="UserLdapFilterinput" value="<?php echo $model->ldap_user_filter_custom;?>" type="text" name="UserLdapFilterinput" size="30" class="form-control" aria-label="Ldap Filter input"></div>
				 </div>
				</div>
				<?php */?>
				<?= $form->field($model, 'ldap_group_filter',['template' => '<div class="row"><div class="col-md-2">{label}</div><div class="col-md-10">{input}{error}{hint}</div></div>','labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $group_filter,
					'options' => ['prompt' => 'Select Group LDAP Filter','nolabel'=>true],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/]); ?>
				<div class="row">
				 <div class="form-group">
					<div class="col-md-2">&nbsp;</div>
					<div class="col-md-10"><input id="GroupLdapFilterinput" value="<?php echo $model->ldap_group_filter_custom;?>" type="text" name="GroupLdapFilterinput" size="30" class="form-control" aria-label="Group Ldap Filter input"></div>
				 </div>
				</div>

		    </div>
			</fieldset>

			<div class="button-set text-right">
				<input type="hidden" value="" name="buttonSubmit" id="buttonSubmit">
				<input type="hidden" id="checkAd" value="<?php if((isset($model->id) && $model->id!="")){ ?>1<?php }else{ ?>0<?php } ?>" />
				<?php if((isset($model->id) && $model->id!="")){
            echo  Html::button('Disable LDAP', ['title'=>" Disable LDAP",'class' => 'btn btn-primary pull-left','id'=>'submitbtncancel','style'=>'margin-right:10px;']);
            ?>
            <?= Html::button('Test', ['title'=>"Test",'class' => 'btn btn-primary pull-left','id'=>'testAd']) ?><span id="testmessagestatus" class="pull-left"></span>
        <?php } else { ?>
          <?= Html::button('Test', ['title'=>"Test",'class' => 'btn btn-primary pull-left','id'=>'testAd']) ?><span id="testmessagestatus" class="pull-left"></span>
          <?php
          echo  Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','id'=>'cancelldapform']);
        } ?>
					<?= Html::button('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitbtnconfig']) ?><span id="updatemessagestatus" class="pull-left"></span>
			</div>
			<?php ActiveForm::end(); ?>
		   </div>
<script>
$(function() {
  $('input').customInput();
});
$('#cancelldapform').click(function(){
	commonAjax(baseUrl +'/system/ldapconfig','admin_main_container');
});
$(function() {
  $('input').customInput();
  $('#active_form_name').val('Settings'); // form name
  $('input').bind('input', function(){
		$('#Settings #is_change_form').val('1');
		$('#Settings #is_change_form_main').val('1');
  });
  $(':radio').change(function(){
		$('#Settings #is_change_form').val('1');
		$('#Settings #is_change_form_main').val('1');
  });
  $('select').on('change', function() {
		$('#Settings #is_change_form').val('1');
		$('#Settings #is_change_form_main').val('1');
  });
});
</script>
<noscript></noscript>
