<?php
// User Form
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
?>
<fieldset class="two-cols-fieldset">
	<div id="user-access-tabs">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
			<li><a href="#user_access_first" title="My Cases">My Cases</a></li>
			<li><a href="#user_access_second" title="My Teams">My Teams</a></li>
		</ul>
		<?php $form = ActiveForm::begin(['id' => $model->formName().'_access', 'action'=>Url::toRoute(['user/user-access-update','id'=>$model->id]), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
		<?= IsataskFormFlag::widget(); // change flag ?>
		<input type="hidden" name="txtusers" value="<?= implode(',',$ids) ?>"/>
		<div class="mycontainer" id="user_access_second">
			<div class="roleall_chk pull-left">
				<?php $chk_usr_inherent = ''; if($model->usr_inherent_teams==1){$chk_usr_inherent='checked';} ?>
				<div class="custom-full-width"><input type="checkbox" <?php echo $chk_usr_inherent; ?> name="usr_inherent_teams" id="usr_inherent_teams_id" value="1" /><label for="usr_inherent_teams_id" title="Automatically Inherent New Teams">Automatically Inherit New Teams</label></div> 
			</div>
			<div class="roleall_chk pull-right">
				<input type="checkbox" class="team_all" name="team_all" id="team_all" title="Select All/None" value="all" /> Select All <label for="team_all" title="Select All/None" class="team_class_all"></label>
			</div>
				<?php $i=0; foreach($myteams as $key => $val){ ?>
				   <?php foreach($val as $key1 => $val1){ $outerteamchk=''; ?>
				   <div class="myheader">
						<span><?= $key ?></span>
							<div class="pull-right"> 
							<input type="checkbox" <?= $outerteamchk ?> class="outer_security outerchk_<?php echo $i; ?>" name="my_teams[team][<?= $key1 ?>]" id="team_chk_<?php echo $i; ?>" value="<?= $key1 ?>" onClick="chkall_team('<?php echo $i; ?>');" />
							<label for="team_chk_<?php echo $i; ?>" class="team_chk_class outerchk_team_all_<?php echo $i; ?>">&nbsp;</label> 
						</div>
				   </div>
				   <div class="content">
						<ul>
							<?php $j=0; foreach($val1 as $val2=>$team_location_name){ $j++; ?>
							<li><span><?= $team_location_name ?></span>
								<div class="pull-right"> 
									<input type="checkbox" <?= $innerteamchk ?> class="inner_security innerchk_<?php echo $i; ?>" name="my_teams[team_loc][<?= $key1 ?>][]" id="innerchkteam_<?php echo $i;?>_<?php echo $j; ?>" value="<?= $val2 ?>" onClick="chkall_inner_team('<?php echo $i; ?>');"/>
									<label for="innerchkteam_<?php echo $i; ?>_<?php echo $j; ?>" class="innerchkteam_class">&nbsp;</label>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
			   <?php } $i++; 
			} ?>
		</div>
		<div class="mycontainer" id="user_access_first" style="display:none;">
			<div class="roleall_chk pull-left">
				<?php $chk_usr_cases_inherent = ''; if($model->usr_inherent_cases==1){$chk_usr_cases_inherent='checked';} ?>
				<div class="custom-full-width"><input type="checkbox" <?php echo $chk_usr_cases_inherent; ?> class="custom-full-width" name="usr_inherent_cases" id="usr_inherent_cases_id" value="1" />  <label for="usr_inherent_cases_id" title="Automatically Inherent New Cases">Automatically Inherit New Cases</label></div>
			</div>
			<div class="roleall_chk pull-right">
				<input type="checkbox" class="case_all" name="case_all" id="case_all" title="Select All/None" value="all" /> Select All <label for="case_all" class="case_all_class" title="Select All/None"></label>
			</div>
			<?php $i=1; foreach($mycases as $key => $val){ ?>
			   <?php foreach($val as $key1 => $val1){ ?>
				   <?php
						$outercasechk='';
						if(!empty($projectsecurity)){
						$is_selected_client = array_search($key1, array_map(function($element) {return $element['client_id'];}, $projectsecurity));
						if($is_selected_client !== false){
							$outercasechk = 'checked="checked"';
						}}
				   ?> 
				   <div class="myheader">
						<span title="<?= $key ?>"><?= $key ?></span>
						<div class="pull-right"> 
							<input type="checkbox" <?= $outercasechk ?> class="outer_security_case outerchk_case_<?php echo $i; ?>" name="clients[<?= $key1 ?>]" id="chk_case_<?php echo $i; ?>" value="<?= $key1 ?>" onClick="chkall_case('<?php echo $i; ?>');" />
							<label for="chk_case_<?php echo $i; ?>" class="outer_security_case_all outerchk_case_all_<?php echo $i; ?>">&nbsp;</label> 
						</div>
				   </div>
				   <div class="content">
						<ul>
							<?php $j=0; foreach($val1 as $val2=>$case_name){ $j++; ?>
							<?php
								$innercasechk='';
								if(!empty($projectsecurity)){
								$is_selected_client_case_id = array_search($val2, array_map(function($element) {return $element['client_case_id'];}, $projectsecurity));
								if($is_selected_client_case_id !== false){
									$innercasechk = 'checked="checked"';
								}}
							?>
							<li><span><?= $case_name ?></span>
								<div class="pull-right"> 
									<input type="checkbox" <?= $innercasechk ?> class="inner_security_case innerchk_case_<?php echo $i; ?>" name="my_cases[<?= $key1 ?>][]" id="innerchkcase_<?php echo $i; ?>_<?php echo $j; ?>" value="<?= $val2 ?>" onClick="chkall_inner_case('<?php echo $i; ?>');"/>
									<label for="innerchkcase_<?php echo $i; ?>_<?php echo $j; ?>" class="inner_security_case_all innerchk_case_class_<?php echo $i; ?>">&nbsp;</label>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
			   <?php $i++; }  
			} ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</fieldset>
<script>
$(':checkbox').change(function(){
	$('#User_access #is_change_form').val('1');
	$('#User_access #is_change_form_main').val('1');
});	

/**
 * myheader span show contant of each header
 */
$(".myheader span").click(function () {
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
	$('input').customInput();
	
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
	var total_chk_box = $('.outer_security').size();
	var cnt = $('.outer_security:checked').size();
	$('.team_all').prop('checked',false);
	$('.team_class_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('.team_all').prop('checked',true);
		$('.team_class_all').addClass('checked');
	}
	$("#team_all").change(function () {
		if ($('#team_all').is(':checked')){	
			$('.innerchkteam_class').addClass('checked');
			$('.team_chk_class').addClass('checked');
			$(".outer_security").prop('checked',true);
	    	$(".inner_security").prop('checked',true);
		} else {
			$('.innerchkteam_class').removeClass('checked');
			$('.team_chk_class').removeClass('checked');
			$(".outer_security").prop('checked',false);
	    	$(".inner_security").prop('checked',false);
		}	
	});
	
	/* MyCase */
	var total_chk_box = $('.outer_security_case').size();
	var cnt = $('.outer_security_case:checked').size();
	$('.case_all').prop('checked',false);
	$('.case_all_class').removeClass('checked');
	if(total_chk_box == cnt){
		$('.case_all').prop('checked',true);
		$('.case_all_class').addClass('checked');
	}
	$("#case_all").change(function () {
		if ($('#case_all').is(':checked')){
			$(".outer_security_case_all").addClass('checked');
	    	$(".inner_security_case_all").addClass('checked');	
			$(".outer_security_case").prop('checked',true);
	    	$(".inner_security_case").prop('checked',true);
		}else{
			$(".outer_security_case_all").removeClass('checked');
	    	$(".inner_security_case_all").removeClass('checked');	
			$(".outer_security_case").prop('checked',false);
	    	$(".inner_security_case").prop('checked',false);
		}	
	});
});

/**
 * Check All Inner checkbox for my Teams
 * @ return checked
 */
function chkall_inner_team(loop1){
	var total_chk_box = $('.innerchk_'+loop1).size();
	var count = $('.innerchk_'+loop1+':checked').size();
	if(count >= 1){	
		$(".outerchk_"+loop1).prop('checked',true);	
		$(".outerchk_team_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$(".outerchk_"+loop1).prop('checked',false);	
		$(".outerchk_team_all_"+loop1).removeClass('checked');
	}
}

function chkall_team(loop1){
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
}

/**
 * Check All Inner checkbox For my Cases
 * @ return checked
 */
function chkall_inner_case(loop1){
	var total_chk_box = $('.innerchk_case_'+loop1).size();
	var count = $('.innerchk_case_'+loop1+':checked').size();
	if(count >= 1){	
		$(".outerchk_case_"+loop1).prop('checked',true);	
		$(".outerchk_case_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$(".outerchk_case_"+loop1).prop('checked',false);	
		$(".outerchk_case_all_"+loop1).removeClass('checked');
	}
}

function chkall_case(loop1){
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
}

/**
 * Check All Inner checkbox For User Settings
 * @ return checked
 */
function chkall_inner_setting(loop1, loop2)
{
	var total_chk_box = $('.innerchk_setting_'+loop1).size();
	var count = $('.innerchk_setting_'+loop1+':checked').size();
	$('#force_options_'+loop1).css('display','block');
	$(".outerchk_setting_"+loop1).prop('checked',false);
	if(total_chk_box == count){
		$(".outerchk_setting_"+loop1).prop('checked',true);
	}
	
	/** chk inner settings **/
	if(!$('#innerchk_setting_'+loop1+'_'+loop2).is(':checked')){
		$(".force_inner_label_"+loop2).removeClass('checked');
		$('#innerchk_setting_'+loop1+'_'+loop2).prop('checked',false);
	}
}

$(document).ready(function(){
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
});

function chkall_setting(loop1){
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
