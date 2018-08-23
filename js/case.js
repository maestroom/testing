$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});

jQuery(document).ready(function(){
    /* Start : get website base url */
    var host = window.location.href; //.hostname
    var httPpath = "";
    if (host.indexOf('index.php')) {
        httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
    }
    /* End : get website base url */
	/* Start : Case Management */
	jQuery(".caseModules").click(function(event) {
            var chk_status = checkformstatus(event); // check form edit status 
            if(chk_status==true) {
                setTitle('fa-briefcase','<a href="javascript:void(0);" title="Case Management" class="tag-header-red">Case Management</a>');
                var module=jQuery(this).data('module');
                jQuery('.caseModules').removeClass('active');
                if(module == 'CaseManagement'){
                    commonAjax(baseUrl +'/case/index','admin_main_container');
                    jQuery(this).addClass('active');
                }
            }
	}); 
	jQuery(".myCaseModules").on('click',function(event){
			var case_id= jQuery("#case_id").val();
			var module=jQuery(this).data('module'); 
			jQuery('.myCaseModules').removeClass('active');
			if(module == 'list_custodian'){
				showLoader();
				setTitle('fa-file-o','Case Custodians');
				location.href = baseUrl +'/case-custodians/index&case_id='+case_id; //,'admin_main_container');
				jQuery(this).addClass('active');
			}
			if(module == 'interview_form'){
				var keys = $('.grid-view').yiiGridView('getSelectedRows');
				if(keys.length > 1 || !keys.length){
					alert('Please select a single record to perform this action.');
				}else{
					showLoader();
					ShowInterviewForm(keys);
					jQuery(this).addClass('active');
					jQuery('a[data-module="pdf_interview_form"]').closest('li').hide();
				}
			}
			if(module == 'pdf_interview_form'){
				var keys = $('.grid-view').yiiGridView('getSelectedRows');
				
				if(keys.length==0 || $('.grid-view').length == 0){
					alert('Please select at least 1 record to perform this action.');
				}else{
					showLoader();
					PDFInterviewForm(keys,case_id);
					jQuery(this).addClass('active');
				}
			}
			if(module == 'case_summary'){
				setTitle('fa-pencil','Case Summary');
				location.href = baseUrl +'case/case-summary&case_id='+case_id;
				
			}
			if(module == 'case_summary_comment'){
				location.href = baseUrl +'summary-comment/index&case_id='+case_id;
			}
			if(module == 'track_project'){
				var keys = $('.grid-view').yiiGridView('getSelectedRows');
				if(keys.length > 1 || !keys.length){
					alert('Please select a single record to perform this action.');
				}else{
					showLoader();
					location.href = baseUrl +'track/index&taskid='+keys+'&case_id='+case_id;
				}
			}
			if(module == 'post_project_comment'){
				var keys = $('#caseprojects-grid').yiiGridView('getSelectedRows');
				if(keys.length > 1 || !keys.length){
					alert('Please select a single record to perform this action.');
				} else {
					showLoader();
					location.href = baseUrl +'case-projects/post-comment&task_id='+keys+'&case_id='+case_id;
				}
			}
			if(module == 'total_projects'){
				showLoader();
				location.href = baseUrl +'case-overview/total-projects&case_id='+case_id;
			}
			if(module == 'total_media'){
				showLoader();
				location.href = baseUrl +'case-overview/total-media-projects&case_id='+case_id;
			}
			if(module == 'mediatype_by_size'){
				showLoader();
				location.href = baseUrl +'case-overview/total-media-unit-size&case_id='+case_id;
			}
			if(module == 'media_by_custodian'){
				showLoader();
				location.href = baseUrl +'case-overview/media-by-custodian&case_id='+case_id;
			}
			if(module == 'total_productions'){
				showLoader();
				location.href = baseUrl +'case-overview/production-by-type&case_id='+case_id;
			}
			if(module == 'producing_parties'){
				showLoader();
				location.href = baseUrl +'case-overview/production-producing-parties&case_id='+case_id;
			}
			if(module == 'list_projects'){
				showLoader();
				loadProjects();
			}
			if(module == 'uncancel_projects'){
				uncancelProjects();
			}
			if(module == 'delete_projects'){
				deletesavedprojects();
			}
	//		if(module == 'change_project'){
	//			changeprojects();
	//		}
			if(module == 'reopen_projects'){
				var bulkreopendialog = $('#bulkreopen-closed-dialog');
				if(bulkreopendialog.hasClass('hide')){
					bulkreopendialog.removeClass('hide');
				}
				reopenProjects(bulkreopendialog);
			}
			if(module == 'close_projects'){
				close_projects();
			}
			if(module == 'cancel_project'){
				cancel_project();
			}
			if(module == 'remove_project'){
				var keys = $('#caseprojects-grid').yiiGridView('getSelectedRows');
				if(!keys.length) {
					alert('Please select at least 1 record to perform this action.');
				} else if(keys.length > 1) {
					alert('Please select a single record to perform this action.');
				} else {
					remove_project(keys,case_id);	
				}
			}
			if(module == 'casebudget_chart'){
				showLoader();
				location.href = baseUrl +'case-budget/index&case_id='+case_id;
			}
			if(module == 'casebudget_pdf'){
				//showLoader();
				url = baseUrl +'pdf/case-budget&case_id='+case_id;
				var form = document.createElement("form");
				document.body.appendChild(form);
				form.method = "POST";
				form.action = url;
				imageval=$("#pdfimage").val();
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", 'pdfimage');
				hiddenField.setAttribute("value", imageval);
				form.appendChild(hiddenField);
				var hiddenField1 = document.createElement("input");
				hiddenField1.setAttribute("type", "hidden");
				hiddenField1.setAttribute("name", '_csrf');
				hiddenField1.setAttribute("value", yii.getCsrfToken());
				form.appendChild(hiddenField1);
				
				
				form.submit();
				/*$.ajax({
					url:url,
					type:'post',
					data:$("#ClientCase").serialize(),
				});*/
			}
	});
	/* End : Case Management */
});

/* Start : Case Custodiant Interview Form*/
function ShowInterviewForm(cust_id){
	var case_id= jQuery("#case_id").val();
	jQuery.ajax({
	   url: baseUrl +'/case-custodians/interview-form&id='+cust_id,
	   data:{case_id:case_id},
	   type: 'get',
	   beforeSend:function (data) {showLoader();},
	   success: function (data) {
		   hideLoader();
		   jQuery('#media_container').html(data);
	   }
	});
}
/* Start : Delete Case Custodiant Interview Form*/
function DeleteInterViewForm(id){
	var id_value = $('#'+id+' :selected').text();
	if(confirm("Are you sure you want to Delete "+id_value+"?")){
		jQuery.ajax({
		       url: baseUrl +'/case-custodians/deleteinterview-form&id='+id,
		       type: 'get',
		       beforeSend:function (data) {showLoader();},
		       success: function (data) {
		    	   location.reload();
		    	   //hideLoader();
		    	   //$("#template_loading_"+id).html(null);
		    	   //$(".cust_SelectDropDown").removeAttr('disabled');
		    	   //$(".cust_SelectDropDown").val('');
		       }
		});
	}
}
/* Start : PDF Case Custodiant Interview Form*/
function PDFInterviewForm(cust_id,case_id){
	hideLoader();
	location.href=baseUrl +'/pdf/custodiant-ineterview&id='+cust_id+'&case_id='+case_id;
}
/* Start : Case Management */
/* Start : Used to load Client list */
function loadClients(){
		commonAjax(baseUrl +'/case/index','admin_main_container');
}
/* End : Used to load Cases list */

/* Start : Used to load cases list from select box */
function loadCasesByClientSelect(client_id){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) {
		loadCasesByClient(client_id);
	}
}


/* Start : Used to load Cases list */
function loadCasesByClient(client_id){
	commonAjax(baseUrl +'/case/load-cases-by-client&client_id='+client_id,'clientbasecaseslist');
	jQuery('#client_id').val(client_id);
}
/* End : Used to load Cases list */

/* Start : Used to load Add new Case Form */
function addCaseNew(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) 
		addCase();
}
/* End : Used to load Cases list */

/* Start : Used to load Add new Case Form */
function addCase(){
	var client_id = jQuery('#client_id').val();
	if(client_id != '' && client_id != 0){
		jQuery.ajax({
	       url: baseUrl +'/case/create&client_id='+client_id,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);
	       }
		});
	} else {
		alert("Please select client.");
	}
}
/* End : Used to load Add new Case Form */

/* Start : Used to load Edit Case Form */
function updateCaseSelect(case_id){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) {
		updateCase(case_id);
	}
}
/* End : Used to load Edit Case Form */

/* Start : Used to load Edit Case Form */
function updateCase(case_id){
	
		var client_id = jQuery('#client_id').val();
		jQuery.ajax({
			url: baseUrl +'/case/update&id='+case_id,
			type: 'get',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				jQuery('#admin_right').html(data);
				jQuery('#case_id').val(case_id);
			}
		});
	
}
/* End : Used to load Edit Case Form */

/* Start : Used to update form */
function updateCaseForm(case_id,form_id,btn,successFunc){
	var client_id = jQuery('#client_id').val();
	var case_name = jQuery('#clientcase-case_name').val();
	if($("#clientcase-is_close").is(':checked')){
	jQuery.ajax({
		url: baseUrl +'/case/chk-can-close-case&id='+case_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft'){
				hideLoader();
    			alert("The selected Case cannot be Closed because it has outstanding Billing Items.  Please Finalize Invoices for the selected Case before Closing the Case.");
    			return false;
    		} else if(data.replace(/^\s+|\s+$/g, "") == 'accumalateditemsleft'){
    			if(confirm("Are you sure you want to Close "+case_name+" as it has Accumulated Billing Items that are captured in Invoicing?")){
    				var form = $('form#'+form_id);
        			jQuery.ajax({
        		        url    : form.attr('action'),
        		        cache: false,
        		        type   : 'post',
        		        data   : form.serialize(),
        		        beforeSend : function() {
        		        	$(btn).attr('disabled','disabled');
        		        },
        		        success: function (response){
        		        	if(response == 'OK'){
        						eval(successFunc);
        					}else{
        						hideLoader();
        						$('#edit-case').html(response);
        		        		$(btn).removeAttr("disabled");
        		        	}
        		        },
        		        error  : function (){
        		            console.log('internal server error');
        		        }
        		    });
    			} else {
    				hideLoader();
    			}
    		} else {
    			var form = $('form#'+form_id);
    			jQuery.ajax({
    		        url    : form.attr('action'),
    		        cache: false,
    		        type   : 'post',
    		        data   : form.serialize(),
    		        beforeSend : function() {
    		        	$(btn).attr('disabled','disabled');
    		        },
    		        success: function (response){
    		        	if(response == 'OK'){
    						eval(successFunc);
    					}else{
    						hideLoader();
    						$('#edit-case').html(response);
    		        		$(btn).removeAttr("disabled");
    		        	}
    		        },
    		        error  : function (){
    		            console.log('internal server error');
    		        }
    		    });
    		}
		}
	});
	}else{
			var form = $('form#'+form_id);
    			jQuery.ajax({
    		        url    : form.attr('action'),
    		        cache: false,
    		        type   : 'post',
    		        data   : form.serialize(),
    		        beforeSend : function() {
    		        	$(btn).attr('disabled','disabled');
    		        },
    		        success: function (response){
    		        	if(response == 'OK'){
    						eval(successFunc);
    					}else{
    						hideLoader();
    						$('#edit-case').html(response);
    		        		$(btn).removeAttr("disabled");
    		        	}
    		        },
    		        error  : function (){
    		            console.log('internal server error');
    		        }
    		    });
	}
}
/* End : Used to update form */

/* Start : Used to delete Case */
function removeCase(case_id){
	var case_name = $('#clientcase-case_name').val();
	var client_id = jQuery('#client_id').val();
	jQuery.ajax({
		url: baseUrl +'/case/case-has-projects&id='+case_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			if(data == 'OK' && confirm('Are you sure you want to Delete '+case_name+'?')){
				jQuery.ajax({
					url: baseUrl +'/case/delete&id='+case_id,
					type: 'get',
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						hideLoader();
						if(data == 'media'){
							alert(case_name+" cannot be Deleted as it is associated to 1+ Media.");
						}
						if(data == 'production'){
							alert(case_name+" cannot be Deleted as it is associated to 1+ Production.");
						}
						if(data == 'OK'){
							loadCasesByClient(client_id);
						}
					}
				});
			} else {
				hideLoader();
				alert(case_name+" cannot be Deleted as it is associated to 1+ Project.");
				return false;
			}
		}
	});
}
/* End : Used to load delete Case */

/* Start : Used to load Case's contact List */
function loadCaseContactListCancel(){
	var chk_form_status = checkformstatus("event"); // check form edit status 
	if(chk_form_status == true) 
		loadCaseContactList();
}
/* End : Used to load delete Case */

/* Start : Used to load Case's contact List */
function loadCaseContactList(){
	
	var client_id = jQuery('#client_id').val();
	var client_case_id = jQuery('#case_id').val();
	jQuery.ajax({
		url: baseUrl +'/case/contact-list&client_id='+client_id+'&client_case_id='+client_case_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#add-case-contacts').html(data);
			//jQuery.pjax.reload({container:'#casecontactsgrid-pajax', replace: false, url: baseUrl +'/case/contact-list&client_id='+client_id+'&client_case_id='+client_case_id});
		}
	});
	
}
/* End : Used to load Case's contact List */

/* start : change project display grid */
function changeprojects(task_id,case_id){
	showLoader();
	var case_id = jQuery('#case_id').val();
	location.href = baseUrl +'case-projects/change-project&case_id='+case_id+'&task_id='+task_id;
}
/* End :  change project display grid */


/* Start : Used to load Add new Case contact Form */
function addCaseContacts(client_id){
	
	var client_id = jQuery('#client_id').val();
	var client_case_id = jQuery('#case_id').val();
	
	jQuery.ajax({
	       url: baseUrl +'/case/add-case-contacts&client_id='+client_id+'&client_case_id='+client_case_id,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#add-case-contacts').html(data);
	       }
	});
	
}
/* End : Used to load Add new Case contact Form */

/* Start : Used to load Edit Case contact Form */
function updateCaseContact(contact_id){
	jQuery.ajax({
		url: baseUrl +'/case/update-case-contacts&id='+contact_id+'&case_id='+jQuery('#case_id').val(),
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#add-case-contacts').html(data);
		} 
	});
}
/* End : Used to load Edit Case contact Form */

/* Start : Used to delete Case contacts */
function deleteCaseContact(case_contact_id,case_contact_name){
	
	var client_id = jQuery('#client_id').val();
	
	if (case_contact_id == '' || case_contact_id == 0 || case_contact_id == 'undefined') {
		alert('Something is wrong, please try again.');
		return false;
	} else {
		if(confirm('Are you sure you want to Remove '+case_contact_name+'?')){
			jQuery.ajax({
				url: baseUrl +'/case/delete-case-contact',
				data:{contact_id: case_contact_id},
				type: 'post',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					if(data == 'OK'){
						loadCaseContactList();
					} else {
						alert('Something is wrong, please try again.');
						return false;
					}
				}
			});
		}
	}
}
/* End : Used to delete Case contacts */

/* Start : Used to delete selected Case contacts */
function deleteSelectedCaseContact(){
	var case_id = jQuery('#case_id').val();
	var client_id = jQuery('#client_id').val();
	var contact_id = $('#casecontactsgrid').yiiGridView('getSelectedRows');
	var newkeys = contact_id.toString().split(",");
	var str = [];var str_val;
	for(var i=0;i<newkeys.length;i++){
		var val = JSON.parse(decodeURIComponent($( '.chk_contact_type_'+newkeys[i] ).val()));
		str_val =  "" + val['contact_type'];
		str.push(str_val);
	}
	var str_length = str.length;
	if (case_id == '' || case_id == 0 || case_id == 'undefined') {
		alert('Case is not identified, please try again.');
		return false;
	} else if(contact_id == '' || contact_id == 'undefined' || contact_id.length == 0){
		alert('Please select a record to perform this action.');
		return false;		
	} else {
		if(confirm('Are you sure you want to Remove the selected '+str_length+' record(s): '+str+'?')){
			jQuery.ajax({
				url: baseUrl +'/case/delete-selected-case-contacts',
				data:{contact_id:contact_id},
				type: 'post',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					if(data == 'OK'){
						loadCaseContactList();
					} else {
						alert('Something is wrong, please try again.');
						return false;
					}
				}
			});
		}
	}
}
/* End : Used to delete selected case contacts */

/* Start : Used to load Case's Summary */
function loadClientCaseSummary(){	
	
	var client_id = jQuery('#client_id').val();
	
	jQuery.ajax({
		url: baseUrl +'/case/clientcase-summary&client_case_id='+jQuery('#case_id').val()+'&client_id='+client_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#case-summary').html(data);
		}
	});
	
}
/* End : Used to load Case's Summary  */

/* Start : To Load Case's Excluded Service list */
function loadExcludedServiceList(){
	var case_id = jQuery('#case_id').val();
	
	jQuery.ajax({
		url: baseUrl +'/case/excluded-service-list',
		data:{client_case_id: case_id},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#exclude-services').html(data);
		}
  	});
}
/* End : To Load Case's Excluded Service list */

/* Start : To Load Case's assigned user's list */
function loadAssignedCaseUserList(){
	var case_id = jQuery('#case_id').val();
	var client_id = jQuery('#client_id').val();
	
	jQuery.ajax({
		url: baseUrl +'/case/assigned-user-list',
		data:{client_case_id: case_id, client_id: client_id},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('#assigned-case-user').html(data);
		}
  	});
}
/* End : To Load Case's assigned user's list */

function showHideBlock(showid,hideid){
	jQuery('#'+showid).show();
	jQuery('#'+hideid).hide();
}

/* End : Case Management */

/* Start : Used to load Cases list option by client */
function loadCaselistByClient(client_id,update_id){
	commonAjax(baseUrl +'/case/load-caselist-by-client&client_id='+client_id,update_id);
	jQuery('#client_id').val(client_id);
}
/* End : Used to load Cases list  option by client */
/* Start : Used to load Add new custodian Form */
function openaddcustodiant()
{
		var case_id = jQuery('#case_id').val();
		if(!$('#addevidcust').length){
			$('body').append("<div id='addevidcust'></div>");
		}
     	var $custodianDialogContainer = $('#addevidcust');
     	 $custodianDialogContainer.dialog({
         	title:"Add New Custodian",
             autoOpen: false,
             resizable: false,
             height:456,
             width:"50em",
             modal: true,
            
             buttons: {
            	 'Cancel': {
                         text: 'Cancel',
                         "title":"Cancel",
                         "class": 'btn btn-primary',
                         'aria-label': "Cancel Add Custodian",
                         click:  function (event) {
							 trigger = 'Cancel';
							 $custodianDialogContainer.dialog('close');
					     }
                },
                 "Add":  {
                             text: 'Add',
                             "title":"Add",
                             "class": 'btn btn-primary',
                             'aria-label': "Add New Custodian",
                             click: function () {
								 trigger = 'Add';
                            	 var form = $("#addevidcust #EvidenceCustodians");
                            	 jQuery.ajax({
                             		url : form.attr('action'),
                             		data:form.serialize(),
                             		type: 'post',
                             		beforeSend:function (data) {showLoader();},
                             		success: function (data) {
                             			hideLoader();
                             			if(data=='OK'){
                             				$custodianDialogContainer.dialog('destroy').remove();
                             				$.pjax.reload('#dynagrid-casecustodians-pjax', $.pjax.defaults);
                             			}else{
                             				$("#addevidcust").html(data);
                             			}
                             		}
                               	});
                             }
                 }
             },
             beforeClose: function(event) {
				if(event.keyCode == 27) trigger = 'esc';
				if(trigger != 'Add') checkformstatus(event); 
			 }, 
			 close: function(event) {
				$custodianDialogContainer.dialog('destroy').remove();
			 }
         });
         
         /**
          * Case custodian form create 
          */
     	 jQuery.ajax({
    		url: baseUrl +'/case-custodians/create',
    		data:{client_case_id: case_id},
    		type: 'get',
    		beforeSend:function (data) {showLoader();},
    		success: function (data) {
    			hideLoader();
    			$custodianDialogContainer.html(data);
    			$custodianDialogContainer.dialog("open");
    		}
      	 });
       
     
 }
/* Start : Used to load Edit  custodian Form */
function UpdateCust(cust_id){
	var case_id = jQuery('#case_id').val();
	if(!$('#addevidcust').length){
		$('body').append("<div id='addevidcust'></div>");
	}
 	var $custodianDialogContainer = $('#addevidcust');
 	 $custodianDialogContainer.dialog({
     	title:"Edit Custodian",
         autoOpen: false,
         resizable: false,
         height:456,
         width:"50em",
         modal: true,
         beforeClose: function(event){
			 var trigger = '';
			if(event.keyCode==27) trigger = 'esc';
			if(trigger!='Update') checkformstatus(event);
		 },
         buttons: {
             'Cancel': {
                     text: 'Cancel',
                     "title":"Cancel",
                     "class": 'btn btn-primary',
                     'aria-label': "Cancel Edit Custodian",
                     click:  function (event) {
						 trigger = 'Cancel';
						$custodianDialogContainer.dialog('close');
                     }
             },
             "Update":  {
                         text: 'Update',
                         "title":"Update",
                         "class": 'btn btn-primary',
                         'aria-label': "Edit Custodian",
                         click: function () {
							 trigger = 'Update';
                        	 var form = $("#addevidcust #EvidenceCustodians");
								jQuery.ajax({
									url : form.attr('action'),
									data:form.serialize(),
									type: 'post',
									beforeSend:function (data) {showLoader();},
									success: function (data) {
										hideLoader();
										if(data=='OK'){
											$custodianDialogContainer.dialog('destroy').remove();
											$.pjax.reload('#dynagrid-casecustodians-pjax', $.pjax.defaults);
										}else{
											$("#addevidcust").html(data);
										}
								}
                           	});
                         }
             }
         },
         close: function(event) {
			$custodianDialogContainer.dialog('destroy').remove();
		 }
     });
 	jQuery.ajax({
		url: baseUrl +'/case-custodians/update&id='+cust_id,
		data:{client_case_id: case_id},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			$custodianDialogContainer.html(data);
			$custodianDialogContainer.dialog("open");
		}
  	});
}
function DeleteCust(cust_id,cust_name){
	
	//return false;
	var case_id = jQuery('#case_id').val();
	jQuery.ajax({
		url: baseUrl +'/case-custodians/checkassociated&id='+cust_id,
		data:{client_case_id: case_id},
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			if(data==1){
				hideLoader();
				alert(""+cust_name+" cannot be Deleted as it is associated with a Media, Project or Custodian Form.");
		        return false;
			}else{
				$.ajax({
	                type: "post",
	                url: baseUrl +'/case-custodians/delete&id='+cust_id,
	                data: {client_case_id: case_id},
	                cache: false,
	                success: function (data) {
	                	hideLoader();
	                	$.pjax.reload('#dynagrid-casecustodians-pjax', $.pjax.defaults);
	                }
	            });
			}
		}
  	});	
}

/* Start : on Click of Load Projects Link from Left Panel of My Case module */
function loadProjects(){
	var case_id = jQuery('#case_id').val();
	location.href = baseUrl +'case-projects/index&case_id='+case_id;
}
/* End : on Click of Load Projects Link from Left Panel of My Case module */

/* Start : on Click of Load Canceled Project Button in Case Projects Main Grid */
function loadCanceledProjects(){
	showLoader();
	var case_id = jQuery('#case_id').val();
	location.href = baseUrl +'case-projects/load-canceled-projects&case_id='+case_id;
}
/* End : on Click of Load Canceled Project Button in Case Projects Main Grid */

/* Start : on Click of Bulk Assign project buttone in case project Main Grid */
function bulkassignproject()
{
	showLoader();
	var case_id = jQuery('#case_id').val();
//	var checkboxVal = $('#caseprojects-grid').yiiGridView('getSelectedRows');
	location.href = baseUrl +'case-projects/bulk-assign-project&case_id='+case_id;
//	var checkboxVal = $('#caseprojects-grid').yiiGridView('getSelectedRows');
//	var case_id = jQuery('#case_id').val();
//	jQuery.ajax({
//		url: baseUrl +'case-projects/bulk-assign-project&case_id='+case_id+"&checkboxVal="+checkboxVal,
//		type: 'post',
//		beforeSend:function (data) {showLoader();},
//		success: function (data) {
//			$('#media_container').html(data);
//		},
//		complete: function (data) {hideLoader();}
//  	});	
}
/* End : on Click of Load Canceled Project Button in Case Project Main Grid */

/* start : get all project details */
function godisplayresult(){
	  var url = baseUrl+"case-projects/get-assign-data";
	  var caseId          = $("#caseId").val();
	  var checkboxVal     = $("#caseProjectVal").val();
	  var token           = $('input[name="YII_CSRF_TOKEN"]').val();
	  var dropdownVal     = $("#displayResult").val();

	  if(dropdownVal == 0) {
	      var error="";
	      error   +="Please select display result by to perform bulk assign operation.";
	      $('#errorContent').html(error);
	  } else {
	      $.ajax({
	          type: "POST",
	          url: url,
	          data: {'YII_CSRF_TOKEN':token,'checkboxVal':checkboxVal,'caseId':caseId,'dropdownVal':dropdownVal},
	          dataType: 'html',
	          cache: false,
	          success: function (data) {
	            $('#bulktableDiv').html(data);
	            $('#updateBulkUserByCaseBtn').show();
	          }
	      });
	  }
}
/* End */


function bulkupdateassignuser()
{
	var found = 0;
	$('form#add-bulkassignproject-form .taskdropdown').each(function(){
		if($(this).val() != 0)
		{
			found = 1;
		}
	});
	if(found != 1){
		alert('Please select a User to perform this action.');
		return false;
	}
	$('#loding').show();
    var url = httpPath+"case-projects/assign-bulk-user";
    var data = $('#add-bulkassignproject-form').serialize();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'html',
        cache: false,
        success: function (data) {
           if (data != "") {
            	//alert(data);return false;
            	location.reload();
           }
        }
    });
}

/**
 * start : Delete Single Saved Projects from Grid
 */
function removesavedprojects(task_id,case_id)
{
	if(confirm("Are you sure you want to Delete #"+task_id+"?")){
		jQuery.ajax({
			url:baseUrl +'case-projects/deletesavedprojects&case_id='+case_id,
			data:{task_list:task_id},
			type: 'post',
			beforeSend:function(data){showLoader();},
			success:function(data){
				$.pjax.reload('#saved-projects-grid-container', $.pjax.defaults);
			},
			complete: function(data){hideLoader();}
		});
	}
}
/**
 * End : Delete Single saved projects from grid
 */

/**
 * start : Edit Single Saved Projects from Grid
 */
function edit_saved_project(instruction_id,case_id)
{
	location.href = baseUrl +'project/saved&instruction_id='+instruction_id+'&case_id='+case_id;
}
/**
 * End : Edit Single saved projects from grid
 */

/**
 *  start : Delete Multiple Saved Projects from mycase "Case Project"
 */
function deletesavedprojects(){
	var case_id = jQuery('#case_id').val();
	if($('#saved-projects-grid').length > 0){
		var keys = $('#saved-projects-grid').yiiGridView('getSelectedRows');
		if(!keys.length){
			alert("Please select at least 1 record to perform this action.");
			return false;
		} else {
			if(confirm("You are about to Saved Project. Are you sure?")){
				jQuery.ajax({
					url:baseUrl +'case-projects/deletesavedprojects&case_id='+case_id,
					data:{task_list:keys},
					type: 'post',
					beforeSend:function(data){showLoader();},
					success:function(data){
						$.pjax.reload('#saved-projects-pajax', $.pjax.defaults);
					},
					complete: function(data){hideLoader();}
				});
			}
		}
	}
}

/* Start : on Click of uncancel Project Link from left panel in Canceled Projects Grid */
function uncancelProjects(){
	var case_id = jQuery('#case_id').val();
	if($('#canceled-projects-grid').length > 0){
		var keys = $('#canceled-projects-grid').yiiGridView('getSelectedRows');
		var sel_row = "";
		var count = keys.length;
		for (var k in keys){
			if(sel_row=="") {
				sel_row='#'+keys[k];
			} else {
				sel_row+=",#"+keys[k];
			}
		}
		if(!keys.length){
			alert('Please select at least 1 record to perform this action.');
			return false;
		} else {
			if(confirm("Are you sure you want to UnCancel the selected "+count+" record(s): "+sel_row+"?")){
				jQuery.ajax({
					url: baseUrl +'case-projects/uncancel-projects&case_id='+case_id,
					data:{task_list:keys},
					type: 'post',
					beforeSend:function (data) {showLoader();},
					success: function (data) {                                                
						$.pjax.reload('#dynagrid-canceled-projects-pjax', $.pjax.defaults);
					},
					complete: function (data) {hideLoader();}
			  	});	
			}
		}
	}
}
/* End : on Click of uncancel Project Link from left panel in Canceled Projects Grid */

/* Start : on Click of Load Canceled Project Button in Case Projects Main Grid */
function loadClosedProjects(){
	showLoader();
	var case_id = jQuery('#case_id').val();
	location.href = baseUrl +'case-projects/load-closed-projects&case_id='+case_id;
}
/* End : on Click of Load Canceled Project Button in Case Projects Main Grid */

/* start : on Click of Load Saved Project Button in Case Projects Main Grid */
function loadSavedProjects(){
	showLoader();
	var case_id = jQuery('#case_id').val();
	location.href = baseUrl +'case-projects/load-saved-projects&case_id='+case_id;
}
/* End : on Click of Load Canceled Project Button in Case Project Main Grid */

/* Start : on Click of project # Link in Case Projects Grid */
function viewMedia(task_id){
	if(!$( "#media_preview" ).length){
		$('body').append("<div id='media_preview'></div>");
	}
	$.ajax({
        url: httpPath + "case-projects/view-task-media&task_id="+task_id,
        // async: false,
        cache: false,
        dataType: 'html',
        success: function (data) {
            if (data != "") {
                $('#media_preview').html(data);
		
		$( "#media_preview" ).dialog({
		      autoOpen: false,
		      resizable: false,
		      width: "80em",
		      height:392,
		      modal: true,
		      
			  show: {
			effect: "fade",
			duration: 500
		      },
		      hide: {
			effect: "fade",
			duration: 500
		      },
			create: function(event, ui) { 
			     $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
			     $('.ui-dialog-titlebar-close').attr("title", "Close");
			     $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			},
			  buttons: [
			{
			    text: "Cancel",
			    "title":"Cancel",
			    "class": 'btn btn-primary',
			    click: function() {
					$(this).dialog("close");
			    }
			}
		    ],
		    close: function() {
			$(this).dialog('destroy').remove();
			}
		}).parent().find('.ui-dialog-title').html("Project Media");
		$( "#media_preview" ).dialog( "open" );
		
		
            }
        }
    });
}
function viewInstruction(task_id){
    if(!$( "#instruction_preview" ).length){
		$('body').append("<div id='instruction_preview'></div>");
	}
	var Url=httpPath + "case-projects/view-task-instructions&task_id="+task_id;
	var duedate="";
	if($('#dd_'+task_id).length){
		duedate=$('#dd_'+task_id).html();
		Url=httpPath + "case-projects/view-task-instructions&task_id="+task_id+"&duedate="+duedate;
	}
    $.ajax({
        url: Url,
        // async: false,
        cache: false,
        dataType: 'html',
        success: function (data) {
            if (data != "") {
                $('#instruction_preview').html(data);
		
		$( "#instruction_preview" ).dialog({
		      autoOpen: false,
		      resizable: false,
		      width: "80em",
		      height:692,
		      modal: true,
		      
			  show: {
			effect: "fade",
			duration: 500
		      },
		      hide: {
			effect: "fade",
			duration: 500
		      },
			create: function(event, ui) { 
			     $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
			     $('.ui-dialog-titlebar-close').attr("title", "Close");
			     $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			},
			  buttons: [
			{
			    text: "Cancel",
			    "title":"Cancel",
			    "class": 'btn btn-primary',
			    click: function() {
					$(this).dialog("close");
			    }
			},
			{
			    text: "PDF",
			    "title":"PDF Export",
			    "class": 'btn btn-primary',
			    click: function() {
				instruction_previewpdf(task_id,duedate);
			    }
			}
		    ],
		    close: function() {
			$(this).dialog('destroy').remove();
			// Close code here (incidentally, same as Cancel code)
		    }
		}).parent().find('.ui-dialog-title').html("View Instructions").after("<a href='javascript:toggleinstructions();' title='Expand/Collapse All' id='instruction-view' tab-index='0'><span id='showhideall' class='glyphicon glyphicon-plus'></span></a>");
		$( "#instruction_preview" ).dialog( "open" );
		
		
            }
        }
    });
}
/* End : on Click of project # Link in Case Projects Grid */

/* Start : Show / hide all instructions sections */
function toggleinstructions() 
{
    if($('#showhideall').hasClass('glyphicon-plus')){
	    $('#showhideall').removeClass('glyphicon-plus');
	    $('#showhideall').addClass('glyphicon-minus');
	    $(".myheader").each(function() {
			$(this).addClass('myheader-selected-tab');
			$(this).next().show(); // show all content  
		});
	}else{
	    $('#showhideall').removeClass('glyphicon-minus');
	    $('#showhideall').addClass('glyphicon-plus');
	    $(".myheader").each(function() {
			$(this).removeClass('myheader-selected-tab');
			$(this).next().hide(); // hide all content
		});
	}	
    //$(".myheader").each(function() {
	//	$(this).next().show();
		/*var classname = $('#showhideall').attr('class');
		$content = $(this).next();
		$content.slideToggle(500, function() {});
		if($(this).hasClass('myheader-selected-tab')){
			$(this).removeClass('myheader-selected-tab');
		}else{
			$(this).addClass('myheader-selected-tab');
		}*/	
   // });
}
/* End : Show / hide all instructions sections */

/* Start : Show instruction preview pdf */
function instruction_previewpdf(task_id,duedate)
{
    var host = window.location.href;//.hostname
    var httPpath = "";
    if (host.indexOf('index.php'))
    {
        httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
    }
	if(duedate!="")
    	location.href = httpPath + "pdf/task-instructions&task_id=" + task_id + "&duedate="+duedate;
	else
		location.href = httpPath + "pdf/task-instructions&task_id=" + task_id;	
}
/* End : Show instruction preview pdf */
/* Start : Show instruction preview pdf */
function instruction_pdf(taskinstruction_id)
{
    var host = window.location.href;//.hostname
    var httPpath = "";
    if (host.indexOf('index.php'))
    {
        httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
    }
    location.href = httpPath + "pdf/instructionpdf&id=" + taskinstruction_id;
}
/* End : Show instruction preview pdf */
/* Start : on click of Enter Portal button in My Case landing page. */
function showselectedcase(portal_url) {
        var error = true;
        if ($("#client_id").val() == '') {
			error = false;
        }
        if ($("#client_case_id").val() == "") {
            error = false;
        }
        else if($("#client_case_id option:selected").val() == ""){
            error = false;
        }
        if(isNaN($("#client_case_id").val())){
			error = false;
		}
        if (error != true) {
            alert("Please select a Client and Case to perform this action.");
        } else {
            var caseId = $("#client_case_id").val();
            location.href = portal_url+caseId; 
        }
}
/* End : on click of Enter Portal button in My Case landing page. */   

/* Start : on click of New Project button in My Case landing page. */
function newProjects() {

	var error = true;
    if ($("#client_id").val() == '') {
       // error += "Please select client to perform this action.\n";
       error = false;
    }
    if ($("#client_case_id").val() == "") {
		error = false;
        //error += "Please select case to perform this action.\n";
    }
    else if($("#client_case_id option:selected").val() == ""){
        //error += "Please delect vase from The list to perform this action.\n";
        error = false;
    }
    if(isNaN($("#client_case_id").val())){
			error = false;
	}
    if (error != true) {
        alert("Please select a Client and Case to perform this action.");
    } else {
        var caseId = $("#client_case_id").val();
        location.href = httpPath + "project/add&case_id=" + caseId;
    }
}
/* End : on click of New Project button in My Case landing page. */

/* Start : Bulk ReOpen closed projects */
function reopenProjects(bulkreopendialog){
	
    var case_id = jQuery('#case_id').val();
	var keys = $('#closed-projects-grid').yiiGridView('getSelectedRows');
	var count = keys.length;	
	// open the dialog
	bulkreopendialog.dialog({
        title: 'Bulk ReOpen Projects',
        autoOpen: true,
        resizable: false,
        width: "50em",
        height:302,
        modal: true,
        buttons: [ 
            {
	            text: "Cancel",
	            "title":"Cancel",
	            "class": 'btn btn-primary',
	            click: function() {
            		bulkreopendialog.dialog("close");
	            }
            },
            {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
	            	var taskoperation = $('#bulkreopen-closed-dialog input[type="radio"]:checked').val();
	            	//console.log(taskoperation);return false;                        
	        		if(taskoperation == 'selectedtask'){
        	            if (confirm("Are you sure you want to ReOpen the selected "+count+" record(s)?"))
        	            {
        	            	jQuery.ajax({
        	            		url: baseUrl +'case-projects/reopen-projects&case_id='+case_id,
        	            		data:{task_list:keys, flag:'selected'},
        	            		type: 'post',
        	            		beforeSend:function (data) {showLoader();},
        	            		success: function (data) {
        	            			bulkreopendialog.dialog("close");
        	            			$.pjax.reload('#dynagrid-closed-projects-pjax', $.pjax.defaults);
        	            		},
        	            		complete: function (data) {hideLoader();}
        	              	});
        	            }
	        		} else {
                                    var postData = $('#load-closed-projects-form').serialize()+'&'+'flag=all';
                                    if(confirm("Are you sure you want to ReOpen All record(s)?"))
                                    {
                                            jQuery.ajax({
                                                    url: baseUrl +'case-projects/reopen-projects&case_id='+case_id,
                                                    data:postData,
                                                    type: 'post',
                                                    beforeSend:function (data) {showLoader();},
                                                    success: function (data) {
                                                            bulkreopendialog.dialog("close");
                                                            $.pjax.reload('#dynagrid-closed-projects-pjax', $.pjax.defaults);
                                                    },
                                                    complete: function (data) {hideLoader();}
                                            });
                                    }
	        		}
            	}
            }
        ],
		open: function () {
			bulkreopendialog.find('#alltask').html($('#closed-projects-pajax .summary b#totalItemCount').text());
	    	bulkreopendialog.find('#selectedtask').html(count);
        	if(count == 0){ 
        		bulkreopendialog.find('#rdo_selectedreopen').prop('disabled',true);
        		bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass('disabled');
        		bulkreopendialog.find('#rdo_selectedreopen').prop('checked',false);
    			bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass('checked');
    			bulkreopendialog.find('#rdo_bulkreopen').prop('checked',true);
    			bulkreopendialog.find('label[for="rdo_bulkreopen"]').addClass('checked');
    			
    			bulkreopendialog.find('#rdo_bulkreopen').focus();
    			bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass("focus")
                        bulkreopendialog.find('label[for="rdo_bulkreopen"]').addClass("focus");
        	} else {
        		bulkreopendialog.find('#rdo_selectedreopen').prop('disabled',false);
        		bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass('disabled');
        		bulkreopendialog.find('#rdo_selectedreopen').prop('checked',true);
    			bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass('checked');
    			bulkreopendialog.find('#rdo_bulkreopen').prop('checked',false);
    			bulkreopendialog.find('label[for="rdo_bulkreopen"]').removeClass('checked');
    			
        		bulkreopendialog.find('#rdo_selectedreopen').focus();
        		bulkreopendialog.find('label[for="rdo_bulkreopen"]').removeClass("focus");
            	bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass("focus");
        	}
        }
    });
}
/* End : Bulk ReOpen closed projects */
/* Start : Open dialog box when user click on select cases button in My Cases landing page */
function selectCases(){
    
    var sel_case_ids = $("#rselectedcases").val();
    var sel_client_id = $("#rselectedclient").val();
     $.ajax({
        url: httpPath + "mycase/viewclients",
	type: 'post',
	data: {'case_ids':sel_case_ids, 'client_id':sel_client_id},
        // async: false,
        cache: false,
        dataType: 'html',
        success: function (data) {
            if (data != "") {
                $('#select_cases').html(data);
		
		$( "#select_cases" ).dialog({
		      autoOpen: false,
		      resizable: false,
		      height:456,
		      width: "50em",
		      modal: true,
		      
			  show: {
			effect: "fade",
			duration: 500
		      },
		      hide: {
			effect: "fade",
			duration: 500
		      },
                    create: function(event, ui) { 
			 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
			 $('.ui-dialog-titlebar-close').attr("title", "Close");
			 $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
                    },
		    buttons: [
			{
			    text: "Cancel",
			    "title":"Cancel",
			    "class": 'btn btn-primary',
			    click: function() {
				$( this ).dialog( "close" );
			    }
			},
			{
			    text: "Add",
			    "title":"Add",
			    "class": 'btn btn-primary',
			    click: function() {
				//if($('.chk_clients:checked').length > 0){
				//	var client_id = $('.chk_clients:checked').val();
				    var case_id = $('.case_checkbox:checked').length;
				    if (case_id == 0) {
						alert('Please select 1+ Case to perform this action.');
						return false;
				    }
				    else {
					case_ids = "";
					show_vals = "";
					
					var client_name  = $('.chk_clients:checked').val();
					$("#rselectedclient").val(client_name);
					
					$('.case_checkbox:checked').each(function () {
					    if (case_ids == "")
						case_ids = (this.value);
					    else
						case_ids += "," + (this.value);
					    if (show_vals == "")
						show_vals = (this.title);
					    else
						show_vals += "; " + (this.title);
					});
						$("#rselectedcases").val(case_ids);
						$("#rcases").find('#names').html(show_vals);
				    }
				    $(this).dialog("close");
				   
				  //  } else {
					//alert("Please select Client");
					//return false;
				//}
			    }
			}
		    ],
		    close: function() {
				// Close code here (incidentally, same as Cancel code)
		    }
		}).parent().find('.ui-dialog-title').html("Select Cases");
		$("#select_cases").dialog("open");
		
		$('input').customInput();
            }
        }
    });
}
/* End : Open dialog box when user click on select cases button in My Cases landing page */

/* Start : Close Projects if status = completed */
function close_projects(){
	var case_id = jQuery('#case_id').val();
	if($('#caseprojects-grid').length > 0){
		var keys = $('#caseprojects-grid').yiiGridView('getSelectedRows');
		if(keys.length > 0){
			var newkeys = keys.join(", ");
			var i=0;
			no_of_not_completed_tasks = 0;
			for(i=0;i<keys.length;i++){
				if($(".task_status_"+keys[i]).val() != 4){
					no_of_not_completed_tasks += 1;
				}
			}
			if(no_of_not_completed_tasks > 0){
				alert("This action applies to a Project in Complete Status.  Please select a Completed Project to perform this action.");
				return false;
			} else {
				jQuery.ajax({
					url: baseUrl +'case-projects/chkcanclosecancelproject&case_id='+case_id,
					data:{task_list:keys},
					type: 'post',
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft'){
    	        			alert("The selected Project cannot be Closed because it has outstanding Billing Items. Please Finalize Invoices for the selected Project before Closing the Project.");
    	        			return false;
    	        		} else {
    	        			if (confirm("Are you sure you want to Close #"+ newkeys + "?"))
    			            {
    	        				jQuery.ajax({
    	        					url: baseUrl +'case-projects/close-projects/',
    	        					data:{task_list:keys},
    	        					type: 'post',
    	        					beforeSend:function (data) {showLoader();},
    	        					success: function (data) {
    	        						$.pjax.reload('#dynagrid-caseprojects-pjax', $.pjax.defaults);
    	        					},
    	        					complete: function (data) {hideLoader();}
    	        				});
    			            }
    	        		}
					},
					complete: function (data) {hideLoader();}
			  	});	
			}
		} else {
			alert("Please select at least 1 record to perform this action.");
			return false;
		}
	}
}
/* End : Close Projects if status = completed */

/* Start : Cancel Projects */
function cancel_project(){
	var case_id = jQuery('#case_id').val();
	if($('#caseprojects-grid').length > 0){
		var keys = $('#caseprojects-grid').yiiGridView('getSelectedRows');
		
		if(keys.length > 0){
			if(keys.length > 1){
				alert('Please select a single record to perform this action.');
				return false;
			} else {
				var newkeys = keys.join(", ");
				jQuery.ajax({
					url: baseUrl +'case-projects/chkcanclosecancelproject&case_id='+case_id,
					data:{task_list:keys},
					type: 'post',
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft'){
    	        			alert("The selected Project cannot be Canceled because it has outstanding Billing Items. Please Finalize Invoices for the selected Project before Cancelling the Project.");
    	        			return false;
    	        		} else {
	        				var cancelreasondialog = $('body').find('div#cancel-reason-dialog');
	        				if(cancelreasondialog.length == 0){
	        					$('body').append('<div id="cancel-reason-dialog"></div>');
	        					var cancelreasondialog = $('body').find('div#cancel-reason-dialog');
	        				}
	        				
	        				jQuery.ajax({
	        					url: baseUrl +'case-projects/load-cancel-project/&id='+keys.join(),
	        					type: 'get',
	        					beforeSend:function (data) {showLoader();},
	        					success: function (data) {
	        						cancelreasondialog.html(data);
			        				cancelreasondialog.dialog({
			        			        title: 'Cancel Project',
			        			        autoOpen: true,
			        			        resizable: false,
			        			        width: "50em",
			        			        height:456,
			        			        modal: true,
			        			        close: function(){
			        						cancelreasondialog.dialog('destroy').remove();
			        					},
			        					beforeClose: function(event){
											if(event.keyCode==27) trigger = '';
											if(trigger != 'Update') checkformstatus(event);
										},
			        			        buttons: [ 
			        			            {
			        				            text: "Cancel",
			        				            "title":"Cancel",
			        				            "class": 'btn btn-primary',
			        				            click: function() {
													trigger = 'Cancel';
			        			            		cancelreasondialog.dialog("close");
			        				            }
			        			            },
			        			            {
			        				            text: "Update",
			        				            "title":"Update",
			        				            "class": 'btn btn-primary',
			        				            click: function() {
			        			            		showLoader();
			        			            		trigger = 'Update';
			        			            		// change flag
			        			            		$('#TaskCancelForm').submit();
			        			            		//window.location.reload();
			        			            	}
			        			            }
			        			        ]
			        			    });
	        					},
	        					complete: function () {
	        						hideLoader();
	        						$('#TaskCancelForm').ajaxForm({
	        							success: submitCancelTask,
	        						});
	        					}
	        				});
    	        		}
					},
					complete: function () {hideLoader();}
			  	});	
			}
		} else {
			alert("Please select at least 1 record to perform this action.");
			return false;
		}
	}
}

function submitCancelTask(responseText, statusText){
	hideLoader();
	if(responseText == 'OK'){
		$('#cancel-reason-dialog').dialog('close');
		if($('#dynagrid-caseprojects-pjax').length){
			$.pjax.reload('#dynagrid-caseprojects-pjax', $.pjax.defaults);
		}else{
			$.pjax.reload('#teamassigneduser-pajax', $.pjax.defaults);
		}
	}else{
		$("#cancel-reason-dialog").html(responseText);
		$('#TaskCancelForm').ajaxForm({
			success: submitCancelTask,
		});
	}
}
/* End : Cancel Projects  */
/* Start : Remove Project */
function remove_project(keys,case_id){
	jQuery.ajax({
		url: baseUrl +'case-projects/chkcanclosecancelproject&case_id='+case_id,
		data:{task_list:keys},
		type: 'post',
		success: function (data) {
			var msgconfirm ="Are you sure you want to Delete Project #"+keys+"?"; 
			if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft'){
				msgconfirm ="The selected Project has outstanding Billing Items.  Are you sure you want to Remove the project and all associated outstanding Billing Items.";
    			
    		}
    		if(data.replace(/^\s+|\s+$/g, "") == 'billabled'){
			    alert("“The selected Project cannot be Removed because it is associated with a Finalized invoice.");
				return false;	
			}
			if (confirm(msgconfirm))
            {
				jQuery.ajax({
					url: baseUrl +'case-projects/remove-project/',
					data:{task_list:keys},
					type: 'post',
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						$.pjax.reload('#dynagrid-caseprojects-pjax', $.pjax.defaults);
					},
					complete: function (data) {hideLoader();}
				});
            }
    		
		},
		complete: function (data) {hideLoader();}
  	});	
}
/* End : Remove Projects */
/* Start :  */
function addProject(case_id) 
{
   location.href = baseUrl + 'project/add&case_id='+case_id; 
}
/* End : */
function  casebudget_pdf(case_id){
	//showLoader();
	url = baseUrl +'pdf/case-budget&case_id='+case_id;
	var form = document.createElement("form");
	document.body.appendChild(form);
	form.method = "POST";
	form.action = url;
	imageval=$("#pdfimage").val();
	var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", 'pdfimage');
    hiddenField.setAttribute("value", imageval);
    form.appendChild(hiddenField);
    var hiddenField1 = document.createElement("input");
    hiddenField1.setAttribute("type", "hidden");
    hiddenField1.setAttribute("name", '_csrf');
    hiddenField1.setAttribute("value", yii.getCsrfToken());
    form.appendChild(hiddenField1);
    
    
	form.submit();
	/*$.ajax({
		url:url,
		type:'post',
		data:$("#ClientCase").serialize(),
	});*/
}

/* Start: Post data to client services */
function postExcludedCasesServies(form_id,btn,successFunc,targetid){
	var form = $('form#'+form_id);
	var client_case_id = jQuery('#case_id').val();		
	jQuery(".ckeck_excluded").each(function(){
		$(form).append('<input type="hidden" name="idsToRemove['+$(this).data('teamservice_id')+'][]" value="'+$(this).data('team_loc')+'" >');	
	});
	$.ajax({
		url    :   baseUrl +'/case/update-excluded-service-list&client_case_id='+client_case_id,  
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function() {
        	$(btn).attr('disabled','disabled');
        },
        success: function (response){
			if(response == 'OK'){
				//eval(successFunc);
			//	updateCaseSelect(client_case_id);
				updateCase(client_case_id);
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
