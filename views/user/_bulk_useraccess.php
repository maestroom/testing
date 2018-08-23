<?php
// User Form
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);

?>
<fieldset class="two-cols-fieldset">
	<div id="user-access-tabs">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
			<li><a href="#user_access_first" title="My Cases">My Cases</a></li>
			<li><a href="#user_access_second" title="My Teams">My Teams</a></li>
		</ul>
		<?php $form = ActiveForm::begin(['id' => $model->formName().'_access', 'action'=>Url::toRoute(['user/user-access-update','id'=>$model->id]), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
		<?= IsataskFormFlag::widget(); ?>
		<input type="hidden" name="txtusers" value="<?= implode(',',$ids) ?>"/>
		<input type="hidden" name="txt_role_type" value="<?= $selected_role_data->role_type ?>"/>
                <div class="mycontainer" id="user_access_second">
                    <?=
                    $this->render('_bulk_access_team', [
                        'model' => $model,
                        'teamNames' => $teamNames,
//                        'myteams' => $myteams,
                        //'projectsecurity' => $projectsecurity
                    ])
                    ?>
                </div>
                <div class="mycontainer" id="user_access_first" style="display:none;">
                    <?=
                    $this->render('_bulk_access_client_cases', [
                        'model' => $model,
                        'clientList' => $clientList,                       
                       // 'projectsecurity' => $projectsecurity
                    ])                        
                    ?>                                 
                </div>
<?php ActiveForm::end(); ?>
	</div>
</fieldset>
<script>
$('#user-access-tabs :checkbox').change(function(){
	$('#User_access #is_change_form').val('1');
	$('#User_access #is_change_form_main').val('1');
});	

/**
 * myheader span show contant of each header
 */
$("#user-access-tabs .myheader span").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
          //  change text based on condition
          //return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });
});

/** customInput **/
$(function() {
	
    $( "#user-access-tabs" ).tabs({
        beforeActivate: function (event, ui) {},
        beforeLoad: function( event, ui ) {
          ui.jqXHR.error(function() {
            ui.panel.html(
              "Error loading current tab." );
          });
        }
    });
});

/* Ready Document */
$(document).ready(function(){
	/* MyTeam */
	var total_chk_box = $('#User_access .outer_security').size();
	var cnt = $('#User_access .outer_security:checked').size();
	$('#User_access .team_all').prop('checked',false);
	$('#User_access .team_class_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('#User_access .team_all').prop('checked',true);
		$('#User_access .team_class_all').addClass('checked');
	}
	$("#User_access #access_team_all").change(function () {
		if ($('#User_access #access_team_all').is(':checked')){	
			$('#User_access .innerchkteam_class').addClass('checked');
			$('#User_access .team_chk_class').addClass('checked');
			$("#User_access .outer_security").prop('checked',true);
	    	$("#User_access .inner_security").prop('checked',true);
		} else {
			$('#User_access .innerchkteam_class').removeClass('checked');
			$('#User_access .team_chk_class').removeClass('checked');
			$("#User_access .outer_security").prop('checked',false);
	    	$("#User_access .inner_security").prop('checked',false);
		}	
	});
	
	/* MyCase */
	var total_chk_box = $('#User_access .outer_security_case').size();
	var cnt = $('#User_access .outer_security_case:checked').size();
	$('#User_access .case_all').prop('checked',false);
	$('#User_access .case_all_class').removeClass('checked');
	if(total_chk_box == cnt){
		$('#User_access .case_all').prop('checked',true);
		$('#User_access .case_all_class').addClass('checked');
	}
	$("#User_access #access_case_all").change(function () {
		if ($('#User_access #access_case_all').is(':checked')){
			$("#User_access .outer_security_case_all").addClass('checked');
	    	$("#User_access .inner_security_case_all").addClass('checked');	
			$("#User_access .outer_security_case").prop('checked',true);
	    	$("#User_access .inner_security_case").prop('checked',true);
		}else{
			$("#User_access .outer_security_case_all").removeClass('checked');
	    	$("#User_access .inner_security_case_all").removeClass('checked');	
			$("#User_access .outer_security_case").prop('checked',false);
	    	$("#User_access .inner_security_case").prop('checked',false);
		}	
	});
});

/**
 * Check All Inner checkbox for my Teams
 * @ return checked
 */
function chkall_inner_team(loop1){
	var total_chk_box = $('#User_access .innerchk_'+loop1).size();
	var count = $('#User_access .innerchk_'+loop1+':checked').size();
	if(count >= 1){	
		$("#User_access .outerchk_"+loop1).prop('checked',true);	
		$("#User_access .outerchk_team_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$("#User_access .outerchk_"+loop1).prop('checked',false);	
		$("#User_access .outerchk_team_all_"+loop1).removeClass('checked');
	}
}

function chkall_team(loop1){
	var total_chk_box = $('#User_access .outer_security').size();
	var cnt = $('#User_access .outer_security:checked').size();
	$('#User_access .team_all').prop('checked',false); // select All checkbox unchecked 
	$('#User_access .team_class_all').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('#User_access .team_class_all').addClass('checked');
		$('#User_access .team_all').prop('checked',true);
	}
	if ($('#User_access #access_team_chk_'+loop1).is(':checked')){
		 $("#User_access .innerchk_"+loop1).prop('checked',true);
		 $("#User_access .innerchk_"+loop1).siblings().addClass('checked');
	}else{
		$("#User_access .innerchk_"+loop1).prop('checked',false);
		$("#User_access .innerchk_"+loop1).siblings().removeClass('checked');
	}
}

/**
 * Check All Inner checkbox For my Cases
 * @ return checked
 */
function chkall_inner_case(loop1){
	var total_chk_box = $('#User_access .innerchk_case_'+loop1).size();
	var count = $('#User_access .innerchk_case_'+loop1+':checked').size();
	if(count >= 1){	
		$("#User_access .outerchk_case_"+loop1).prop('checked',true);	
		$("#User_access .outerchk_case_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$("#User_access .outerchk_case_"+loop1).prop('checked',false);	
		$("#User_access .outerchk_case_all_"+loop1).removeClass('checked');
	}
}

function chkall_case(loop1){
	var total_chk_box = $('#User_access .outer_security_case').size();
	var cnt = $('#User_access .outer_security_case:checked').size();
	$('#User_access .case_all').prop('checked',false); // select All checkbox unchecked 
	$('#User_access .case_all_class').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('#User_access .case_all').prop('checked',true);
		$('#User_access .case_all_class').addClass('checked');
	}
	if ($('#User_access #access_chk_case_'+loop1).is(':checked')){
		 $("#User_access .innerchk_case_"+loop1).prop('checked',true);
		 $("#User_access .innerchk_case_class_"+loop1).addClass('checked');
	}else{
		$(".innerchk_case_"+loop1).prop('checked',false);
		$(".innerchk_case_class_"+loop1).removeClass('checked');
	}
}

/**
 * Check All Inner checkbox For User Settings
 * @ return checked
 */
function chkall_inner_setting(loop1, loop2)
{
	var total_chk_box = $('#User_access .innerchk_setting_'+loop1).size();
	var count = $('#User_access .innerchk_setting_'+loop1+':checked').size();
	$('#User_access #access_force_options_'+loop1).css('display','block');
	$("#User_access .outerchk_setting_"+loop1).prop('checked',false);
	if(total_chk_box == count){
		$("#User_access .outerchk_setting_"+loop1).prop('checked',true);
	}
	
	/** chk inner settings **/
	if(!$('#User_access #access_innerchk_setting_'+loop1+'_'+loop2).is(':checked')){
		$("#User_access .force_inner_label_"+loop2).removeClass('checked');
		$('#User_access #access_innerchk_setting_'+loop1+'_'+loop2).prop('checked',false);
	}
}

$(document).ready(function(){
	var total_chk_box = $('#User_access .outer_security_setting').size();
	var cnt = $('#User_access .outer_security_setting:checked').size();
	$('#User_access .user_security_all').prop('checked',false);
	$('#User_access .user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('#User_access .user_security_all').prop('checked',true);
		$('#User_access .user_security_select_all').addClass('checked');
	}
	$("#User_access #access_user_security_all").change(function () {
		if ($('#User_access #access_user_security_all').is(':checked')){
			$("#User_access .outer_security_setting_all").addClass('checked');
	    	$("#User_access .inner_security_setting_all").addClass('checked');
			$("#User_access .outer_security_setting").prop('checked',true);
	    	$("#User_access .inner_security_setting").prop('checked',true);
		}else{
			$("#User_access .outer_security_setting_all").removeClass('checked');
	    	$("#User_access .inner_security_setting_all").removeClass('checked');
			$("#User_access .outer_security_setting").prop('checked',false);
	    	$("#User_access .inner_security_setting").prop('checked',false);
		}	
	});
});

function chkall_setting(loop1){
	var total_chk_box = $('#User_access .outer_security_setting').size();
	var cnt = $('#User_access .outer_security_setting:checked').size();
	$('#User_access .user_security_all').prop('checked',false); // select All checkbox unchecked 
	$('#User_access .user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('#User_access .user_security_all').prop('checked',true);
		$('#User_access .user_security_select_all').addClass('checked');
	}
	if ($('#User_access #access_chk_setting_'+loop1).is(':checked')){
		 $("#User_access .innerchk_setting_"+loop1).prop('checked',true);
	}else{
		$("#User_access .innerchk_setting_"+loop1).prop('checked',false);
	}
}

function SaveUserAccess(form_id,btn){
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
				commonAjax(baseUrl +'/user/manage-user-access','admin_main_container');
			}else{
				$(btn).removeAttr("disabled");
			}
		},
		error  : function (){
			console.log('internal server error');
		}
	});
}
</script>
