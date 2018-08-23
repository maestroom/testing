<div class="mycontainer">
	<div style="float:right; margin: 4px 12px;"><span>Select All</span>
		<div class="pull-right"><input type="checkbox" id="select_all" name="select_all" value="1" aria-label="Select All Case Manager Roles and Teams" />
                    <label for="select_all"><span class="sr-only">Select All Case Manager Roles and Teams</span></label></div>
	</div>
	
	<!-- Case Manager Roles -->
	<div class="myheader">
    	<a href="javascript:void(0);">Case Manager Roles</a>
    	<div class="pull-right header-checkbox">
    		<input type="checkbox" id="case-manage-role" name="casemanager" class="case-manage-role" value="case_manager_role" aria-label="Select All Case Manager Roles"/> 
                <label for="case-manage-role" class="case-manage-role-lbl"><span class="sr-only">Select All Case Manager Roles</span></label> 
    	</div>
    </div>
    
    <div class="content">
        <fieldset>
            <legend class="sr-only">Case Manager Roles</legend>
            <ul>
                <li class="search-caserole-box">
                    <label for="search-case-role" style="display:none"><span class="sr-only">Search Case Role</span></label>
                    <input type="text" name="search_case_role" id="search-case-role" class="form-control" placeholder="Search Case Manager Role" aria-label="Search Case Manager Role" />
                </li>
                <ul class="search-caserole-result">
                        <?php foreach ($role_data as $id=>$role){ ?>
                                <li>
                                    <!--<span id="label_case_role_<?php echo $id ?>"><?php echo $role; ?></span>-->
                                    <label for="case_role_<?php echo $id ?>" class="chkbox-global-design case_role_result"><?php echo $role; ?></label>
                                    <div class="pull-right"> 
                                        <input type="checkbox" aria-labelledby="label_case_role_<?php echo $id ?>" <?php if(in_array($id,$cases_ids)) { ?> checked="checked" <?php } ?> <?php if($id==Yii::$app->user->identity->role_id) {?>checked="checked"<?php } ?> class="case_roles" name="case_role[<?php echo $id?>]" value="<?php echo $id?>" id="case_role_<?php echo $id?>" data-id="<?php echo $id; ?>" >
                                        <!--<label for="case_role_<?php echo $id ?>" class="case_role_result"><span class="sr-only"><?php echo $role; ?></span></label>-->
                                    </div>
                                </li>
                        <?php } ?>
                </ul>
            </ul>
        </fieldset>    
    </div> 
    
    <!-- Teams --> 
    <div class="myheader">
    	<a href="javascript:void(0);">Teams</a>
    	<div class="pull-right header-checkbox">
            <input type="checkbox" id="teams-role" name="teamservice" class="teams-role" value="teams_role" aria-label="Select All Teams"/> 
            <label for="teams-role" class="teams-role-lbl"><span class="sr-only">Teams</span></label> 
    	</div>
    </div>
    
    <div class="content">
        <fieldset>
            <legend class="sr-only">Team</legend>
                <ul>
                    <li class="search-teams-box">
                        <label for="search-teams" style="display:none"><span class="sr-only">Search Teams</span></label>
                                <input type="text" name="search_teams" id="search-teams" class="form-control" placeholder="Search Teams" aria-label="Search Teams" />
                            </li>
                            <ul class="search-teams-result">
                                <?php foreach ($team_data as $team_id=>$team){ ?>
                                <?php $checked = ''; if(in_array($team_id,$teams_ids)) { $checked='checked="checked"';	} ?>
                                <li><!--<span id="lbl_team_<?= $team_id?>"><?= $team; ?></span>-->
                                    <label for="team_<?= $team_id?>" class="chkbox-global-design teams-role-chk-lbl <?= $checked ?>"><?= $team; ?></label>
                                    <div class="pull-right"> 
                                        <input type="checkbox" aria-labelledby="lbl_team_<?= $team_id?>" <?= $checked ?> class="teams-role-chk" name="team[<?php echo $team_id ?>]" id="team_<?= $team_id?>" value="<?= $team_id?>" data-id="<?= $team_id; ?>"  />
                                        <!--<label for="team_<?= $team_id?>" class="teams-role-chk-lbl <?= $checked ?>"><span class="sr-only"><?= $team; ?></span></label>-->
                                    </div>
                                </li>
                            <?php } ?>
                    </ul>
	    </ul>
        </fieldset>
    </div>  
</div>

<script>
$('input').customInput();
$(function() {
	$('input').customInput();
});


$("document").ready(function(){
	/* case role */
	if($(".case_roles").is(':checked')) {
		$(".case-manage-role").prop("checked",true);
		$(".case-manage-role-lbl").addClass("checked");
	} else {
		$(".case-manage-role").prop("checked",false);
		$(".case-manage-role-lbl").removeClass("checked");
	}

	/* team role */
	if($(".teams-role-chk").is(':checked')) {
		$(".teams-role").prop("checked",true);
		$(".teams-role-lbl").addClass("checked");
	} else {
		$(".teams-role").prop("checked",false);
		$(".teams-role-lbl").removeClass("checked");
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

/* team role chk */
$(".teams-role-chk").change(function(){
	if($(".teams-role-chk").is(':checked')) {
		$(".teams-role").prop("checked",true);
		$(".teams-role-lbl").addClass("checked");
	} else {
		$(".teams-role").prop("checked",false);
		$(".teams-role-lbl").removeClass("checked");
	}
});

/* case manage role */
$('#case-manage-role').change(function(){
	if($("#case-manage-role").is(':checked')) {
		$(".case_roles").prop('checked',true);
		$(".case_role_result").addClass('checked');
	} else {
		$(".case_roles").prop('checked',false);
		$(".case_role_result").removeClass('checked');
	}
});

/* Team */
$('#teams-role').change(function(){
	if($("#teams-role").is(':checked')) {
		$(".teams-role-chk").prop('checked',true);
		$(".teams-role-chk-lbl").addClass('checked');
	} else {
		$(".teams-role-chk").prop('checked',false);
		$(".teams-role-chk-lbl").removeClass('checked');
	}
});

/* Select All */
$('#select_all').change(function(event) {
	if($('#select_all').is(':checked')){
		$('.teams-role').prop('checked',true); 
		$('.teams-role-lbl').addClass('checked');
		$('.case-manage-role').prop('checked',true); 
		$('.case-manage-role-lbl').addClass('checked');
		$(".teams-role-chk").prop('checked',true);
		$(".teams-role-chk-lbl").addClass('checked');
		$(".case_roles").prop('checked',true);
		$(".case_role_result").addClass('checked');
	} else {
		$('.teams-role').prop('checked',false); 
		$('.teams-role-lbl').removeClass('checked');
		$('.case-manage-role').prop('checked',false); 
		$('.case-manage-role-lbl').removeClass('checked');
		$(".teams-role-chk").prop('checked',false);
		$(".teams-role-chk-lbl").removeClass('checked');
		$(".case_roles").prop('checked',false);
		$(".case_role_result").removeClass('checked');
	}
});

$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$(".myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
            //change text based on condition
            //return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });
});

$(document).ready(function($){
	/* Search Case Role */
	jQuery("#search-case-role").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery('.search-caserole-result li').each(function() {
		 	if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		      	jQuery(this).hide();
	        } else {
	            jQuery(this).show();
	        }
	    });
	});

	/* Search Team */
	jQuery("#search-teams").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery('.search-teams-result li').each(function() {
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
