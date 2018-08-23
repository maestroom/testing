<?php
// User Form
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use app\models\Options;
use app\models\User;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
// End
/*$js = <<<JS
	function SaveUser(form_id,btn){
		var form = $('form#'+form_id);
			
		$.ajax({
			url    : form.attr('action'),
			cache: false,
			type   : 'post',
			data   : form.serialize(),
			beforeSend : function()    {
				showLoader();
				$(btn).attr('disabled','disabled');
			},
			success: function (response) {
				hideLoader();
				if(response == 'OK'){
					commonAjax(baseUrl +'/user/manage-user','admin_main_container');
				}else{
					$(btn).removeAttr("disabled");
				}
			},
			error  : function (){
				console.log('internal server error');
			}
		});
	}
JS;
$this->registerJs($js);*/
$first_tab = 'Add';
$resultsJs = <<< JS
function (data, params) {
	params.page = params.page || 1;
    return {
        results: data.items,
        pagination: {
            more: (params.page * 50) < data.total_count
        }
    };
}
JS;

?>

<fieldset class="two-cols-fieldset">
	<div id="user-tabs">
		<?php if(!$model->isNewRecord){ $first_tab = 'Edit'; } ?>
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
			<li><a href="#first" title="<?= $first_tab ?> User"><?= $first_tab ?> User</a></li>
			<!--<li><a href="#second" title="User Access">User Access</a></li>
			<?php //if(!$model->isNewRecord && (new User)->checkAccess(8.0621)){ ?><li><a href="#third" title="User Settings">User Settings</a></li><?php //} ?>-->
		</ul>
		<div id="first">
			<?php 
				$style_inner_first='display:block;'; $style_inner_second='display:none;'; 
				
				if(!$model->isNewRecord){$style_inner_first="display:none;"; $style_inner_second="display:block;";} else {$style_inner_second="display:none;"; $style_inner_first="display:block;";} 
				if($is_ad){
					$style_inner_second="display:none;"; $style_inner_first="display:block;";
				}else{
					$style_inner_first="display:none;"; $style_inner_second="display:block;";
				}
				?>
				<div id="inner_first" style="<?= $style_inner_first ?>">
					<div class="tab-inner-fix">
                                            <fieldset>
                                                <legend class="sr-only">User</legend>                                            
				    	<div class='row input-field form-group'>
				    		<div class="custom-full-width">
				    			<div class='col-md-10'><input aria-setsize="3" aria-posinset="1" type="radio" name="add_type" class="ldap" id="add_ADI" value="ADI" title="this field is required" /> 
				    				<label for="add_ADI">LDAP (Individual)</label>
				    			</div>
				    		</div>
				    	</div>
				    	<div class='row input-field form-group'>
				    		<div class="custom-full-width">
				    			<div class='col-md-10'><input aria-setsize="3" aria-posinset="2" type="radio" name="add_type" class="ldap" id="add_ADG" value="ADG" title="this field is required" /> 
				    				<label for="add_ADG">LDAP (Group)</label>
				    			</div>
				    		</div>
				    	</div>
				    	<div class='row input-field form-group'>
				    		<div class="custom-full-width">
				    			<div class='col-md-10'><input aria-setsize="3" aria-posinset="3" type="radio" name="add_type" class="ldap" id="add_IAT" value="IAT" <?php if($model->isNewRecord) {?>checked="checked"<?php }?>  /> 
				    				<label for="add_IAT">IS-A-TASK</label>
				    			</div>
				    		</div>
				    	</div>
                                        </fieldset>
					</div>
					<div class="button-set text-right">
						<?php /* Html::button('Cancel',['title' => 'Cancel','class' =>  'btn btn-primary','onclick'=>'clearLdapRadio("'.$model->formName().'",this);'])*/ ?>
						<?= Html::button('Next',['title' => 'Next','class' => 'btn btn-primary','id'=>'usrNxtAd']) ?>
					</div>
				</div>
				
				<div id="inner_second" style="<?= $style_inner_second ?>">
					<?php $form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
					<?= IsataskFormFlag::widget(); // change flag ?>
					<?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
					<div style="display: none">
						<?= $form->field($model, 'role_type',['template' => "{input}"])->dropDownList($role_types,['title'=>'Role Type']); ?>
				  	</div>
					<div id ="IAT">
						<div class="tab-inner-fix">
						    <div class="create-form">
						    <?php if($model->isNewRecord)$model->usr_type = '1'; ?>
						    	<?php if($model->usr_type==3) {
	        						echo "<div class='form-group'><div class='row input-field'><div class='col-md-3'><label for='user-usr_type' class='form_label'>User Type</label></div><div class='col-md-7'>Active Directory</div></div></div>";
	        						echo $form->field($model, 'usr_type',['template' =>"{input}"])->hiddenInput();
	        					 } else { ?> 	
                                                        <fieldset>
						    	<?= $form->field($model, 'usr_type',['template' => "<div class='row input-field custom-full-width'><legend class='sr-only'>User Type</legend><div class='col-md-3'>{label}</div><div class='col-md-7'><div class='row'>{input}\n{hint}\n{error}</div></div></div>",'labelOptions'=>['class'=>'form_label']])->radioList(['1' => ' Internal', '2' => ' External'],
								  	['item' => function($index, $label, $name, $checked, $value) {
								  		$return = '<div class="col-sm-3"><label for="'.$name.'-'.$value.'" class="form_label" aria-label="User Type,'.$label.'" >';
								  		if($checked)
											$return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'" class="user_type" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '">';
										else
											$return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'" class="user_type" type="radio" name="' . $name . '" value="' . $value . '">';
											$return .= ucwords($label);
										$return .= '</label></div>';
										return $return;
									}]
							  	); ?> </fieldset><?php } ?>
                                                       
							  	<?= $form->field($model, 'role_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
							  		->widget(Select2::classname(), [
											'data' => $role_details,
											'options' => [  'prompt' => 'Select User Role', 
                                                                                                        'title' => 'User Role',
                                                                                                        'nolabel' => true,
                                                                                                        'aria-required' =>'true'
										],
										'pluginOptions' => [
											'allowClear' => true
										],
									]); 
								?>
									<?php if($model->usr_type==3) {
										echo "<div class='form-group'><div class='row input-field'><div class='col-md-3'><label for='user-usr_type' class='form_label'>User Name</label></div><div class='col-md-7'>".$model->usr_username."</div></div></div>";
										echo $form->field($model, 'usr_username',['template' =>"{input}"])->hiddenInput();
										echo "<div class='form-group'><div class='row input-field'><div class='col-md-3'><label for='user-usr_type' class='form_label'>First Name</label></div><div class='col-md-7'>".$model->usr_first_name."</div></div></div>";
										echo $form->field($model, 'usr_first_name',['template' =>"{input}"])->hiddenInput();
										echo "<div class='form-group'><div class='row input-field'><div class='col-md-3'><label for='user-usr_type' class='form_label'>Last Name</label></div><div class='col-md-7'>".$model->usr_lastname."</div></div></div>";
										echo $form->field($model, 'usr_lastname',['template' =>"{input}"])->hiddenInput();
										echo "<div class='form-group'><div class='row input-field'><div class='col-md-3'><label for='user-usr_type' class='form_label'>User MI</label></div><div class='col-md-7'>".$model->usr_mi."</div></div></div>";
										echo $form->field($model, 'usr_mi',['template' =>"{input}"])->hiddenInput();
										echo "<div class='form-group'><div class='row input-field'><div class='col-md-3'><label for='user-usr_type' class='form_label'>User Email</label></div><div class='col-md-7'>".$model->usr_email."</div></div></div>";
										echo $form->field($model, 'usr_email',['template' =>"{input}"])->hiddenInput();
										echo $form->field($model, 'usr_pass',['template' =>"{input}"])->hiddenInput();
										echo $form->field($model, 'confirm_password',['template' =>"{input}"])->hiddenInput();
									}else{ ?>
										<?= $form->field($model, 'usr_username',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['usr_username']]); ?>
										<?= $form->field($model, 'usr_pass',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->passwordInput(['maxlength'=>32,'maxlength'=>$model_field_length['usr_pass']]); ?>
										<?= $form->field($model, 'confirm_password',['template' => "<div class='row input-field'><div class='col-md-3'><label for='user-confirm_password' class='form_label'>Confirm Password<span class='text-danger'>*</span></label></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->passwordInput(['maxlength'=>$model_field_length['usr_pass'],'aria-required'=>'true']); ?>
										<?= $form->field($model, 'change_pass_after',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<em class='text-danger'>*</em></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
											'data' => $changePassAfter,
											'options'=>['prompt'=>'Select Force Password Change', 'title' => 'Force Password Change', 'nolabel' => true,'aria-required' =>'true'],
											'pluginOptions' => [
													// 'allowClear' => true
												],	
											]); 
										?>
										<?= $form->field($model, 'usr_first_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'First Name<span class="require-asterisk-again">*</span>']])->textInput(['maxlength'=>$model_field_length['usr_first_name']]); ?>
										<?= $form->field($model, 'usr_lastname',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Last Name<span class="require-asterisk-again">*</span>']])->textInput(['maxlength'=>$model_field_length['usr_lastname']]); ?>
										<?= $form->field($model, 'usr_mi',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'User MI']])->textInput(['maxlength'=>$model_field_length['usr_mi']]); ?>
										<?= $form->field($model, 'usr_email',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'User Email<span class="require-asterisk-again">*</span>']])->textInput(['maxlength'=>$model_field_length['usr_email'],'aria-required'=>'true']); ?>
									<?php } ?>
										<?= $form->field($model, 'location_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
											'data' => $teamLocation,
											'options'=>['prompt'=>'Select Location','title' => 'Location', 'nolabel' => true],
												/* 'pluginOptions' => [
													'allowClear' => true
												], */ 
											]); ?>
									<?php  
										$userlog_date='';
										if(!$model->isNewRecord){$userlog_date='<div>';
										   foreach ($actLog_user as $userlog) {												   
												$userlog_date .= '<em>('.($userlog->activity_type).' '.(new Options)->ConvertOneTzToAnotherTz($userlog->date_time,"UTC",$_SESSION["usrTZ"]) .')</em><br>';
										   } $userlog_date.='</div>';
										} else 
											$model->status = true;
									?>
									<?= $form->field($model, 'status',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}<label for='user-status'>Active</label>".$userlog_date."\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'custom-full-width']])->checkbox(['label' => '','labelOptions'=>['class'=>'custom-full-width']])->label(false); ?>
						  	</div>
						</div>
					</div>
					<div id="ADI" style="display: none;">
						<div class="tab-inner-fix">
						    <div class="create-form">
								<?= $form->field($model, 'ad_users',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'AD Users<span class="require-asterisk-again">*</span>']])->widget(Select2::classname(), ['options' => ['prompt' => 'Select Active Directory User', 'id' => 'user-ad_users'],
								'pluginOptions' => [
									'allowClear' => false,
									'minimumInputLength' => 1,
									'ajax' => [
										'url' => Url::to(['user/getadusers']),
										'dataType' => 'json',
										'delay' => 250,
										'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
										'processResults' => new JsExpression($resultsJs),
										'cache' => true
										
									],
								]
								]); ?>
								<?= $form->field($model, 'role_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
								'data' => $role_details,
								'options' => ['prompt' => 'Select User Role', 'id' => 'user-role_id_ADI', 'title' => 'User Role', 'nolabel' => true]]); ?>
								<?= 
									$form->field($model, 'location_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
										'data' => $teamLocation,
										'options' => ['prompt' => 'Select Location', 'id' => 'user-location_id_ADI'],
										'pluginOptions' => [
											// 'dropdownParent' => new JsExpression('$("#bulkedituser_parent")')
									],]); 
								?>
							</div>
						</div>	
					</div>
					<div id="ADG"  style="display: none;">
						<div class="tab-inner-fix">
						    <div class="create-form">
								<?= $form->field($model, 'ad_group',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'AD Group<span class="require-asterisk-again">*</span>']])->widget(Select2::classname(), [
									'options' => ['prompt' => 'Select Active Directory Group', 'id' => 'user-ad_group','onchange'=>'showGroupMember(this.value);'],
									]); ?>
								<div id="ad_grmemeber">
								
								</div>
								<?= 
									$form->field($model, 'role_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
										'data' => $role_details,
										'options' => ['prompt' => 'Select User Role', 'id' => 'user-role_id_ADG'],
									]); 
								?>
								<?= 
									$form->field($model, 'location_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
										'data' => $teamLocation,
										'options' => ['prompt' => 'Select Location', 'id' => 'user-location_id_ADG'],
									]); 
								?>
							</div>
						</div>
					</div>
					
					<div class="button-set text-right">
						<?php if(!$model->isNewRecord){
							echo $form->field($model, 'id',['template' => "{input}"])->hiddenInput();
						} ?>
						<input type="hidden" name="usertypes" id="usertypes" value="IAT">
						<?php // Html::button('Back', ['title' => 'Back','class' => 'btn btn-primary','id'=>'user-back']) ?>        
					    <?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete','class' =>  'btn btn-primary','id' => 'user_form','onclick'=>'useraction("'.$model->formName().'","'.$model->id.'");']) ?>
					     <?php 
							if($model->isNewRecord){
								$mod = 'Add';
								$user_type = "";
							}else{
								$mod = 'Edit';
								$user_type = $model->usr_type;
							}
							$btnval = $model->isNewRecord ? 'Add' : 'Update';
						?>
					    <?= Html::button($btnval, ['title' => $btnval,'class' => 'btn btn-primary','onclick'=>'NextUserAdd("User","'.$mod.'","'.$user_type.'",this);']) ?>
					</div>
				</div>
			</div>
			<?php ActiveForm::end(); ?>
		</div>	
	</fieldset>
<script>


/**
 * Previous user settings cancel button
 */
$('#previous_user_settings_last').click(function(event){
	var chk_status = checkformstatus(event); // check form edit status 
	if(chk_status==true) commonAjax(baseUrl +'/user/manage-user','admin_main_container');
});	
$('#User input').bind('input', function(){
	$('#User #is_change_form').val('1');
	$('#User #is_change_form_main').val('1');
});
$('#User :radio').change(function(){
	$('#User #is_change_form').val('1');
	$('#User #is_change_form_main').val('1');
});
$('#User select').on("change",function(){
	$('#User #is_change_form').val('1');
	$('#User #is_change_form_main').val('1');
});
$('#User :checkbox').change(function(){
	$('#User #is_change_form').val('1');
	$('#User #is_change_form_main').val('1');
	$('#update-user-right #is_change_form').val('1');
	$('#update-user-right #is_change_form_main').val('1');
});
$('document').ready(function(){
	$('#active_form_name').val('User'); // Add form 
});
				
$(function() {
   /**
	* Check custom checkbox already exist or not
	*/
	$('input').customInput();
	/**
	 * Outer User Tables
	 */
    $( "#user-tabs" ).tabs({
      beforeActivate: function (event, ui) {
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });
});	


/**
 * User Next Ad
 */
$('#usrNxtAd').click(function() {
	var typeselected=$("input:radio[name=add_type]:checked").val();
	$('#usertypes').val(typeselected);
	if(typeselected=='IAT'){
		$("#IAT").show();
		$("#ADG").hide();
		$("#ADI").hide();
		$('#inner_second').css('display','block');
		$('#inner_first').css('display','none');
		$('#second').hide();
	}
	if(typeselected=='ADI'){
		$("#IAT").hide();
		$("#ADG").hide();
		$("#ADI").show();
		$('#inner_second').css('display','block');
		$('#inner_first').css('display','none');
		$('#second').hide();
		$.ajax({
			url:baseUrl+'user/getapusers',
			type:'get',
			success: function (response){
				$("#user-ad_users").empty();
				$("#user-ad_users").append("<option value=''>Select Active Directory User</option>");
				var obj = jQuery.parseJSON(response);
				$.each(obj, function(key,value){
					$("#user-ad_users").append("<option value='"+key+"'>"+value+"</option>");
				});
			}
		});
		
	}
	if(typeselected=='ADG'){
		$("#IAT").hide();
		$("#ADI").hide();
		$("#ADG").show();
		$('#inner_second').css('display','block');
		$('#inner_first').css('display','none');
		$('#second').hide();
		$.ajax({
			url:baseUrl+'user/get-adgroups',
			type:'get',
			success: function (response){
				$("#user-ad_group").empty();
				$("#user-ad_group").append("<option value=''>Select Active Directory Group</option>");
				var obj = jQuery.parseJSON(response);
				$.each(obj, function(key,value){
					$("#user-ad_group").append("<option value='"+key+"'>"+value+"</option>");
				});
			}
		});
	}
	
});

/**
 * User Create First Page Back Button
 */
/*$('#user-back').click(function(){
	$('#second').hide();	
	$('#first').show();
	$('#inner_second').hide();
	$('#inner_first').show();
	
});*/
$('#IAT #user-role_id').change(function(){
	$('#ADI #user-role_id_ADI').val(this.value);
	$('#ADG #user-role_id_ADG').val(this.value);	
});
$('#ADI #user-role_id_ADI').change(function(){
	$('#IAT #user-role_id').val(this.value);
	$('#ADG #user-role_id_ADG').val(this.value);
});
$('#ADG #user-role_id_ADG').change(function(){
	$('#IAT #user-role_id').val(this.value);
	$('#ADI #user-role_id_ADI').val(this.value);
});
$('#IAT #user-location_id').change(function(){
	$('#ADI #user-location_id_ADI').val(this.value);
	$('#ADG #user-location_id_ADG').val(this.value);	
});
$('#ADI #user-location_id_ADI').change(function(){
	$('#IAT #user-location_id').val(this.value);
	$('#ADG #user-location_id_ADG').val(this.value);
});
$('#ADG #user-location_id_ADG').change(function(){
	$('#IAT #user-location_id').val(this.value);
	$('#ADI #user-location_id_ADI').val(this.value);
});
/**
 * User Access
 
 $('#previous_user_access').click(function(){
	$('#first').show(); $('#second').hide();
	jQuery('li[aria-controls="third"]').hide();
	jQuery('li[aria-controls="second"]').removeClass('ui-tabs-active ui-state-active');
	jQuery('li[aria-controls="first"]').addClass('ui-tabs-active ui-state-active');
 });*/

/**
 * User Action Delete or Clear
 */
function useraction(form_id, user_id){
	 if(user_id != ''){
		var user_name  = $('#user-usr_username').val(); 
		var ans = confirm("Are you sure you want to Delete "+user_name+"?");
		if(ans == true){
		 	$.ajax({
		      	url    : baseUrl +'/user/user-delete',
		        cache: false,
		        type   : 'post',
		        data   : 'user_id='+user_id,
		        success: function (response){
			       if(response == 'OK'){
		        	   commonAjax(baseUrl +'/user/manage-user','admin_main_container');
		           } else {
					 alert(user_name+' is already in Use.');		
					 return false;
				   }    
			    },
		        error  : function (){
		            console.log('internal server error');
		        }
	    	}); 
		}	
	 } else {
//		 $('#user-role_id').val('');
//		 $('#user-usr_username').val('');
//		 $('#user-usr_pass').val('');
//		 $('#user-confirm_password').val('');
//		 $('#user-change_pass_after').val('');
//		 $('#user-usr_first_name').val('');
//		 $('#user-usr_lastname').val('');
//		 $('#user-usr_mi').val('');
//		 $('#user-usr_email').val('');
//		 $('#user-location_id').val('');
//		 $('#user-status').prop('checked',false);
                 commonAjax(baseUrl +'/user/manage-user','admin_main_container');
	 }
 }

 function showGroupMember(group){
	 $.ajax({
			url:baseUrl+'user/get-ad-group-members',
			type:'get',
			data:{'adg':group},
			success: function (response){
				$('#ad_grmemeber').html(response);
			}
		});
 }

/**
 * User Add in Manage User
 */
function NextUserAdd(formid,mod,usr_type,btn)
{
	showLoader();
	var form = $('form#'+formid).serialize();
	var url = $('form#'+formid).attr('action');
	if(mod == 'Edit' && usr_type == '3'){
		var typeselected='ADI';
	}else{
		var typeselected=$("input:radio[name=add_type]:checked").val();
	}
	var typeselected=$("input:radio[name=add_type]:checked").val();
	if(typeselected=='ADI'){
		haserror=false;
		if($('#user-ad_users').val()==""){
			$("#"+typeselected+" #user-ad_users").parent().find('div.help-block').html('AD Users cannot be blank.');
			$("#"+typeselected+" #user-ad_users").parent().parent().parent().addClass('has-error');
			haserror=true;
		}
		if($("#"+typeselected+" #user-role_id_ADI").val()==""){
			$("#"+typeselected+" #user-role_id_ADI").parent().find('div.help-block').html('Role cannot be blank.');
			$("#"+typeselected+" #user-role_id_ADI").parent().parent().parent().addClass('has-error');
			haserror=true;
		}
		if(!haserror){
			$.ajax({
				url    : url,
				cache: false,
				type   : 'post',
				data   : form,
				beforeSend : function()    {
					showLoader();
					$(btn).attr('disabled','disabled');
				},
				success: function (response) {
					hideLoader();
					if(response == 'OK'){
						commonAjax(baseUrl +'/user/manage-user','admin_main_container');
					}else{
						$(btn).removeAttr("disabled");
					}
				},
				error  : function (){
					console.log('internal server error');
				}
			});
			/* Next user access */
			/*$.ajax({
				url:baseUrl+'user/next-user-add-access',
				type:'post',
				data:form,
				success: function (response){
					hideLoader();
					$("#user-tabs").tabs("enable");
					$('#second').show(); $('#first').hide();
					$('#second').html(response); // get the response
					jQuery('li[aria-controls="third"]').show();
					jQuery('li[aria-controls="first"]').removeClass('ui-tabs-active ui-state-active');
					jQuery('li[aria-controls="second"]').addClass('ui-tabs-active ui-state-active');
					$("#user-tabs").tabs("option","disabled", [0]);
				
					$("#user-role_type").val($("#user-role_id").val());
					var role_type = $( "#user-role_type option:selected" ).text();
					if(role_type == '1,2') {
						$( "#user-access-tabs" ).tabs('enable');
					}
					else if(role_type == '2,1'){
						$( "#user-access-tabs" ).tabs('enable');
					}
					else if(role_type == '1'){
						$( "#user-access-tabs" ).tabs("option","disabled", [1]);
						$( "#user-access-tabs" ).tabs("option","active",[0]);
					}
					else if(role_type == '2'){
						$( "#user-access-tabs" ).tabs("option","disabled", [0]);
						$( "#user-access-tabs" ).tabs("option","active",[1]);
					}
				}
			});*/
		}else{
			hideLoader();
		}
	}
	else if(typeselected=='ADG'){
		haserror=false;
		if($('#user-ad_group').val()==""){
			$("#"+typeselected+" #user-ad_group").parent().find('div.help-block').html('AD Group cannot be blank.');
			$("#"+typeselected+" #user-ad_group").parent().parent().parent().addClass('has-error');
			haserror=true;
		}
		var ischecked_grpaduser=0;
		if($("#grp_users-tree").length) {
			$("#grp_users-tree").dynatree("getRoot").visit(function(node){
				if(node.isSelected()) {
					ischecked_grpaduser=1;
				}
			});
		}
		//if($('.grp:checked').length==0){
		if(ischecked_grpaduser==0) {	
			$("#"+typeselected+" .grp").closest('.col-md-7').find('.help-block').html('AD Users cannot be blank.');
			$("#"+typeselected+" .grp").closest('.col-md-7').parent().parent().addClass('has-error');
			haserror=true;
		}
		if($("#"+typeselected+" #user-role_id_ADG").val()==""){
			$("#"+typeselected+" #user-role_id_ADG").parent().find('div.help-block').html('Role cannot be blank.');
			$("#"+typeselected+" #user-role_id_ADG").parent().parent().parent().addClass('has-error');
			haserror=true;
		}
		if(!haserror){
			$.ajax({
				url    : url,
				cache: false,
				type   : 'post',
				data   : form,
				beforeSend : function()    {
					showLoader();
					$(btn).attr('disabled','disabled');
				},
				success: function (response) {
					hideLoader();
					if(response == 'OK'){
						commonAjax(baseUrl +'/user/manage-user','admin_main_container');
					}else{
						$(btn).removeAttr("disabled");
					}
				},
				error  : function (){
					console.log('internal server error');
				}
			});		
			/* Next user access 
			$.ajax({
				url:baseUrl+'user/next-user-add-access',
				type:'post',
				data:form,
				success: function (response){
					hideLoader();
					$("#user-tabs").tabs("enable");
					$('#second').show(); $('#first').hide();
					$('#second').html(response); // get the response
					jQuery('li[aria-controls="third"]').show();
					jQuery('li[aria-controls="first"]').removeClass('ui-tabs-active ui-state-active');
					jQuery('li[aria-controls="second"]').addClass('ui-tabs-active ui-state-active');
					$("#user-tabs").tabs("option","disabled", [0]);
				
					$("#user-role_type").val($("#user-role_id").val());
					var role_type = $( "#user-role_type option:selected" ).text();
					if(role_type == '1,2'){
						$( "#user-access-tabs" ).tabs('enable');
					}
					else if(role_type == '2,1'){
						$( "#user-access-tabs" ).tabs('enable');
					}
					else if(role_type == '1'){
						$( "#user-access-tabs" ).tabs("option","disabled", [1]);
						$( "#user-access-tabs" ).tabs("option","active",[0]);
					}
					else if(role_type == '2'){
						$( "#user-access-tabs" ).tabs("option","disabled", [0]);
						$( "#user-access-tabs" ).tabs("option","active",[1]);
					}
				}
			});*/
		}else{
		hideLoader();
		}
	}
	else {
		typeselected="IAT"; // typeselected
		
		/* Ajax */
		$.ajax({
			url:baseUrl+'user/uservalidate',
			type:'post',
			data:form,
			success:function(response){
				if(response.length == 0){
					$.ajax({
						url    : url,
						cache: false,
						type   : 'post',
						data   : form,
						beforeSend : function() {
							showLoader();
							$(btn).attr('disabled','disabled');
						},
						success: function (response) {
							hideLoader();
							if(response == 'OK'){
								commonAjax(baseUrl +'/user/manage-user','admin_main_container');
							}else{
								$(btn).removeAttr("disabled");
							}
						},
						error  : function (){
							console.log('internal server error');
						}
					});
					/*$.ajax({
						url:baseUrl+'user/next-user-add-access',
						type:'post',
						data:form,
						success: function (response){
							hideLoader();
							$("#user-tabs").tabs("enable");
							$('#second').show(); $('#first').hide();
							$('#second').html(response); // get the response
							jQuery('li[aria-controls="third"]').show();
							jQuery('li[aria-controls="first"]').removeClass('ui-tabs-active ui-state-active');
							jQuery('li[aria-controls="second"]').addClass('ui-tabs-active ui-state-active');
							$("#user-tabs").tabs("option","disabled", [0]);
							
							$("#user-role_type").val($("#user-role_id").val());
							var role_type = $( "#user-role_type option:selected" ).text();
							if(role_type == '1,2'){
								$( "#user-access-tabs" ).tabs('enable');
							}
							else if(role_type == '2,1'){
								$( "#user-access-tabs" ).tabs('enable');
							}
							else if(role_type == '1'){
								$( "#user-access-tabs" ).tabs("option","disabled", [1]);
								$( "#user-access-tabs" ).tabs("option","active",[0]);
							}
							else if(role_type == '2'){
								$( "#user-access-tabs" ).tabs("option","disabled", [0]);
								$( "#user-access-tabs" ).tabs("option","active",[1]);
							}
						}
					});*/
				} else {
					hideLoader();
					for (var key in response) {
						if(key == 'user-change_pass_after' || key == 'user-role_id') {
							$("#"+typeselected+" #"+key).next().next().html(response[key]);
						} else {
							$("#"+typeselected+" #"+key).next().html(response[key]);
						}
						$("#"+typeselected+" #"+key).parent().parent().parent().addClass('has-error');
					}
				}
			}
		});
	}
}

/**
 * myheader span show contant of each header
 */
/*$(".myheader span").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
          //  change text based on condition
          //return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });
});*/


/**
 * Check All Inner checkbox for my Teams
 * @ return checked
 */
/*function chkall_inner_team(loop1){
	var total_chk_box = $('.innerchk_'+loop1).size();
	var count = $('.innerchk_'+loop1+':checked').size();
	if(count >= 1){	
		$(".outerchk_"+loop1).prop('checked',true);	
		$(".outerchk_team_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$(".outerchk_"+loop1).prop('checked',false);	
		$(".outerchk_team_all_"+loop1).removeClass('checked');
	}
}*/

/*function chkall_team(loop1){
	var total_chk_box = $('.outer_security').size();
	var cnt = $('.outer_security:checked').size();
	$('.team_all').prop('checked',false); // select All checkbox unchecked 
	$('.team_class_all').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('.team_class_all').addClass('checked');
		$('.team_all').prop('checked',true);
	}
	if ($('#team_chk_'+loop1).is(':checked')){
		 $(".innerchk_"+loop1).prop('checked',true);
		 $(".innerchk_"+loop1).siblings().addClass('checked');
	}else{
		$(".innerchk_"+loop1).prop('checked',false);
		$(".innerchk_"+loop1).siblings().removeClass('checked');
	}
}*/

/**
 * Check All Inner checkbox For my Cases
 * @ return checked
 */
/*function chkall_inner_case(loop1){
	var total_chk_box = $('.innerchk_case_'+loop1).size();
	var count = $('.innerchk_case_'+loop1+':checked').size();
	if(count >= 1){	
		$(".outerchk_case_"+loop1).prop('checked',true);	
		$(".outerchk_case_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$(".outerchk_case_"+loop1).prop('checked',false);	
		$(".outerchk_case_all_"+loop1).removeClass('checked');
	}
}*/

/*function chkall_case(loop1){
	var total_chk_box = $('.outer_security_case').size();
	var cnt = $('.outer_security_case:checked').size();
	$('.case_all').prop('checked',false); // select All checkbox unchecked 
	$('.case_all_class').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('.case_all').prop('checked',true);
		$('.case_all_class').addClass('checked');
	}
	if ($('#chk_case_'+loop1).is(':checked')){
		 $(".innerchk_case_"+loop1).prop('checked',true);
		 $(".innerchk_case_class_"+loop1).addClass('checked');
	}else{
		$(".innerchk_case_"+loop1).prop('checked',false);
		$(".innerchk_case_class_"+loop1).removeClass('checked');
	}
}*/

/**
 * Check All Inner checkbox For User Settings
 * @ return checked
 */
/*function chkall_inner_setting(loop1, loop2)
{
	var total_chk_box = $('.innerchk_setting_'+loop1).size();
	var count = $('.innerchk_setting_'+loop1+':checked').size();
	$('#force_options_'+loop1).css('display','block');
	$(".outerchk_setting_"+loop1).prop('checked',false);
	if(total_chk_box == count){
		$(".outerchk_setting_"+loop1).prop('checked',true);
	}
	
	/** chk inner settings **/
	/*if(!$('#innerchk_setting_'+loop1+'_'+loop2).is(':checked')){
		$(".force_inner_label_"+loop2).removeClass('checked');
		$('#innerchk_setting_'+loop1+'_'+loop2).prop('checked',false);
	}
}*/

/*$(document).ready(function(){
	var total_chk_box = $('.outer_security_setting').size();
	var cnt = $('.outer_security_setting:checked').size();
	$('.user_security_all').prop('checked',false);
	$('.user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('.user_security_all').prop('checked',true);
		$('.user_security_select_all').addClass('checked');
	}
	$("#user_security_all").change(function () {
		if ($('#user_security_all').is(':checked')){
			$(".outer_security_setting_all").addClass('checked');
	    	$(".inner_security_setting_all").addClass('checked');
			$(".outer_security_setting").prop('checked',true);
	    	$(".inner_security_setting").prop('checked',true);
		}else{
			$(".outer_security_setting_all").removeClass('checked');
	    	$(".inner_security_setting_all").removeClass('checked');
			$(".outer_security_setting").prop('checked',false);
	    	$(".inner_security_setting").prop('checked',false);
		}	
	});
});*/

/*function chkall_setting(loop1){
	var total_chk_box = $('.outer_security_setting').size();
	var cnt = $('.outer_security_setting:checked').size();
	$('.user_security_all').prop('checked',false); // select All checkbox unchecked 
	$('.user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('.user_security_all').prop('checked',true);
		$('.user_security_select_all').addClass('checked');
	}
	if ($('#chk_setting_'+loop1).is(':checked')){
		 $(".innerchk_setting_"+loop1).prop('checked',true);
	}else{
		$(".innerchk_setting_"+loop1).prop('checked',false);
	}
}*/

/**
 * Check user name exist or not
 */
 function checkusernameexist(uname){
	 /*$.ajax({
	      	url    : baseUrl +'/user/check-username-exist',
	        cache: false,
	        type   : 'post',
	        data   : 'usr_username='+uname.value,
	        success: function (response){
		       if(response == 'Exist'){
		    	   $("#user-usr_username").trigger('blur');
		    	   return false;
	           }    
		    },
	        error  : function (){
	            console.log('internal server error');
	        }
 	});*/ 
 }

 /*$('.myheader').on('click',function(){
		if($(this).hasClass('myheader-selected-tab') && $(this).next().css('display') == 'block'){
			$(this).removeClass('myheader-selected-tab');
		}else{
			$(this).addClass('myheader-selected-tab');
		}	
});*/
$(function() {
	setTimeout(function(){
		$('#User #is_change_form').val('0');
		$('#User #is_change_form_main').val('0');
	},200);
});	
</script>
<noscript></noscript>
