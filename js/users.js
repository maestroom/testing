$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});
/**
 * User Management Module
 */
jQuery(document).ready(function(){
	/* User Module Js Code Start */
	jQuery(".userModules").click(function(event){
		var chk_status = checkformstatus(event); // check form edit status 
		if(chk_status==true) {
			var module=jQuery(this).data('module');
			jQuery('.userModules').removeClass('active');
			if(module == 'ManageRoles'){
				commonAjax(baseUrl +'/user/manage-role','admin_main_container');	
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="User Management - Manage Roles" class="tag-header-red">User Management - Manage Roles</a>');
			}else if(module == 'ManageUsers'){
				commonAjax(baseUrl +'/user/manage-user','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="User Management - Manage Users" class="tag-header-red">User Management - Manage Users</a>');
			}else if(module == 'ManageUserAccess'){
				commonAjax(baseUrl +'/user/manage-user-access','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="User Management - Manage User Access" class="tag-header-red">User Management - Manage User Access</a>');	
			}
		}
	})
});

/** 
 * Update User Details
 */
function UserDetails(user_id){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) {
		$('.userlist').removeClass('active');
		jQuery.ajax({
		   url: baseUrl +'/user/user-update&id='+user_id,
		   type: 'get',
		   beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   $('#userlist_'+user_id).addClass('active');
			   jQuery('#admin_right').html(data);
		   }
		});
	}	 
}

var currentUserRequest = null;    

function UserDetailsAccess(user_id,role_type)
{	
	
	$('.userlist').removeClass('active');

	currentUserRequest = $.ajax({
		url:baseUrl+'user/next-user-add-access',
		type:'post',
		data:{'User[id]':user_id},
		beforeSend:function(){
			//console.log(currentUserRequest);
			if(currentUserRequest != null) {
				currentUserRequest.abort();
			}
			showLoader();
		},
		success: function (response){			
			hideLoader();
			$('#userlist_'+user_id).addClass('active');
			jQuery('#admin_right').html(response);
			/*$("#user-tabs").tabs("enable");
			$('#second').show(); $('#first').hide();
			$('#second').html(response); // get the response
			jQuery('li[aria-controls="third"]').show();
			jQuery('li[aria-controls="first"]').removeClass('ui-tabs-active ui-state-active');
			jQuery('li[aria-controls="second"]').addClass('ui-tabs-active ui-state-active');
			$("#user-tabs").tabs("option","disabled", [0]);
			*/
			//$("#user-role_type").val($("#user-role_id").val());
			//var role_type = $( "#user-role_type option:selected" ).text();
			if(role_type == '1,2') {
				$('#user-main-access-tabs #user_access_first').data('loaded','yes');
				initClientCasePermission(user_id,'#user-main-access-tabs #user_access_first','user/get-only-client-cases-permissions');
				$( "#user-main-access-tabs" ).tabs('enable');
			}
			else if(role_type == '2,1'){
				$('#user-main-access-tabs #user_access_first').data('loaded','yes');
				initClientCasePermission(user_id,'#user-main-access-tabs #user_access_first','user/get-only-client-cases-permissions');
				$( "#user-main-access-tabs" ).tabs('enable');
			}
			else if(role_type == '1'){
				$('#user-main-access-tabs #user_access_first').data('loaded','yes');
                                initClientCasePermission(user_id,'#user-main-access-tabs #user_access_first','user/get-only-client-cases-permissions');
				$( "#user-main-access-tabs" ).tabs("option","disabled", [1]);
				$( "#user-main-access-tabs" ).tabs("option","active",[0]);
			}
			else if(role_type == '2'){               
				$('#user-main-access-tabs #user_access_second').data('loaded','yes');
                                $( "#user-main-access-tabs" ).tabs("option","active",[1]);
                                initClientCasePermission(user_id,'#user-main-access-tabs #user_access_second','user/get-only-teams-permissions'); 
				$( "#user-main-access-tabs" ).tabs("option","disabled", [0]);				
			}			
		}
	});
}

function initClientCasePermission(userID,responseDiv,funParams){
    showLoader();
    $.ajax({
        type: 'POST',
        url: baseUrl + funParams,
        data: {user_id:userID},
        success: function(data){
            $(responseDiv).html(data);                      
            hideLoader();
        } 
    });            
}

function UserAddNew(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true)
		UserAdd();
}

/**
 * Add User Details
 */
function UserAdd(){
	$('.userlist').removeClass('active');
	jQuery.ajax({
	       url: baseUrl +'/user/user-add',
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}

/**
 * Update Role Details
 */
function RoleDetails(role_id){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) {
		if(role_id == ""){
			commonAjax(baseUrl +'/user/manage-role','admin_main_container');
		}else{
			jQuery.ajax({
			   url: baseUrl +'/user/role-update&role_id='+role_id,
			   type: 'get',
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   hideLoader();
				   jQuery('#admin_right').show();	
				   jQuery('#admin_right').html(data);
			   }
			});
		}
	}
}


/**
 * Add User From Manage Users
 */
function ManageRoleSubmitAjaxForm(){
	jQuery.ajax({
       url: baseUrl +'/user/add-user',
       type: 'get',
       beforeSend:function (data) {showLoader();},
       success: function (data) {
    	   hideLoader();
    	   jQuery('#admin_right').html(data);
       }
	});
}


