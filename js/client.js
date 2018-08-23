$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});
jQuery(document).ready(function(){
	/* Start : Client Management */
	jQuery(".clientModules").click(function(){
		var chk_status = checkformstatus("event"); // check form status
		if(chk_status == true){
			setTitle('fa-wrench','<a href="javascript:void(0);" title="Client Management" class="tag-header-red">Client Management</a>');
			var module=jQuery(this).data('module');
			jQuery('.clientModules').removeClass('active');
			if(module == 'ClientManagement'){
				commonAjax(baseUrl +'/client/index','admin_main_container');
				jQuery(this).addClass('active');
			}
		}
	});
	/* End : Client Management */
});

/* Start : Client Management */
/* Start : Used to load clients list */
function loadClient(){
	commonAjax(baseUrl +'/client/index','admin_main_container');
}
/* End : Used to load clients list */

/** Start : Add New Client **/
function addNewClient(){
	var chk_status = checkformstatus("event"); // check form status
	if(chk_status == true) addClient();
}

/* Start : Used to load Add new client Form */
function addClient(){
	jQuery.ajax({
	   url: baseUrl +'/client/create',
	   type: 'get',
	   beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   jQuery('#admin_right').html(data);
	   } 
	});
}
/* End : Used to load Add new client Form */

/* Start : Used to load Edit client Form */
function updateClient(client_id){
	var chk_status = checkformstatus("event"); // check form status
	if(chk_status == true){
		updateClientExclusiveService(client_id);
		/*if(client_id == 'Select Client' || client_id == ''){
			addClient();
			return false;
		} else {
			jQuery.ajax({
				url: baseUrl +'/client/update&id='+client_id,
				type: 'get',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					jQuery('#admin_right').html(data);
					jQuery('#client_id').val(client_id);
					jQuery('.admin-left-module-list ul li').removeClass('active');
					jQuery('#client_'+client_id).addClass('active');
					jQuery('#client_list_dropdown').val(client_id);
				} 
			});
		}*/
	}
}
/* End : Used to load Edit client Form */

/* Start : Used to load Edit client Form */
function updateClientExclusiveService(client_id)
{
	if(client_id == 'Select Client' || client_id == ''){
		addClient();
		return false;
	} else {
		jQuery.ajax({
			url: baseUrl +'/client/update&id='+client_id,
			type: 'get',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				jQuery('#admin_right').html(data);
				jQuery('#client_id').val(client_id);
				jQuery('.admin-left-module-list ul li').removeClass('active');
				jQuery('#client_'+client_id).addClass('active');
				jQuery('#client_list_dropdown').val(client_id);
			} 
		});
	}
}
/* End : Used to load Edit client Form */

/* Start : Used to delete client */
function removeClient(client_id){
	var client_name = $('#client-client_name').val();
	jQuery.ajax({
		url: baseUrl +'/client/client-has-case&id='+client_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			if($.trim(data) == 'OK' && confirm('Are you sure you want to Delete '+client_name+'?')){
				jQuery.ajax({
					url: baseUrl +'/client/delete&id='+client_id,
					type: 'get',
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						hideLoader();
						loadClient();
					}
				});
			} else {
				hideLoader();
				alert(client_name+" cannot be Deleted as it is associated to 1+ Case.");
				return false;
			}
		}
	});
}
/* End : Used to load delete client */

/* Start : Cancel load client contact list */
function loadClientContactListCancel(){
	var chk_status = checkformstatus("event");
	if(chk_status == true) {	
		loadClientContactList();
	}
}


/* Start : Used to load client's contact List */
function loadClientContactList(){
	jQuery.ajax({
		url: baseUrl +'/client/contact-list',
		data:{client_id: jQuery('#client_id').val()},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#add-client-contacts').html(data);
			//jQuery.pjax.reload({container:'#contactsgrid-pajax', replace: false, url: baseUrl +'/client/contact-list&client_id='+jQuery('#client_id').val()});
		}
	});
}
/* End : Used to load client's contact List */

/* Start : Used to load Add new client contact Form */
function addClientContacts(client_id){
	jQuery.ajax({
		   url: baseUrl +'/client/add-client-contacts&client_id='+client_id,
		   type: 'get',
		   beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   jQuery('#add-client-contacts').html(data);
		   }
	});
}
/* End : Used to load Add new client contact Form */

/* Start : Used to load Edit client contact Form */
function updateClientContact(contact_id){
	jQuery.ajax({
		url: baseUrl +'/client/update-client-contacts&id='+contact_id+'&client_id='+jQuery('#client_id').val(),
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#add-client-contacts').html(data);
		} 
	});
}
/* End : Used to load Edit client contact Form */

/* Start : Used to delete client contacts */
function deleteClientContact(client_id, contact_id, contact_type){
	if (client_id == '' || client_id == 0 || client_id == 'undefined') {
		alert('Something is wrong, please try again.');
		return false;
	} else if(contact_id == '' || contact_id == 0 || contact_id == 'undefined'){
		alert('Something is wrong, please try again.');
		return false;		
	} else {
		if(confirm('Are you sure you want to Remove '+contact_type+'?')){
			jQuery.ajax({
				url: baseUrl +'/client/delete-client-contact',
				data:{client_id: client_id,contact_id:contact_id},
				type: 'post',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					if(data == 'OK'){
						loadClientContactList();
					} else {
						alert('Something is wrong, please try again.');
						return false;
					}
				}
			});
		}
	}
}
/* End : Used to delete client contacts */

/* Start : Used to delete selected client contacts */
function deleteSelectedClientContact(){
	var client_id = jQuery('#client_id').val();
	var contact_id = $('#contactsgrid').yiiGridView('getSelectedRows');
	if(contact_id=='')
			alert("Please select a record to perform this action.");
	
	var newkeys = contact_id.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent($( '.chk_contact_type_'+newkeys[i] ).val()));
		str_val =  " "+val['contact_type'];
		str.push(str_val);
	}
	var str_length = str.length;
	if (client_id == '' || client_id == 0 || client_id == 'undefined') {
		alert('Client is not identified, please try again.');
		return false;
	} else if(contact_id == '' || contact_id == 'undefined' || contact_id.length == 0){
		alert('Please select a record to perform this action.');
		return false;		
	} else {
		if(confirm('Are you sure you want to Remove the selected '+str_length+' record(s):'+str+'?')){
			jQuery.ajax({
				url: baseUrl +'/client/delete-selected-client-contacts',
				data:{client_id: client_id,contact_id:contact_id},
				type: 'post',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					if(data == 'OK'){
						loadClientContactList();
					} else {
						alert('Something is wrong, please try again.');
						return false;
					}
				}
			});
		}
	}
}
/* End : Used to delete selected client contacts */

/* Start : To Load Client's assigned user's list */
function loadAssignedUserList(){
	var client_id = jQuery('#client_id').val();
	jQuery.ajax({
		url: baseUrl +'/client/assigned-user-list',
		data:{client_id: client_id},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#assigned-user').html(data);
		}
  	});
}
/* End : To Load Client's assigned user's list */

function ClientshowHideBlock(showid,hideid){
	if(showid=='second'){
	var contact_type = $('#clientcontacts-contact_type').val();
	var lname = $('#clientcontacts-lname').val();
	var fname = $('#clientcontacts-fname').val();
	var email = $('#clientcontacts-email').val();
	var city  = $('#clientcontacts-city').val();
		if(fname!="" && lname!="" && contact_type!="" && email!="" && city!=""){
			jQuery('#'+showid).show();jQuery('#'+hideid).hide();
		}else{
			$('#clientcontacts-contact_type').blur();
			$('#clientcontacts-lname').blur();
			$('#clientcontacts-fname').blur();
			$('#clientcontacts-email').blur();
			$('#clientcontacts-city').blur();
			
		}
	}else{
		jQuery('#'+showid).show();jQuery('#'+hideid).hide();
	}
}
/* Start : To Load Client's Excluded Service list */
function loadExcludedClientServiceList(){
	var client_id = jQuery('#client_id').val();	
	//return false;
	jQuery.ajax({
		url: baseUrl +'/client/excluded-service-list',
		data:{client_id: client_id},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#exclude-services').html(data);
		}
  	});
}
/* End : To Load Cleint's Excluded Service list */
/* Start: Post data to client services */
function postExcludedClientsServies(form_id,btn,successFunc,targetid){
	var form = $('form#'+form_id);
	var client_id = jQuery('#client_id').val();		
	jQuery(".ckeck_excluded").each(function(){
		$(form).append('<input type="hidden" name="idsToRemove['+$(this).data('teamservice_id')+'][]" value="'+$(this).data('team_loc')+'" >');	
	});
	$.ajax({
		url    :   baseUrl +'/client/update-excluded-service-list&client_id='+client_id,  
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function() {
        	$(btn).attr('disabled','disabled');
        },
        success: function (response){
			if(response == 'OK'){
				//eval(successFunc);
				//updateClient(client_id);
				updateClientExclusiveService(client_id);
			}else{
				$('#'+targetid).html(response);
        		$(btn).removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
    return false;
	}
/* End : To Post Cleint's Excluded Service list */

/* End : Client Management */
