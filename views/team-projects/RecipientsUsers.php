<?php 
//echo "<pre>",print_r($case_users),"</pre>"; die();
?>
<div class="col-sm-12 custom-full-width">
	<div class="col-sm-6"><input type="radio" id="all_users" class="users" name="users" value="1" /><label for="all_users">All Users</label></div>
	<div class="col-sm-6"><input type="radio" checked id="select_users" class="users" name="users" value="0" /><label for="select_users">Selected Users</label></div>
</div>

<div class="mycontainer">
	<!-- Case Manager Roles -->
	<div style="float:right;margin:4px 12px;"><span>Select All</span><div class="pull-right">
            <input type="checkbox" id="select_user_all" aria-label="Select All" name="select_user_all" value="select_user_all" />
            <label for="select_user_all">&nbsp; </label>
	</div></div>
        <?php if(!empty($case_users)){ ?>
            <div class="myheader" id="myheader-users">
	    	<a href="javascript:void(0);">Case Manager Role Users</a>
	    	<div class="pull-right header-checkbox">
                    <input type="checkbox" aria-label="Select All Case Manager Role Users" id="case-manage-role-user" name="casemanager" class="case-manage-role-user" value="case_manager_role" /> 
                    <label for="case-manage-role-user" class="case-manage-role-user-lbl">&nbsp;</label> 
	    	</div>
	    </div>
	    <div class="content" id="content-users">
                <fieldset>
                    <legend class="sr-only">Case Manager Role Users</legend>
                    <ul>
                        <li class="search-caserole-box">
                            <label for="search-case-role" style="display:none;">&nbsp;</label>
                            <input type="text" name="search_case_role" id="search-case-role-user" class="form-control" placeholder="Search Case Manager Role" />
                        </li>
                        <ul class="search-caserole-result-recipient">
                            <?php foreach ($case_users as $role) { ?>
                                <?php
                                    $checked="";
                                    $current_idss = $role['role_id'].' '.$role['id'];
                                    if(in_array($current_idss, $case_ids)) {
                                        $checked = "checked='checked'";
                                    }
                                ?>
                                <li>
                                    <!--<span><?php //echo $role['role']['role_name'].' - '.$role['fullname'] ?></span>-->
                                    <label for="case_role_user_<?php echo $role['id'] ?>" class="chkbox-global-design case_roles_user_lbl"><?php echo $role['role']['role_name'].' - '.$role['fullname'] ?></label>
                                    <div class="pull-right "> 
                                        <input type="checkbox" <?= $checked ?> class="case_roles_user" value="<?php echo $role['role_id'].' '.$role['id']; ?>" name="case_role[<?php echo $role['id'] ?>]" value="<?php echo $case_users['user_id']?>" id="case_role_user_<?php echo $role['id'] ?>" title="<?php echo $role['role']['role_name'].' - '.$role['fullname'] ?>">
                                        <!--<label for="case_role_user_<?php echo $role['id'] ?>" class="case_roles_user_lbl">&nbsp; </label>-->
                                    </div>
                                </li>
                            <?php } ?> 	
                        </ul>
                    </ul>
                </fieldset>
	    </div> 
	<?php } ?>
		
    <?php if(!empty($team_users)){ ?>
	    <div class="myheader" id="myheader-users">
	    	<a href="javascript:void(0);">Team Users</a>
	    	<div class="pull-right header-checkbox">
                    <input type="checkbox" aria-label="Select All Team Users" id="teams-role-user" name="teamservice" class="teams-role-user" value="teams_role" /> 
                    <label for="teams-role-user" class="teams-role-user-lbl">&nbsp;</label> 
	    	</div>
	    </div>
	    
	    <div class="content" id="content-users">
                <fieldset>
            <legend class="sr-only">Team Users</legend>
	        <ul>
                        <li class="search-teams-box">
                            <label for="search-teams" style="display:none">&nbsp;</label>
                            <input type="text" name="search_teams_user" id="search-teams-user" class="form-control" placeholder="Search Team User" />
                        </li>
                        <ul class="search-teams-result-user-recipient">
		            <?php foreach ($team_users as $team) { ?>
                                <?php
                                    $checked="";
                                    $current_ids = $team['team_id'].' '.$team['user_id'];
                                    if(in_array($current_ids, $team_ids)) {
                                        $checked = "checked = 'checked'";
                                    }
                                ?>
                                <li>
                                    <!--<span><?= $team['team']['team_name'].' - '.$team['user']['fullname']; ?></span>-->
                                    <label for="team_user_<?= $team['team_id'] ?>_<?= $team['user_id'] ?>" class="chkbox-global-design teams-role-user-chk-lbl"><?= $team['team']['team_name'].' - '.$team['user']['fullname']; ?></label>
                                    <div class="pull-right"> 
                                        <input type="checkbox" class="teams-role-user-chk" <?= $checked  ?>  name="team[<?php echo $team_id?>]" id="team_user_<?= $team['team_id'] ?>_<?= $team['user_id'] ?>" value="<?= $team['team_id'].' '.$team['user_id'] ?>" title="<?= $team['team']['team_name'].' - '.$team['user']['fullname']; ?>">
                                        <!--<label for="team_user_<?= $team['team_id'] ?>_<?= $team['user_id'] ?>" class="teams-role-user-chk-lbl">&nbsp; </label>-->
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
		    </ul>
                </fieldset>
		</div>  
    <?php } ?>
</div>

<script>
$('input').customInput();
$(function() {
	$('input').customInput();
});


$("document").ready(function(){
	/* case role */
	if($(".case_roles_user").is(':checked')) {
		$(".search-case-role-user").prop("checked",true);
		$(".case-manage-role-user-lbl").addClass("checked");
	} else {
		$(".case-manage-role-user").prop("checked",false);
		$(".case-manage-role-user-lbl").removeClass("checked");
	}

	/* team role */
	if($(".teams-role-user-chk").is(':checked')) {
		$(".teams-role-user").prop('checked',true);
		$(".teams-role-user-lbl").addClass('checked');
	} else {
		$(".teams-role-user").prop('checked',false);
		$(".teams-role-user-lbl").removeClass('checked');
	}
});

$('.users').change(function(){
	if($('#select_users').is(':checked'))
		$('.mycontainer').css('display','block');
	if($('#all_users').is(':checked'))
		$('.mycontainer').css('display','none');
});

/* select all */
$('#select_user_all').change(function(event) {
	if($('#select_user_all').is(':checked')) {
		$('.case_roles_user').prop('checked',true);
		$('.teams-role-user-chk').prop('checked', true);
		$('.teams-role-user').prop('checked', true); 
		$('.teams-role-user-chk-lbl').addClass('checked');
		$('.case_roles_user_lbl').addClass('checked'); 
		$('.teams-role-user-lbl').addClass('checked');
		$('.case-manage-role-user').prop('checked',true);
		$('.case-manage-role-user-lbl').addClass('checked');
	} else {
		$('.case_roles_user').prop('checked',false);
		$('.teams-role-user-chk').prop('checked', false);
		$('.teams-role-user').prop('checked', false); 
		$('.case_roles_user_lbl').removeClass('checked');
		$('.teams-role-user-chk-lbl').removeClass('checked');
		$('.teams-role-user-lbl').removeClass('checked');
		$('.case-manage-role-user').prop('checked',false);
		$('.case-manage-role-user-lbl').removeClass('checked');
	}
});

$('#all_users').change(function(){
	if($('#all_users').is(':checked')) {
		$('.case_roles_user').prop('checked',true);
		$('.teams-role-user-chk').prop('checked', true);
		$('.teams-role-user').prop('checked', true); 
		$('.teams-role-user-chk-lbl').addClass('checked');
		$('.case_roles_user_lbl').addClass('checked'); 
		$('.teams-role-user-lbl').addClass('checked');
		$('.case-manage-role-user').prop('checked',true);
		$('.case-manage-role-user-lbl').addClass('checked');
	} else {
		$('.case_roles_user').prop('checked',false);
		$('.teams-role-user-chk').prop('checked', false);
		$('.teams-role-user').prop('checked', false); 
		$('.case_roles_user_lbl').removeClass('checked');
		$('.teams-role-user-chk-lbl').removeClass('checked');
		$('.teams-role-user-lbl').removeClass('checked');
		$('.case-manage-role-user').prop('checked',false);
		$('.case-manage-role-user-lbl').removeClass('checked');
	}
});

/* case role */
$(".case_roles").change(function(){
	if($(".case_roles").is(':checked')) {
		$(".case-manage-role").prop("checked",true);
		$(".case-manage-role-lbl").addClass("checked");
	} else {
		$(".case-manage-role").prop("checked",false);
		$(".case-manage-role-lbl").removeClass("checked");
	}
});

/* case role chk */
$(".case_roles_user").change(function(){
	if($(".case_roles_user").is(':checked')) {
		$(".case-manage-role-user").prop("checked",true);
		$(".case-manage-role-user-lbl").addClass("checked");
	} else {
		$(".case-manage-role-user").prop("checked",false);
		$(".case-manage-role-user-lbl").removeClass("checked");
	}
});

/* team role chk */
$(".case-manage-role-user").change(function(){
	if($(".case-manage-role-user").is(':checked')) {
		$(".case_roles_user").prop("checked",true);
		$(".case_roles_user_lbl").addClass("checked");
	} else {
		$(".case_roles_user").prop("checked",false);
		$(".case_roles_user_lbl").removeClass("checked");
	}
});

/* case manage role Users */
 $('.teams-role-user-chk').change(function(){
	if($(".teams-role-user-chk").is(':checked')) {
		$(".teams-role-user").prop('checked',true);
		$(".teams-role-user-lbl").addClass('checked');
	} else {
		$(".teams-role-user").prop('checked',false);
		$(".teams-role-user-lbl").removeClass('checked');
	}
}); 

/* Team Users */
$('#teams-role-user').change(function(){
	if($("#teams-role-user").is(':checked')) {
		$(".teams-role-user-chk").prop('checked',true);
		$(".teams-role-user-chk-lbl").addClass('checked');
	} else {
		$(".teams-role-user-chk").prop('checked',false);
		$(".teams-role-user-chk-lbl").removeClass('checked');
	}
}); 

/* Select All */
$('#myheader-users').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$("#myheader-users a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
            // change text based on condition
            // return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });
});

$(document).ready(function($){
	jQuery("#search-case-role-user").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery('.search-caserole-result-recipient li').each(function() {
	    	if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		      	jQuery(this).hide();
	        } else {
	            jQuery(this).show()
	        }
	    });
	});
	jQuery("#search-teams-user").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery('.search-teams-result-user-recipient li').each(function() {
	    	if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		      	jQuery(this).hide();
	        } else {
	            jQuery(this).show()
	        }
	    });
	});
});
</script>
<noscript></noscript>
