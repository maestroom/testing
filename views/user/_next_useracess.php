<?php
// User Form
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Options;
use app\models\User;
use app\components\IsataskFormFlag;
use kartik\widgets\Select2; 
use yii\web\JsExpression;
	
//	echo "<pre>",print_r($mycases); die;
?>
<fieldset class="two-cols-fieldset">
	<div id="user-tabs">           
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
			<li><a href="#first" title="Edit Access" data-loaded="no">Edit Access</a></li>
			<?php if((new User)->checkAccess(8.0621)){ ?><li><a href="#second" title="User Settings">User Settings</a></li><?php } ?>
		</ul>
		<div id="first">
			<div id="user-main-access-tabs">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
                                    <li id="first-client-view"><a href="#user_access_first" title="My Cases">My Cases</a></li>
                                    <li id="second-team-view"><a href="#user_access_second" title="My Teams">My Teams</a></li>
				</ul>
				<?php $form = ActiveForm::begin(['id' => $model->formName(), 'action'=>Url::toRoute(['user/user-access-update','id'=>$model->id]), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
				<?= IsataskFormFlag::widget(); // change flag ?>
				<div class="mycontainer" id="user_access_second" data-loaded="no">
                                     <?php
//                                     $this->render('_user_access_team', [
//                                            'model' => $model,
//                                            'teamNames' =>$teamNames,
//                                            'myteams' =>$myteams,
//                                            'projectsecurity' => $projectsecurity
//                                        ]);
                                     ?>
				</div>
				<div class="mycontainer" id="user_access_first" style="display:none;" data-loaded="no">
                                    <?php
//                                    $this->render('_user_access_client_cases', [
//                                            'model' => $model,
//                                            'clientList' =>$clientList,
//                                            'myteams' =>$myteams,
//                                            'projectsecurity' => $projectsecurity
//                                        ]) ?>                                 
				</div>
			</div>
			<div class="button-set text-right"> 
				<?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary','id' => 'previous_user_settings_last','onclick'=>'getRoleUser();']) ?>
				<?= Html::button('Update', ['title' => 'Update','class' => 'btn btn-primary','onclick'=>'SaveUserAccess("User",this);']) ?>
			</div>
			<?php ActiveForm::end(); ?>
		</div>
		<div id="second" data-loaded="no">			
                    
            	</div>
        </div>
        <input type="hidden" value="<?=$model->id?>" id="cur-user-id">
</fieldset>
<script>
$('form#User :checkbox').change(function(){
	$('form#User #is_change_form').val('1');
	$('form#User #is_change_form_main').val('1');
	$('form#User #update-user-right #is_change_form').val('1');
	$('form#User #update-user-right #is_change_form_main').val('1');
});	
/** customInput **/
$(function() {
//	$("#active_form_name").val('User');
	$('#user-tabs input').customInput();
    // Below id the main tabs
    $( "#user-tabs" ).tabs({
      activate: function( event, ui ) { 
          var user_id = $('#cur-user-id').val();
          if(ui.newPanel.attr('id') == 'second'){
              if($('#user-tabs #second').data('loaded') == 'no'){
                  $('#user-tabs #second').data('loaded','yes');
                    showLoader();
                    $.ajax({
                        type: 'POST',
                        url: baseUrl + 'user/get-single-user-settings',
                        data: {user_id:user_id},
                        success: function(data){
                            $('#user-tabs #second').html(data);                      
                            hideLoader();
                        }
                    });              
                }
          }
      },
      beforeActivate: function (event, ui) {
      	//if(ui.newPanel.attr('id') == 'first'){
		//	e.preventDefault();	  
    	//}
      	//if(ui.newPanel.attr('id') == 'second'){
		//	e.preventDefault();	  
     	//}
     	var chk_status = checkformstatus(event,"User");
     	
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });
    // Below is the second panel in first tab
    $( "#user-main-access-tabs" ).tabs({
        activate: function(event, ui){
            var user_id = $('#cur-user-id').val();
            if(ui.newPanel.attr('id') == 'user_access_first'){
                if($('#user-main-access-tabs #user_access_first').data('loaded') == 'no'){
                    $('#user-main-access-tabs #user_access_first').data('loaded','yes');
                    initClientCasePermission(user_id,'#user-main-access-tabs #user_access_first','user/get-only-client-cases-permissions');
                }
            }else if(ui.newPanel.attr('id') == 'user_access_second'){
                if($('#user-main-access-tabs #user_access_second').data('loaded') == 'no'){
                    $('#user-main-access-tabs #user_access_second').data('loaded','yes');
                    initClientCasePermission(user_id,'#user-main-access-tabs #user_access_second','user/get-only-teams-permissions');   
                }
                
            }            
        },
//        activate: function( event, ui ) { 
//          var user_id = $('#cur-user-id').val();
//          if(ui.newPanel.attr('id') == 'second'){
//              $.ajax({
//                  type: 'POST',
//                  url: baseUrl + 'user/get-single-user-settings',
//                  data: {user_id:user_id},
//                  success: function(data){
//                      $('#user-tabs #second').html(data);                      
//                  }
//              });              
//          }
//        },
        beforeActivate: function (event, ui) {},        
        beforeLoad: function( event, ui ) {
          ui.jqXHR.error(function() {
            ui.panel.html(
              "Error loading current tab." );
          });
        }
    });
    //$("#user-tabs").tabs("option","disabled", [1]);
   

});

function chkall_inner_setting(loop1){
	var total_chk_box = $('.innerchk_setting_'+loop1).size();
	var count = $('.innerchk_setting_'+loop1+':checked').size();
	if(count == 0){
		$(".outerchk_setting_"+loop1).prop('checked',false);
	}else{
		$(".outerchk_setting_"+loop1).prop('checked',true);	
	}
	//if(total_chk_box == count){
		
	//}
}

/* Ready Document */
$(document).ready(function(){   
	/* MyTeam starts */
	var total_chk_box = $('form#User .outer_security').size();
	var cnt = $('form#User .outer_security:checked').size();
	$('form#User .team_all').prop('checked',false);
	$('form#User .team_class_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('form#User .team_all').prop('checked',true);
		$('form#User .team_class_all').addClass('checked');
	}
//	$("form#User #team_all").change(function () {
//		if ($('form#User #team_all').is(':checked')){	
//			$('form#User .innerchkteam_class').addClass('checked');
//			$('form#User .team_chk_class').addClass('checked');
//			$("form#User .outer_security").prop('checked',true);
//	    	$("form#User .inner_security").prop('checked',true);
//		} else {
//			$('form#User .innerchkteam_class').removeClass('checked');
//			$('form#User .team_chk_class').removeClass('checked');
//			$("form#User .outer_security").prop('checked',false);
//	    	$("form#User .inner_security").prop('checked',false);
//		}	
//	});
	/* MyTeam ends */
	/* MyCase starts */
	var total_chk_box = $('form#User .outer_security_case').size();
	var cnt = $('form#User .outer_security_case:checked').size();
	$('form#User .case_all').prop('checked',false);
	$('form#User .case_all_class').removeClass('checked');
	if(total_chk_box == cnt){
		$('form#User .case_all').prop('checked',true);
		$('form#User .case_all_class').addClass('checked');
	}
	$("form#User #case_all").change(function () {
		if ($('form#User #case_all').is(':checked')){
			$("form#User .outer_security_case_all").addClass('checked');
	    	$("form#User .inner_security_case_all").addClass('checked');	
			$("form#User .outer_security_case").prop('checked',true);
	    	$("form#User .inner_security_case").prop('checked',true);
		}else{
			$("form#User .outer_security_case_all").removeClass('checked');
	    	$("form#User .inner_security_case_all").removeClass('checked');	
			$("form#User .outer_security_case").prop('checked',false);
	    	$("form#User .inner_security_case").prop('checked',false);
		}	
	});       
	var total_chk_box = $('form#update-user-right .outer_security_setting').size();
	var cnt = $('form#update-user-right .outer_security_setting:checked').size();
	$('form#update-user-right .user_security_all').prop('checked',false);
	$('form#update-user-right .user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('form#update-user-right .user_security_all').prop('checked',true);
		$('form#update-user-right .user_security_select_all').addClass('checked');
	}
	$("form#update-user-right #user_security_all").change(function () {
		if ($('form#update-user-right #user_security_all').is(':checked')){
			$("form#update-user-right .outer_security_setting_all").addClass('checked');
	    	$("form#update-user-right .inner_security_setting_all").addClass('checked');
			$("form#update-user-right .outer_security_setting").prop('checked',true);
	    	$("form#update-user-right .inner_security_setting").prop('checked',true);
		}else{
			$("form#update-user-right .outer_security_setting_all").removeClass('checked');
	    	$("form#update-user-right .inner_security_setting_all").removeClass('checked');
			$("form#update-user-right .outer_security_setting").prop('checked',false);
	    	$("form#update-user-right .inner_security_setting").prop('checked',false);
		}	
	});
         $(document).on('keyup','#filterFromTeamLocs',function () {
          	var filter = $(this).val();
				$("ul.fromTeams li").each(function () {
                if ($(this).attr('id') != 'header') {
                    if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
                    	$(this).hide();
                    } else {
                    	$(this).show();
                	}
            	}
        	});
        });
});

</script>