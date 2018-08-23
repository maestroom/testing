$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});

jQuery(document).ready(function(){
	/* Datepicker For SLA Business Hours*/
	/* System Module Js Code Start*/
	jQuery(".sysModules").click(function(event){
		var chk_form_status = checkformstatus(event); // check form edit status 
		if(chk_form_status == true) {
			var module=jQuery(this).data('module');
			jQuery('.sysModules').removeClass('active');
			if(module == 'managedd'){
				CaseTypeLoad();
				jQuery(this).addClass('active');
				setTitle('fa-wrench', '<a href="javascript:void(0);" title="System Management - Manage Dropdown" class="tag-header-red">System Management - Manage Dropdown</a>');
			}
			else if(module == 'unitconversion'){
				SizeConversions();
				jQuery(this).addClass('active');
				setTitle('fa-wrench', '<a href="javascript:void(0);" title="System Management - Unit Conversions" class="tag-header-red">System Management - Unit Conversions</a>');
			}
			else if(module == 'project_sort'){
				commonAjax(baseUrl +'/system/projectsort','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench', '<a href="javascript:void(0);" title="System Management - Project Sort Display" class="tag-header-red">System Management - Project Sort Display</a>');
			}
			else if(module == 'rebrandsystem'){
				commonAjax(baseUrl +'/system/rebrand-system','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench', '<a href="javascript:void(0);" title="System Management - Rebrand System" class="tag-header-red">System Management - Rebrand System</a>');
			}
			else if(module == 'custodianforms'){
				commonAjax(baseUrl +'/system/custodianforms','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench', '<a href="javascript:void(0);" title="Form Field Management - Custodian Interview Forms" class="tag-header-red">Form Field Management - Custodian Interview Forms</a>');
			}
			else if(module == 'customwording'){
				commonAjax(baseUrl +'/system/custom-wording-login','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Custom Wording" class="tag-header-red">System Management - Custom Wording</a>');
			}
			else if(module == 'emailsetting'){
				commonAjax(baseUrl +'/system/emailsetting','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Email Alerts Configuration" class="tag-header-red">System Management - Email Alerts Configuration</a>');
			}
			else if(module == 'ldapconfig'){
				commonAjax(baseUrl +'/system/ldapconfig','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - LDAP Configuration" class="tag-header-red">System Management - LDAP Configuration</a>');
			}
			else if(module == 'sysupdate'){
				commonAjax(baseUrl +'/system/sysupdate','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - System Updates" class="tag-header-red">System Management - System Updates</a>');
			}
			else if(module == 'emailtempconfig'){
				commonAjax(baseUrl +'/system/emailtempconfig','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Email Template Configuration" class="tag-header-red">System Management - Email Template Configuration</a>');
			}
			else if(module == 'SlaBusinessHrs'){
				commonAjax(baseUrl +'/system/slabusinesshours','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Business Hours" class="tag-header-red">System Management - Business Hours</a>');
			}
			else if(module == 'SessionTimeoutSetting'){
				commonAjax(baseUrl +'/system/sessiontimeoutsetting','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Session Timeout" class="tag-header-red">System Management - Session Timeout</a>');
			}
			else if(module == 'SessionTimeoutSetting'){
				commonAjax(baseUrl +'/system/sessiontimeoutsetting','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - Session Timeout" class="tag-header-red">System Management - Session Timeout</a>');
			}
			else if(module == 'SystemMaintenance'){
				commonAjax(baseUrl +'/system/system-maintenance','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="System Management - System Maintenance" class="tag-header-red">System Management - System Maintenance</a>');
			}		
		}
    });
    /*System Form*/
    jQuery(".FormfieldModules").click(function(event){
		var chk_form_status = checkformstatus(event); // check form edit status 
		if(chk_form_status == true) {
				commonAjax(baseUrl +'/system/form','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="Form Field Management - System Form" class="tag-header-red">Form Field Management - System Form</a>');
		}
	});
	/*Workflow Js Code Start*/
	jQuery(".workflowModules").click(function(event){
		var chk_form_status = checkformstatus(event); // check form edit status 
		if(chk_form_status == true) {
			setTitle('fa-wrench','Workflow Management');
			var module=jQuery(this).data('module');
			jQuery('.workflowModules').removeClass('active');
			if(module == 'CaseManagerTeam'){
				commonAjax(baseUrl +'/workflow/caseteam&team_id=1','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="Workflow Management - Case Manager Team" class="tag-header-red">Workflow Management - Case Manager Team</a>');
			}
			if(module == 'OperationTeams'){
				commonAjax(baseUrl +'/workflow/operationalteam&team_id=0','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="Workflow Management - Operation Teams" class="tag-header-red">Workflow Management - Operation Teams</a>');
			}
			if(module == 'WorkflowTemplates'){
				commonAjax(baseUrl +'/workflow/templates','admin_main_container');
				jQuery(this).addClass('active');
				setTitle('fa-wrench','<a href="javascript:void(0);" title="Workflow Management - Workflow Templates" class="tag-header-red">Workflow Management - Workflow Templates</a>');
			}
		}
	});
	
	jQuery('body').on('click', '#demo2', function () {
		$(this).next('span').find('a').trigger('click');
	});
	
	jQuery('body').on('click', '#addServiceTaskForm', function () {
        //var sel_row = jQuery('.grid-view').yiiGridView('getSelectedRows');
		var sel_row = jQuery('#servicetaskgrid').yiiGridView('getSelectedRows');
        var teamId = jQuery('#team_id').val();
        var teamservice_id = jQuery('#teamservice_id').val();
        var serviceTaskFormChk = jQuery('input[name=serviceTaskForm]:checked').val();
        if(!sel_row.length){
    		alert('Please select at least 1 record to perform this action.');
    	}
        else if(sel_row.length > 1){
    		alert('Please select a single record to perform this action.');
    	}else{
    		jQuery.ajax({
                type: "get",
                url: baseUrl + "workflow/chktaskfrom&serviceTask=" + sel_row + "&form=" + serviceTaskFormChk,
                dataType: 'html',
                cache: false,
                success: function (data) {
			        if (data.replace(/\s/g, '') == "no"){
                    	AddEditFormbuilderData(teamId,teamservice_id,sel_row,serviceTaskFormChk,'add');
                    }else{
                        alert("A Form has already been added for the selected Task.");
                    }
                }
            });
        }
    });
    
    /** 
     * Service Task Ajax fro workflow
     * Click from Icon (Instructions, Billable, Data Form)
     */
    $('body').on('click', '#serviceTaskForm', function () {
		var data_id = $(this).attr( "data-id" );
		var sel_row = new Array(data_id); // Convert Array
		var teamId = $(this).attr( "data-team-id" );
		var teamservice_id = $(this).attr( "data-team-serviceid" );
		var serviceTaskFormChk = $(this).attr( "data-name" );
		
		jQuery.ajax({
			type: "POST",
			url: baseUrl + "workflow/chktaskfrom&serviceTask=" + sel_row + "&form=" + serviceTaskFormChk,
			dataType: 'html',
			cache: false,
			success: function (data) {
				console.log(data);
				if (data.replace(/\s/g, '') == "yes") {
					AddEditFormbuilderData(teamId,teamservice_id,sel_row,serviceTaskFormChk,'edit');
				} else {
					alert("A Form cannot be Edited until it is Added.");
				} 
			}
		});
    });
    
	$('body').on('click', '#editServiceTaskForm', function () {

            var sel_row = jQuery('#teamservice-gird .grid-view').yiiGridView('getSelectedRows');
        var teamId = jQuery('#team_id').val();
        var teamservice_id = jQuery('#teamservice_id').val();
        var serviceTaskFormChk = jQuery('input[name=serviceTaskForm]:checked').val();
		
	    if(!sel_row.length){
    		alert('Please select at least 1 record to perform this action.');
    	}
        else if(sel_row.length > 1){
    		alert('Please select a single record to perform this action.');
    	}else{
    		jQuery.ajax({
                type: "POST",
                url: baseUrl + "workflow/chktaskfrom&serviceTask=" + sel_row + "&form=" + serviceTaskFormChk,
                dataType: 'html',
                cache: false,
                success: function (data) {
					console.log(data);
                    if (data.replace(/\s/g, '') == "yes") {
                    	AddEditFormbuilderData(teamId,teamservice_id,sel_row,serviceTaskFormChk,'edit');
                    }else{
                        alert("A Form cannot be Edited until it is Added.");
                    } 
                }
            });
        }
    });
	
	jQuery("body").on('click',"#AddLogicTeamservice",function(){
        var error = '';
    	var Teamservice_teamid = $('#teamservice-teamid').val();
    	var Teamservice_id = $('#teamservice-id').val();
    	var Teamservice_loc = "";
    	if($('#teamservice-team_location :checked').length > 0){
	    	$('#teamservice-team_location :checked').each(function(i, selected){ 
	    		if(Teamservice_loc != "")
	    			Teamservice_loc += ","+$(this).val();
	    		else 
	    			Teamservice_loc = $(this).val(); 
	    	});
    	}else{
    		error+='Team Location is Required Field \n'; 
    		alert(error);
    		return false;
    	}
		
    	var url = baseUrl+"workflow/add-logic-taskform/";
    	$.ajax({
			type: "post",
			url: url,
			data:{teamservice_id:Teamservice_id,teamservice_teamid:Teamservice_teamid,teamservice_loc:Teamservice_loc},
			dataType:'html',
			beforeSend:function () {showLoader();},
			success:function(data){
				//console.log(data);return false;
				hideLoader();
				if($('#addLogicBox').length == 0){
					$('#Teamservice').append("<div id='addLogicBox'></div>");
				}
				
				$('#addLogicBox').html(data);
				var $DialogContainer = $('#addLogicBox');
				$('#TeamserviceSla').ajaxForm({                 
					success: SubmitSuccesfulAddLogic,
                });
				
				$DialogContainer.dialog({ 
			        modal: true,
			        width:'65em',
			        height:456,
					title:'Add SLA Logic',
					open: function() {
						$('.ui-dialog-buttonpane').find('button:contains("Add Another")').addClass('btn btn-primary');
						$('.ui-dialog-buttonpane').find('button:contains("Add")').addClass('btn btn-primary');
						$('.ui-dialog-buttonpane').find('button:contains("Cancel")').addClass('btn btn-primary');
					},
					close: function() {
						$DialogContainer.dialog('destroy').remove();
					},
					buttons: {
						Cancel:{
                            text: "Cancel",
                            "title":"Cancel",
                            "class": 'btn btn-primary',
                            'aria-label': "Cancel",
                            click:function() {
								$DialogContainer.dialog( "close" );
								$.each($('.ui-dialog'), function (i, e) {
									$DialogContainer.dialog("close");
								});
                            }
	                   },
						"Add Another":{
	                            text: "Add Another",
	                            "title":"Add Another",
	                            "class": 'btn btn-primary',
	                            'aria-label': "Add Another",
	                            click:function () {
									validateslalogic($DialogContainer);
							    }
	                        },
						
						"Add":{
                            text: "Add",
                            "title":"Add",
                            "class": 'btn btn-primary',
                            'aria-label': "Add",
                            click:function () {      
							    var start_qty = $('#teamservicesla-start_qty').val();
                                var end_qty = $('#teamservicesla-end_qty').val();  
                                var start_qty_d = parseInt(start_qty);
                                var end_qty_d = parseInt(end_qty);                              
                                if(start_qty_d > end_qty_d){
                                    alert('End Size cannot be less than the Start Size');
                                }else {
									/* change flag */
									$("#Teamservice #is_change_form").val('1');
									$("#Teamservice #is_change_form_main").val('1');
								    $("#TeamserviceSla").submit();
                                }                                 
                                // $DialogContainer.dialog("close");
                            }
	                    },
					}
				});
			}
    	});
	});	
}); 
function SubmitSuccesfulAddLogic(responseText, statusText){
	if(responseText!="no"){
		$('#logic_sla_list').append(responseText);
		$('#addLogicBox').dialog('close');
	}
	else{
		alert("Opps. Something Wrong...");
	}
}

/**
 * Select Manage Dropdown
 */
function SelectManageDropdown(opt, dropdown){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) {
		switch (opt)
		{	
			case 'CaseCloseType': CaseCloseType(); break;
			case 'CaseType': CaseType(); break;
			case 'Industries': Industries(); break;
			case 'MediaCategory': MediaCategory(); break;
			case 'MediaDataType': MediaDataType(); break;
			case 'MediaDataUnits': MediaDataUnits(); break;
			case 'MediaEncrypt': MediaEncrypt(); break;
			case 'MediaTo': MediaTo(); break;
			case 'MediaType': MediaType(); break;
			case 'MediaLocation': MediaLocation(); break;
			case 'ProjectPriority': ProjectPriority(); break;
			case 'ProjectPriorityTeam': ProjectPriorityTeam(); break;
			case 'ProjectRequestType': ProjectRequestType(); break;
			case 'TeamLocations': TeamLocations(); break;
			case 'TaskPriceUnits': TaskPriceUnits(); break;
			case 'ToDoFollowupCategory': ToDoFollowupCategory(); break;
			default: alert('Default case'); break;
		}
	}
}

function validateslalogic(objdialog){
	$flag=false;
	$.ajax({
		url:baseUrl+'workflow/validateslalogin',
		type:'post',
		data:$("#TeamserviceSla").serialize(),
		success:function(response){
			if(response.length==0){
				var form = $('form#TeamserviceSla');
				var start_qty = $('#teamservicesla-start_qty').val();
				var end_qty = $('#teamservicesla-end_qty').val();  
				var start_qty_d = parseInt(start_qty);
				var end_qty_d = parseInt(end_qty);
				if(start_qty_d > end_qty_d){
					alert('End Size cannot be less than the Start Size');
					return false;
				}else {
					$.ajax({
					url    : form.attr('action'),
					cache: false,
					type   : 'post',
					data   : form.serialize(),
					success: function (responseText){
						$('#logic_sla_list').append(responseText);
						$("#Teamservice #is_change_form").val('1'); // change flag to 1
						$("#is_change_form_main").val('1'); // change flag to 1
						objdialog.dialog( "close" );
						$.each($('.ui-dialog'), function (i, e) {
                                objdialog.dialog("close");
                        }); 
                        $("#AddLogicTeamservice").trigger('click');
							/*if(responseText!="no"){
									$('#logic_sla_list').append(responseText);
									$('#teamservicesla-team_loc_id').val('');
									$('#teamservicesla-start_qty').val('');
									$('#teamservicesla-end_qty').val('');
									$('#teamservicesla-del_qty').val('');
									$('#teamservicesla-del_time_unit').val('').change();
									$('#teamservicesla-project_priority_id').val('').change();
							/*}else{
									alert("Oops. Something is wrong…");
							}*/
						},
					 });
				}
			}else{
				for (var key in response) {
						$("#"+key).parent().find('.help-block').html(response[key]);
						$("#"+key).closest('div.form-group').addClass('has-error');
				}
			}
		}
	});
	return $flag;
}

/*CaseType*/
function CaseType(){
	jQuery.ajax({
	       url: baseUrl +'/site/casetype',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_main_container').html(data);
	       },
	       complete: function(){
				$('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function CaseTypeLoad(){
	jQuery.ajax({
	   url: baseUrl +'/site/casetype',
	   type: 'get',beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   jQuery('#admin_main_container').html(data);
	   }
	});
}
function SizeConversions(){
	jQuery.ajax({
	   url: baseUrl +'/site/sizeconversions',
	   type: 'get',
	   beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   jQuery('#admin_main_container').html(data);
	   }
	});	
}
function AddCaseType(){
	jQuery.ajax({
	       url: baseUrl +'/site/addcasetype',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function AddProcessCaseType(){
	jQuery.ajax({
	       url: jQuery('#frm-casetype').attr('action'),
	       data:jQuery('#frm-casetype').serialize(),
	       type: 'post',
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   CaseType();
	    	   else
	    		   jQuery('#casetype-case_type_name').blur();
	    		  //alert('Error');
	       }
	  });
}
function UpdateCaseType(id){
	jQuery.ajax({
	       url: baseUrl +'/site/updatecasetype&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}

/*function CancelCaseType(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) {
		CaseType()
	}
}*/

function SelectCaseType(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) {
		CaseType();
	}
}

function UpdateProcessCaseType(){
	jQuery.ajax({
	       url: jQuery('#frm-casetype').attr('action'),
	       data:jQuery('#frm-casetype').serialize(),
	       type: 'post',
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   CaseType();
	    	   /*else
	    		  alert('Error');*/
	       }
	  });
}
function DeleteCaseType(id){
	jQuery.ajax({
	       url: baseUrl +'/site/deletecasetype&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   CaseType();
	    	  /* else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveCaseType(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a single record to perform this action.");
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_case_type_name_'+newkeys[i] ).val())));
		str_val =  ' '+val['case_type_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/site/deleteselectedcasetype',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   CaseType();
			    	 /*  else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/* Select Case Close Type */
function SelectCaseCloseType(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) 
		CaseCloseType();
}

/*Case Close Type*/
function CaseCloseType(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#CaseCloseType').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/case-close-type',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	       complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddCaseCloseType(){
	jQuery.ajax({
	       url: baseUrl +'/case-close-type/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}  
function UpdateCaseCloseType(id){
	jQuery.ajax({
	       url: baseUrl +'/case-close-type/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteCaseCloseType(id){
	jQuery.ajax({
	       url: baseUrl +'/case-close-type/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   CaseCloseType();
	    	  /* else
	    		  alert('Error');*/
	       }
	  });
}

function RemoveCaseCloseType(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_close_type_'+newkeys[i] ).val())));
		str_val =  ' '+val['close_type'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	} else {
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/case-close-type/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   CaseCloseType();
			    	  /* else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Client Industries*/
function Industries(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#Industries').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/industry',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddIndustry(){
	jQuery.ajax({
	       url: baseUrl +'/industry/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function UpdateIndustry(id){
	jQuery.ajax({
	       url: baseUrl +'/industry/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteIndustry(id){
	jQuery.ajax({
	       url: baseUrl +'/industry/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   Industries();
	    	/*   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveIndustry(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_industry_name_'+newkeys[i] ).val())));
		str_val =  ' '+val['industry_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/industry/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   Industries();
			    	/*   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Media Category*/
function MediaCategory()
{
	jQuery('.dropdown').removeClass('active');
	jQuery('#MediaCategory').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/evidence-category',
	       type: 'get', beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	       complete: function(){
				$('#admin_right').find('.select-on-check-all').next('label').focus();
		   }
	  });	
}
function AddMediaCategory(){
	jQuery.ajax({
	       url: baseUrl +'/evidence-category/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaCategory(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-category/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteMediaCategory(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-category/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaCategory();
	    	/*   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaCategory(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	//console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_category_'+newkeys[i] ).val())));
		str_val =  "\n" + val['category'];
		str_val =  ' '+val['category'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/evidence-category/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaCategory();
			    /*	   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Media Data Type*/
function MediaDataType(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#MediaDataType').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/data-type',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	       complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddMediaDataType(){
	jQuery.ajax({
	       url: baseUrl +'/data-type/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaDataType(id){
	jQuery.ajax({
	       url: baseUrl +'/data-type/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteMediaDataType(id){
	jQuery.ajax({
	       url: baseUrl +'/data-type/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaDataType();
	    	 /*  else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaDataType(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_data_type_'+newkeys[i] ).val())));
		str_val =  ' '+val['data_type'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/data-type/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaDataType();
			    	/*   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Media Data Units*/
function MediaDataUnits(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#MediaDataUnits').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/unit',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddMediaDataUnits(){
	jQuery.ajax({
	       url: baseUrl +'/unit/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaDataUnits(id){
	jQuery.ajax({
	       url: baseUrl +'/unit/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function UpdateUnitHidden(id,is_hidden){
	jQuery.ajax({
	   url: baseUrl +'/unit/update-unit-hidden&id='+id+'&is_hidden='+is_hidden,
	   type: 'get',beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   if(data == 'OK')
			MediaDataUnits();
	   }
	});
}
function DeleteMediaDataUnits(id){
	jQuery.ajax({
	       url: baseUrl +'/unit/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaDataUnits();
	    /*	   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaDataUnits(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_unit_name_'+newkeys[i] ).val())));
		str_val =  ' '+val['unit_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/unit/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaDataUnits();
			    	/*   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Media Encrypt*/
function MediaEncrypt(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#MediaEncrypt').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/evidence-encrypt-type',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddMediaEncrypt(){
	jQuery.ajax({
	       url: baseUrl +'/evidence-encrypt-type/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaEncrypt(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-encrypt-type/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteMediaEncrypt(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-encrypt-type/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaEncrypt();
	    	 /*  else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaEncrypt(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_encrypt_'+newkeys[i] ).val())));
		str_val =  ' '+val['encrypt'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/evidence-encrypt-type/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaEncrypt();
			    /*	   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Media To*/
function MediaTo(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#MediaTo').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/evidence-to',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	       complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
		   }
	  });
}
function AddMediaTo(){
	jQuery.ajax({
	       url: baseUrl +'/evidence-to/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaTo(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-to/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteMediaTo(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-to/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaTo();
	    	/*   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaTo(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_to_name_'+newkeys[i] ).val())));
		//str_val =  "\n" + val['to_name'];
		str_val =  ' '+val['to_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/evidence-to/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaTo();
			    	/*   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*Media Type*/
function MediaType(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#MediaType').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/evidence-type',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddMediaType(){
	jQuery.ajax({
	       url: baseUrl +'/evidence-type/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaType(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-type/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteMediaType(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-type/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaType();
	    	 /*  else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaType(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_evidence_name_'+newkeys[i] ).val())));
		//str_val =  "\n" + val['evidence_name'];
		str_val =  ' '+val['evidence_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/evidence-type/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaType();
			    /*	   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}
/*Media Location*/
function MediaLocation(){
	jQuery('.dropdown').removeClass('active');
	jQuery('.MediaLocation').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/evidence-stored-loc',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddMediaLocation(){
	jQuery.ajax({
	       url: baseUrl +'/evidence-stored-loc/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateMediaLocation(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-stored-loc/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteMediaLocation(id){
	jQuery.ajax({
	       url: baseUrl +'/evidence-stored-loc/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   MediaLocation();
	    	/*   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveMediaLocation(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_stored_loc_'+newkeys[i] ).val())));
	//	str_val =  "\n" + val['stored_loc'];
		str_val =  ' '+val['stored_loc'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/evidence-stored-loc/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   MediaLocation();
			    	/*   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}
/*Project Priority*/
function ProjectPriority(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#ProjectPriority').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/priority-project',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
                }
	  });
}
function AddProjectPriority(){
	jQuery.ajax({
	       url: baseUrl +'/priority-project/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateProjectPriority(id){
	jQuery.ajax({
	       url: baseUrl +'/priority-project/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteProjectPriority(id){
	jQuery.ajax({
	       url: baseUrl +'/priority-project/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   ProjectPriority();
	    	 /*  else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveProjectPriority(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent($( '.chk_priority_'+newkeys[i] ).val()));
	//	str_val =  "\n" + val['priority'];
		str_val =  ' '+val['priority'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/priority-project/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   ProjectPriority();
			    /*	   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}
/*Project Priority Team*/
function ProjectPriorityTeam(){
	jQuery('.dropdown').removeClass('active');
	jQuery('.ProjectPriorityTeam').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/priority-team',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	       complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddProjectPriorityTeam(){
	jQuery.ajax({
	       url: baseUrl +'/priority-team/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateProjectPriorityTeam(team_id, team_loc_id){
	jQuery.ajax({
	       url: baseUrl +'/priority-team/update&team_id='+team_id+'&team_loc_id='+team_loc_id,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}

/* IRT 169 Changes */
function DeleteProjectPriorityTeam(team_id,team_loc){
	jQuery.ajax({
	       url: baseUrl +'/priority-team/delete&team_id='+team_id+'&team_loc='+team_loc,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		  ProjectPriorityTeam();
	    	   else
	    		  alert('you can not delete Team & TeamLoc'); return false;
	       }
	  });
}
function RemoveProjectPriorityTeam()
{
	var keys = new Array();
	$('input:checked').each(function (e) {
		keys.push($(this).val());
	});
	
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		var conf = confirm("Are you sure you want to delete ?"); // confirmation
		if(conf){
			jQuery.ajax({
			   url: baseUrl +'/priority-team/deleteselected',
			   data:{keylist: keys},
			   type: 'post',
			   success: function (data) {
				   hideLoader();
				   if(data == 'Ok')
					   ProjectPriorityTeam();
				   else
					  alert('Already in used');
			   }
			});
		}
	}
}
/*Project Request Type*/
function ProjectRequestType(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#ProjectRequestType').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/project-request-type',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddProjectRequestType(){
	jQuery.ajax({
	       url: baseUrl +'/project-request-type/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateProjectRequestType(id){
	jQuery.ajax({
	       url: baseUrl +'/project-request-type/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteProjectRequestType(id){
	jQuery.ajax({
	       url: baseUrl +'/project-request-type/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   ProjectRequestType();
	    	   else
	    		  alert("You cannot delete this Project Request Type because it is used in a Workflow Template or a Project."); return false;
	       }
	  });
}
function RemoveProjectRequestType(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_request_type_'+newkeys[i] ).val())));
	//	str_val =  "\n" + val['request_type'];
		str_val =  ' '+val['request_type'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/project-request-type/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   ProjectRequestType();
			    	/*   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}
/*Team Locations*/
function TeamLocations(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#TeamLocations').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/teamlocation',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddTeamLocations(){
	jQuery.ajax({
	       url: baseUrl +'/teamlocation/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateTeamLocations(id){
	jQuery.ajax({
	       url: baseUrl +'/teamlocation/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteTeamLocations(id){
	jQuery.ajax({
       url: baseUrl +'/teamlocation/delete&id='+id,
       type: 'get',
       success: function (data) {
       	   hideLoader();
       	   if(data == 'OK'){
       		   TeamLocations();
       	   }else{
       		   alert(data);   
       	   }
       }
  });
}
function RemoveTeamLocations(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_team_location_name_'+newkeys[i] ).val())));
	//	str_val =  "\n" + val['team_location_name'];
		str_val =  ' '+val['team_location_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/teamlocation/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   TeamLocations();
			    	/* else
			    		   alert('Error'); */
			       }
			  });
		}
	}
}
/*Task Price Units*/
function TaskPriceUnits(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#TaskPriceUnits').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/unitprice',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddTaskPriceUnits(){
	jQuery.ajax({
	       url: baseUrl +'/unitprice/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateTaskPriceUnits(id){
	jQuery.ajax({
	       url: baseUrl +'/unitprice/update&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function DeleteTaskPriceUnits(id){
	jQuery.ajax({
	       url: baseUrl +'/unitprice/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   TaskPriceUnits();
	    	/*   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveTaskPriceUnits(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_unit_price_name_'+newkeys[i] ).val())));
		str_val =  ' '+val['unit_price_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/unitprice/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   TaskPriceUnits();
			  /*  	   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}
/* ToDo Follow-up Category */
function ToDoFollowupCategory(){
	jQuery('.dropdown').removeClass('active');
	jQuery('#ToDoFollowupCategory').addClass('active');
	jQuery.ajax({
	       url: baseUrl +'/todocat',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       },
	        complete: function(){
			 $('#admin_right').find('.select-on-check-all').next('label').focus();
			}
	  });
}
function AddToDoFollowupCategory(){
	jQuery.ajax({
	       url: baseUrl +'/todocat/create',
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
	  });
} 
function UpdateToDoFollowupCategory(id){
	jQuery.ajax({
	   url: baseUrl +'/todocat/update&id='+id,
	   type: 'get',beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   jQuery('#admin_right').html(data);
	   }
  });
}
function DeleteToDoFollowupCategory(id){
	jQuery.ajax({
	       url: baseUrl +'/todocat/delete&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK')
	    		   ToDoFollowupCategory();
	    	/*   else
	    		  alert('Error');*/
	       }
	  });
}
function RemoveToDoFollowupCategory(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent(escape($( '.chk_todo_cat_'+newkeys[i] ).val())));
		str_val =  ' '+val['todo_cat'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/todocat/deleteselected',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   hideLoader();
			    	   if(data == 'OK')
			    		   ToDoFollowupCategory();
			    /*	   else
			    		  alert('Error');*/
			       }
			  });
		}
	}
}

/*CustodianFormNew */
function updateCustodianFormNew(id){
	var chk_status = checkformstatus("event");
	if(chk_status==true)
		updateCustodianForm(id);
}

/*CustodianForm*/
function updateCustodianForm(id){
	jQuery('.cfrom').removeClass('active');
	jQuery.ajax({
	       url: baseUrl +'/system/updatecustodianforms&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#cfrom_'+id).addClass('active');
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
/* DeleteCustForm */
function deleteCustForm(id){
	jQuery.ajax({
	       url: baseUrl +'/system/deletecustodianforms&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   commonAjax(baseUrl +'/system/custodianforms','admin_main_container');
	       }
	  });
} 


function CustomWordingInstructionLoginSelect(){
	var chk = checkformstatus("event");
	if(chk == true)
		commonAjax(baseUrl +'/system/custom-wording-login','admin_main_container');
}
/* IRT-15 Login page Bottom Section*/
function CustomWordingLoginBottomSelect(){
	var chk = checkformstatus("event");
	if(chk == true)	CustomWordingLoginBottom();		
}
/* IRT-15 Login page Bottom Section*/
function CustomWordingLoginBottom(){
	jQuery('.wordingloginlinks').removeClass('active');	
	jQuery.ajax({
		   url: baseUrl +'/system/custom-wording-login-bottom',
		   type: 'get',beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();			   
			   jQuery('.customwordloginbottom').addClass('active');
			   jQuery('#admin_right').html(data);
		   }
	  });
	}
/* IRT-15 Code ends */	
/** Custom Wording Login **/
function CustomWordingInstructionLogin() {
	var chk = checkformstatus("event");
	if(chk == true)
		commonAjax(baseUrl +'/system/custom-wording-login','admin_main_container');
}

/* Cancle Custom wording Header */
function CustomWordingInstructionSelect(){
	var chk = checkformstatus("event");
	if(chk == true)
		CustomWordingInstruction();
}

/* Custom Wording */
function CustomWordingInstruction(){
	jQuery('.wordingloginlinks').removeClass('active');	
	jQuery.ajax({
		   url: baseUrl +'/system/custom-wording-instruction',
		   type: 'get',beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   jQuery('.customwordinstructionheader').addClass('active');
			   jQuery('#admin_right').html(data);
		   }
	  });
}

/* Custom Wording */
function CustomWordingInstructionFooterSelect(){
	var chk = checkformstatus("event");
	if(chk == true)
		CustomWordingInstructionFooter();
}

/* Custom Wording Repor Header */
function CustomWordingReportHeaderSelect(){
	var chk = checkformstatus("event");
	if(chk == true)	CustomWordingReportHeader();
}

/* Custom Wording Header for Report */
function CustomWordingReportHeader() {
	jQuery('.wordingloginlinks').removeClass('active');	
	jQuery.ajax({
		   url: baseUrl+'/system/custom-wording-report-header',
		   type: 'get',
		   beforeSend: function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   jQuery('.customwordreportheader').addClass('active');
			   jQuery('#admin_right').html(data);
		   }
	  });
}

/* Custom Wording */
function CustomWordingInstructionFooter(){
	jQuery('.wordingloginlinks').removeClass('active');	
	jQuery.ajax({
		   url: baseUrl +'/system/custom-wording-instruction-footer',
		   type: 'get',beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   jQuery('.customwordinstructionfooter').addClass('active');
			   jQuery('#admin_right').html(data);
		   }
	  });
}

/*Email and Ldap Config*/
function cancelConfig(){
    $('#buttonSubmit').val('cancel');
	 submitForm();
}

/*Email Template Config*/
function showEmailTemplateConfiguration(id,sort_id){
	var chk_status = checkformstatus("event");
	if(chk_status==true)
		showEmailTemplate(id,sort_id);
}

/*Email Template Config*/
function showEmailTemplate(id,sort_id){
	jQuery('.emailtemp').removeClass('active');
	jQuery.ajax({
		   url: baseUrl +'/system/emailtemplatedata&id='+id,
		   type: 'get',beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   jQuery('#emailtemp_'+id).addClass('active');
			   jQuery('#admin_right').html(data);
		   }
	  });
}
/* Cancel Team Servide form */
function CancelTeamService(team_id){
	var chk_status = checkformstatus("event");
	if(chk_status==true){
		TeamServide(team_id)
	}
}

/* Work Flow */
function TeamServide(team_id){
	jQuery.ajax({
	   url: baseUrl +'/workflow/teamservice&team_id='+team_id,
	   type: 'get',beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   jQuery('#admin_right').html(data);
	   }
	});
}

function updateTeamSelect(team_id){
	var chk_status = checkformstatus("event"); // check form status
	if(chk_status==true) {
		updateTeam(team_id); // Update Team
	}
}
function updateTeam(team_id){
	jQuery('.teamlist').removeClass('active');
	if(team_id == 1){
		commonAjax(baseUrl +'/workflow/caseteam&team_id='+team_id,'admin_main_container');
	}else{
		if(team_id == ''){
			commonAjax(baseUrl +'/workflow/operationalteam&team_id=0','admin_main_container');
		}else{
			jQuery.ajax({
			   url: baseUrl +'/workflow/teamservice&team_id='+team_id,
			   type: 'get',beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   hideLoader();
				   jQuery('#team_list').find("[data-id='" + team_id + "']").addClass('active');
				   jQuery('#admin_right').html(data);
			   }
			});
		}
	}
}
function AddCaseTeamService(team_id){
	jQuery.ajax({
	       url: baseUrl +'/workflow/addteamservice&team_id='+team_id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#tabs-teamservice').html(data);
	       }
	  });
}
function UpdateTeamService(id,team_id){
	jQuery.ajax({
	       url: baseUrl +'/workflow/updateteamservice&id='+id+'&team_id='+team_id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#tabs-teamservice').html(data);
	       }
	  });
}

function slalogiccontentaction(action, id,logic_name) {
    if (action == 'delete') {
        if (confirm("Are you sure you want to Delete "+logic_name+"?")) {
            var deletedLogicId = $('#deletedLogicId').val();
            if (deletedLogicId != "") {
                deletedLogicId = deletedLogicId + "," + id;
            } else {
                deletedLogicId = id;
            }
            $('#deletedLogicId').val(deletedLogicId);
            $('#Teamservice #is_change_form').val('1'); // change flag to 1
			$('#is_change_form_main').val('1'); // change flag to 1
            $('#logic_sla_list #sla_logic_content_' + id).remove();
        }
    } else {
        var id = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][id]"]').val();
        var teamservice_id = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][teamservice_id]"]').val();
        var team_loc_id = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][team_loc_id]"]').val();
        var start_logic = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][start_logic]"]').val();
        var start_qty = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][start_qty]"]').val();
        var size_start_unit_id = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][size_start_unit_id]"]').val();
        var end_logic = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][end_logic]"]').val();
        var end_qty = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][end_qty]"]').val();
        var size_end_unit_id = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][size_end_unit_id]"]').val();
        var del_qty = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][del_qty]"]').val();
        var del_time_unit = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][del_time_unit]"]').val();
        var project_priority_id = $('#logic_sla_list input[name="TeamserviceSla[' + id + '][project_priority_id]"]').val();

        $.ajax({
            type: "post",
            url: baseUrl +'/workflow/update-sla-logic-form',
            data: {id: id, teamservice_id: teamservice_id, team_loc_id: team_loc_id,
                start_logic: start_logic, start_qty: start_qty, size_start_unit_id: size_start_unit_id,
                end_logic: end_logic, end_qty: end_qty, size_end_unit_id: size_end_unit_id,
                del_qty: del_qty, del_time_unit: del_time_unit, project_priority_id: project_priority_id},
            cache: false,
            beforeSend:function (data) {showLoader();},
            success: function (data) {
            	hideLoader();
            	if($('#addLogicBox').length == 0){
					$('#Teamservice').append("<div id='addLogicBox'></div>");
				}
				$('#addLogicBox').html(data);
                $('#editLogicId').val(id);
            },
            complete: function () {
            	//$('#addLogicBox').html('');
                var $otherDialogContainer = $('#addLogicBox');
                $otherDialogContainer.dialog({
                    autoOpen: true,
                    resizable: false,
                    title: 'Edit SLA Logic',
                    width: "65em",
                    modal: true,
                    open: function(){
						$('.ui-dialog-buttonpane').find('button:contains("Update")').addClass('btn btn-primary');
						$('.ui-dialog-buttonpane').find('button:contains("Cancel")').addClass('btn btn-primary');
                    },
                    close: function () {
                        $otherDialogContainer.dialog('destroy').remove();
                    },
                    buttons: {
                    	"Update":{
                            text: "Update",
                            "title":"Update",
                            "class": 'btn btn-primary',
                            'aria-label': "Update",
                            click: function () {
								var form = $('form#TeamserviceSla');
	                        	 var start_qty = $('#teamservicesla-start_qty').val();
									var end_qty = $('#teamservicesla-end_qty').val();  
									var start_qty_d = parseInt(start_qty);
									var end_qty_d = parseInt(end_qty);                              
									if(start_qty_d > end_qty_d){
										alert('End Size cannot be less than the Start Size');
									}else { 
										$.ajax({
											url    : form.attr('action'),
											cache: false,
											type   : 'post',
											data   : form.serialize(),
											success: function (responseText){
												if (responseText != "no") {
													var id = $('#editLogicId').val();
													$('#Teamservice #is_change_form').val('1'); // change flag to 1
													$('#is_change_form_main').val('1'); // change flag to 1
													$('#logic_sla_list #sla_logic_content_' + id).html(responseText);
													$('#editLogicId').val('');
													$otherDialogContainer.dialog('close');
												} else {
											   //     alert("Opps. Something Wrong...");
												}
											},
											error  : function (){
												console.log('internal server error');
											}
										});
								}
                            }
                    	},
                        Cancel:{
                            text: "Cancel",
                            "title":"Cancel",
                            "class": 'btn btn-primary',
                            'aria-label': "Cancel",
                            click: function () {
								$otherDialogContainer.dialog("close");
                            	$('#editLogicId').val('');
                            }
                    	}
                    }
                });
            }
        });
    }
}

function DeleteTeamService(id,team_id){
	jQuery.ajax({
	       url: baseUrl +'/workflow/deleteteamservice&id='+id,
	       type: 'get',beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   if(data == 'OK'){
	    		   TeamServide(team_id);
	    	   }else{
	    		   alert(data);
	    	   }
	    	}
	  });
}
function RemoveCaseTeamService(team_id){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	console.log(keys.length);
	var newkeys = keys.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent($( '.chk_service_name_'+newkeys[i] ).val()));
	//	str_val =  "\n" + val['service_name'];
		str_val =  ' '+val['service_name'];
		str.push(str_val);
	}
	var str_count = str.length;
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	}
	else{
		if(confirm("Are you sure you want to Remove the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/workflow/deleteselectedteamservice',
			       data:{keylist: keys},
			       type: 'post',beforeSend:function (data) {showLoader();},
			       success: function (data) {
			    	   hideLoader();  
			    	   if(data == 'OK'){
			    		   TeamServide(team_id)
			    	   }else{
			    		   alert("Team Service has Service Task in Workflow Template OR Team Service used in exclude Case Service Logic.");
			    	   }
			    	}
			  });
		}
	}
}
function AddServiceTask(){
	var keys = $('#teamservice_id_d').val();
 	if(keys == ''){
 		alert('Please select at least 1 record to perform this action.');
 	}else{
		teamservice_id=$('#teamservice_id').val();
		team_id=$('#team_id').val();
		if(teamservice_id!=""){
			jQuery.ajax({
		       url: baseUrl +'/workflow/servicetaskcreate&team_id='+team_id+'&teamservice_id='+teamservice_id,
		       type: 'get',
		       beforeSend:function (data) {showLoader();},
		       success: function (data) {
		    	   hideLoader();
		    	   jQuery('#tabs-servicetask').html(data);
		       }
		  });
		}
 	}
}
function UpdateServiceTask(id,team_id,teamservice_id){
	jQuery.ajax({
	       url: baseUrl +'/workflow/servicetaskupdate&id='+id+'&team_id='+team_id+'&teamservice_id='+teamservice_id,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#tabs-servicetask').html(data);
	       }
	  });
}
function DeleteServiceTask(id,teamservice_id){
	jQuery.ajax({
	       url: baseUrl +'/workflow/checkisserviceuse',
	       data:{id:id,teamservice_id:teamservice_id},
	       type: 'get',
	       beforeSend : function(){
	            showLoader();
	       },
	       success: function (response) {
		    if(response=='N'){
				jQuery.ajax({
				       url: baseUrl +'/workflow/deleteservicetask&id='+id+'&teamservice_id='+teamservice_id,
				       type: 'get',
				       success: function (data) {
				    	  if($.trim(data)=='OK'){
								hideLoader();
								showservicegrid(teamservice_id,0);
							} else {
								alert(data);
								hideLoader();
								return false;
							}
				    	   
				       }
				  });
		    }else{
				 hideLoader();
				 alert(response);
			}
	     }
	   });		
}
function showservicegrid(teamservice_id,task_hide){
    
	if(task_hide == true){ 
		task_hide = 1;
	} 
	if(task_hide == false){
		task_hide = 0; 
	}
	if(teamservice_id==""){
		jQuery('#teamservice-gird').html(null);
	}else{
            
            $('#maincontainer').removeClass('slide-close');
            
	jQuery.ajax({
	       url: baseUrl +'/workflow/servicetaskajax&teamservice_id='+teamservice_id+'&task_hide='+task_hide,
	       type: 'post',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#teamservice-gird').html(data);
	    	   jQuery('#teamservice_id').val(teamservice_id);
	       }
	  });
	}
}

function CancelServiceTaskMainForm(teamId,teamservice_id){
	var chk_status = checkformstatus("event"); // check form status
	if(chk_status == true) 
		CancelServiceTask(teamId,teamservice_id);
}

function CancelServiceTaskForm(teamId, teamservice_id){
	var chk_status = checkformstatus("event"); // check form status
	if(chk_status == true) 
		CancelServiceTask(teamId,teamservice_id);
}
function CancelServiceTask(teamId,teamservice_id){
	jQuery.ajax({
	   url: baseUrl +'/workflow/servicetask',
	   data:{team_id: teamId},
	   type: 'post',
	   beforeSend:function (data) {showLoader();},
	   success: function (data) {
		 hideLoader();
                 $('#maincontainer').removeClass('slide-close');
		 $('#tabs-servicetask').html(data);
		 $("#teamservice_id").val(teamservice_id).trigger("change");
		 //showservicegrid(teamservice_id,0);
	   }
	});
}
function AddEditFormbuilderData(team_id,teamservice_id,servicetask_id,form,mod){
	jQuery.ajax({
	       url: baseUrl +'/workflow/addservicetaskbuilder',
	       data:{team_id: team_id,teamservice_id:teamservice_id,servicetask_id:servicetask_id,form:form,mod:mod},
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	       	 hideLoader();
	       	 $('#maincontainer').addClass('slide-close');
	       	 $('#tabs-servicetask').html(data);
	       }
	});
}

/**
* get selected Field Types Operator add/edit page
*/
function getAllRoleTypes(project_request_type_id)
{	
	$.ajax({
		url:baseUrl+'project-request-type/get-project-request-type-roles&id='+project_request_type_id,
		beforeSend:function (data) {showLoader();},
		type:"POST",
		data:{project_request_type_id:project_request_type_id},			
		success:function(response){
		hideLoader();		
		if($('body').find('#availabl-roles').length == 0){
			$('body').append('<div class="dialog" id="availabl-roles" title="Available User Roles"></div>');
		}
		$('#availabl-roles').html('').html(response);							
		$('#availabl-roles').dialog({ 
		modal: true,
		width:'40em',
		create: function(event, ui){ 
			$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
			$('.ui-dialog-titlebar-close').prop('title', 'Close');
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
		},
		close:function(){
			$(this).dialog('destroy').remove();
		},
		buttons: [
					{ 
					  text: "Cancel", 
					  "class": 'btn btn-primary',
					  "title": 'Cancel',
					  click: function () { 
						  $(this).dialog("close");						  
					  } 
				   },
				   { 
					  text: "Update", 
						"class": 'btn btn-primary',
						"title": 'Update',
						click: function () {							
							$.ajax({
								url:baseUrl+'project-request-type/update-project-request-type-roles&id='+project_request_type_id,
								beforeSend:function (data) {showLoader();},
								type:"POST",
								data:$("#ProjectRequestTypeRoles").serialize(),			
								success:function(response){
									hideLoader();										
									$('#availabl-roles').dialog("close");										
									ProjectRequestType();									
								}
								});
							return false;													
						}
				  }
			],
			close: function(){
				$(this).dialog('destroy').remove();
			} 
		});			
	}
});
}
/**
 * IRT-42
* get selected Request Types in add/edit page
* 
*/
function getallrequesttypes(id)
{		
		var request_typeids = $(".total_request_type_ids").val();
		//alert(request_typeids);
		$.ajax({
			url:baseUrl+'workflow/get-project-request-type-lists&id='+id,
			beforeSend:function (data) {showLoader();},
			type:"POST",
			data:{request_typeids:request_typeids},			
			success:function(response){
			hideLoader();
			
			if($('body').find('#availabl-request-types').length == 0){
				$('body').append('<div class="dialog" id="availabl-request-types" title="Add Project Request Types"></div>');
			}
			$('#availabl-request-types').html('').html(response);							
			$('#availabl-request-types').dialog({ 
			modal: true,
			width:'40em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
				$('.ui-dialog-titlebar-close').prop('title', 'Close');
                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			},
			close:function(){
				$(this).dialog('destroy').remove();
			},
			buttons: [
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog("close");
							  // $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
						  text: "Add", 
							"class": 'btn btn-primary',
							"title": 'Add',
							click: function () { 
								$("#request-types-tree").dynatree("getRoot").visit(function(node) {
									selKeys = $.map(node.tree.getSelectedNodes(), function(node){
										if(node.childList===null)
											return node.data.key.toString();
									});
									if(node.isSelected()) {
										var reporttype_id = node.data.key;
										var field_type = node.data.title;
										if($('#form-request-type').find('.fieldtype_'+reporttype_id).length == 0)
										{
											$('#form-request-type').append('<tr class="requestType_'+reporttype_id+'"><input class="request_type" type="hidden" name="request_type[]" value='+reporttype_id+' /><td class="inner_text">'+field_type+'</td><td><a href="javascript:void(0);" onClick="remove_dialog_single_request(\'form-request-type\',\'requestType_\',\''+reporttype_id+'\');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
										}
										if(request_typeids=='')
											request_typeids=node.data.key;
										else	
											request_typeids=request_typeids+','+node.data.key;
									}
								});
								/*var fieldtype = $('.primary_table_checkbox').is(':checked');
								if(fieldtype == false){
									alert("Please Select Field Types");	return false;
								}							
								$('.primary_table_checkbox:checked').each(function(){
									var reporttype_id = this.value;
									var field_type = $(this).data('tbl_field_type');
									if($('#form-request-type').find('.fieldtype_'+reporttype_id).length == 0)
									{
										$('#form-request-type').append('<tr class="requestType_'+reporttype_id+'"><input class="request_type" type="hidden" name="request_type[]" value='+reporttype_id+' /><td class="inner_text">'+field_type+'</td><td><a href="javascript:void(0);" onClick="remove_dialog_single_request(\'form-request-type\',\'requestType_\',\''+reporttype_id+'\');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
									}
									if(request_typeids == ''){
										request_typeids = reporttype_id;									
									}else{
										request_typeids = request_typeids +','+ reporttype_id;									
									}									
								});*/
								$(".total_request_type_ids").val(request_typeids);
								$('#ReportsFieldOperators #is_change_form').val('1'); // change flag to 1 
								$('#is_change_form_main').val('1'); // change flag to 1 
								//jQuery('input').customInput();
								$("#report-fieldtypes").html('');                    
								$(this).dialog('destroy').remove(); 							
							}
					  }
				],
				close: function(){
					$(this).dialog('destroy').remove();
				} 
			});			
		}
	});
}
/* IRT-42 
 * Remove Dialog Data from the list (Chart Format types,)
 * Code Starts
 */
 function remove_dialog_single_request(form_id,moduleclass,id){	
	if(confirm('Are you sure you want to Remove the "'+$('.'+moduleclass+id+' .inner_text').html()+'" record?')){
		var request_typeids = $(".total_request_type_ids").val();		
		var new_data = '';                
                var oldArr = request_typeids.split(",");
                var curIndex = oldArr.indexOf(id);
                if(curIndex > -1){
                    oldArr.splice(curIndex,1);
                }		
                new_data = oldArr.join(',');
		$(".total_request_type_ids").val(new_data);
		var rs = $('#'+form_id).find('.'+moduleclass+id);
		$('#is_change_form').val('1'); $('#is_change_form_main').val('1');
		rs.remove();	
	} 
 }
/*IRT -202  Code starts*/
function ManageDropdownSubmitHook(formName, btn, successFunc, form_mode, id) {
    var projectPriorityOrder = $('#priorityproject-project_priority_order').val();
    if (projectPriorityOrder != '') {
        $.ajax({
            url: baseUrl + '/priority-project/validate-project-priority-order',
            type: 'post',
            data: {form_mode: form_mode, projectPriorityOrder: projectPriorityOrder, id: id},
            success: function (response) {
                if (response == 'OK') {
                    ManageDropdownSubmitAjaxForm(formName, btn, successFunc);
                } else {
                    $('.field-priorityproject-project_priority_order').addClass('has-error');
                    var mode_txt = '';
                    if (form_mode == 'add') {
                        mode_txt = 'Add ';
                    } else {
                        mode_txt = 'Change ';
                    }
                    res_text = 'The ' + mode_txt + ' Project Sort Order of ' + projectPriorityOrder + ' has already been used for another Project Priority.  Please enter a unique value to perform this action.';
                    $('.field-priorityproject-project_priority_order .help-block').html(res_text);
                }
            }
        });
    }
}
$('document').ready(function () {
    $('#priorityproject-project_priority_order').keypress(function (event) {
        var $this = $(this);
        if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
                ((event.which < 48 || event.which > 57) &&
                        (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }

        var text = $(this).val();
        if ((event.which == 46) && (text.indexOf('.') == -1)) {
            setTimeout(function () {
                if ($this.val().substring($this.val().indexOf('.')).length > 3) {
                    $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
                }
            }, 1);
        }

        if ((text.indexOf('.') != -1) &&
                (text.substring(text.indexOf('.')).length > 2) &&
                (event.which != 0 && event.which != 8) &&
                ($(this)[0].selectionStart >= text.length - 2)) {
            event.preventDefault();
        }
    });
});
/*IRT -202  Code Ends*/  
      
/*IRT-434 Code Starts*/
function teamChangeEvent(){
	var selValues = $("#useraccessTeams").val();
	var allFlag = '0';        
	if(selValues.indexOf('All') != '-1'){
		allFlag = 'All';         
	}    
    $.ajax({
		url    : baseUrl +'user/get-team-location-list',
		cache: false,
		type   : 'post',
		data   : $('form#User').serialize(),
		beforeSend : function()    {
			showLoader();			
		},
		success: function (response) {
			hideLoader();
			$('.fromTeams').html(response);
			if(allFlag == 'All'){
			   $("#useraccessTeams").select2("val", ""); 
			} 
			
		},
		error  : function (){
			console.log('internal server error');
		}
	});
}

/*IRT-434 Code Starts*/


/**
 * Check All Inner checkbox for my Teams
 * @ return checked
 */
function chkall_inner_team(loop1){
	var total_chk_box = $('form#User .innerchk_'+loop1).size();
	var count = $('form#User .innerchk_'+loop1+':checked').size();
	if(count >= 1){	
		$("form#User .outerchk_"+loop1).prop('checked',true);	
		$("form#User .outerchk_team_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$("form#User .outerchk_"+loop1).prop('checked',false);	
		$("form#User .outerchk_team_all_"+loop1).removeClass('checked');
	}
}
function chkall_team(loop1){
	var total_chk_box = $('form#User .outer_security').size();
	var cnt = $('form#User .outer_security:checked').size();
	$('form#User .team_all').prop('checked',false); // select All checkbox unchecked 
	$('form#User .team_class_all').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('form#User .team_class_all').addClass('checked');
		$('form#User .team_all').prop('checked',true);
	}
	if ($('form#User #team_chk_'+loop1).is(':checked')){
		 $("form#User .innerchk_"+loop1).prop('checked',true);
		 $("form#User .innerchk_"+loop1).siblings().addClass('checked');
	}else{
		$("form#User .innerchk_"+loop1).prop('checked',false);
		$("form#User .innerchk_"+loop1).siblings().removeClass('checked');
	}
}
/**
 * Check All Inner checkbox For my Cases
 * @ return checked
 */
function chkall_inner_case(loop1){
	var total_chk_box = $('form#User .innerchk_case_'+loop1).size();
	var count = $('form#User .innerchk_case_'+loop1+':checked').size();
	if(count >= 1){	
		$("form#User .outerchk_case_"+loop1).prop('checked',true);	
		$("form#User .outerchk_case_all_"+loop1).addClass('checked');
	}else if(count < 1){
		$("form#User .outerchk_case_"+loop1).prop('checked',false);	
		$("form#User .outerchk_case_all_"+loop1).removeClass('checked');
	}
}
function chkall_case(loop1){
	var total_chk_box = $('form#User .outer_security_case').size();
	var cnt = $('form#User .outer_security_case:checked').size();
	$('form#User .case_all').prop('checked',false); // select All checkbox unchecked 
	$('form#User .case_all_class').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('form#User .case_all').prop('checked',true);
		$('form#User .case_all_class').addClass('checked');
	}
	if ($('form#User #chk_case_'+loop1).is(':checked')){
		 $("form#User .innerchk_case_"+loop1).prop('checked',true);
		 $("form#User .innerchk_case_class_"+loop1).addClass('checked');
	}else{
		$("form#User .innerchk_case_"+loop1).prop('checked',false);
		$("form#User .innerchk_case_class_"+loop1).removeClass('checked');
	}
}
/**
 * Check All Inner checkbox For User Settings
 * @ return checked
 */
function chkall_inner_setting(loop1, loop2)
{
	var total_chk_box = $('form#update-user-right .innerchk_setting_'+loop1).size();
	var count = $('form#update-user-right .innerchk_setting_'+loop1+':checked').size();
	$('form#update-user-right #force_options_'+loop1).css('display','block');
	$("form#update-user-right .outerchk_setting_"+loop1).prop('checked',false);
	if(total_chk_box == count){
		$("form#update-user-right .outerchk_setting_"+loop1).prop('checked',true);
	}
	
	/** chk inner settings **/
	if(!$('form#update-user-right #innerchk_setting_'+loop1+'_'+loop2).is(':checked')){
		$("form#update-user-right .force_inner_label_"+loop2).removeClass('checked');
		$('form#update-user-right #innerchk_setting_'+loop1+'_'+loop2).prop('checked',false);
	}
}
function chkall_setting(loop1){
	var total_chk_box = $('form#update-user-right .outer_security_setting').size();
	var cnt = $('form#update-user-right .outer_security_setting:checked').size();
	$('form#update-user-right .user_security_all').prop('checked',false); // select All checkbox unchecked 
	$('form#update-user-right .user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){ // select All checkbox checked
		$('form#update-user-right .user_security_all').prop('checked',true);
		$('form#update-user-right .user_security_select_all').addClass('checked');
	}
	if ($('form#update-user-right #chk_setting_'+loop1).is(':checked')){
		 $("form#update-user-right .innerchk_setting_"+loop1).prop('checked',true);
	}else{
		$("form#update-user-right .innerchk_setting_"+loop1).prop('checked',false);
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
$(document).ready(function(){
	$(document).on('keyup','#filterFromTeamLocs',function () {    
	 var filter = $(this).val();
	 $(this).next('.clear_text').css('visibility',$(this).val()? "visible" : "hidden");
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
	$(document).on('keyup','#filterFromPostTeamLocs',function () {    
	var filter = $(this).val();
	$(this).next('.clear_text').css('visibility',$(this).val()? "visible" : "hidden");
	$("ul.teamDataToPost li").each(function () {
		if($(this).attr('id')!='header'){
			if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
				$(this).hide();
			} else {
				$(this).show();
			}   
		}
	});	
});
 $(document).on('click','#user_access_second #remove_selected_teams',function () {   
	var activeteamlocs = $('.teamDataToPost').find('li.active');
	activeteamlocs.each(function(){
            var team_id = $(this).data('team_id');
            var team_loc = $(this).data('team_loc');
            $('ul.fromTeams').append('<li class="clear teams_li_from custom-full-width" data-team_id="'+team_id+'" data-team_loc="'+team_loc+'"><a href="javascript:void(0)">'+$(this).children('a').html()+'</a></li>');            

	});
	$('ul.teamDataToPost li.active').remove();
});
$(document).on('click','#user_access_second #get_selected_teams',function () {      
	var activeteamlocs = $('.fromTeams').find('li.active');
	activeteamlocs.each(function(){                     
            var team_id  = $(this).data('team_id');
            var team_loc = $(this).data('team_loc');            
            var input_cal = '<input name="teamLocations[]" type="hidden" value="'+team_id+','+team_loc+'">';
            if(!$('ul.teamDataToPost').children('li').hasClass('T_'+team_id+'_L_'+team_loc)){
                 $('ul.teamDataToPost').append('<li class="clear teams_li_to custom-full-width T_'+team_id+'_L_'+team_loc+'" data-team_id="'+team_id+'" data-team_loc="'+team_loc+'">'+$(this).html()+input_cal+'</li>');
            }           
	});        
	$('ul.fromTeams li.active').remove();
});
	});
	$(document).ready(function(){
		/*IRT-434*/
		$(document).on('click', '.bulk-team-locs ul.fromTeams li', function() {        		
			if($(this).attr('id') != 'header')
				$(this).toggleClass('active');
		});
		$(document).on('click', '.bulk-team-locs ul.teamDataToPost li', function() {
		   if($(this).attr('id') != 'header')
			$(this).toggleClass('active');
		});
		$(document).on('click', '.bulk-client-cases ul.fromClientCases li', function() {        		
			if($(this).attr('id') != 'header')
				$(this).toggleClass('active');
		});
		$(document).on('click', '.bulk-client-cases ul.clientCaseDataToPost li', function() {
		   if($(this).attr('id') != 'header')
			$(this).toggleClass('active');
		});	
		
		$(document).on('input','.filter-user-inp', function() {
				$(this)[togUserInpStatus(this.value)]('x');
		}).on('mousemove', '.x', function(e) {                                                                            
				$(this)[togUserInpStatus(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');   
		}).on('click', '.onX', function(){
				$(this).removeClass('x onX').val('').change();
				getFilteredUsers('');
		});
		//$( ".select-items-dropdown  .filter-manage-user-inp" ).on( "keyup", function() {
		
		$(document).on('keyup','.select-items-dropdown .filter-manage-user-inp', function() {
			if($(this).hasClass('manage-user-inp'))
				getFilteredUsers($(this).val());
			else			
				getRoleUser();
			$(this).next('.clear_text').css('visibility',$(this).val()? "visible" : "hidden");			
		});
		$(document).on('keyup','#filterClientCases,#bulkFilterClientCases',function () {    
		var filter = $(this).val();
		$(this).next('.clear_text').css('visibility',$(this).val()? "visible" : "hidden");
		$("ul.fromClientCases li").each(function () {            
				if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
					$(this).hide();
				} else {
					$(this).show();
				}               
		});
			});
		$(document).on('keyup','#filterclientCaseDataToPost',function () {    
		var filter = $(this).val();
		$(this).next('.clear_text').css('visibility',$(this).val()? "visible" : "hidden");
		$("ul.clientCaseDataToPost li").each(function () {		
				if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
					$(this).hide();
				} else {
					$(this).show();
				}   		
		});
		});
		$(document).on('click','.clear_text',function(){
			$(this).css('visibility',$(this).val()? "visible" : "hidden");  						
			$('#'+$(this).data('idname')).val('').keyup();
		});
		$(document).on('click','#user_access_first #move_selected_client_cases',function () {        
			var activeteamlocs = $('.fromClientCases').find('li.active');
			activeteamlocs.each(function(){                     
	           var client_case_id  = $(this).data('client_case_id');
	            var client_id = $(this).data('client_id');            
	            var input_cal = '<input name="clientCasesWithCleint[]" type="hidden" value="'+client_id+','+client_case_id+'">';
	            
	            if(!$('ul.clientCaseDataToPost').children('li').hasClass('C_'+client_id+'_CC_'+client_case_id)){
	                 $('ul.clientCaseDataToPost').append('<li class="clear client_case_li_to custom-full-width C_'+client_id+'_CC_'+client_case_id+'" data-client_id="'+client_id+'" data-client_case_id="'+client_case_id+'">'+$(this).html()+input_cal+'</li>');
	            }           
			});        
	$('ul.fromClientCases li.active').remove();
    });
   $(document).on('click','#user_access_first #remove_selected_client_cases',function () {        
	var activeteamlocs = $('.clientCaseDataToPost').find('li.active');
	activeteamlocs.each(function(){
            var client_case_id = $(this).data('client_case_id');
            var client_id = $(this).data('client_id');
            $('ul.fromClientCases').append('<li class="clear client_case_li_from custom-full-width" data-client_id="'+client_id+'" data-client_case_id="'+client_case_id+'"><a href="javascript:void(0)">'+$(this).children('a').html()+'</a></li>');
	});
	$('ul.clientCaseDataToPost li.active').remove();
    });
	});	
function togUserInpStatus(v){
		return v?'addClass':'removeClass';
	} 	
/**
 * Get Role wise user details in Usermanagement Manage user
 */
function getFilteredUsers(filtertext){
	jQuery.ajax({
       url: baseUrl +'/user/ajax-filter-user&filteruser='+filtertext,
       type: 'get',
       beforeSend:function (data) { /*showLoader();*/},
       success: function (data) {
    	   //hideLoader();
    	   jQuery('#sub-links-user').html(data);
       }
	});
}
function clientChangeEvent(){
        var selValues = $("#useraccessclients").val();
        var allFlag = '0';
        if(selValues.indexOf('All') != '-1') {
            allFlag = 'All';         
        }        
        $.ajax({
            url         : baseUrl +'user/get-cleint-case-list',
            cache       : false,
            type        : 'post',
            data        : $('form#User').serialize(),
            beforeSend  : function()    {
            	showLoader();			
            },
            success     : function (response) {
                hideLoader();
                $('#user_access_first .fromClientCases').html(response);               
                if(allFlag == 'All'){
                   $("#useraccessclients").select2("val", ""); 
                }                
            },
            error       : function (){
            	console.log('internal server error');
            }
        });
}

/*IRT-434 Code Ends*/
