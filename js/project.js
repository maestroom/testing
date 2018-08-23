function cancelProject(case_id,flag,task_id){
	if(flag=='saved'){
		location.href=baseUrl+'case-projects/load-saved-projects&case_id='+case_id;
	}else if(flag=='change'){
		location.href=baseUrl+'case-projects/change-project&case_id='+case_id+'&task_id='+task_id;
	}else{
		location.href=baseUrl+'case-projects/index&case_id='+case_id;
	}
} 
function validateSteps(step){
	$flag=false;
	$.ajax({
		url:baseUrl+'project/validatesteps&step='+step,
		type:'post',
		data:$("#Tasks").serialize(),
		success:function(response){
			if(response.length==0){
				$( "#tabs" ).tabs('enable', parseInt(step)).tabs("option", "active", parseInt(step));
			} else {
				for (var key in response) {
					$("#"+key).parent().find('.help-block').html(response[key]);
					$("#"+key).closest('div.form-group').addClass('has-error');
				}
			}
		}
	});
	return $flag;
}
function gotostep(step) {
	if(step==2) {
		var triggerChange = $('#triggerChange').val();
		var totalHours = 0;
		if($('#txt_esthours').length > 0)
			totalHours = $('#txt_esthours').val(); 
		if($('#txt_manesthours').length > 0)
			totalHours = parseFloat(totalHours) + parseFloat($('#txt_manesthours').val());
		if(triggerChange == 1){
			adjustDateTime();
		} else if($('#taskinstruct-task_duedate').val()=='' && $('#taskinstruct-task_duedate').val()=='' && totalHours==0) {
			var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
			var con = $('#service_task_container');
			var priority = $('#taskinstruct-task_priority').val();
			calculateprojectedtime(media, con,  priority, "add");
			updatecustom_sort();
		} else {
			var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
			var con = $('#service_task_container');
			var priority = $('#taskinstruct-task_priority').val();
			calculateprojectedtime(media, con,  priority, "adjustedDateTime");
		}
	}
	$( "#tabs" ).tabs('enable', step).tabs("option", "active", step);
}

function adjustDateTime(){
	var task_instruct_id = '';
	
	var due_date=$('input[name="TaskInstruct[task_duedate]"]').val();
	var due_time=$('select[name="TaskInstruct[task_timedue]"]').val();
	if(due_time == null){
		due_time ='';
	}
	var diffslahours = $('#diffslahours').val();
	var totalHours = 0;
	var slackHours = 0;
	if(due_date!='' && due_time!=''){
		if($('#txt_esthours').length > 0)
			totalHours = $('#txt_esthours').val(); 
		if($('#txt_manesthours').length > 0)
			totalHours = parseFloat(totalHours) + parseFloat($('#txt_manesthours').val());
		if($('#txt_slackhours').length > 0)
			slackHours = $('#txt_slackhours').val();
		$.ajax({
			type: "POST",
			url: baseUrl + "project/get-updated-due-date",
			data: {'diffslahours': diffslahours, 'task_instruct_id':task_instruct_id, 'due_date':due_date, 'due_time':due_time, 'slackHours':slackHours},
			beforeSend:function(){showLoader();},
			success: function(response){
				var response = JSON.parse(response);
				var new_due_date = response.current_date;
				var new_due_time = response.current_time;
				$('#task_duedate_by_st').val(new_due_date);
				$('#task_duetime_by_st').val(new_due_time);
				if(slackHours>0){
					$('input[name="TaskInstruct[task_duedate]"]').val(response.slackdate);
					$('input[name="TaskInstruct[task_duedate]"]').datepicker("option", "minDate", response.slackdate);	
					$('select[name="TaskInstruct[task_timedue]"]').val(response.slacktime).change();
				} else {
					$('input[name="TaskInstruct[task_duedate]"]').val(new_due_date);
					$('input[name="TaskInstruct[task_duedate]"]').datepicker("option", "minDate", new_due_date);	
					$('select[name="TaskInstruct[task_timedue]"]').val(new_due_time).change();
				}
				
				var res = new_due_date.split("/"); 
				datePickerController.setRangeLow("taskinstruct-task_duedate", (res[2]+res[0]+res[1]));

				var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
				var con = $('#service_task_container');
				var priority = $('#taskinstruct-task_priority').val();
				calculateprojectedtime(media, con,  priority, "adjustedDateTime");
			},
			complete:function(){hideLoader();}
		});
		$('#triggerChange').val(0);
		$('#diffslahours').val(0);
		
	} else {
		var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
		var con = $('#service_task_container');
		var priority = $('#taskinstruct-task_priority').val();
		calculateprojectedtime(media, con,  priority, "add");
		$('#triggerChange').val(0);
		$('#diffslahours').val(0);
	}
}

function checktotalhours()
{
	var removed_servicetask_id = 0;

	//if(totalHours > 0){
		var priority = $('#taskinstruct-task_priority').val();
		var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
		var services = [];
		var teamserviceSLA = [];
		$('#service_task_container li').each(function () {
			var id = $(this).attr('id');
			var loc = $('#service_task_container li .sloc_' + id).val();
			var teamservice_id = $('#service_task_container li #workflow_servicetasks_' + id).attr('data-teamservice_id');
			var estslahours = $('#service_task_container li #est_time_' + id).val();
			var hdn_service_logic = $('#service_task_container li #hdn_service_logic_' + id).val();
			if(id != 'first_row'){
				var serviceLoc = {'id': id, 'teamservice_id':teamservice_id, 'loc': loc, 'hours':estslahours, 'hdn_service_logic' : hdn_service_logic};
				var teamservices = {'teamservice_id':teamservice_id,'hours':estslahours,'loc': loc};
				var objindex = teamserviceSLA.findIndex(function (element) {
					return element.teamservice_id === teamservice_id && element.loc === loc;
				});
				if(objindex >= 0){
					teamserviceSLA[objindex].hours = parseFloat(teamserviceSLA[objindex].hours) + parseFloat(estslahours);
				} else {
					teamserviceSLA.push(teamservices);
				}
				//console.log(teamserviceSLA);
				services.push(serviceLoc);
			}
		});

		if (services.length > 0) {
			$.ajax({
				type: "POST",
				url: baseUrl + "project/get-total-hours",
				data: {'priority': priority, 'evidence': media, 'service': services, 'teamserviceSLA':teamserviceSLA, 'removed_servicetask_id':removed_servicetask_id},
				beforeSend:function(){showLoader();},
				success: function(response){
					//console.log(response);
					var response = JSON.parse(response);
					var diffHours = response.diffHours;
					
					$('#diffslahours').val(diffHours);
					$('#triggerChange').val(1);
					console.log(diffHours);
					if(diffHours!=0){
						//alert(diffHours);
						adjustDateTime();
					} /*else {
						var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
						var con = $('#service_task_container');
						var priority = $('#taskinstruct-task_priority').val();
						calculateprojectedtime(media, con,  priority, "adjustedDateTime");
					}*/
				},
				complete:function(){hideLoader();}
			});
		}
	//}
}

function loadSaveformBuilder(instruction_id,flag){
	var error = 0;
	var due_date=$('#taskinstruct-task_duedate').val();
	var due_time=$('#taskinstruct-task_timedue').val();
	if(flag == 'Edit'){
		if(!$('#service_task_container li').length || $('#service_task_container li').length == 1){
			alert('Please add 1+ Task to perform this action.');
			error = 1;
		}
    }else{
		if(!$('#service_task_container li').length || $('#service_task_container li').length == 1){
			alert('Please add 1+ Task to perform this action.');
			error = 1;
		}
	}
	if(due_date == ""){
		$("#taskinstruct-task_duedate").parent().next().find('.help-block').html('Due Date is required.');
		$("#taskinstruct-task_duedate").closest('div.form-group').addClass('has-error');
		error = 1;
	}
	if(due_time == ""){
		$("#taskinstruct-task_timedue").parent().next().find('.help-block').html('Due Time is required.');
		$("#taskinstruct-task_timedue").closest('div.form-group').addClass('has-error');
		error = 1;
	}
	if(error == 1){
		return false;
	}
    gotostep(3);
    var servicetask_ids = "";
    var new_servicetask_ids = [];
    var project_id;
	var servicetask_len=$('#service_task_container').find('li input[type=checkbox]').length;
	
    $('#service_task_container').find('li input[type=checkbox]').each(function() {
		project_id=0;
		if($('#service_task_container').find('li[id="'+$(this).val()+'"]').data('project')){
			project_id = $('#service_task_container').find('li[id="'+$(this).val()+'"]').data('project');
		}
		if(servicetask_ids!="")
			servicetask_ids = servicetask_ids+","+$(this).val();
		else
			servicetask_ids = $(this).val();
		
		if($('#service_task_container').find('input[name="ServicetaskInstruct['+$(this).val()+']"]').length == 0){
			if($(this).val() != 'on')
				new_servicetask_ids.push($(this).val());
		}
	});
    //console.log(new_servicetask_ids);
    $.ajax({
	    url: baseUrl +'/project/getformbuilderdata',
	    type:"post",
	    data:{servicetask_ids: servicetask_ids, instruction_id: instruction_id,flag:flag,new_servicetask_ids:new_servicetask_ids},
	    cache: false,
	    dataType:'html',
	    context: this,
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(result){
		    $('#formbuilder_data').html(result);
		  
		    $('#service_task_container').find('li input[type=checkbox]').each(function(index){
		    //var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk';
		    var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk_instruction';
		    var into = $("#form_builder_panel"+$(this).val()+" ol");
		    var taskid = $(this).val();
		    $('#form_builder_panel'+taskid).prepend('<h3 class="servicetask_title"><a href="javascript:void(0);" class="tag-header-black" title="'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'">'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'</a><a href="javascript:void(0);" class="pull-right" onclick="triggerInput('+taskid+');"><em class="fa fa-paperclip" title="Attach"></em></a></h3>');
		    $.ajax({
			    url: Url,
			    type:"post",
			    data:$('#formbuilder-edit-'+$(this).val()+' :input').serialize(),
			    cache: false,
			    dataType:'json',
			    context: this,
			    success:function(res){
				    //$(into).html('');
				    if(res.length!=0){
					}
					if(res.length==0){
					  $('#form_builder_panel'+taskid).empty();
					}
					//$('#form_builder_panel'+taskid).prepend('<h3 class="servicetask_title">'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'</h3>');
				  
				    $.each(res,function(key,val){
					    $(into).prepend(val);
					    var $newrow = $(into).find('li:first');
					    Admin.formbuilder.properties($newrow);
					    Admin.formbuilder.layout($newrow);
					    Admin.formbuilder.attr.update($newrow);
					    //show
					    
					    var id=$('#form_builder_panel'+taskid+' ol li label:first').attr('for');
					    var id_a=$('#form_builder_panel'+taskid+' ol li label:first').find('a').attr('href');
					    $newrow.hide().slideDown('slow');
				    });
				    if($.trim(res)!=""){
					    dd = new Date();
					    id = dd.getTime();
					    $(into).append('<li style="display: list-item;" id="attach_'+taskid+'"><div class="row border-saprater"><div class="col-md-3"><label class=" form_label" for="element_'+id+'">Attachment</label></div><div class="block col-md-7"><span style="display:inline-block;" class="values element_5697a3995ae35"><input type="file" id="'+id+'" name="TaskInstruct[attachment]['+taskid+'][]" class="multi-pt" ><span class="note '+id+'"></span></div></div></li>');
						if((index+1)==servicetask_len){
				    		showAttachments(0,instruction_id);
				    	}
					    $('#'+id).MultiFile({
				    	    STRING: {
				    	      remove:'<em class="fa fa-close text-danger" title="Remove"></em>',
				    	    },
				    		maxsize:102400
				    	  });
			    	}
				    delete res;
					
				    //$('#custodian-edit').remove();
			    },
			    complete:function(){
			    	$('#formbuilder-edit-'+$(this).val()).remove(); 
				$('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements });	
				});
			    	$('input').customInput();    	
			    }
		    }); //}, 5000);
		});
		//delete result;
	    },
	    complete:function(){
	    	$('input').customInput();
		$('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements });	
				});
	    	hideLoader(); 
	    }
    });
}
function showAttachments(project_id,instruction_id){
	 $.ajax({
		    url: baseUrl +'/project/show-attachment',
		    type:"get",
		    data:{project_id: project_id,instruction_id: instruction_id},
		    cache: false,
		    dataType:'json',
		    context: this,
		    success:function(result){
					$.each(result,function(servicetask_id,key){
			    			service_html = "";
			    			$.each(key,function(id,attachment_data){
			    				service_html = service_html +  '<div class="MultiFile-label" style="margin-left:7px;"><a href="javascript:void(0);" class="MultiFile-remove" onclick="removeinstruction_image('+attachment_data.id+', this);"><em class="fa fa-close text-danger" title="Remove"></em></a><span title="File selected: '+attachment_data.name+'" class="MultiFile-title">'+attachment_data.name+'</span></div>';
			    			});
			    			console.log(servicetask_id+' =>'+service_html);
							if(service_html!="")
			    				$('#attach_'+servicetask_id).find('.row').find('.col-md-7').append(service_html);
			    	});
		    	
		    }
	 });
		    
}
function removeinstruction_image(id,obj){
	$(obj).parent().remove();
	if($('#remove_attachment').val()!=''){
		$('#remove_attachment').val(id+','+$('#remove_attachment').val());
	} else {
		$('#remove_attachment').val(id);
	}
}

/*function loadformBuilder() {
	var error = 0;
	var due_date=$('#taskinstruct-task_duedate').val();
	var due_time=$('#taskinstruct-task_timedue').val();
	if(!$('#service_task_container li:not(#first_row)').length){
		alert('Please add 1+ Task to perform this action.');
		error = 1;
	}
	if(due_date == "" || due_date == null){
		$("#taskinstruct-task_duedate").parent().next().find('.help-block').html('Due Date is required.');
		$("#taskinstruct-task_duedate").closest('div.form-group').addClass('has-error');
		error = 1;
	}
	if(due_time == "" || due_time == null){
		$("#taskinstruct-task_timedue").parent().next().find('.help-block').html('Due Time is required.');
		$("#taskinstruct-task_timedue").closest('div.form-group').addClass('has-error');
		error = 1;
	}
	if(error == 1){
		return false;
	}
	showLoader();
   
    var servicetask_ids = "";
    var project_id;
    $('#service_task_container').find('li input[type=checkbox]').each(function() {
    project_id=0;
    
    if($('#service_task_container').find('li[id="'+$(this).val()+'"]').data('project')){
    	project_id = $('#service_task_container').find('li[id="'+$(this).val()+'"]').data('project');
    }
    
	if(servicetask_ids!="")
	    servicetask_ids = servicetask_ids+","+$(this).val();
	else
	    servicetask_ids = $(this).val();
    });
    
    var loadprevoius = 0;
    if($('#load_prev_project_id').length > 0 && $('#load_prev_project_id').val() != '')
		loadprevoius = $('#load_prev_project_id').val();
    //alert(servicetask_ids);
    $.ajax({
	    url: baseUrl +'/project/getformbuilderdata',
	    type:"post",
	    data:{servicetask_ids: servicetask_ids, project_id: project_id, 'loadprevoius':loadprevoius},
	    cache: false,
	    dataType:'html',
	    context: this,
	    beforeSend:function(){
		$('#formbuilder_data').hide();
			showLoader();
	    },
	    success:function(result){
		    $('#formbuilder_data').html(result);
		  
		    $('#service_task_container').find('li input[type=checkbox]').each(function(){
		    if(this.id == 'chkall') { return true;} 
		    var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk_instruction';
		    var into = $("#form_builder_panel"+$(this).val()+" ol");
		    var taskid = $(this).val();
		    
		    $('#form_builder_panel'+taskid).prepend('<h3 class="servicetask_title"><a href="javascript:void(0);" class="tag-header-black" title="'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'">'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'</a><a href="javascript:void(0);" class="pull-right" onclick="triggerInput('+taskid+');" title="Attach"><em class="fa fa-paperclip"></em></a></h3>');
		    $.ajax({
			    url: Url,
			    type:"post",
			    data:$('#formbuilder-edit-'+$(this).val()+' :input').serialize(),
			    cache: false,
			    dataType:'json',
			    context: this,
			    success:function(res){
				    if(res.length==0){
					  $('#form_builder_panel'+taskid).empty();
					}
				    $.each(res,function(key,val){
					    $(into).prepend(val);
					    var $newrow = $(into).find('li:first');
					    Admin.formbuilder.properties($newrow);
					    Admin.formbuilder.layout($newrow);
					    Admin.formbuilder.attr.update($newrow);
					    //show
					    
					    var id=$('#form_builder_panel'+taskid+' ol li label:first').attr('for');
					    var id_a=$('#form_builder_panel'+taskid+' ol li label:first').find('a').attr('href');
					    //$newrow.hide().slideDown('slow');
					    $newrow.hide().show();
				    });
				    if($.trim(res)!=""){
					    dd = new Date();
					    id = dd.getTime();
						
						if(loadprevoius > 0)
							$(into).append('<li style="display: list-item;" id="attach_'+taskid+'"><div class="row border-saprater"><div class="col-md-3"><label class=" form_label" for="element_'+id+'">Attachment</label></div><div class="block col-md-7"><span style="display:inline-block;" class="values element_5697a3995ae35"><input type="file" id="'+id+'" name="TaskInstruct[attachment]['+taskid+'][]" class="multi-pt" ><span class="note '+id+'"></span></div></div></li>');
						else
					    	$(into).append('<li style="display: list-item;"><div class="row border-saprater"><div class="col-md-3"><label class=" form_label" for="element_'+id+'">Attachment</label></div><div class="block col-md-7"><span style="display:inline-block;" class="values element_5697a3995ae35"><input type="file" id="'+id+'" name="TaskInstruct[attachment]['+taskid+'][]" class="multi-pt" ><span class="note '+id+'"></span></div></div></li>');

					    $('#'+id).MultiFile({
				    	    STRING: {
				    	      remove:'<em class="fa fa-close text-danger"></em>',
				    	    },
				    		maxsize:102400
				    	  });
			    	}
				    delete res;
				    //$('#custodian-edit').remove();
			    },
			    complete:function(){
			    	$('#formbuilder-edit-'+$(this).val()).remove(); 
				  $('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements });	
				});
			    	$('input').customInput();
			    }
		    }); //}, 5000);
		});
		$('input').customInput();
	    	hideLoader(); 
		$('#formbuilder_data').show();
		gotostep(3);
		//delete result;
	    },
	    complete:function(){
			$('.datepickers').each(function(e){
				var datepicker_id = $(this).attr('id');
				var formElements={};
				formElements[datepicker_id] = "%m/%d/%Y";
				datePickerController.createDatePicker({formElements: formElements });	
			});
			if(loadprevoius > 0){
				setTimeout(function(){
					showAttachments(loadprevoius,0);
				},100);
			}
	    }
    });
    
}*/
function loadformBuilder() {
	var error = 0;
	var due_date=$('#taskinstruct-task_duedate').val();
	var due_time=$('#taskinstruct-task_timedue').val();
	if(!$('#service_task_container li:not(#first_row)').length){
		alert('Please add 1+ Task to perform this action.');
		error = 1;
	}
	if(due_date == "" || due_date == null){
		$("#taskinstruct-task_duedate").parent().next().find('.help-block').html('Due Date is required.');
		$("#taskinstruct-task_duedate").closest('div.form-group').addClass('has-error');
		error = 1;
	}
	if(due_time == "" || due_time == null){
		$("#taskinstruct-task_timedue").parent().next().find('.help-block').html('Due Time is required.');
		$("#taskinstruct-task_timedue").closest('div.form-group').addClass('has-error');
		error = 1;
	}
	if(error == 1){
		return false;
	}
	showLoader();
   
    var servicetask_ids = "";
    var project_id;
    var loadprevoius = 0;
    if($('#load_prev_project_id').length > 0 && $('#load_prev_project_id').val() != '')
		loadprevoius = $('#load_prev_project_id').val();

	var servicetask_len = $('#service_task_container').find('li input[type=checkbox]').length;
	//alert(servicetask_len);
    $('#service_task_container').find('li input[type=checkbox]').each(function(index) {
    	
    project_id=0;
    if($('#service_task_container').find('li[id="'+$(this).val()+'"]').data('project')){
    	project_id = $('#service_task_container').find('li[id="'+$(this).val()+'"]').data('project');
    }
    
	if(servicetask_ids!="")
	    servicetask_ids = servicetask_ids+","+$(this).val();
	else
	    servicetask_ids = $(this).val();
    });
    
    

    //alert(servicetask_ids);
    $.ajax({
	    url: baseUrl +'/project/getformbuilderdata',
	    type:"post",
	    data:{servicetask_ids: servicetask_ids, project_id: project_id, 'loadprevoius':loadprevoius},
	    cache: false,
	    dataType:'html',
	    context: this,
	    beforeSend:function(){
		$('#formbuilder_data').hide();
			showLoader();
	    },
	    success:function(result){
		    $('#formbuilder_data').html(result);
		  
		    $('#service_task_container').find('li input[type=checkbox]').each(function(index){
		    if(this.id == 'chkall') { return true;} 
		    var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk_instruction';
		    var into = $("#form_builder_panel"+$(this).val()+" ol");
		    var taskid = $(this).val();
		    
		    $('#form_builder_panel'+taskid).prepend('<h3 class="servicetask_title"><a href="javascript:void(0);" class="tag-header-black" title="'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'">'+$('#service_task_container').find('li#'+taskid).find('.sername_div').html()+'</a><a href="javascript:void(0);" class="pull-right" onclick="triggerInput('+taskid+');" title="Attach"><em class="fa fa-paperclip" title="Attach"></em></a></h3>');
		    $.ajax({
			    url: Url,
			    type:"post",
			    data:$('#formbuilder-edit-'+$(this).val()+' :input').serialize(),
			    cache: false,
			    dataType:'json',
			    context: this,
			    success:function(res){
				    if(res.length==0){
					  $('#form_builder_panel'+taskid).empty();
					}
				    $.each(res,function(key,val){
					    $(into).prepend(val);
					    var $newrow = $(into).find('li:first');
					    Admin.formbuilder.properties($newrow);
					    Admin.formbuilder.layout($newrow);
					    Admin.formbuilder.attr.update($newrow);
					    //show
					    
					    var id=$('#form_builder_panel'+taskid+' ol li label:first').attr('for');
					    var id_a=$('#form_builder_panel'+taskid+' ol li label:first').find('a').attr('href');
					    //$newrow.hide().slideDown('slow');
					    $newrow.hide().show();
				    });
				    if($.trim(res)!=""){
					    dd = new Date();
					    id = dd.getTime();
						
						if(loadprevoius > 0)
							$(into).append('<li style="display: list-item;" id="attach_'+taskid+'"><div class="row border-saprater"><div class="col-md-3"><label class=" form_label" for="element_'+id+'">Attachment</label></div><div class="block col-md-7"><span style="display:inline-block;" class="values element_5697a3995ae35"><input type="file" id="'+id+'" name="TaskInstruct[attachment]['+taskid+'][]" class="multi-pt" ><span class="note '+id+'"></span></div></div></li>');
						else
					    	$(into).append('<li style="display: list-item;"><div class="row border-saprater"><div class="col-md-3"><label class=" form_label" for="element_'+id+'">Attachment</label></div><div class="block col-md-7"><span style="display:inline-block;" class="values element_5697a3995ae35"><input type="file" id="'+id+'" name="TaskInstruct[attachment]['+taskid+'][]" class="multi-pt" ><span class="note '+id+'"></span></div></div></li>');

					    $('#'+id).MultiFile({
				    	    STRING: {
				    	      remove:'<em class="fa fa-close text-danger" title="Remove"></em>',
				    	    },
				    		maxsize:102400
				    	  });
			    	}
				    delete res;
				    //$('#custodian-edit').remove();
				    if((index+1)==servicetask_len){
				    	if(loadprevoius > 0){
							showAttachments(loadprevoius,0);
						}
				    }
			    },
			    complete:function(){
			    	$('#formbuilder-edit-'+$(this).val()).remove(); 
				  $('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements });	
				});
			    	$('input').customInput();
			    }
		    }); //}, 5000);
		});
		$('input').customInput();
	    	hideLoader(); 
		$('#formbuilder_data').show();
		gotostep(3);
		//delete result;
	    },
	    complete:function(){
			$('.datepickers').each(function(e){
				var datepicker_id = $(this).attr('id');
				var formElements={};
				formElements[datepicker_id] = "%m/%d/%Y";
				datePickerController.createDatePicker({formElements: formElements });	
			});
			
	    }
    });
    
}
function triggerInput(servicetask_id){
	// change flag to 1
	$("#is_change_form").val('1'); $("#is_change_form_main").val('1');
	$("#form_builder_panel"+servicetask_id+" .multi-pt:last").trigger('click');
}
function validateFormBuilder(){
	var has_error=false;
	$(document).find('.form-builder-ol .required-entry').filter(':input').each(function(){
		if ($(this).is("input:checkbox") || $(this).is("input:radio")){
			var name = $(this).attr('name');
			var $myLabel = ($('label[for="'+ name.replace('[]','') +'"]').text());
			if($('input[name="'+name+'"]:checked').length == 0){
				if(!$(this).closest( ".block" ).hasClass('has-error')){
					$(this).closest( ".block" ).addClass('has-error');
					$(this).closest( ".block" ).append("<div class='help-block clear'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				$('input[name="'+name+'"]').focus();
				has_error=true;
			}
			else{
				if($(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().removeClass('has-error');
					$(this).parent().find('.help-block').html(null);
				}
			}
		}
		else if ($(this).is("select")){
			if($(this).val()=="0"){
				var name = $(this).attr('name');
				var $myLabel = ($('label[for="'+ name +'"]').text());
				if(!$(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().addClass('has-error');
					$(this).parent().append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}	
				has_error=true;
				$(this).focus();
			}else{
				if($(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().removeClass('has-error');
					$(this).parent().find('.help-block').html(null);
				}
			}
		}
		else{
			if($(this).val()==""){
				var name = $(this).attr('name');
				var $myLabel = ($('label[for="'+ name +'"]').text());
				if(!$(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().addClass('has-error');
					$(this).parent().append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				$(this).focus();
				has_error=true;
			}else{
				if($(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().removeClass('has-error');
					$(this).parent().find('.help-block').html(null);
				}
			}
		}
	});
	
	$('.clsunit').each(function(){
		parent_text = $(this).parent().parent().find('.user_input').val();
		parent_obj = $(this).parent().parent().find('.user_input');
		
		if(parent_obj.hasClass('required-entry')){
			if(parent_text=="" && $(this).val()==""){
				if(!$(parent_obj).parent().parent().hasClass('has-error')){
					$(parent_obj).parent().parent().addClass('has-error');
					$(parent_obj).after("<div class='help-block'>Unit cannot be blank.</div>");
				}else{
					$(parent_obj).next().html("Unit cannot be blank");
				}	
			has_error=true;
			}
			if(parent_text!="" && $(this).val()==""){
				if(!$(parent_obj).parent().parent().hasClass('has-error')){
					$(parent_obj).parent().parent().addClass('has-error');
					$(parent_obj).after("<div class='help-block'>Unit cannot be blank.</div>");
				}else{
					$(parent_obj).next().html("Unit cannot be blank");
				}	
			has_error=true;
			}
			if(parent_text=="" && $(this).val()!=""){
				if(!$(parent_obj).parent().parent().hasClass('has-error')){
					$(parent_obj).parent().parent().addClass('has-error');
					$(parent_obj).after("<div class='help-block'>Unit cannot be blank.</div>");
				}else{
					$(parent_obj).next().html("Unit cannot be blank");
				}	
				has_error=true;
			}
		}
		if(!parent_obj.hasClass('required-entry')){
			if(parent_text!="" && $(this).val()==""){
				if(!$(parent_obj).parent().parent().hasClass('has-error')){
					$(parent_obj).parent().parent().addClass('has-error');
					$(parent_obj).after("<div class='help-block'>Unit cannot be blank.</div>");
				}else{
					$(parent_obj).next().html("Unit cannot be blank");
				}
				has_error=true;
			}
			if(parent_text=="" && $(this).val()!=""){
				if(!$(parent_obj).parent().parent().hasClass('has-error')){
					$(parent_obj).parent().parent().addClass('has-error');
					$(parent_obj).after("<div class='help-block'>Unit cannot be blank.</div>");
				}else{
					$(parent_obj).next().html("Unit cannot be blank");
				}
				has_error=true;
			}
		}
	});
	
	return has_error;
}
function SaveProject(){
	if(!validateFormBuilder()){
		showLoader();
		$('#flag').val('save');
		$('#Tasks').submit();
	}
}
function SubmitProject(){
	if(!validateFormBuilder()){
		showLoader();
		$('#flag').val('submit');
		$('#Tasks').submit();
	}
}
function ResubmitProject(){
	if(!validateFormBuilder()){
		showLoader();
		$('#flag').val('resubmit');
		$('#Tasks').submit();
	}
}
function AddPorjectWorkflow(flag){
	var case_id = jQuery('#case_id').val();
	var ids=$('#filter_team_location').val();
	var request_type = $("#projectReqType").val();
	$.ajax({
		url:baseUrl + "project/workflow",
		data:{case_id:case_id,loc_ids:ids,request_type:request_type,flag:flag},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
		    if($( "#add-project-workflow" ).length) {
				$('#add-project-workflow').dialog('destroy').remove();
				 $('#add-project-workflow').remove();
			}
			if(!$( "#add-project-workflow" ).length){
				$('body').append("<div id='add-project-workflow'></div>");
			}
		   	$( "#add-project-workflow" ).html(mydata);
			$( "#add-project-workflow" ).dialog({
				  title:"Add Template / Tasks to Workflow",
			      autoOpen: true,
				  resizable: false,
			      width: "80em",
			      height:692,
			      modal: true,
				  buttons: [
			        {
			            text: "Cancel",
			            "title":"Cancel",
			            "class": 'btn btn-primary',
			            click: function() {
						    $( this ).dialog("close");
			            }
			        },
			        {
			            text: "Add",
			            "title":"Add",
			            "class": 'btn btn-primary',
			            click: function() {
							var currrent_tab=$("#wftabs").find("li.ui-tabs-active").find("a").html();
							if(currrent_tab=='Previous Workflow') {
								project_id=$('#previous_workflow_project_id').val();
								if(project_id == ""){
									alert('Please select previous workflow to perform this action.');
								} else {
									loadprevoiusprojectworkflownew(project_id);
								}
							} else if(currrent_tab=='Filter Task Locations') {
								var json_loc="";
								var selKeys;
								$("#location-tree").dynatree("getRoot").visit(function(node) {
									selKeys = $.map(node.tree.getSelectedNodes(), function(node){
										if(node.childList===null)
											return node.data.key.toString();
									});
									if(node.isSelected()) {
										if(json_loc=='')
											json_loc=node.data.key;
										else	
											json_loc=json_loc+', '+node.data.key;
									}
								});
								locationids=JSON.stringify(selKeys);
								$('#filter_team_location').val(json_loc);
					            $('#add-project-workflow').dialog('close');
								$.ajax({
									url:baseUrl + "project/savelocation",
									data:{loc:locationids},
									type:"post",
									beforeSend:function() {
										showLoader();
									},
									success:function(mydata) {
										hideLoader();
										AddPorjectWorkflow(flag);
									}
								});
							} else {
								var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
								var con = $('#service_task_container');
								var priority = $('#taskinstruct-task_priority').val();
								addservicetaskprocess(media, con,  priority, 0);
							}
			            }
			        }
			    ],
			    close: function() {
			    	$(this).dialog('destroy').remove();
			    }
			});
		},
		complete:function(){
			$('#add-project-workflow input').customInput();
		}
	});
}
function addservicetaskprocess(media, con,  priority, t) {
    setTimeout(function () {
        if (t == 0) {
            addintoworkflowbuilder(media, con,  priority);
        } else {
            if (media != "") {
				if($('#taskinstruct-task_duedate').val() != '' && $('#taskinstruct-task_timedue').val()!=''){
					checktotalhours();
				} else {
                	calculateprojectedtime(media, con,  priority, "add");
				}
            }
        }
        if (t == 0) {
            t++;
            addservicetaskprocess(media, con,  priority, t);
        }
    }, 1000);
}
function addintoworkflowbuilder(media, con,  priority){
	if(!$('#service_task_container #first_row').length){
		$('#service_task_container').prepend('<li id="first_row"><div class="pull-left"><input type="checkbox"  id="chkall" name="checkall_workflow" class="left checkall_workflow"><label title="Select All" class="pull-left servicetask-info-label" for="chkall"><span class="sr-only">Select All</span></label></div><div class="pull-left"><span class="pull-left servicetask-info-label">Select All</span></div> <span class="est_time_header">SLA/Est Time</span><div class="icon-set pull-right"><a href="javascript:void(0);" onclick="AddManEstimatedTime();" title="Bulk Add Estimated Time"><span class="fa fa-clock-o text-primary"></span><span class="sr-only">Bulk Add Estimated Time</span></a><a class="" href="javascript:void(0);" onclick="removealltasks();" ><span title="Bulk Delete" class="fa fa-close text-primary"></span><span class="sr-only">Bulk Delete</span></a></div></li>');
	}else{
		if($('#service_task_container #first_row').length){
			if($('#service_task_container #first_row').html()==''){
				$('#service_task_container #first_row').html('<div class="pull-left"><input type="checkbox"  id="chkall" name="checkall_workflow" class="left checkall_workflow"><label title="Select All" class="pull-left servicetask-info-label" for="chkall"><span class="sr-only">Select All</span></label></div><div class="pull-left"><span class="pull-left servicetask-info-label">Select All</span></div> <span class="est_time_header">SLA/Est Time</span><div class="icon-set pull-right"><a href="javascript:void(0);" onclick="AddManEstimatedTime();" title="Bulk Add Estimated Time"><span class="fa fa-clock-o text-primary"></span><span class="sr-only">Bulk Add Estimated Time</span></a><a class="" href="javascript:void(0);" onclick="removealltasks();" ><span title="Bulk Delete" class="fa fa-close text-primary"></span><span class="sr-only">Bulk Delete</span></a></div>');
			}
		}
	}

	var currrent_tab=$("#wftabs").find("li.ui-tabs-active").find("a").html();
	if(currrent_tab=='Workflow Templates')
		var wfstask = $('#temp_service_task').val();
	else	
		var wfstask = $('#wftasks_service_task').val();

	var Url = baseUrl + "project/get-servicetask-json";

	var case_id = jQuery('#case_id').val();
	var ids=$('#filter_team_location').val();
	var request_type = $("#projectReqType").val();
	$.ajax({
		type: "POST",
		url: Url,
		data: {'case_id':case_id,'loc_ids':ids,'request_type':request_type,'currrent_tab':currrent_tab,'wfstask':wfstask},
		beforeSend:function(){
			showLoader();
	    },
	    success: function (data) {
			var json_data = $.parseJSON(data);
			$(json_data).each(function(index,val){

				var est_timeval 	 = 0;
				var service_task_id  = val.servicetask_id;
				var service_location = val.team_loc;
				var teamservice_id   = val.teamservice_id;
				var service          = val.service_task;
				var teamservice_task = val.service_name;
				var hidden = '<input type="hidden" name="ServiceteamLoc1[' + service_task_id + '][]" class="sloc_' + service_task_id + '" id="stl_' + service_task_id + '" value="' + service_location + '"/>';
				hdn_service_logic_id=  "<input type='hidden' name='hdn_service_logic[" + service_task_id + "]' id='hdn_service_logic_" + service_task_id + "' value='" + est_timeval.toFixed(2) + "'/>";
				var chk = '<input aria-labelledby="lbl_workflow_servicetasks_'+service_task_id+'" type="checkbox" data-teamservice_id="'+teamservice_id+'" value="' + service_task_id + '" id="workflow_servicetasks_'+service_task_id+'" name="Service_tasks[]" class="left aaa"><label id="lbl_workflow_servicetasks_'+service_task_id+'" for="workflow_servicetasks_'+service_task_id+'">&nbsp;<span class="sr-only">Select Service task '+service+' of Teamservice '+teamservice_task+'</span></label>' + hidden;
				if($('#service_task_container').find('#'+service_task_id).length == 0){
					$('#service_task_container').append('<li class="li_'+service_task_id+' clear" id="'+service_task_id+'"><div class="pull-left">'+chk+'</div><span title="ServiceTask" class="pull-left servicetask-info-label" for="Service_tasks_'+service_task_id+'"><span class="sername_div">'+teamservice_task+' - '+service+'</span></span>' + hdn_service_logic_id + '<input type="text" readonly="readonly" class="right est_time est-time-read-only" name="Est_times[' + service_task_id + ']" id="est_time_' + service_task_id + '" value="" aria-label="Estimated Time"><div class="icon-set pull-right"><a class=" handel_sort" href="javascript:void(0);"  title="Move"><span class="fa fa-arrows text-primary"></span><span class="sr-only">Move</span></a><a  href="javascript:void(0);" onclick="AddServiceManEstimatedTime('+service_task_id+');" ><span class="fa fa-clock-o text-primary" title="Add Estimated Time"></span><span class="sr-only">Add Estimated Time</span></a><a  href="javascript:void(0)" onclick="removestask('+service_task_id+');"><span class="sr-only">Remove task</span><span title="Delete" class="fa fa-close text-primary"></span></a></div></li>');
				}
			});
			hideLoader();
		},
		complete:function(){
			$('#service_task_container input').customInput();
			updatecustom_sort();
			$('#add-project-workflow').dialog('close');
		}
	});	
	
	/*$('.service_checkbox:checked').each(function(){
		var est_timeval = 0;
		service_task_id=$(this).attr('rel');
		service_location=$(this).data('loc');
		var teamservice_id=$(this).data('teamservice_id');
		var service = $(this).data('service');
		var teamservice_task = $(this).data('teamservice');
		var hidden = '<input type="hidden" name="ServiceteamLoc1[' + service_task_id + '][]" class="sloc_' + service_task_id + '" id="stl_' + service_task_id + '" value="' + service_location + '"/>';
		hdn_service_logic_id=  "<input type='hidden' name='hdn_service_logic[" + service_task_id + "]' id='hdn_service_logic_" + service_task_id + "' value='" + est_timeval.toFixed(2) + "'/>";
		var chk = '<input aria-labelledby="lbl_workflow_servicetasks_'+service_task_id+'" type="checkbox" data-teamservice_id="'+teamservice_id+'" value="' + service_task_id + '" id="workflow_servicetasks_'+service_task_id+'" name="Service_tasks[]" class="left aaa"><label id="lbl_workflow_servicetasks_'+service_task_id+'" for="workflow_servicetasks_'+service_task_id+'">&nbsp;<span class="sr-only">Select Service task '+service+' of Teamservice '+teamservice_task+'</span></label>' + hidden;
		if($('#service_task_container').find('#'+service_task_id).length == 0){
			$('#service_task_container').append('<li class="li_'+service_task_id+' clear" id="'+service_task_id+'"><div class="pull-left">'+chk+'</div><span title="ServiceTask" class="pull-left servicetask-info-label" for="Service_tasks_'+service_task_id+'"><span class="sername_div">'+teamservice_task+' - '+service+'</span></span>' + hdn_service_logic_id + '<input type="text" readonly="readonly" class="right est_time est-time-read-only" name="Est_times[' + service_task_id + ']" id="est_time_' + service_task_id + '" value="" aria-label="Estimated Time"><div class="icon-set pull-right"><a class=" handel_sort" href="javascript:void(0);"  title="Move"><span class="fa fa-arrows text-primary"></span><span class="sr-only">Move</span></a><a  href="javascript:void(0);" onclick="AddServiceManEstimatedTime('+service_task_id+');" ><span class="fa fa-clock-o text-primary" title="Add Estimated Time"></span><span class="sr-only">Add Estimated Time</span></a><a  href="javascript:void(0)" onclick="removestask('+service_task_id+');"><span class="sr-only">Remove task</span><span title="Delete" class="fa fa-close text-primary"></span></a></div></li>');
		}
		//"<li id=" + service_task_id + " class='li_" + service_task_id + " clear'>" + chk + "<span style='width:120px!important;'><span class='sername_div'>"+teamservice_task+' - '+service+ "</span><input type='text' class='right est_time' name='Est_times[" + service_task_id + "]' id='est_time_" + service_task_id + "' value=''>" + hdn_service_logic_id + "</span><ul id='media_" + service_task_id + "' style='padding: 5px;'></ul></li>"
	});
	*/
}
$(document).on('change','.checkall_workflow',function(){
	if(this.checked){
		$('#service_task_container li input[type="checkbox"]').prop('checked',true);
		$('#service_task_container li input[type="checkbox"]').next('label').addClass('checked');
	}else{
		$('#service_task_container li input[type="checkbox"]').prop('checked',false);
		$('#service_task_container li input[type="checkbox"]').next('label').removeClass('checked');
	}
});
function calculateEstByWorkingHours(workingHours){
	var chk_arr=new Array();
	var service_arr=new Array();
	var totalest_times=0;
	var totalest_times1=0;
	var totalest_times2=0;
	
	$("#service_task_container .est_time").each(function () {
		if($(this).val()!=0 && $(this).val()!="") {
			totalest_times=totalest_times + parseFloat($(this).val()); // all
			var tid = $(this).closest("li").attr('id');
			if($('#hdn_service_logic_'+tid).val() != 0.00) {
				totalest_times1=totalest_times1 + parseFloat($(this).val()); //service_logic
			} else {
				totalest_times2=totalest_times2 + parseFloat($(this).val()); //not service_logic
			}
		}
	});
	
	totalest_times = (totalest_times).toFixed(2);
	totalest_times1 = (totalest_times1).toFixed(2);
	totalest_times2 = (totalest_times2).toFixed(2);
	var totalslack_times3 = '0.00';
	if($('input[name="TaskInstruct[total_slack_hours]"]').length > 0)
		totalslack_times3 = $('input[name="TaskInstruct[total_slack_hours]"]').val();

	//console.log(totalest_times);
	//console.log(totalest_times1);
	//console.log(totalest_times2);
	
	var floatVal = totalest_times.split('.');
	if(floatVal.length > 1 && floatVal[1]!=0) {
		if(floatVal[1] > 0 && floatVal[1] <= 50){
			var totalest_times = floatVal[0]+".50";
		} else if (floatVal[1] > 50 && floatVal[1] <= 99) {
			var totalest_times = parseFloat(parseInt(floatVal[0])+1);
		}
	}
	
	var floatVal = totalest_times1.split('.');
	if(floatVal.length > 1 && floatVal[1]!=0) {
		if(floatVal[1] > 0 && floatVal[1] <= 50){
			var totalest_times1 = floatVal[0]+".50";
		} else if (floatVal[1] > 50 && floatVal[1] <= 99) {
			var totalest_times1 = parseFloat(parseInt(floatVal[0])+1);
		}
	}
	
	var floatVal = totalest_times2.split('.');
	if(floatVal.length > 1 && floatVal[1]!=0) {
		if(floatVal[1] > 0 && floatVal[1] <= 50){
			var totalest_times2 = floatVal[0]+".50";
		} else if (floatVal[1] > 50 && floatVal[1] <= 99) {
			var totalest_times2 = parseFloat(parseInt(floatVal[0])+1);
		}
	}

	var floatVal = totalslack_times3.split('.');
	if(floatVal.length > 1 && floatVal[1]!=0) {
		if(floatVal[1] > 0 && floatVal[1] <= 50){
			var totalslack_times3 = floatVal[0]+".50";
		} else if (floatVal[1] > 50 && floatVal[1] <= 99) {
			var totalslack_times3 = parseFloat(parseInt(floatVal[0])+1);
		}
	}
	
	totalest_times = parseFloat(totalest_times);
	totalest_times1 = parseFloat(totalest_times1);
	totalest_times2 = parseFloat(totalest_times2);
	totalslack_times3 = parseFloat(totalslack_times3);

	//console.log(totalest_times);
	//console.log(totalest_times1);
	//console.log(totalest_times2);
	$('#totalHours').val(totalest_times);
	var sladays=0;
	if(totalest_times1 > 0){
		str="<strong>SLA Estimated Time </strong>";
		sladays=0;
		var htmlslatotal = '<input id="txt_esthours" type="hidden" value="'+totalest_times1+'">'; 
		
		if(totalest_times1 > workingHours)
			sladays=parseInt(totalest_times1/workingHours);
		
		if(sladays > 0){
			var hrs = ((totalest_times1-(sladays*workingHours)).toFixed(2));
		}else{
			var hrs = (totalest_times1.toFixed(2));
		}
		fraction = hrs - parseInt(hrs);
		if(fraction > 0){
			roundfraction = parseInt(fraction*100);
			if(roundfraction > 60){
				hrs = (hrs - parseFloat(fraction));// + 1;
				hrs = hrs + parseInt(1);
			}
		}
		var hrs = (parseFloat(hrs).toFixed(1));
		if(sladays > 0){
			str= str + " " +sladays + " D " + hrs + " H";
		}else{
			str= str + " "+ hrs + " H";
		}	
		$('#esttime .left').html(htmlslatotal+str);
		$('#esttime .left').show();
	}else{
		$('#esttime .left').hide();
	}
	var days=0;
	if(totalest_times > 0){
		str="<strong>Projected Project Time </strong>";
		days=0;
		
		var htmlslatotal = '<input id="txt_prohours" type="hidden" value="'+totalest_times+'">'; 
		if(totalest_times > workingHours)
			days=parseInt(totalest_times/workingHours);
		
		if(days > 0){
				var hrs = ((totalest_times-(days*workingHours)).toFixed(2));
		}else{
			var hrs = (totalest_times.toFixed(2));
		}
		fraction = hrs - parseInt(hrs);
		if(fraction > 0){
			roundfraction = parseInt(fraction*100);
			if(roundfraction > 60){
				hrs = (hrs - parseFloat(fraction));// + 1;
				hrs = hrs + parseInt(1);
			}
		}
		var hrs = (parseFloat(hrs).toFixed(1));
		if(days > 0){
			str= str + " " +days + " D " + hrs + " H";
		}else{
			str= str + " 0 D "+ hrs + " H";
		}	
		$('#esttime .projprojecttime_left').html(htmlslatotal+str);
		$('#esttime .projprojecttime_left').show();
	}else{
		$('#esttime .projprojecttime_left').hide();
	}
	var mandays=0;
	if(totalest_times2 > 0)  
	{
		str="<strong>Manual Estimated Time </strong>";
		mandays=0;
		var htmlmantotal = '<input id="txt_manesthours" type="hidden" value="'+totalest_times2+'">'; 
		if(totalest_times2 >= workingHours)
			mandays=parseInt(totalest_times2/workingHours);
		
		if(mandays > 0){
			var hrs = ((totalest_times2-(mandays*workingHours)).toFixed(2));
		}else{
			var hrs = (totalest_times2.toFixed(2));
		}
		fraction = hrs - parseInt(hrs);
		if(fraction > 0){
			roundfraction = parseInt(fraction*100);
			if(roundfraction > 60){
				hrs = (hrs - parseFloat(fraction));// + 1;
				hrs = hrs + parseInt(1);
			}
		}
		var hrs = parseFloat(hrs).toFixed(1);
		if(mandays > 0) 
		{
			str= str + " " +mandays + " D " + (hrs) + " H";
		} 
		else 
		{
			str= str + " 0 D "+ (hrs) + " H";
		}	
		$('#esttime .manestleft').html(htmlmantotal+""+str);
		$('#esttime .manestleft').show();
		
		/* Start : Manage Again Projected Time */
		str="<strong>Projected Project Time </strong>";
		totaldays=0;
		var totaltimes = parseFloat(totalest_times2) + (parseInt(sladays*workingHours) + ((totalest_times1-(sladays*workingHours))));
		var htmlslatotal = '<input id="txt_prohours" type="hidden" value="'+totaltimes+'">'; 
		
		if(totaltimes >= workingHours)
			totaldays=parseInt(totaltimes/workingHours);
		
		if(totaldays > 0){
			var hrs = ((totaltimes-(totaldays*workingHours)).toFixed(2));
		}else{
			var hrs = (totaltimes.toFixed(2));
		}
		fraction = hrs - parseInt(hrs);
		if(fraction > 0){
			roundfraction = parseInt(fraction*100);
			if(roundfraction > 60){
				hrs = (hrs - parseFloat(fraction));// + 1;
				hrs = hrs + parseInt(1);
			}
		}
		var hrs = (parseFloat(hrs).toFixed(1));
		if(totaldays > 0){
			str = str + " " + totaldays + " D " + hrs + " H";
		}else{
			str = str + " 0 D "+ hrs + " H";
		}	
		$('#esttime .projprojecttime_left').html(htmlslatotal+str);
		$('#esttime .projprojecttime_left').show();
		/* End : Manage Again Projected Time */
	}
	else
	{
		$('#esttime .manestleft').hide();
	}


	if(totalslack_times3 > 0)  
	{
		str="<strong>Slack Time </strong>";
		mandays=0;
		var htmlmantotal = '<input id="txt_slackhours" type="hidden" value="'+totalslack_times3+'">'; 
		if(totalslack_times3 >= workingHours)
			mandays=parseInt(totalslack_times3/workingHours);
		
		if(mandays > 0){
			var hrs = ((totalslack_times3-(mandays*workingHours)).toFixed(2));
		}else{
			var hrs = (totalslack_times3.toFixed(2));
		}
		fraction = hrs - parseInt(hrs);
		if(fraction > 0){
			roundfraction = parseInt(fraction*100);
			if(roundfraction > 60){
				hrs = (hrs - parseFloat(fraction));// + 1;
				hrs = hrs + parseInt(1);
			}
		}
		var hrs = parseFloat(hrs).toFixed(1);
		if(mandays > 0) 
		{
			str= str + " " +mandays + " D " + hrs + " H";
		} 
		else 
		{
			str= str + " 0 D "+ hrs + " H";
		}	
		$('#esttime .slackleft').html(htmlmantotal+""+str);
		if(parseFloat(totalest_times2) + parseFloat(totalest_times1) > 0)
		{
			$('#esttime .slackleft').show();
		}
		else
		{
			$('#esttime .slackleft').hide();
		}
		
		/* Start : Manage Again Projected Time */
		str="<strong>Projected Project Time </strong>";
		totaldays=0;
		var totaltimes = parseFloat(totalslack_times3) + parseFloat(totalest_times2) + (parseInt(sladays*workingHours) + ((totalest_times1-(sladays*workingHours))));
		var htmlslatotal = '<input id="txt_prohours" type="hidden" value="'+totaltimes+'">'; 
		
		if(totaltimes >= workingHours)
			totaldays=parseInt(totaltimes/workingHours);
		
		if(totaldays > 0){
			var hrs = ((totaltimes-(totaldays*workingHours)).toFixed(2));
		}else{
			var hrs = (totaltimes.toFixed(2));
		}
		fraction = hrs - parseInt(hrs);
		if(fraction > 0){
			roundfraction = parseInt(fraction*100);
			if(roundfraction > 60){
				hrs = (hrs - parseFloat(fraction));// + 1;
				hrs = hrs + parseInt(1);
			}
		}
		var hrs = (parseFloat(hrs).toFixed(1));
		if(totaldays > 0){
			str = str + " " + totaldays + " D " + hrs + " H";
		}else{
			str = str + " 0 D "+ hrs + " H";
		}	
		$('#esttime .projprojecttime_left').html(htmlslatotal+str);
		if(parseFloat(totalest_times2) + parseFloat(totalest_times1) > 0)
		{
			$('#esttime .projprojecttime_left').show();
		} else {
			$('#esttime .projprojecttime_left').hide();
		}
		/* End : Manage Again Projected Time */
	}
	else
	{
		$('#esttime .slackleft').hide();
	}
	
	//$('#esttime .slackleft').html("");
	//$('#esttime .slackleft').hide();
	/*if(totalslack_times3 == 0){
		$('#task_duedate_by_st').val('');
    	$('#task_duetime_by_st').val('');
	}*/
	$('#loding').hide();
}

function getEstimatedDateTime(workinghours,type)
{
	var totalest_timestotal = 0;
	var service = [];
	$("#service_task_container .est_time").each(function () {
		if($(this).val()!=0 && $(this).val()!=""){
			if(!$(this).hasClass('est_sys')){
				totalest_timestotal = totalest_timestotal + parseFloat($(this).val());
			}
			var tid = $(this).closest("li").attr('id');
			var serviceTime = {'service_id':tid,'logic_id':$('#hdn_service_logic_'+tid).val(),'time':$(this).val()};
			service.push(serviceTime);
		}
	});
	
	if(totalest_timestotal > 0){
		var current_date = '';
		var current_time = '';
		var taskId = '';
		var Url = baseUrl + "project/get-hours-manual-projected-time";

		if($('#taskId').length > 0 && type == 'estchanged'){
			var taskId = $('#taskId').val();
			var current_date = $('input[name="TaskInstruct[task_duedate]"]').val();
    		var current_time = $('select[name="TaskInstruct[task_timedue]"]').val();
    		Url = httpPath + "task/getchangedHoursManualProjectedTime";
		}
		var totalhours = totalest_timestotal;
		
		var slackhours = 0;
		if($('#txt_slackhours').length > 0){
			var slackhours = $('#txt_slackhours').val();
		}
    	$.ajax({
            type: "POST",
            url: Url,
            data: {'current_date':current_date,'current_time':current_time,'total_hours':totalhours,'service':service,'taskId':taskId, 'slackhours':slackhours},
            success: function (data) {
            	if(data.replace(/\s+/g, '') != 0) {
                	var val = $.parseJSON(data);
                	$('#addestimatedtime').html(val.jquery);
                	if(type == 'load' && $('#task_duedate_by_st').val() != "" && $('#task_duedate_by_st').val() != $('input[name="TaskInstruct[task_duedate]"]').val()){
                		var res = val.due_date.split("/"); 
	                	datePickerController.setRangeLow("taskinstruct-task_duedate", (res[2]+res[0]+res[1]));
                	}
                	
                	if(type != 'load'){
						$('#task_duedate_by_st').val(val.due_date);
						$('#task_duetime_by_st').val(val.due_time);
						if(slackhours > 0){
							$('input[name="TaskInstruct[task_duedate]"]').val(val.slackdate);
							$("#taskinstruct-task_duedate").datepicker("option", "minDate", val.due_date);
							$('select[name="TaskInstruct[task_timedue]"]').val(val.slacktime).trigger('change');
						} else {
							$('input[name="TaskInstruct[task_duedate]"]').val(val.due_date);
							$("#taskinstruct-task_duedate").datepicker("option", "minDate", val.due_date);
							//var res = val.due_date.split("/"); 
							//datePickerController.setRangeLow("taskinstruct-task_duedate", (res[2]+res[0]+res[1]));
							$('select[name="TaskInstruct[task_timedue]"]').val(val.due_time).trigger('change');	
						}
	                	
						var res = val.due_date.split("/"); 
						datePickerController.setRangeLow("taskinstruct-task_duedate", (res[2]+res[0]+res[1]));
                	}
            	} else {
            		if(type!="editinstruction"){
	            		$('#taskinstruct-task_duedate').val('');
	                	$('#taskinstruct-task_timedue').val('');
	                	$('#task_duedate_by_st').val('');
	                	$('#task_duetime_by_st').val('');
            		}
            	}
				checkServiceEmpty();
            }
    	});
	} else if(type != "load") {
		/*$('#Taskindividual_task_duedate').val('');
    	$('#Taskindividual_task_timedue').val('');
    	$('#task_duedate_by_st').val('');
    	$('#task_duetime_by_st').val('');*/		
	} else if(type == 'load') {
		var d = new Date();
		var curr_date = d.getDate();
		var curr_month = parseInt(d.getMonth())+1;
		var curr_year = d.getFullYear();
		if(curr_date < 10){
			curr_date = "0" + curr_date;
		}
		if(curr_month < 10){
			curr_month = "0" + curr_month;
		}
		var dateform = curr_month + "/" + curr_date + "/" + curr_year;
		
		//$("#taskinstruct-task_duedate").datepicker("option", "minDate", dateform);
		//datePickerController.setRangeLow("taskinstruct-task_duedate", "'"+curr_year+curr_month+curr_date+"'");
		datePickerController.setRangeLow("taskinstruct-task_duedate", (curr_year+curr_month+curr_date));
	}
}
function getslackhours() {
		var workingHours = $("#workinghours").val();
        var selected_date = $('input[name="TaskInstruct[task_duedate]"]').val();
        var selected_time = $('select[name="TaskInstruct[task_timedue]"]').val();
        var current_date = $('#task_duedate_by_st').val();
        var current_time = $('#task_duetime_by_st').val();
		
        if (selected_date != "" && selected_time!='') {
            $.ajax({
                type: "POST",
                url: baseUrl + "project/getslackhours",
                data: {'current_date': current_date, 'current_time': current_time, 'adjusted_date': selected_date, 'adjusted_time': selected_time},
                success: function (data) {
                    if (data.replace(/^\s+|\s+$/g, "") != "") {
                        var val = $.parseJSON(data);
                        var str = "<strong>Slack Time </strong> ";
                      
                        var days = val.days;
                        var hours = val.hours;
						$('input[name="TaskInstruct[total_slack_hours]"').val(val.totalhours);
						calculateEstByWorkingHours(workingHours);
                        /*if (days > 0) {
                            str = str + parseInt(days) + " D ";
                        }else{
                        	str = str + " 0 D ";
                        }
                        if (hours > 0) {
                            str = str + parseFloat(hours).toFixed(1) + " H";
                        }else{
                        	str = str + " 0 H";
                        }

						var htmlslacktotal = '<input id="txt_slackhours" type="hidden" value="' + val.totalhours + '">';

                         $('#esttime .slackleft').html(htmlslacktotal+" "+str);
                        if(days == 0 && hours == 0){
							$('#esttime .slackleft').hide();
						}else{
							$('#esttime .slackleft').show();
					   }
                        var prohours = $('#txt_prohours').val();
                        var totalest_times = parseFloat(val.totalhours);
                        var manslatotaltime = prohours;
                        
                        totalest_times = totalest_times + parseFloat(prohours);
                        
                        if (totalest_times > 0){ 
                        	str = "<strong>Projected Project Time </strong>";
                            days = 0;
                            var htmlslatotal = '<input id="txt_prohours" type="hidden" value="' + manslatotaltime + '">';
                            if (totalest_times > workingHours)
                                days = parseInt(totalest_times / workingHours);
                            
                            if(days > 0){
                    			var hrs = ((totalest_times - (days * workingHours)).toFixed(2));
                    		}else{
                    			var hrs = (totalest_times.toFixed(2));
                    		}
                    		fraction = hrs - parseInt(hrs);
                    		if(fraction > 0){
                    			roundfraction = parseInt(fraction*100);
                    			if(roundfraction > 60){
                    				hrs = (hrs - parseFloat(fraction));// + 1;
                    				hrs = hrs + parseInt(1);
                    			}
                    		}
                    		var hrs = (parseFloat(hrs).toFixed(1));
                            if (days > 0)
                            {
                                str = str + " " + days + " D " + hrs + " H";
                            }
                            else
                            {
                                str = str + " 0 D " + hrs + " H";
                            }
                            
                            str = htmlslatotal + "" +str;
                            $('#esttime .projprojecttime_left').html(str);
                            $('#esttime .projprojecttime_left').show();
                        }else{
                            $('#esttime .projprojecttime_left').hide();
                        }*/
                    }
                }
            });
        }
    }
function ClearServices(){
				$("#service_task_container").empty();
		        $('#load_prev').val(0);
		        $('#load_prev_instr_id').val(0);
		        updatecustom_sort();
		        $('#esttime .left').hide();
		        $('#esttime .manestleft').hide();
		        $('#esttime .slackleft').hide();
		        $('#esttime .projprojecttime_left').hide();
		        $('#taskinstruct-task_duedate').val('');
		        var d = new Date();
		        var curr_date = d.getDate();
		        var curr_month = parseInt(d.getMonth()) + 1;
		        var curr_year = d.getFullYear();
		        if (curr_date < 10) {
		            curr_date = "0" + curr_date;
		        }
		        if (curr_month < 10) {
		            curr_month = "0" + curr_month;
		        }
		        var dateform = curr_month + "/" + curr_date + "/" + curr_year;
		        //$("#taskinstruct-task_duedate").datepicker("option", "minDate", dateform);
		        datePickerController.setRangeLow("taskinstruct-task_duedate", (curr_year+curr_month+curr_date));
		        $('#taskinstruct-task_timedue').val('');
		        $('#task_duedate_by_st').val('');
		        $('#task_duetime_by_st').val('');
		        $('#overflow').hide();
}
function FilterLocation(){
	var case_id = jQuery('#case_id').val();
	filter_ids=$('#filter_team_location').val();
	$.ajax({
		url:baseUrl + "project/filterlocation",
		data:{case_id:case_id,filter_ids:filter_ids},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
			   if(!$( "#filterloc-project-workflow" ).length){
					$('body').append("<div id='filterloc-project-workflow'></div>");
				}
			   	$( "#filterloc-project-workflow" ).html(mydata);
				$( "#filterloc-project-workflow" ).dialog({
					  title:"Filter Templates / Tasks by Team Location",
				      autoOpen: true,
					  resizable: false,
					  height:456,
				      width: "50em",
				      modal: true,
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
				            text: "Update",
				            "title":"Update",
				            "class": 'btn btn-primary',
				            click: function() {
							    var locs='';
					            var ids='';
								var locationids=$('#filterloc').val();
								json_loc = jQuery.parseJSON($('#filterloc').val()); 
								$("#location-tree").dynatree("getRoot").visit(function(node){
									if(node.isSelected()) {
										if(locs=='')
											locs=node.data.title;
										else	
											locs=locs+', '+node.data.title;
									}
								});
								/*$('.filter_locs:checked').each(function(){
					            	if(locs==""){
				            			locs=$(this).parent().parent().siblings().html();
				            			ids=$(this).val();
				            		}else{
					            		locs=locs+', '+$(this).parent().parent().siblings().html();
					            		ids=ids+','+$(this).val();
					            	} 	
					            });*/
								if(locs!="")
					            	$('#fl_locs').html("Filtered Location: "+locs);
								else
									$('#fl_locs').html(null);	

					            $('#filter_team_location').val(json_loc);
					            $( this ).dialog( "close" );
								$.ajax({
									url:baseUrl + "project/savelocation",
									data:{loc:locationids},
									type:"post",
									success:function(mydata){
									}
								});
				            }
				        }
				    ],
				    close: function() {
				    	$(this).dialog('destroy').remove();
				    }
				});
		},complete:function(){
			//$('#filterloc-project-workflow input').customInput();
		}
	});
}
function LoadPreviousNew(case_id){
	$.ajax({
		url:baseUrl + "project/load-previous-new",
		data:{case_id:case_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
			hideLoader();
			$('#tabs-loadprev').html(mydata);
		}
	});
}
function LoadPrevious(case_id){
	$.ajax({
		url:baseUrl + "project/load-previous",
		data:{case_id:case_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
			   if(!$( "#loadprev-project-workflow" ).length){
					$('body').append("<div id='loadprev-project-workflow'></div>");
				}
			   	$( "#loadprev-project-workflow" ).html(mydata);
				$( "#loadprev-project-workflow" ).dialog({
					  title:"Add/Load Previous Workflow",
				      autoOpen: true,
					  resizable: false,
				      width: "80em",
				      height:692,
				      modal: true,
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
								var sel_row = jQuery('#loadprev-project-workflow .grid-view').yiiGridView('getSelectedRows');
				           	    if(!sel_row.length){
				            		alert('Please select a record to perform this action.');
				            	}else if(sel_row.length > 1){
				            		alert('Please select at least 1 record to perform this action.');
					            }else{
					            	loadprevoiusprojectworkflow(sel_row);
						        } 
					        }
				        }
				    ],
				    close: function() {
				    	$(this).dialog('destroy').remove();
				    }
				});
	    },complete:function(){
			$('#loadprev-project-workflow input').customInput();
		}
	});
}

function loadprevoiusprojectworkflownew(project_id){
	$.ajax({
		url:baseUrl + "project/load-previous-project-workflow&project_id="+project_id,
		type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	$('#service_task_container').html(mydata);
	    	$('#load_prev_project_id').val(project_id);
	    	$('#add-project-workflow').dialog('close');
	    	$('#service_task_container input').customInput();
	    	updatecustom_sort();
	    	setTimeout(function () {
                var con = $('#service_task_container');
                var priority = $('#taskinstruct-task_priority').val();
                var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
                
                //calculateprojectedtime(media, con,  priority, "add");
				checktotalhours();

                hideLoader();
            }, 1000);
            
	    }
	});
}

function loadprevoiusprojectworkflow(project_id){
	$.ajax({
		url:baseUrl + "project/load-previous-project-workflow&project_id="+project_id,
		type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	$('#service_task_container').html(mydata);
	    	$('#load_prev_project_id').val(project_id[0]);
	    	$('#loadprev-project-workflow').dialog('close');
	    	$('#service_task_container input').customInput();
	    	updatecustom_sort();
	    	setTimeout(function () {
                var con = $('#service_task_container');
                var priority = $('#taskinstruct-task_priority').val();
                var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
                
                //calculateprojectedtime(media, con,  priority, "add");
				checktotalhours();

                hideLoader();
            }, 1000);
            
	    }
	});
}
function updatecustom_sort(){
	 var liIds = "";
	 $('#service_task_container li').each(function(i,n) {
		 	if($(this).attr('id') !="0"){
		 		if(liIds==""){
		 			liIds=$(this).attr('id');
		 		}else{
		 			liIds+=','+$(this).attr('id');
		 		}
		 	}
	});
	$('#service_custom_sort').val(liIds);
}
function AddServiceManEstimatedTime(servicetask_id){
	var workinghours = $('#workinghours').val();
	var found=false;
	var fonund_ck=false;
	var service_task_id="";
	var i=0;
	var chk_arr=new Array();
	var service_arr=new Array();
	var totalest_times=0;
	var no_apply=new Array();
	var islogic = 0;
	if($('#hdn_service_logic_'+servicetask_id).val() != 0.00 && $('#hdn_service_logic_'+servicetask_id).val() != ''){
		islogic = 1;
	} else {
		if($("#est_time_"+servicetask_id).val()!="")
			chk_arr.push($("#est_time_"+servicetask_id).val());
			service_arr.push(servicetask_id);
			no_apply.splice( no_apply.indexOf(servicetask_id), 1 );//delete selected element
			service_task_id = servicetask_id;
	}
	if(islogic == 1){
		//alert('One/All of your selected service Task has been calculated by SLA logic \n-Please select other Service Task');
		alert('Please select Tasks, which are not tied to SLA  Estimated Time, to add Manual Estimated Time.');
		return false;
	}
	else{
	  	 $.ajax({
			    type: "POST",
			    data:{"stask_id":service_task_id,"val":$('#est_time_'+service_task_id).val()},
				url: baseUrl + "project/addestime",
				cache: false,
				beforeSend:function(){
					showLoader();
			    },
		        success:function(mydata){
	      			hideLoader();
				    if(!$( "#estimated-task-time" ).length){
						$('body').append("<div id='estimated-task-time'></div>");
					}
				   	$( "#estimated-task-time" ).html(mydata);
					$( "#estimated-task-time" ).dialog({
						  title:"Add Estimated Time",
					      autoOpen: true,
						  resizable: false,
					      width: "40em",
					      modal: true,
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
					            text: "Update",
					            "title":"Update",
					            "class": 'btn btn-primary',
					            click: function() {
					            	    var st_id=$("#stask_id").val();
							  			var est_timeval=$('#estime').val();
							  			if(est_timeval <= 0){
							  				alert('Please enter a number greater than or equal to 1.');
											$('#estime').val(null);
											return false;
										}
							  			if(service_arr.length>1 && est_timeval>0)
							  				est_timeval=(est_timeval/service_arr.length).toFixed(2);
							  			
							  			var est_timeval = new Number(est_timeval);
							  			for(var i = 0; i < service_arr.length; i++)
							  			{
							  				if(parseInt(est_timeval)>0)
							  				{
							  					$('#est_time_'+service_arr[i]).removeAttr('value');
							  					$('#est_time_'+service_arr[i]).attr('value',est_timeval.toFixed(2));
							  					$('#est_time_'+service_arr[i]).removeClass('est_sys');
							  					$('#est_time_'+service_arr[i]).removeClass("grey");
							  					//.val(null).val(est_timeval);
							  				}
							  				else
							  				{
							  					est_timeval = 0.00;
							  					$('#est_time_'+service_arr[i]).removeAttr('value');
							  					$('#est_time_'+service_arr[i]).attr('value',est_timeval.toFixed(2));
							  					$('#est_time_'+service_arr[i]).removeClass('est_sys');
							  					$('#est_time_'+service_arr[i]).addClass("grey");
							  					//.val(null).val(est_timeval);
							  				}
							  			}
							  			
							  			/*if($("#est_time_"+service_task_id).val().replace(/\s/g, '')=="" || $("#est_time_"+service_task_id).hasClass('est_sys')){
							  				no_apply.push($(this).val());
							  			}
							  			$("#service_task_container .est_time").each(function () {
							  				if($(this).val()!=0 && $(this).val()!=""){
							  					var id = $(this).attr('id').split("_");
							  					var service_id = id[2];
							  					//console.log($('#hdn_service_logic_'+service_id).val());
							  					if($('#hdn_service_logic_'+service_id).val() == '0.00' || $('#hdn_service_logic_'+service_id).val() == ''){
							  						//console.log($(this).val());
							  						totalest_times=totalest_times + parseFloat($(this).val());
							  					}
							  				}
							  			});
							  			
							  			if(totalest_times > 0) 
							  			{
							  				str="<strong>Manual Estimated Time </strong>";
							  				//alert('2');
							  				days=0;
							  				var htmlmantotal = '<input id="txt_prohours" type="hidden" value="'+totalest_times+'">'; 
							  				if(totalest_times > workinghours)
							  					days=parseInt(totalest_times/workinghours);
							  				
							  				if(days > 0){
							  					var hrs = ((totalest_times-(days*workinghours)).toFixed(2));
							  				}else{
							  					var hrs = (totalest_times.toFixed(2));
							  				}
							  				fraction = hrs - parseInt(hrs);
							  				if(fraction > 0){
							  					roundfraction = parseInt(fraction*100);
							  					if(roundfraction > 60){
							  						hrs = (hrs - parseFloat(fraction));// + 1;
							  						hrs = hrs + parseInt(1);
							  					}
							  				}
							  				
							  				if(days > 0)
							  				{
							  					str= str + " " +days + " D " + parseInt(hrs) + " H";
							  				}
							  				else
							  				{
							  					str= str + " 0 D "+ parseInt(hrs) + " H";
							  				}	
							  				$('#esttime .manestleft').html(htmlmantotal+" "+str);
							  				$('#esttime .manestleft').show();
							  			}
							  			else
							  			{
							  				$('#esttime .manestleft').hide();
							  			}
							  			
							  			//calculate projected and over time
							  			var projected_hours=$('#txt_prohours').val();
							  			
							  			if(projected_hours!="")
							  			{
						  					//new logic to apply projected hrs accross all remaining tasks
						  					if(no_apply.length > 0)
						  					{
						  						var totalest_times=0;	
						  						$("#service_task_container .est_time").each(function () {
						  			  				if($(this).val()!=0 && $(this).val()!="" && !$(this).hasClass('est_sys'))
						  			  					totalest_times=totalest_times + parseFloat($(this).val());
						  			  			});
						  					}
						  					newprojected=(projected_hours - totalest_times);
						  					
						  					var est_timeval = new Number((newprojected/no_apply.length).toFixed(2));
								  			for(var i = 0; i < no_apply.length; i++)
								  			{
								  				if(parseInt(est_timeval)>0)
								  				{
								  					$('#est_time_'+no_apply[i]).removeAttr('value');
								  					$('#est_time_'+no_apply[i]).attr('value',est_timeval.toFixed(2));
								  					$('#est_time_'+no_apply[i]).addClass('est_sys');
								  					//.val(null).val(est_timeval);
								  				} else {
								  					var est_timeval = 0.00;
								  					$('#est_time_'+no_apply[i]).removeAttr('value');
								  					$('#est_time_'+no_apply[i]).attr('value',est_timeval.toFixed(2));
								  					$('#est_time_'+no_apply[i]).addClass('est_sys');
								  				}
								  			}
								  			var totalest_times_new=0;
								  			var totalest_times_new1 = 0;
								  			$("#service_task_container .est_time").each(function () {
								  				if($(this).val()!=0 && $(this).val()!="") {
								  					if(!$(this).hasClass('est_sys')) {
								  						var id = $(this).attr('id').split("_");
									  					var service_id = id[2];
									  					//console.log($('#hdn_service_logic_'+service_id).val());
									  					if($('#hdn_service_logic_'+service_id).val() == '0.00' || $('#hdn_service_logic_'+service_id).val() == '') {
									  						totalest_times_new=totalest_times_new + parseFloat($(this).val());
									  					}
									  					totalest_times_new1=totalest_times_new + parseFloat($(this).val());
								  					}
								  				}
								  			});
								  			
								  			if(totalest_times_new > 0) 
								  			{
								  				str="<strong>Manual Estimated Time </strong>";
								  				//alert('3');
								  				days=0;
								  				var htmlmantotal = '<input id="txt_manesthours" type="hidden" value="'+totalest_times_new+'">'; 
								  				if(totalest_times_new > workinghours)
								  					days=parseInt(totalest_times_new/workinghours);
								  			
								  				if(days > 0){
								  					var hrs = ((totalest_times_new-(days*workinghours)).toFixed(2));
								  				}else{
								  					var hrs = (totalest_times_new.toFixed(2));
								  				}
								  				fraction = hrs - parseInt(hrs);
								  				if(fraction > 0){
								  					roundfraction = parseInt(fraction*100);
								  					if(roundfraction > 60){
								  						hrs = (hrs - parseFloat(fraction));// + 1;
								  						hrs = hrs + parseInt(1);
								  					}
								  				}
								  				
								  				if(days > 0)
								  				{
								  					str= str + " " +days + " D " + parseInt(hrs) + " H";
								  				}
								  				else
								  				{
								  					str= str + " 0 D "+ parseInt(hrs) + " H";
								  				}	
								  				$('#esttime .manestleft').html(htmlmantotal+""+str);
								  				$('#esttime .manestleft').show();
								  			}
								  			else
								  			{
								  				$('#esttime .manestleft').hide();
								  			}
								  			//end of new code
								  			var totalest_timestotal=0;
								  			$("#service_task_container .est_time").each(function () {
								  				if($(this).val()!=0 && $(this).val()!=""){
								  					if(!$(this).hasClass('est_sys'))
								  						totalest_timestotal=totalest_timestotal + parseFloat($(this).val());
								  				}
								  			});
								  			
								  			if(totalest_timestotal > 0){
					  							calculateEstByWorkingHours(workinghours);
					  							if($('#taskId').length > 0) {
					  								getEstimatedDateTime(workinghours,'estchanged');
					  							} else {
					  								getEstimatedDateTime(workinghours,"manest");
						  						}
											}
							  			} else {
							  				if(totalest_timestotal > 0){
							  					if($('#taskId').length > 0) {
					  								getEstimatedDateTime(workinghours,'estchanged');
					  							} else {
					  								getEstimatedDateTime(workinghours,"manest");
					  							}
								  			}		  				
							  			}*/

										getEstimatedDateTime(workinghours,'manest');

							  			$('#is_change_form_main').val('1'); // change flag
							  			$(this).dialog( "close" );
							  			$("#service_task_container input:checkbox").attr("checked",false);
								}
					        }
					    ],
					    close: function() {
					    	$(this).dialog('destroy').remove();
					    }
					});
					$("#estime").focus();
		        }
	      });
	}
}
function AddManEstimatedTime()
{
	var workinghours = $('#workinghours').val(); 
	var found=false;
	var fonund_ck=false;
	var service_task_id="";
	var i=0;
	$('#service_task_container li').each(function(i,n) {
	    if($(this).attr('id')!=undefined){
	    	found=true;
	    }
	    chk=($(this).find('input:checkbox'))
	    if($(chk).prop("checked")){
	    	found=true;
	    	fonund_ck=true;
	    }
	});
	var chk_arr=new Array();
	var service_arr=new Array();
	var totalest_times=0;
	var no_apply=new Array();
	var islogic = 0;
	$("#service_task_container input:not(.checkall_workflow):checkbox:checked ").each(function () {
		i++;
		if($('#hdn_service_logic_'+$(this).val()).val() != 0.00 && $('#hdn_service_logic_'+$(this).val()).val() != ''){
			islogic = 1;
		} else {
			if($("#est_time_"+$(this).val()).val()!="")
			chk_arr.push($("#est_time_"+$(this).val()).val());
			service_arr.push($(this).val());
			no_apply.splice( no_apply.indexOf($(this).val()), 1 );//delete selected element
			if(service_task_id=="")
				service_task_id=$(this).val(); 
			else 
				service_task_id+= "," +$(this).val();
		}
	});
	var allsame=true;
	if(chk_arr.length > 0){
	        for(var i = 1; i < chk_arr.length; i++){
	            if(chk_arr[i] !== chk_arr[0])
	            	allsame=false;
	        }
	} 
	if(!found){
		alert('Please add 1+ Task to perform this action.');
		return false;
	}
	else if(!fonund_ck){
		alert('Please select a record to perform this action.');
		return false;
	}
	else if(islogic == 1){
		alert('Please select Tasks, which are not tied to SLA  Estimated Time, to add Manual Estimated Time.');
		//alert('One/All of your selected service Task has been calculated by SLA logic \n-Please select other Service Task');
		return false;
	}
	else{
	  	 $.ajax({
			    type: "POST",
			    data:{"stask_id":service_task_id,"val":$('#est_time_'+service_task_id).val()},
				url: baseUrl + "project/addestime",
				cache: false,
				beforeSend:function(){
					showLoader();
			    },
		        success:function(mydata){
	      			hideLoader();
				    if(!$( "#estimated-task-time" ).length){
						$('body').append("<div id='estimated-task-time'></div>");
					}
				   	$( "#estimated-task-time" ).html(mydata);
					$( "#estimated-task-time" ).dialog({
						  title:"Add Estimated Time",
					      autoOpen: true,
						  resizable: false,
					      width: "40em",
					      modal: true,
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
					            text: "Update",
					            "title":"Update",
					            "class": 'btn btn-primary',
					            click: function() {
										
					            	    var st_id=$("#stask_id").val();
							  			var est_timeval=$('#estime').val();
							  			//if(est_timeval <= 0){$('#estime').val(null);return false;}
										if(est_timeval <= 0){
							  				alert('Please enter a number greater than or equal to 1.');
											$('#estime').val(null);
											return false;
										}
							  			if(service_arr.length>1 && est_timeval>0)
							  				est_timeval=(est_timeval/service_arr.length).toFixed(2);
							  			
							  			var est_timeval = new Number(est_timeval);
							  			for(var i = 0; i < service_arr.length; i++){
							  				if(parseInt(est_timeval)>0){
							  					$('#est_time_'+service_arr[i]).removeAttr('value');
							  					$('#est_time_'+service_arr[i]).attr('value',est_timeval.toFixed(2));
							  					$('#est_time_'+service_arr[i]).removeClass('est_sys');
							  					$('#est_time_'+service_arr[i]).removeClass("grey");
							  					//.val(null).val(est_timeval);
							  				}else{
							  					est_timeval = 0.00;
							  					$('#est_time_'+service_arr[i]).removeAttr('value');
							  					$('#est_time_'+service_arr[i]).attr('value',est_timeval.toFixed(2));
							  					$('#est_time_'+service_arr[i]).removeClass('est_sys');
							  					$('#est_time_'+service_arr[i]).addClass("grey");
							  					//.val(null).val(est_timeval);
							  				}
							  			}
							  			$("#service_task_container input:not(.checkall_workflow):checkbox").each(function () {
							  				if($("#est_time_"+$(this).val()).val().replace(/\s/g, '')=="" || $("#est_time_"+$(this).val()).hasClass('est_sys')){
							  					no_apply.push($(this).val());
							  				}
							  			});
							  			
							  			$("#service_task_container .est_time").each(function () {
							  				if($(this).val()!=0 && $(this).val()!=""){
							  					var id = $(this).attr('id').split("_");
							  					var service_id = id[2];
							  					//console.log($('#hdn_service_logic_'+service_id).val());
							  					if($('#hdn_service_logic_'+service_id).val() == '0.00' || $('#hdn_service_logic_'+service_id).val() == ''){
							  						//console.log($(this).val());
							  						totalest_times=totalest_times + parseFloat($(this).val());
							  					}
							  				}
							  			});
							  			
							  			if(totalest_times > 0) 
							  			{
							  				str="<strong>Manual Estimated Time </strong>";
							  				//alert('4');
							  				days=0;
							  				var htmlmantotal = '<input id="txt_prohours" type="hidden" value="'+totalest_times+'">'; 
							  				if(totalest_times > workinghours)
							  					days=parseInt(totalest_times/workinghours);
							  				
							  				if(days > 0){
							  					var hrs = ((totalest_times_new-(days*workinghours)).toFixed(2));
							  				}else{
							  					var hrs = (totalest_times_new.toFixed(2));
							  				}
							  				fraction = hrs - parseInt(hrs);
							  				if(fraction > 0){
							  					roundfraction = parseInt(fraction*100);
							  					if(roundfraction > 60){
							  						hrs = (hrs - parseFloat(fraction));// + 1;
							  						hrs = hrs + parseInt(1);
							  					}
							  				}
							  				
							  				if(days > 0)
							  				{
							  					str= str + " " +days + " D " + parseInt(hrs) + " H";
							  				}
							  				else
							  				{
							  					str= str + " 0 D "+ parseInt(hrs) + " H";
							  				}	
							  				$('#esttime .manestleft').html(htmlmantotal+" "+str);
							  				$('#esttime .manestleft').show();
							  			}
							  			else
							  			{
							  				$('#esttime .manestleft').hide();
							  			}
							  			
							  			//calculate projected and over time
							  			var projected_hours=$('#txt_prohours').val();
							  			
							  			if(projected_hours!="")
							  			{
						  					//new logic to apply projected hrs accross all remaining tasks
						  					if(no_apply.length > 0)
						  					{
						  						var totalest_times=0;	
						  						$("#service_task_container .est_time").each(function () {
						  			  				if($(this).val()!=0 && $(this).val()!="" && !$(this).hasClass('est_sys'))
						  			  					totalest_times=totalest_times + parseFloat($(this).val());
						  			  			});
						  					}
						  					newprojected=(projected_hours - totalest_times);
						  					
						  					var est_timeval = new Number((newprojected/no_apply.length).toFixed(2));
								  			for(var i = 0; i < no_apply.length; i++)
								  			{
								  				if(parseInt(est_timeval)>0)
								  				{
								  					$('#est_time_'+no_apply[i]).removeAttr('value');
								  					$('#est_time_'+no_apply[i]).attr('value',est_timeval.toFixed(2));
								  					$('#est_time_'+no_apply[i]).addClass('est_sys');
								  					//.val(null).val(est_timeval);
								  				} else {
								  					var est_timeval = 0.00;
								  					$('#est_time_'+no_apply[i]).removeAttr('value');
								  					$('#est_time_'+no_apply[i]).attr('value',est_timeval.toFixed(2));
								  					$('#est_time_'+no_apply[i]).addClass('est_sys');
								  				}
								  			}
								  			var totalest_times_new=0;
								  			var totalest_times_new1 = 0;
								  			$("#service_task_container .est_time").each(function () {
								  				if($(this).val()!=0 && $(this).val()!="") {
								  					if(!$(this).hasClass('est_sys')) {
								  						var id = $(this).attr('id').split("_");
									  					var service_id = id[2];
									  					//console.log($('#hdn_service_logic_'+service_id).val());
									  					if($('#hdn_service_logic_'+service_id).val() == '0.00' || $('#hdn_service_logic_'+service_id).val() == '') {
									  						totalest_times_new=totalest_times_new + parseFloat($(this).val());
									  					}
									  					totalest_times_new1=totalest_times_new + parseFloat($(this).val());
								  					}
								  				}
								  			});
								  			
								  			if(totalest_times_new > 0) 
								  			{
								  				str="<strong>Manual Estimated Time </strong>";
								  				//alert('5');
								  				days=0;
								  				var htmlmantotal = '<input id="txt_manesthours" type="hidden" value="'+totalest_times_new+'">'; 
								  				if(totalest_times_new > workinghours)
								  					days=parseInt(totalest_times_new/workinghours);
								  				
								  				if(days > 0){
								  					var hrs = ((totalest_times_new-(days*workinghours)).toFixed(2));
								  				}else{
								  					var hrs = (totalest_times_new.toFixed(2));
								  				}
								  				fraction = hrs - parseInt(hrs);
								  				if(fraction > 0){
								  					roundfraction = parseInt(fraction*100);
								  					if(roundfraction > 60){
								  						hrs = (hrs - parseFloat(fraction));// + 1;
								  						hrs = hrs + parseInt(1);
								  					}
								  				}
								  				
								  				if(days > 0){
								  					str= str + " " +days + " D " + parseInt(hrs) + " H";
								  				}else{
								  					str= str + " 0 D "+ parseInt(hrs) + " H";
								  				}	
								  				$('#esttime .manestleft').html(htmlmantotal+""+str);
								  				$('#esttime .manestleft').show();
								  			}
								  			else
								  			{
								  				$('#esttime .manestleft').hide();
								  			}
								  			//end of new code
								  			var totalest_timestotal=0;
								  			$("#service_task_container .est_time").each(function () {
								  				if($(this).val()!=0 && $(this).val()!=""){
								  					if(!$(this).hasClass('est_sys'))
								  						totalest_timestotal=totalest_timestotal + parseFloat($(this).val());
								  				}
								  			});
								  			
								  			if(totalest_timestotal > 0){
					  							calculateEstByWorkingHours(workinghours);
					  							if($('#taskId').length > 0) {
					  								getEstimatedDateTime(workinghours,'estchanged');
					  							} else {
					  								getEstimatedDateTime(workinghours,"manest");
						  						}
											}
							  			} else {
							  				if(totalest_timestotal > 0){
							  					if($('#taskId').length > 0) {
					  								getEstimatedDateTime(workinghours,'estchanged');
					  							} else {
					  								getEstimatedDateTime(workinghours,"manest");
					  							}
								  			}		  				
							  			}
							  			$(this).dialog( "close" );
							  			$("#service_task_container input:checkbox").prop("checked",false);
							  			$("#service_task_container input:checkbox").next('label').removeClass('checked');
								}
							}
					    ],
					    close: function() {
					    	$(this).dialog('destroy').remove();
					    }
					});
					$("#estime").focus();
		        }
	      });
	}
}
function removestask(service_id){
    $('#loding').show();
	
	var removed_servicetask_id = $('#service_task_container li[id="'+service_id+'"]').find('input:checkbox').attr('data-teamservice_id');
	//var isTeamserviceExist = $('#service_task_container input:checkbox[data-teamservice_id="'+teamservice_id+'"]').length;

	var priority = $('#taskinstruct-task_priority').val();
	var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
	var services = [];
	var teamserviceSLA = [];
	$('#service_task_container li').each(function () {
		var id = $(this).attr('id');
		var loc = $('#service_task_container li .sloc_' + id).val();
		var teamservice_id = $('#service_task_container li #workflow_servicetasks_' + id).attr('data-teamservice_id');
		var estslahours = $('#service_task_container li #est_time_' + id).val();
		var hdn_service_logic = $('#service_task_container li #hdn_service_logic_' + id).val();
		if(id != 'first_row'){
			var serviceLoc = {'id': id, 'teamservice_id':teamservice_id, 'loc': loc, 'hours':estslahours, 'hdn_service_logic' : hdn_service_logic};
			var teamservices = {'teamservice_id':teamservice_id,'hours':estslahours,'loc': loc};
			var objindex = teamserviceSLA.findIndex(function (element) {
				return element.teamservice_id === teamservice_id && element.loc === loc;
			});
			if(objindex >= 0){
				teamserviceSLA[objindex].hours = parseFloat(teamserviceSLA[objindex].hours) + parseFloat(estslahours);
			} else {
				teamserviceSLA.push(teamservices);
			}
			//console.log(teamserviceSLA);
			services.push(serviceLoc);
		}
	});

	if (services.length > 0) {
		$.ajax({
			type: "POST",
			url: baseUrl + "project/get-total-hours",
			data: {'priority': priority, 'evidence': media, 'service': services, 'teamserviceSLA':teamserviceSLA, 'removed_servicetask_id':removed_servicetask_id},
			beforeSend:function(){showLoader();},
			success: function(response){
				var response = JSON.parse(response);
				var diffHours = response.diffHours;
		
				$('#diffslahours').val(diffHours);
				$('#triggerChange').val(1);

				if(diffHours!=0){
					adjustDateTime();
				}
			},
			complete:function(){hideLoader();}
		});
	}

	var remove_vals = new Array();
    remove_vals.push(service_id);
	$('#service_task_container li[id="'+service_id+'"]').remove().hide('slow');
    if (remove_vals.length > 0){
        for (i = 0; i < remove_vals.length; i++){
            $("input[type='hidden'][name='Est_times[" + remove_vals[i] + "]']").remove();
        }
    }
    calculateEst();
    
	var selected_date = $('#taskinstruct-task_duedate').val();
    var selected_time = $('#taskinstruct-task_timedue').val();
	var media = $('.media:checked').map(function () {return this.value;}).get().join(",");
	var con = $('#service_task_container');
	var priority = $('#taskinstruct-task_priority').val();
	calculateprojectedtime(media, con,  priority, "remove");
	$('#esttime .slackleft').html("");
    $('#esttime .slackleft').hide();
    $('#task_duedate_by_st').val('');
    $('#task_duetime_by_st').val('');
    $('#is_change_form_main').val('1');
    updatecustom_sort();
	if(!$('#service_task_container li').length || $('#service_task_container li').length == 1){
		$('#service_task_container li').empty();
		if($('#duedate').length){
			$('#duedate').val('');
		}
		if($('#taskinstruct-task_duedate').length){
			$('#taskinstruct-task_duedate').val('');
		}
		if($('#duetime').length){
			$('#duetime').val('').trigger('change');
		}
		if($('#taskinstruct-task_timedue').length){
			$('#taskinstruct-task_timedue').val('').trigger('change');
		}

	}



}
function checkServiceEmpty(){
	if(!$('#service_task_container li').length || $('#service_task_container li').length == 1){
		$("#service_task_container").empty();	
		if($('#duedate').length) {
			$('#duedate').val('');
		}
		if($('#taskinstruct-task_duedate').length) {
			$('#taskinstruct-task_duedate').val('');
		}
		if($('#duetime').length) {
			$('#duetime').val('').trigger('change');
		}
		if($('#taskinstruct-task_timedue').length) {
			$('#taskinstruct-task_timedue').val('').trigger('change');
		}
	}
}
function removealltasks(){
	
	var fonund_ck=false;
	$('#service_task_container li').each(function(i,n) {
	    chk=($(this).find('input:checkbox'))
	    if($(chk).prop("checked")){
	    	fonund_ck=true;
	    }
	});
	if(fonund_ck == false){
		alert('Please select a record to perform this action.');
		return false;
	}else{
		$("#service_task_container input:checked").each(function () {
			removestask($(this).val());
		});
		if($("#service_task_container li").length==1){
			$("#service_task_container").empty();	
			if($('#duedate').length){
			$('#duedate').val('');
		}
		if($('#taskinstruct-task_duedate').length){
			$('#taskinstruct-task_duedate').val('');
		}
		if($('#duetime').length){
			$('#duetime').val('').trigger('change');
		}
		if($('#taskinstruct-task_timedue').length){
			$('#taskinstruct-task_timedue').val('').trigger('change');
		}
		}
	}	
}
function calculateEst(){
	var workinghours = $('#workinghours').val(); 
	var chk_arr=new Array();
	var service_arr=new Array();
	var totalest_times=0;
	$("#service_task_container .est_time").each(function () {
			if($(this).val()!=0 && $(this).val()!="")
				totalest_times=totalest_times + parseFloat($(this).val());
		});
		if(totalest_times > 0){
			str="<strong>Estimated Project Time </strong>";
			days=0;
			if(totalest_times > workinghours)
				days=parseInt(totalest_times/workinghours);
			if(days > 0){
				str= str + " " +days + " D " + ((totalest_times-(days*workinghours)).toFixed(2)) + " H";
			}else{
				str= str + " "+(totalest_times.toFixed(2))+ " H";
			}	
			$('#esttime .left').html(str);
			$('#esttime .left').show();
		}else{
			$('#esttime .left').hide();
		}
		//calculate projected and over time
}
