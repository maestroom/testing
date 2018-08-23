<?php
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\Session;
use app\models\User;
use app\components\IsataskFormFlag;
use kartik\widgets\Select2;
$session = Yii::$app->session;
$email_alert_options=Yii::$app->params['email_alert_options'];
$newEmailAlerts = [];
foreach ($email_alert_options as $alertName => $security_id){
    $newEmailAlerts[$alertName] = $model->getAttributeLabel($alertName);
}
//echo "<pre>",print_r($newEmailAlerts),"</pre>";die;
natcasesort($newEmailAlerts);
foreach($newEmailAlerts as $alertName => $securityName){
    $finalEmailAlerts[$alertName] = $email_alert_options[$alertName];
}
//echo '<pre>',print_r($session['myaccess']['security_force']);
//echo '<pre>';
//print_r($finalEmailAlerts);
//die();
$this->title = 'Options';
$this->params['breadcrumbs'][] = $this->title;
$permissions= [
            'is_sub_new_task' =>10.01,
            'is_sub_com_task' =>10.02,
            'is_sub_new_production' =>10.021,
            'is_sub_production_posted' =>10.0101,
            'is_sub_past_due' =>10.03,
            'opt_posted_comment' =>10.04,
            'opt_posted_summary_comment' =>10.05,
            'is_sub_self_assign' =>10.06,
            'is_new_todo_post' =>10.07,
            'is_completed_todos' =>10.08,
            'is_todos_assign_to_me' =>10.09,
            'is_servicetask_transists' =>10.15,
            'is_cancel' =>10.011,
            'is_uncanceled' =>10.012,
            'is_unassign' =>10.061,
            'changed_instructions' =>10.0121,
            'pending_tasks' =>10.11,
            'approaching_case_budget_spend' =>10.13,
						'approaching_project_due_date'=>10.131,
            'reached_case_budget_spend' =>10.14,
            'changed_casedetail' =>10.12,
            'is_reopen_project' =>10.16,
            'is_sub_com_service' =>10.022,
						            
        ];
?>
<style>
.mycontainer .content {
    display: none;
    padding : 5px;
}
.alert{
margin: 7px 0!important;
}
</style>
<div class="row">
  <div class="col-md-12">
      <h1 id="page-title" role="heading" class="page-header"> <em class="fa fa-cogs" title="Options"></em><a href="javascript:void(0);" title="Options" class="tag-header-red"> Options </a></h1>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 single-cols-container">
    <?php $form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
    <?= IsataskFormFlag::widget(); // change flag ?>
    <div class="options-main">
      <fieldset class="single-cols-fieldset">
      	<div class="col-md-12">
            <div id="success-alert" class="alert alert-success <?php if($saved==0){?>hide<?php }?>" tabindex="0" aria-label="Success! Your Options Saved Successfully." >
		    	<button data-dismiss="alert" class="close" type="button" aria-label="Hide Alert">x</button>
		    	<strong>Success! </strong>
		    	Your Options Saved Successfully.                        
			</div>
            <?php if($saved!=0){?> 
            <script type="text/javascript">
                setTimeout(function(){                    
                    $('#success-alert').focus();
                },1000);
            </script><?php }?>
		</div>
      <div class="mycontainer">
     
        <?php if(Yii::$app->user->identity->usr_type!=3){?>
        <div class="myheader"> <a href="javascript:void(0);" title="Change Password">Change Password</a> </div>
        <div class="content">
          <div id="form_div" class="option-form-div">
            <?= $form->field($model, 'old_password',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-3'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','title'=>'Old Password']])->passwordInput(['value'=>'','maxlength'=>$model_field_length['usr_pass']]); ?>
            <?= $form->field($model, 'usr_pass',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-3'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','title'=>'New Password']])->passwordInput(['onchange' => 'user_passvalidation();','value'=>'','maxlength'=>$model_field_length['usr_pass']]); ?>
            <?= $form->field($model, 'confirm_password',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-3'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','title'=>'Confirm Password']])->passwordInput(['onchange' => 'Confirmvalidation();','value'=>'','maxlength'=>$model_field_length['usr_pass']]); ?>
          </div>
        </div>
        <?php }?>
        <div class="myheader"> <a href="javascript:void(0);" title="Configure Time Settings">Configure Time Settings</a> </div>
        <div class="content">
          <div class="option-form-div">
            <?php
			if((!isset($settings_info->fieldvalue) || isset($settings_info->fieldvalue)) && ($settings_info->fieldvalue=='default' || $settings_info->fieldvalue==1)){?>
            <div class="form-group field-user-old_password">
              <div class="row input-field">
                <div class="col-md-3">
                  <label for="evidencecustodiansforms-form_name" class="form_label" title="Set Session Timeout">Set Session Timeout</label>
                </div>
                <div class="col-md-3">
					<?php 
						echo Select2::widget([
							'name' => 'Options[session_timeout]',
							'attribute' => 'Options[session_timeout]',
							'value'=>$model->session_timeout,
							'data' => array('1200'=>'20 Min','3600'=>'1 Hours','10800'=>'3 Hours','21600'=>'6 Hours','28800'=>'8 Hours','never'=>'Never'),
							'options' => ['prompt' => 'Set Session Timeout','class' => 'form-control'],
							'pluginOptions' => [
							  'allowClear' => false
							]
						]);
					?>
                </div>
              </div>
            </div>
            <?php }?>
            <div class="form-group field-user-old_password">
					<?php 
						foreach ($timezones as $k=>$v){
							$timezone_dropdown[$k] = $v;
						}
						if(isset($model->timezone_id) && $model->timezone_id==""){
							$model->timezone_id = 'America/New_York';
						}
						echo $form->field($model, 'timezone_id',
						  [
								'template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-3'>{input}\n{hint}\n{error}</div></div>",
								'labelOptions'=>[
										'label'=>'Set Time Zone',
										'class'=>'form_label',
										'title'=>'Set Time Zone'
								]
							])->widget(Select2::classname(), [
							'data' => $timezone_dropdown,
							'value' => $model->timezone_id,
							'options' => ['prompt' => 'Select Time Zone'],
							'pluginOptions' => [
								'allowClear' => false
							],
						]);
						
						
						/*Select2::widget([
									'name' => 'Options[timezone_id]',
									'attribute' => 'Options[timezone_id]',
									'value'=>$model->timezone_id,
									'data' => $timezone_dropdown,
									'options' => ['prompt' => 'Select Timezone','class' => 'form-control'],
									'pluginOptions' => [
									  'allowClear' => true
									]
								]);*/
					?>
            </div>
          </div>
        </div> <!-- complete here content-->
        <?php if ((new User)->checkAccess(10)) { ?>
        <div class="myheader"> <a href="javascript:void(0);"  title="Subscribe to Email Alerts">Subscribe to Email Alerts</a> </div>
        <div class="content">
          <div id="form_div" class="option-form-div">
					<fieldset>
					<legend class="sr-only">Subscribe to Email Alerts</legend>
            <div class="row">
              <div class="col-md-4 col-sm-4">
                <div class="form-group custom-inline-block-width">
                  <input type="checkbox" value="1" id="Options_select_all" name="Options[select_all]" onclick="$('.alert_emails').prop('checked',this.checked); if(this.checked){ $('.alert_emails').next('label').addClass('checked');} else { $('.alert_emails').not('.force_class').next('label').removeClass('checked');}   ">
                  <label class="option_chk_cls" for="Options_select_all" title="Select All"><strong>Select All</strong></label>
                </div>
              </div>
            </div>
            <?php 
				$i=0;
				$j=0;
				$t=1;
				$limit = count($email_alert_options);
				foreach ($finalEmailAlerts as $alert_name=>$security_id){
						$force = false;
						//if(in_array($security_id,array_keys($session['myaccess']["feature_sort"])) || in_array('all',array_keys($session['myaccess']["feature_sort"]))){
						if((new User)->checkAccess($security_id)){
							if($model->$alert_name==1) {$i++;}$j++;
							if(!in_array('all',array_keys($session['myaccess']))){
								$key = array_search($security_id,$session['myaccess']['feature_sort']);                                                                                                                                 
								if(isset($session['myaccess']['security_force'][$key]) && $session['myaccess']['security_force'][$key]){
                                                                        //echo $session['myaccess']['security_force'][$key].'=>'.$key;
                                                                        //echo $security_id.'=>'.$alert_name.'=>'.$key.'<br>';
									$force = true;
								}else{
                                                                    //echo $key;
                                                                } 
							}
			  $limit--;		
			  if($t==1){ ?><div class="row"><?php } ?>
              <div class="col-md-12 col-sm-12" <?php /*if($alert_name=='is_sub_past_due') { IRT-746 style="display:none;" }*/?> >
                <div class="form-group custom-inline-block-width">
                  <input class="alert_emails <?php if($force == 1){?> force_class <?php }?>" 
                  type="checkbox" 
                  id="Options_<?=$alert_name?>" 
                  <?php if($model->$alert_name==1 || $force==1){?> checked="checked" <?php }?> 
                  name="Options[<?=$alert_name?>]" 
									aria-label="<?=$newEmailAlerts[$alert_name];?>"
                  <?php if($force){?>onclick='event.preventDefault();'<?php }?> 
                  >
                  <label class="option_chk_cls" for="Options_<?=$alert_name?>" <?php if($force){?>onclick='event.preventDefault();'<?php }?> title="<?=$newEmailAlerts[$alert_name];?>"><?=$newEmailAlerts[$alert_name];?></label>
                </div>
              </div>
            <?php if($t==3 || $limit < $t){ $t=1;?></div><?php } else { $t++; } ?>
            
            <?php 
           			}
   			} ?>
				 </fieldset>
          </div>
        </div>
		<?php } ?>
		<div class="myheader"> <a href="javascript:void(0);" title="Set Display Options">Set Display Options</a></div>
        <div class="content">
			<div class="option-form-div">
				<div class="form-group field-user-old_password">
				  <div class="row input-field">
					<div class="col-md-3">
					  <label for="Options_project_sort_display" class="form_label" title="Set Display Option">Set Display Option</label>
					</div>
					<div class="col-md-3">
					<?php 
						echo Select2::widget([
							'id'=>'Options_project_sort_display',	
							'name' => 'Options[project_sort_display]',
							'attribute' => 'Options[project_sort_display]',
							'value'=>$model->project_sort_display,
							'data' => [3=>'By Team Priority then by Project Priority then by Project Due Date in descending order',
								0=>'By Project Priority then by Project Due Date in descending order (Default)',
								1=>'By Due Date in descending order',
								2=>'By Project # in descending order'
							],
							'options' => ['prompt' => '','class' => 'form-control'],
							'pluginOptions' => [
							  'allowClear' => true
							]
						]);
					?>
					</div>
				  </div>
              </div>
              <div class="form-group field-user-old_password">
				  <div class="row input-field">
					<div class="col-md-3">
					  <label for="Options_default_landing_page" class="form_label" title="Set Default Landing Module">Set Default Landing Module</label>
					</div>
					<div class="col-md-3">
						<?php echo Select2::widget([
							'id'=>'Options_default_landing_page',	
							'name' => 'Options[default_landing_page]',
							'attribute' => 'Options[default_landing_page]',
							'value'=>$model->default_landing_page,
							'data' => $default_landing_page,
							'options' => ['prompt' => '','class' => 'form-control'],
							'pluginOptions' => [
							  'allowClear' => false
							]
						]); ?>
					</div>
				</div>
            </div>
          </div>
        </div> <!-- complete here content-->
      <!-- content End Here -->
      </div>
      </fieldset>
      <div class="button-set text-right">
        <button title="Update" class="btn btn-primary" id="submitMediaDataType" type="button">Update</button>
      </div>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>
<script>
var bool = false;
<?php if($i==$j){?> 
$('#Options_select_all').prop("checked",true);
<?php }?>
function user_passvalidation(){
	console.log('user password');
}
function Confirmvalidation(){
	console.log('confirmvalidation');
}
$(document).ready(function()  {
    $('input').customInput(true);
$(".myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();    
    $content.slideToggle(500, function () {
	//$content.is(":visible") ? $header.addClass("myheader-selected-tab") : $header.removeClass("myheader-selected-tab");
		$header.text(function () {
            //change text based on condition
            //return $content.is(":visible") ? "Collapse" : "Expand";
        });
     });
});
$('.myheader').on('click',function(){
			if($(this).hasClass('myheader-selected-tab')){
				$(this).removeClass('myheader-selected-tab');
			}else{
				$(this).addClass('myheader-selected-tab');
			}	
		});
});
<?php if($saved==1){?>
setTimeout(function(){
	// Remove URL Tag Parameter from Address Bar
	if (window.parent.location.href.match(/saved=/)){
		if (typeof (history.pushState) != "undefined") {
			var obj = { Title: document.title, Url: window.parent.location.pathname+'?r=user/options' };
	        history.pushState(obj, obj.Title, obj.Url);
	    } else {
	        window.parent.location = window.parent.location.pathname;
	    }
	}
	$('#success-alert').addClass('hide');	
},4000);
<?php }?>
/* changeflag */
$('input').bind('input', function(){
	$("#Options #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});
$('select').on("change", function(){
	$("#Options #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});
$(':checkbox').change(function(){
	$("#Options #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});
</script>
<noscript></noscript>
