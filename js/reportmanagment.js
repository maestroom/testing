jQuery(document).ready(function(){
	/*Start : Fields Types,Field Operator Report Management*/
	jQuery(".reportModules").click(function(event){		
		var chk_status = checkformstatus(event); // check form edit status 
		if(chk_status == true) {
			var module	=	jQuery(this).data('module');		
			var title	=	jQuery(this).text();		
			setTitle('fa-wrench','<a href="javascript:void(0);" title="Report Management - '+title+'" class="tag-header-red">Report Management - '+title);
			jQuery('.reportModules').removeClass('active');		
			commonAjax(baseUrl +'/report-management/index-'+module,'admin_main_container');				
			jQuery(this).addClass('active');				
		}
	});
	/*End : Fields Types,Field Operator Report Management*/	
	
	/*
	 * To set select All/ Individual while Table Add
	 * */
	$(document).on('click','.check-individual',function(){
		var status = $(this).prop('checked');
		if($('.check-individual:checked').length == $('.check-individual').length){
			$('.check-all').prop('checked', true);
			$('.check-all').next('label').addClass('checked');
		} else {
			$('.check-all').prop('checked', false);
			$('.check-all').next('label').removeClass('checked');
		}
	});
	
});

/**
 * saved report
 */
 function RunSavedReport(){
	 var keys = $('#report-user-saved-grid').yiiGridView('getSelectedRows');
	 if(keys==''){
		alert("Please select a record to perform this action."); hideLoader(); return false;
	 }
	 
	 var newkeys = keys.toString().split(",");
	 var str = [];
	 for(var i=0;i<newkeys.length;i++){
		str.push(newkeys[i]);
	 }
	 
	 if(str.length > 1){
		alert("Please select only 1 record to perform this action.");
		return false;
	 } else {
		 jQuery.ajax({
			url: baseUrl +'/saved-report/check-report-access&id='+keys,
			type: 'get',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				if(data='OK'){
					location.href=baseUrl +'/saved-report/run-savereport&id='+keys;
				}else{
					alert("You don't have access to run selected report.");
					return false;
				}
			}
		});	
	 }
 }

/** Delete Saved Report **/
function DeleteSavedReport(){
	if($('#report-user-saved-grid').length > 0){
		var keys = $('#report-user-saved-grid').yiiGridView('getSelectedRows');
		
		if(keys=='')
			alert("Please select a record to perform this action.");
	
		var newkeys = keys.toString().split(",");
		var str = [];var str_val="";
		for(var i=0;i<newkeys.length;i++){
			//alert($( '.chk_saved_report_'+newkeys[i]).val());
			//var val = JSON.parse(decodeURIComponent($( '.chk_saved_report_'+newkeys[i]).val()));
			str_val =  " "+$( '.chk_saved_report_'+newkeys[i]).val();
			//+val['custom_report_name'];
			str.push(str_val);
		}
		if(!keys.length){
			alert("Please select at least 1 record to perform this action.");
			return false;
		} else {
			if(confirm("Are you sure you want to Delete '"+str+"' ?"))
			{
				 jQuery.ajax({
					url:baseUrl + 'saved-report/deletesavedreports&reportId='+keys,
					data:{task_list:keys},
					type: 'post',
					beforeSend:function(data){showLoader();},
					success:function(data){
						// success
						if(data=='OK'){
							$.pjax.reload('#report-user-saved-grid', $.pjax.defaults);
						}
						// fail
						if(data=='Fail'){
							alert("You are not creator of this report so you can't delete."); return false;
						}
					},
					complete: function(data){hideLoader();}
				 }); 
			}
		}
	}
}
/** End **/

/* Start : Used to load Add new Form for (Field Type,Field Operator)*/
function addReportFieldRightNew(module){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) addReportFieldRight(module);
}
/** End **/

	
/* Start : Used to load Add new Form for (Field Type,Field Operator)*/
function addReportFieldRight(module){
	jQuery.ajax({
	       url: baseUrl +'/report-management/'+module,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);	    	   
	       } 
	});
}
function addReportTableRelationships(module){
	jQuery.ajax({
	       url: baseUrl +'/report-management/'+module,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('#admin_right').html(data);	    	   
	       } 
	});
}
/* End : Used to load Add new Form for (Field Type,Field Operator)*/

/* Start : Used to load Add new Form for (Field Type,Field Operator)*/
function addReportFieldRightreportType(module){
	jQuery.ajax({
	       url: baseUrl +'/report-management/'+module,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   /*jQuery.ajax({
					   url: baseUrl +'/report-management/add-dbtables-report-type',
					   type: 'get',					   
					   success: function (data) {
						   if(data == 'success'){
							   //console.log('table loaded in session.');
						   } else if(data == 'loaded') {
							   //console.log('table  already loaded in session.');
						   }		   
					   } 
				});*/
	    	   jQuery('#admin_right').html(data);	    	   
	       } 
	});
}
/* End : Used to load Add new Form for (Field Type,Field Operator)*/

/* Start : Used to load Report Field Types Cancel Button */
function loadReportFieldTypesCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) loadReportFieldTypes();
}
/* End : Used to load Report Field Types */

/* Start : Used to load Report Field Types */
function loadReportFieldTypes(){
	commonAjax(baseUrl +'/report-management/index-field-type','admin_main_container');			
}
/* End : Used to load Report Field Types */

/* Start : Used to load Report Format */
function loadReportFormatCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) loadReportFormat();
}
/* End : Used to load Report Format */

/* Start : Used to load Report Format */
function loadReportFormat(){
	commonAjax(baseUrl +'/report-management/index-report-format','admin_main_container');		
	addReportFieldRight('create-report-format');	
}
/* End : Used to load Report Format */

/* Start : Used to load Report Field Operators */
function loadReportFieldOperatorsCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) loadReportFieldOperators();
}
/* End : Used to load Report Field Operators */

/* Start : Used to load Report Field Operators */
function loadReportFieldOperators(){
	commonAjax(baseUrl +'/report-management/index-field-operator','admin_main_container');
}
/* End : Used to load Report Field Operators */

/* Start : Used to load Report Field Lookups */
function loadReportFieldRelationshipsCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) loadReportFieldRelationships();
}
/* End : Used to load Report Field Lookups */

/* Start : Used to load Report Field Lookups */
function loadReportFieldRelationships(){
	commonAjax(baseUrl +'/report-management/index-field-relationship','admin_main_container');
}
/* End : Used to load Report Field Lookups */

/* Start : Used to load Report Field Lookups */
function loadReportFieldLookups(){
	commonAjax(baseUrl +'/report-management/index-field-lookup','admin_main_container');
}
/* End : Used to load Report Field Lookups */

/* Start : Used to load Report Field Calculation */
function loadReportFieldCalculationCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) loadReportFieldCalculation();
}
/* End : Used to load Report Field Lookups */

/* Start : Used to load Report Field Calculation */
function loadReportFieldCalculation(){
	commonAjax(baseUrl +'/report-management/index-field-calculation','admin_main_container');		
	addReportFieldRight('create-field-calculation');	
}
/* End : Used to load Report Field Types */

/* Start : Used to load Report Function Calculation */
function loadReportCalculationFunctionCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) loadReportCalculationFunction();
}
/* End : Used to load Report Field Types */

/* Start : Used to load Report Function Calculation */
function loadReportCalculationFunction(){
	commonAjax(baseUrl +'/report-management/index-calculation-function','admin_main_container');		
	addReportFieldRight('create-calculation-function');	
}
/* End : Used to load Report Function Calculation */

/* Start : Used to load Report Sp Calculation */
function loadReportCalculationSp(){
	commonAjax(baseUrl +'/report-management/index-calculation-sp','admin_main_container');		
	addReportFieldRight('create-calculation-sp');	
}
/* End : Used to load Report Sp Calculation */

/* Start : Used to load Report Field Calculation */
function loadReportChartFormatCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) loadReportChartFormat();
}
/* End : Used to load Report Sp Calculation */

/* Start : Used to load Report Field Calculation */
function loadReportChartFormat() {
	commonAjax(baseUrl +'/report-management/index-chart-format','admin_main_container');		
	// addReportFieldRight('create-chart-format');		
}
/* End : Used to load Report Field Types */

/* Start : Used to load Report Type */
function loadReportsReportTypeCancel() {
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) loadReportsReportType();
}
/* End : Used to load Report Field Types */

/* Start : Used to load Report Type */
function loadReportsReportType(){
	commonAjax(baseUrl +'/report-management/index-report-type','admin_main_container');		
	addReportFieldRight('create-report-type');		
}
/* End : Used to load Report Field Types */

function loadReportChartDisplayByCancel(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) loadReportChartDisplayBy();
}

function loadReportChartDisplayBy(){
	commonAjax(baseUrl +'/report-management/index-chart-display-by','admin_main_container');		
	//addReportFieldRight('create-chart-display-by');		
	}
	
/** Edit report user criteria **/
function edit_report_user_criteria()
{
	 var keys = $('#report-user-saved-grid').yiiGridView('getSelectedRows');
	 if(keys==''){
		alert("Please select a record to perform this action."); hideLoader(); return false;
	 }
	 
	 var newkeys = keys.toString().split(",");
	 var str = [];
	 for(var i=0;i<newkeys.length;i++){
		str.push(newkeys[i]);
	 }
	 if(str.length > 1){
		alert("Please select only 1 record to perform this action.");
		return false;
	 } else {
		jQuery.ajax({
			url: baseUrl +'/saved-report/check-report-access&id='+keys,
			type: 'get',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				if(data='OK'){
					location.href=baseUrl +'/saved-report/edit-savereport&id='+keys;
				} else {
					alert("You don't have access to edit selected report.");
					return false;
				}
			}
		});	
	 }
}	
	
/* Start : Used to load Edit Report Format Form,Field type,Field Operator,Chart Format,Chart Display By */
function updateReportDropdown(field_id,select_val,module)
{
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) {
		if(field_id == select_val){
			addReportFieldRight('create-'+module);
			return false;
		} else {
			jQuery.ajax({
				url: baseUrl +'/report-management/update-'+module+'&id='+field_id,
				type: 'get',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					jQuery('#admin_right').html(data);
					jQuery('.admin-left-module-list ul li').removeClass('active');
					jQuery('#report_'+module+'_'+field_id).addClass('active');				
					jQuery('#'+module+'_list_dropdown').val(field_id);
				} 
			});
		}
	}
}
/* Code End */

/* Start : Used to load Edit Report Format Form,Field type,Field Operator,Chart Format,Chart Display By */
function updateSelect2Dropdown(field_id,select_val,module)
{
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) {
		if(field_id == select_val){
			addReportFieldRight('create-'+module);
			return false;
		} else {
			jQuery.ajax({
				url: baseUrl +'/report-management/update-'+module+'&id='+field_id,
				type: 'get',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					jQuery('#admin_right').html(data);
					jQuery('.admin-left-module-list ul li').removeClass('active');
					jQuery('#report_'+module+'_'+field_id).addClass('active');				
					// jQuery('#'+module+'_list_dropdown').val(field_id);												
					// console.log(field_id);
					$('#'+module+'_list_dropdown').val(field_id);
					// .select2('val',field_id);				
				} 
			});
		}
	}
}
/* Code End */

/* Start : Used to load Edit Report Field type */
function updateReportfieldtype(field_id,select_val,module,theme_id)
{
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status == true) {
		if(field_id == select_val){
			addReportFieldRight('create-'+module);
			return false;
		} else {
			jQuery.ajax({
				url: baseUrl +'/report-management/update-'+module+'&id='+field_id,
				type: 'get',
				beforeSend:function (data) {showLoader();},
				success: function (data) {
					hideLoader();
					jQuery('#admin_right').html(data);
					jQuery('.admin-left-module-list ul li').removeClass('active');
					jQuery('#report_'+module+'_'+field_id).addClass('active');				
					jQuery('#'+module+'_list_dropdown').val(theme_id);
				} 
			});
		}
	}
}
/* Code End */

 /*
 * Function to remove Single product model
 * Report Chart display By, Report Format,Chart Format, Field Calculation,Field Operator,Field Type
 * code Starts
 * */
function removeReportSingleData(field_id,field_name,module){
	if(confirm('Are you sure you want to Delete '+field_name+'?')){
		jQuery.ajax({
			url: baseUrl +'/report-management/delete-'+module+'&id='+field_id,
			type: 'GET',
			beforeSend:function (data) {showLoader();},
			success: function (data) 
			{
				if(data == "OK")
				{
					hideLoader();
					commonAjax(baseUrl +'/report-management/index-'+module,'admin_main_container');		
					addReportFieldRight('create-'+module);		
				}
				else
				{
					hideLoader();
					alert("This report type is already saved for a custom report which cannot be deleted!");
					commonAjax(baseUrl +'/report-management/index-'+module,'admin_main_container');		
				}
			}
		});
	}	
}
/* code Ends */

/**
* get selected Field Types Operator add/edit page
*/
function getallfieldtypes(id)
{		
		var field_typeids = $(".total_field_type_ids").val();
		$.ajax({
			url:baseUrl+'report-management/get-report-fieldtype-lists&id='+id,
			beforeSend:function (data) {showLoader();},
			type:"POST",
			data:{field_typeids:field_typeids},			
			success:function(response){
			hideLoader();
			
			if($('body').find('#availabl-field-types').length == 0){
				$('body').append('<div class="dialog" id="availabl-field-types" title="Available Field Types"></div>');
			}
			$('#availabl-field-types').html('').html(response);							
			$('#availabl-field-types').dialog({ 
			modal: true,
			width:'40em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
				$('.ui-dialog-titlebar-close').prop('title', 'Close');
				$('.ui-dialog-titlebar-close').prop('aria-label', 'Close');
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
								var fieldtype = $('.primary_table_checkbox').is(':checked');
								if(fieldtype == false){
									alert("Please Select Field Types");	return false;
								}							
								$('.primary_table_checkbox:checked').each(function(){
									var fieldtype_id = this.value;
									var field_type = $(this).data('tbl_field_type');
									if($('#form-fieldtype-report').find('.fieldtype_'+fieldtype_id).length == 0)
									{
										$('#form-fieldtype-report').append('<tr class="fieldtype_'+fieldtype_id+'"><input class="field_type" type="hidden" name="field_type[]" value='+fieldtype_id+' /><td>'+field_type+'</td><td><a href="javascript:void(0);" onClick="remove_dialog_single_data(\'form-fieldtype-report\',\'fieldtype\',\''+fieldtype_id+'\');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
									}
									field_typeids = field_typeids +','+ fieldtype_id;									
								});
								$(".total_field_type_ids").val(field_typeids);
								$('#ReportsFieldOperators #is_change_form').val('1'); // change flag to 1 
								$('#is_change_form_main').val('1'); // change flag to 1 
								jQuery('input').customInput();
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

/**
* get selected Field Types Operator add/edit page
*/
function getallchartformats(id)
{		
	var chart_ids = $(".total_chart_ids").val();	
	$.ajax({
		url:baseUrl+'report-management/get-chart-format-lists&id='+id,
		beforeSend:function (data) {showLoader();},
		data:{chart_ids:chart_ids},
		type:'POST',
		success:function(response){
			hideLoader();		
			if($('body').find('#availabl-chart-format').length == 0){
				$('body').append('<div class="dialog" id="availabl-chart-format" title="Available Chart Formats"></div>');
			}
			$('#availabl-chart-format').html('').html(response);							
			$('#availabl-chart-format').dialog({ 
			modal: true,
			width:'40em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon" title="close"></span>');
				$('.ui-dialog-titlebar-close').prop('title', 'Close');
				$('.ui-dialog-titlebar-close').prop('aria-label', 'Close');
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
							  $(this).dialog('close');
						  } 
					   },
					   { 
						  text: "Add", 
							"class": 'btn btn-primary',
							"title": 'Add',
							click: function () { 
								var chart_format = $('.report_chart_format').is(':checked');							
								if(chart_format==false){
									alert("Please Select Chart Formats"); return false;
								}								
								$('.report_chart_format:checked').each(function(){
									var chart_format_id = this.value;									
									var chart_format = $(this).data('chart_format');
									if($('#form-fieldtype-report').find('.chart_format_'+chart_format_id).length == 0){										
										$('#form-fieldtype-report').append('<tr class="chart_format_'+chart_format_id+'"><input class="report_chart_format" type="hidden" name="chart_format[]" value='+chart_format_id+' /><td>'+chart_format+'</td><td><a href="javascript:void(0);" onClick="remove_dialog_single_data(\'ReportsChartFormatDisplayBy\',\'chart_format\',\''+chart_format_id+'\');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
										chart_ids = chart_ids +','+ chart_format_id;	
									}
								});								
								$(".total_chart_ids").val(chart_ids);
								jQuery('input').customInput();
								$("#report-fieldtypes").html(''); 								                   
								$('#ReportsChartFormatDisplayBy #is_change_form').val('1'); // change flag to 1
								$('#is_change_form_main').val('1'); // change flag to 1
								$(this).dialog('destroy').remove(); 							
							}
					  }
				]
			});			
		}
	});
}

/**
* get selected Field Calculation pop up
*/
function getallfieldcalculation(id)
{		
	var fc_ids = $(".total_fc_ids").val();	
	$.ajax({
		url:baseUrl+'report-management/get-field-calculation-lists&id='+id,
		beforeSend:function (data) {showLoader();},
		data:{fc_ids:fc_ids},
		type:'POST',
		success:function(response){
			hideLoader();		
			if($('body').find('#availabl-field-calculation').length == 0){
				$('body').append('<div class="dialog" id="availabl-field-calculation" title="Available Field Calculation"></div>');
			}
			$('#availabl-field-calculation').html('').html(response);							
			$('#availabl-field-calculation').dialog({ 
			modal: true,
			width:'40em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon" title="close"></span>');
				$('.ui-dialog-titlebar-close').prop('title', 'Close');
				$('.ui-dialog-titlebar-close').prop('aria-label', 'Close');
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
							  $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
						  text: "Add", 
							"class": 'btn btn-primary',
							"title": 'Add',
							click: function () {			
								var report_calculation = $('.report_field_calculation').is(':checked');							
								if(report_calculation == false){
									alert("Please Select Field Calculations"); return false;
								}					
								$('.report_field_calculation:checked').each(function() {
									var fc_id = this.value;		
									var calculation_name = $(this).data('calculation_name');
									if($('#form-fieldtype-report').find('.report_type_calculation_'+fc_id).length == 0) {										
										$('#form-fieldtype-report').append('<tr class="report_type_calculation_'+fc_id+'"><input class="report_field_calculation" type="hidden" name="field_calculation[]" value='+fc_id+' /><td>'+calculation_name+'</td><td><a href="javascript:void(0);" onClick="remove_dialog_single_data(\'ReportsReportType\',\'report_type_calculation\',\''+fc_id+'\');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
										fc_ids = fc_ids +','+ fc_id;											
									}
								});								
								$(".total_chart_ids").val(fc_ids);
								jQuery('input').customInput();
								$("#report-fieldtypes").html(''); 								                   
								$(this).dialog('destroy').remove(); 							
							}
					  }
				]
			});			
		}
	});
}

/**
 * Remove Dialog Data from the list (Chart Format types,)
 * Code Starts
 */
 function remove_dialog_single_data(form_id,moduleclass,id){
	$.ajax({
		url:baseUrl+'report-management/check-calculation-inuse&id='+id,
		beforeSend:function (data) {showLoader();},
		type:'GET',
		success:function(response){
			var data = JSON.parse(response);
			if(data.inuse == 0){
				if(form_id == 'form-fieldtype-report'){
					var field_typeids = $(".total_field_type_ids").val();		
					field_typeids = field_typeids.replace(','+id,'');		
					$(".total_field_type_ids").val(field_typeids);
				}else if(form_id == 'ReportsChartFormatDisplayBy'){
					var field_typeids = $(".total_chart_ids").val();		
					field_typeids = field_typeids.replace(','+id,'');		
					$(".total_chart_ids").val(field_typeids);
				}
				var rs = $('#'+form_id).find('.'+moduleclass+'_'+id);
				$('#is_change_form').val('1'); $('#is_change_form_main').val('1');
				rs.remove();
			} else {
				hideLoader();
				alert("Calculation field is already in use, Please remove it from saved report to perform this action.");
				return false;
			}
		},complete:function(){
			hideLoader();
		}
	});
 }
 
 /* Code Ends*/
 /*
  * Function to add fields from primary table*/
  function add_calculation(primary_table_name){
	  var reportstypefields = $('#ReportsReportType').serialize();
	  $.ajax({
			url:baseUrl+'report-management/get-calculation-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:reportstypefields,
			//data:{tbl_lists:tbl_lists, sel_table:sel_table, primary_table:primary_table},
			success:function(response){
				hideLoader();	
				if($('body').find('#availabl-calculation').length == 0){
					$('body').append('<div class="dialog" id="availabl-calculation" title="Add Calculation Fields"></div>');
				}			
				$('#availabl-calculation').html('').html(response);	
				$('#availabl-calculation').dialog({ 
					modal: true,
			        width:'50em',
			        height: 456,
			        title:'Add Calculation Fields',
			        close: function(){
						$(this).dialog('destroy').remove();
					},
			        create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
						 //$('.ui-dialog-title').html('Add Fields From Primary Table');						 
						},
			        buttons: [
								{ 
			                	  text: "Cancel", 
			                	  "class": 'btn btn-primary',
								  "title": 'Cancel',
			                	  click: function () { 
			                		  $(this).dialog('destroy').remove();
		 	                	  } 
			                  },
			                   { 
			                	  text: "Add", 
			                	  "class": 'btn btn-primary',
								  "title": 'Add',
				                   click: function () {
									   var checked_length = 	$('.cal-fields:checked').length;
									   if(checked_length==0){
											alert("Select Table and related fields"); 
											return false;
										  }
										  
										  if(checked_length > 0){
											jQuery.ajax({
												url: baseUrl +'/report-management/show-primary-table-grid',
												type: 'POST',
												data: $('#availabl-calculation input').serialize()+'&'+$('#ReportsReportType').serialize(),
												success: function (data) {															
													$('#primary_table_fields').html('');
													$('#ReportsReportType #is_change_form').val('1');	// change flag to 1
													$('#is_change_form_main').val('1');	// change flag to 1	 
													$('#primary_table_fields').html(data);	
												},
												complete:function(){
													// alert($('div#get-report-type-grid-container .kv-grid-table tr.sort').length);
													if($('div#get-report-type-grid-container .kv-grid-table tr.sort').length > 1){
														$('.join-stat-icon').show();
													} else {
														$('.join-stat-icon').hide();
													}
												}
											});	
										}
										$('input').customInput();
										$(this).dialog('destroy').remove();
								   }
							}
						]
					});
			},complete:function(){
				$('#availabl-calculation').customInput();
			}
		});
	  
  }
  function add_primary_table(primary_table_name, flag){	
		//var primary_table_name =  $('.primary_table_name').val();
		/*var sel_table = [];
		$('.tbl_name').each(function() {
			sel_table.push($(this).val());
		});
		var tbl_lists = []; 
		$('.table_lists').each(function(){
			tbl_lists.push($(this).val());
		});*/
		
		var reportstypefields = $('#ReportsReportType').serialize()+'&primary_table_name='+primary_table_name+'&flag='+flag;
		//+'&current_table='+current_table;
		//console.log(edit_table_lists);
		$.ajax({
			url:baseUrl+'report-management/get-table-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:reportstypefields,
			//data:{tbl_lists:tbl_lists, sel_table:sel_table, primary_table:primary_table},
			success:function(response){
			hideLoader();						
			if($('body').find('#availabl-primary-tables').length == 0){
				$('body').append('<div class="dialog" id="availabl-primary-tables" title="Add Fields From Primary Table"></div>');
			}		
			$('#availabl-primary-tables').html('').html(response);	
			$('#availabl-primary-tables').dialog({ 
					modal: true,
			        width:'50em',
			        height: 456,
			        title:'Add Fields From Primary Table',
			        close: function(){
						$(this).dialog('destroy').remove();
					},
			        create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
						 //$('.ui-dialog-title').html('Add Fields From Primary Table');						 
						},
					
			        buttons: [
								{ 
			                	  text: "Cancel", 
			                	  "class": 'btn btn-primary',
								  "title": 'Cancel',
			                	  click: function () { 
									  $(this).dialog('destroy').remove();
		 	                	  } 
			                  },
			                   { 
			                	  text: "Add", 
			                	  "class": 'btn btn-primary',
									"title": 'Add',
				                	  click: function () {
									 	  var counter = parseInt(Math.random()*1000000); 
										  var tbl_name	= $('#my_primaryheader').val(); 
										  var checked_length = 	$('.column-fields:checked').length;
										  var filter_data = $('#filter_data').val();
										  
										  // check if checkbox not selected
										  if(checked_length==0){
											alert("Select Table and related fields"); 
											return false;
										  }
										  
										  if(checked_length > 0){
											jQuery.ajax({
												url: baseUrl +'/report-management/show-primary-table-grid',
												type: 'POST',
												data: $('#availabl-primary-tables input, select').serialize()+'&'+$('#ReportsReportType').serialize(),
												success: function (data) {															
													$('#primary_table_fields').html('');	
													$('#ReportsReportType #is_change_form').val('1'); // change flag to 1	
													$('#is_change_form_main').val('1'); // change flag to 1	
													$('#primary_table_fields').html(data);	
												},
												complete:function(){
													// alert($('div#get-report-type-grid-container .kv-grid-table tr.sort').length);
													if($('div#get-report-type-grid-container .kv-grid-table tr.sort').length > 1){
														$('.join-stat-icon').show();
													} else {
														$('.join-stat-icon').hide();
													}
												}
											});	
										}
										$('input').customInput();
										$(this).dialog('destroy').remove();
				                	  }
			                  }
			        ]
			    });
			},complete:function(){
				$('#availabl-primary-tables input').customInput();
				$('#availabl-primary-tables .custom-table-list').select2({
					allowClear: false,
					placeholder: 'Select Table',
					dropdownParent: $('#availabl-primary-tables')
				});
			}
		});

}

/**
 * Table relation popup from report type
 */
function relation_table_fields()
{
	var sel_table = [];
	$('.table_lists').each(function(){
		sel_table.push($(this).val());
	});
	var table_relation = [];
	$('.table_relation').each(function(){
		table_relation.push($(this).val());
	});
	var primary_table_name = $('#primary_table').val();
	if(primary_table_name==""){
		alert('In order to add relationship please select primary table first');
		return false;
	}
	
	$.ajax({
		url:baseUrl+'report-management/get-table-relation-fields',
		beforeSend:function (data) {showLoader();},
		type:'post',
		data:{sel_table:sel_table,primary_table:primary_table_name,table_relation:table_relation},
		success:function(response){
		hideLoader();						
		if($('body').find('#availabl-second-tables').length == 0){
			$('body').append('<div class="dialog" id="availabl-second-tables" title="Add Relationships"></div>');
		}			
		$('#availabl-second-tables').html('').html(response);			
		$('#availabl-second-tables').dialog({ 
				modal: true,
				width:'50em',
			    height: 456,
				title:'Add Relationships',
				close: function(){
					$(this).dialog('destroy').remove();
				},
				create: function(event, ui) { 						  
					 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
                                         $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					 //$('.ui-dialog-title').html('Add Relationships');						 
				},
				buttons: [
							{ 
							  text: "Cancel", 
							  "class": 'btn btn-primary',
							  "title": 'Cancel',
							  click: function () { 
								  $(this).dialog('destroy').remove();
	 						  } 
						  },
						   { 
							  text: "Add", 
							  "class": 'btn btn-primary',
								"title": 'Add',
								click: function () { 		
									var primary_table = $('#availabl-second-tables .select-primary-table').val();
									var primary_table_field = $('#availabl-second-tables .select-primary-table-field').val();
									var join_type = $('#availabl-second-tables .join-type').val();
									var secondary_table = $('#availabl-second-tables .select-secondary-table-list').val();
									var secondary_table_field = $('#availabl-second-tables .select-secondary-table-field-list').val();
									var filter_data = $('#filter_data').val();
									var table_relation = $('.table_relation').val();
									//console.log(filter_data);
									//#filter_relation
									
									// filter relation
									if(table_relation != '' && table_relation != 'null'){
										var relationdetails = JSON.parse(table_relation); // Table relation with json
										//	$('#filter_relation').each(function(){
										//		console.log($(this).val());
										//	});
										//console.log(relationdetails); //return false;
									} else {
										var relationdetails=[];
									}
									
									if(primary_table==""){
										alert("Please select primary table.");
										return false;
									}
									
									if(primary_table_field==""){
										alert("Please select primary table field.");
										return false;
									}
									
									if(join_type==""){
										alert("Please select join type.");
										return false;
									}
									
									if(secondary_table==""){
										alert("Please select secondary table.");
										return false;
									}
									
									if(secondary_table_field==""){
										alert("Please select secondary table field.");
										return false;
									}
									
									relationdetails.push({
										"primary_table_name":primary_table,
										"primary_table_field":primary_table_field,
										"secondary_table_name":secondary_table,
										"secondary_table_field":secondary_table_field,
										"join_type":join_type,
									});
									
									/**
									 * To set relation tickmark to existing primary table
									 * */
									var p_to_s_field = JSON.parse(filter_data);
									if(p_to_s_field != '' && p_to_s_field != '[]'){
										var flag=false;
										$(p_to_s_field[primary_table]).each(function(key,value){
											if(value.field_name == primary_table_field){
												p_to_s_field[primary_table][key].relationship = 1;
												p_to_s_field[primary_table][key].is_field_associated = 1;
												flag=true;
											}
										});
										
										$(p_to_s_field[secondary_table]).each(function(key,value){
											if(value.field_name == secondary_table_field){
												p_to_s_field[secondary_table][key].relationship = 1;
												p_to_s_field[secondary_table][key].is_field_associated = 1;
												flag=true;
											}
										});
										if(flag){
											filter_data =  JSON.stringify(p_to_s_field);
										}
									}
									
									// Ajax show primary table grid
									jQuery.ajax({
										url: baseUrl +'/report-management/show-primary-table-grid',
										type: 'POST',
										data: {relationdetails: relationdetails, filter_data: filter_data, table_relation:table_relation, 'primary_table_name':primary_table_name},
										success: function (data) {															
											$('#primary_table_fields').html(data);	
										},
										complete:function(){
											if($('div#get-report-type-grid-container .kv-grid-table tr.sort').length > 1){
												$('.join-stat-icon').show();
											} else {
												$('.join-stat-icon').hide();
											}
										}
									});	
									
									$('.table-relations-details').html("<input type='hidden' name='table_relation' class='table_relation' value='"+JSON.stringify(relationdetails)+"'/>");
									$('.primary_table_name').val(primary_table);		
									$('#availabl-second-tables input').customInput();
									$(this).dialog('destroy').remove();
								}
						  }
				]
			});
		}
	});
}

//
function remove_dialog_report_table_data(tbl_name){
	jQuery.ajax({
		url: baseUrl +'/report-management/delete-report-table-name',
		type: 'POST',
		data: {tbl_name:tbl_name,table_type:table_type},													
		success: function (data) {															
			$('#primary_table_fields').html(data);																				
		}
	});		
}

/**
 * Table delete from report type grid
 */
 function remove_grid_report_type_table(table_name,flagfrom)
 {
	 $.ajax({
		url:baseUrl+'report-management/check-field-inuse',
		beforeSend:function (data) {showLoader();},
		data: {'table_name':table_name,'field_name':''},
		type:'POST',
		success:function(response){
			var data = JSON.parse(response);
			if(data.inuse == 0){
				var sel_table = [];
				$('.table_lists').each(function(){
					sel_table.push($(this).val());
				});
				
				var table_relation = $('.table_relation').val();
				var primary_table = $('#primary_table').val();
				
				var relation_exist = false;
				if(table_relation!=''){
					var jsonrelation = JSON.parse(table_relation); 
					$.each(jsonrelation, function( key, value ) {
						if(value.secondary_table_name==table_name || value.primary_table_name==table_name){
							relation_exist = true;
						}
					});
					if(relation_exist){
						hideLoader();
						alert("Please remove relationship prior to remove this Table.");
						return false;
					}
				}
				//console.log(flagfrom + " : " +relation_exist);
				if(flagfrom != 'field_remove' && flagfrom != 'relation_remove'){
					if(confirm("Are you sure you want to remove this Table?")){
						jQuery.ajax({
							url: baseUrl +'/report-management/delete-report-type-field-name',
							type: 'POST',
							data: {tbl_name_field:table_name, table_lists:sel_table,table_relation: table_relation,primary_table:primary_table},	
							success: function (data) {		
								$('#primary_table_fields').html(data);	// primary table field
							},
							complete:function(){
								if($('div#get-report-type-grid-container .kv-grid-table tr.sort').length > 1){
									$('.join-stat-icon').show();
								} else {
									$('.join-stat-icon').hide();
								}
								hideLoader();
							}
						});		
					}
				} else {
					jQuery.ajax({
						url: baseUrl +'/report-management/delete-report-type-field-name',
						type: 'POST',
						data: {tbl_name_field:table_name, table_lists:sel_table,table_relation: table_relation,primary_table:primary_table},	
						success: function (data) {		
							$('#primary_table_fields').html(data);	// primary table field
						},
						complete:function(){
							if($('div#get-report-type-grid-container .kv-grid-table tr.sort').length > 1){
								$('.join-stat-icon').show();
							} else {
								$('.join-stat-icon').hide();
							}
							hideLoader();
						}
					});	
				}	
			} else {
				hideLoader();
				alert("This Table is already in use, Please remove it from saved report to perform this action.");
				return false;
			}
		},complete:function(){
			hideLoader();
		}
	});	
 }

/**
 * Remove Dialog Data from the list (Only for Report type)
 * Code Starts
 */
 function remove_dialog_report_type_field_relation(unique_id,primary_table_name,primary_table_field,secondary_table_name,secondary_table_field,join_type){
	 if(confirm("Are you sure you want to Delete relationship?")){
	   var jsonrelation = JSON.parse($('.table_relation').val()); 
	   $(jsonrelation).each(function( key, value ) {
		   console.log(value);
		   	if(value.primary_table_name==primary_table_name &&
			   value.primary_table_field==primary_table_field && 
			   value.secondary_table_name==secondary_table_name && 
			   value.secondary_table_field==secondary_table_field && 
			   value.join_type==join_type){
				//delete jsonrelation[key];
				jsonrelation.splice(key, 1);
			}
		});
		$('.table_relation').val(JSON.stringify(jsonrelation));
		
		/**
		 * To set relation tickmark to existing primary table
		 * */
		 var primary_table_list = JSON.parse($('input[name="table_lists['+primary_table_name+']"]').val());
		 $(primary_table_list).each(function( key, value ) {
			 if(value.field_name== primary_table_field){
				 primary_table_list[key].relationship='';
				 primary_table_list[key].is_field_associated = 0;
			 }
		 });
		 $('input[name="table_lists['+primary_table_name+']"]').val(JSON.stringify(primary_table_list));
		/**
		 * To set relation tickmark to existing secondary table
		 * */
		 var secondary_table_list = JSON.parse($('input[name="table_lists['+secondary_table_name+']"]').val());
		 $(secondary_table_list).each(function( key, value ) {
			 if(value.field_name== secondary_table_field){
				 secondary_table_list[key].relationship='';
				 secondary_table_list[key].is_field_associated = 0;
			 }
		 });
		 $('input[name="table_lists['+secondary_table_name+']"]').val(JSON.stringify(secondary_table_list));
		
		
		
		//$('.field_raw_'+unique_id).closest('tr').remove();  
		if(!$('.table_relation_expand_tr').length){
			remove_grid_report_type_table("Table Relationships", 'relation_remove');
		}
		
		removefieldfromtable();
	} 
 }
 
function removefieldfromtable(){
	 var formdata = $('#ReportsReportType').serialize();
		 //console.log(formdata);return false;
		 jQuery.ajax({
			url: baseUrl +'/report-management/delete-report-type-field-name',
			type: 'POST',
			data: formdata,
			success: function (data) {															
				$('#primary_table_fields').html(data);	// primary table field
			},
			complete:function(){
				if($('div#get-report-type-grid-container .kv-grid-table tr.sort').length > 1){
					$('.join-stat-icon').show();
				} else {
					$('.join-stat-icon').hide();
				}
			}
		});
}
 
 function remove_dialog_report_type_field_data(unique_id,tbl_name,tbl_field_name,table_type)
 {	
	 $.ajax({
		url:baseUrl+'report-management/check-field-inuse',
		beforeSend:function (data) {showLoader();},
		data: {'table_name':tbl_name,'field_name':tbl_field_name},
		type:'POST',
		success:function(response){
			var data = JSON.parse(response);
			if(data.inuse == 0){
				 /** table lists **/
				 var table_lists = []; var new_table_lists = {};
				 var jsonlist;
				 if(tbl_name!="" && tbl_field_name!=""){ 
					 
					 // table relation
					 var table_relation_dt = $('.table_relation').val();
					 var relation_exist = false;
					 if(table_relation_dt!=''){
						 var jsonrelation = JSON.parse($('.table_relation').val()); 
						 var new_relation_lists = [];
						$.each(jsonrelation, function( key, value ) {
							if((value.secondary_table_name==tbl_name && value.secondary_table_field==tbl_field_name) || (value.primary_table_name==tbl_name && value.primary_table_field==tbl_field_name)){
								relation_exist = true;
							}else{
								new_relation_lists.push(value);
							}
						});
						if(relation_exist){
							alert("Please remove relationship prior to remove this Field.");
							return false;
						}
						// tbl relations details
						//$('.table-relations-details').html("<input type='hidden' name='table_relation' class='table_relation' value='"+JSON.stringify(new_relation_lists)+"'/>");
						$('.table_relation').val(JSON.stringify(new_relation_lists));
						//console.log(new_relation_lists);
						if($('tr[data-primary-field="'+tbl_name+'.'+tbl_field_name+'"]').length>0){
							$('tr[data-primary-field="'+tbl_name+'.'+tbl_field_name+'"]').remove();
						}
						if($('tr[data-secondary-field="'+tbl_name+'.'+tbl_field_name+'"]').length>0){
							$('tr[data-secondary-field="'+tbl_name+'.'+tbl_field_name+'"]').remove();
						}
						// To remove Table if No Rows of relation exist. 
						if(!$('.table_relation_expand_tr').length){
							remove_grid_report_type_table("Table Relationships", 'field_remove');
						}
					 }
					 
					 // following code is use when table field get deleted
					 // field raw
					 //$('.field_raw_'+unique_id).closest('tr').remove();
					 $('input[name="table_lists['+tbl_name+']"]').each(function(){
						 jsonlist = JSON.parse($(this).val());
						 $(jsonlist).each(function(key,value){
							 if(tbl_field_name == value.field_name){
								//delete jsonlist[key];
								jsonlist.splice(key, 1);
							 }
						 });
					 });
					 $('input[name="table_lists['+tbl_name+']"]').val(JSON.stringify(jsonlist));
					 
					 // To remove Table if No Rows of exist in table.
					 if(!$('.kv-expanded-row tr.table_'+tbl_name).length){
						 remove_grid_report_type_table(tbl_name, 'field_remove');
					 }
					 
					removefieldfromtable();
				 }
			} else {
				hideLoader();
				alert("This field is already in use, Please remove it from saved report to perform this action.");
				return false;
			}
		},complete:function(){
			hideLoader();
		}
	});
}
/*
* Function to add secondory fields from primary table field
* */
/*function add_secondory_table(relation_field,table_index){	  	
	 var primary_table_name =  $('.primary_table_name').val(); 
	 var secondary_tables_name =  $('.secondary_tables_name').val();
		$.ajax({
			url:baseUrl+'report-management/get-second-table-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{primary_table_name:primary_table_name,secondary_tables_name:secondary_tables_name},
			success:function(response){
			hideLoader();						
			if($('body').find('#availabl-second-tables').length == 0){
				$('body').append('<div class="dialog" id="availabl-second-tables" title="Add Secondary Fields For"></div>');
			}			
			$('#availabl-second-tables').html('').html(response);			
			$('#availabl-second-tables').dialog({ 
					modal: true,
					width:'40em',
					create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
						 $('.ui-dialog-title').html(relation_field+': Relate to Secondary Table');						 
						},
					buttons: [
								{ 
								  text: "Cancel", 
								  "class": 'btn btn-primary',
								  "title": 'Cancel',
								  click: function () { 
									  $(this).dialog('destroy').remove();
		 
								  } 
							  },
							   { 
								  text: "Add", 
								  "class": 'btn btn-primary',
									"title": 'Add',
									  click: function () { 		
											//var counter = parseInt(Math.random()*1000000);	
											var tbl_name	= $('.tbl_secondary_name').val();  ;	
											var checked_length = 	$('.primary_table_checkbox:checked').length;	
											var html = '';
											var relation_id = relation_join_type = '';
											var selectedradio = $(".primary_table_relation:checked");
											var radiolength = $(".primary_table_relation:checked").length; 										
											var relation_name = selectedradio.data('primary_relation');
											var checkbox_id = selectedradio.data('unique_id');											
											relation_join_type = $('.tbl_join_type_'+checkbox_id).val();
											if(radiolength == 0 ){
												alert('Please select a Relation Field to perform this action.');
												return false;
											}											
											if(relation_join_type == ''){
												alert('Please select a Join Type to perform this action.');
												return false;
											}
											//$('.relation_'+unique_id).val(relation_id);
											//$('.relation_join_type_'+unique_id).val(relation_join_type);										
											if(checked_length > 0){			
												var table_list ={tabledetail:[]};										  
												$('.primary_table_checkbox:checked').each(function(){												
													var tbl_field_name			=	$(this).val();
													var tbl_field_type			=	$(this).data('tbl_field_type');
													var tbl_field_display_name	=	$(this).data('tbl_display_name');
													var table_full_name			= 	'Secondary: '+$(this).data('tbl_name');								
													var tbl_name 				= 	$(this).data('tbl_name');
													var counter 				= $(this).data('unique_cnt_id');
													if(checkbox_id == counter){
														var relation_value = relation_field;
														var relation_join_type_value = relation_join_type; 
													}else {
														var relation_value = relation_join_type_value = '';
													}
													table_list.tabledetail.push({
													"field_name" 				: 	tbl_field_name,
													"reports_field_type_id"		:	tbl_field_type,
													"field_display_name"		:	tbl_field_display_name,
													"table_name"				:	tbl_name,
													"table_type"				:	'2',
													"relationship"				:	relation_value,
													"join_type"					:	relation_join_type_value,
													"table_full_name"			:	table_full_name, 
													"is_field_associated" 		: 0,
													});	
													counter++;													
											});	
											jQuery.ajax({
													url: baseUrl +'/report-management/add-secondary-table-grid',
													type: 'POST',
													data: {table_list:table_list,table_index:table_index,relation_field:relation_field,relation_join_type:relation_join_type,relation_name:relation_name},
													beforeSend:function (data) {showLoader();},
													success: function (data) {
														hideLoader();					
														$('#primary_table_fields').html(data);										
													}
											});											
										}
										var sec_table = $('.secondary_tables_name').val();
										sec_table = sec_table+ ','+tbl_name;				
										$('.secondary_tables_name').val(sec_table);						
										$('input').customInput();
										$(this).dialog('destroy').remove();
									  }
							  }
					]
				});
			}
		});

}*/
/* Code Ends*/
/*
 * fUNCTION TO Go previous page on report type
*/
function report_type_Prev(){
	$("#first").show();
	$(".next").hide();
}
/*
 * Function to change display name of the table field 
 * */
 function report_type_change_label(unique_id, actual_value, field_name,table_name){
	 var lbl_name = $('.display_name_'+unique_id).html().trim();
	 if($('body').find('#edit-table-field-name').length == 0){
				$('body').append('<div class="dialog" id="edit-table-field-name" title="Edit Field Display Name"><div class="form-group"><div class="row input-field"><div class="col-md-3"><label for="reportsfieldtype-field_type" class="form_label">Display Name</label></div><div class="col-md-7"><input type="text" name="ReportsFieldType[field_type]" class="form-control" id="field-reportsfieldtype-label" value="'+lbl_name+'"><div class="help-block"></div></div></div></div></div>');
			}
	$('#edit-table-field-name').dialog({ 
					modal: true,
			        width:'40em',
			        height:302,
			        title:'Edit Field Display Name',
			        create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
						},
			        buttons: [
								{ 
			                	  text: "Default", 
			                	  "class": 'btn btn-primary pull-left',
								  "title": 'Default',
			                	  click: function () { 
			                		  /*$('.table_'+table_name+' .display_name_'+unique_id).val(actual_value);
			                		  var table = JSON.parse($('input[name="table_lists['+table_name+']"').val());
			                		  $(table).each(function(key,value){
										  if(value.field_name == field_name){
											  table[key].field_display_name=actual_value;
										  }
									  });
									  $('input[name="table_lists['+table_name+']"').val(JSON.stringify(table));*/
									  $('#field-reportsfieldtype-label').val(actual_value);
			                	  } 
			                  },
								{ 
			                	  text: "Cancel", 
			                	  "class": 'btn btn-primary',
								  "title": 'Cancel',
			                	  click: function () { 
			                		  $(this).dialog('destroy').remove();
		 	                	  } 
			                  },
			                   { 
			                	  text: "Update", 
			                	  "class": 'btn btn-primary',
									"title": 'Update',
				                	  click: function () {
										  var display_name = $('#field-reportsfieldtype-label').val();
										  $('.display_name_'+unique_id).html(display_name);
										  var array_index = $('.display_name_'+unique_id).data('index');
										  var tbl_name = $('.display_name_'+unique_id).data('tbl_name');
										  var table = JSON.parse($('input[name="table_lists['+table_name+']"').val());
										  $(table).each(function(key,value){
											  if(value.field_name == field_name){
												  table[key].field_display_name=display_name;
											  }
										  });
										  $('input[name="table_lists['+table_name+']"').val(JSON.stringify(table));										  
										  /*jQuery.ajax({
													url: baseUrl +'/report-management/report-type-change-display-name',
													type: 'POST',
													data: {display_name:display_name,array_index:array_index,tbl_name:tbl_name},
													beforeSend:function (data) {showLoader();},
													success: function (data) {
														hideLoader();																		
													}
											});*/
											
											$(this).dialog('destroy').remove();											  
				                	  }
			                  }
			        ]
			    });			
	 //$("."+class_label).val('nelson');
	 //alert(class_label);
	 } 
	 
/*
 * Function to change display name of the table field 
 * */
function report_type_change_table_label(unique_value, actual_value)
{
	var table_display_name = $('.table_display_name_'+unique_value).val();
	if($('body').find('#edit-table-name').length == 0){
		$('body').append('<div class="dialog" id="edit-table-name" title="Edit Table Diplay Name"><div class="form-group"><div class="row input-field"><div class="col-md-3"><label for="table_full_name" class="form_label">Display Name</label></div><div class="col-md-7"><input type="text" name="ReportsFieldType[table_display_name]" class="form-control" id="field-reportsfieldtype-label" value="'+table_display_name+'"><div class="help-block"></div></div></div></div></div>');
	}
	$('#edit-table-name').dialog({ 
		modal: true,
		width:'40em',
		height:302,
		title:'Edit Table Display Name',
		close:function(){
			$(this).dialog('destroy').remove();
		},
		create: function(event, ui) { 						  
			 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                         $('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			},
		buttons: [
				{ 
					  text: "Default", 
					  "class": 'btn btn-primary pull-left',
					  "title": 'Default',
					  click: function () { 
						  $('#field-reportsfieldtype-label').val(actual_value);
					  } 
				  },
					{ 
					  text: "Cancel", 
					  "class": 'btn btn-primary',
					  "title": 'Cancel',
					  click: function () { 
						  $(this).dialog('destroy').remove();
					  } 
				  },
				   { 
					  text: "Update", 
					  "class": 'btn btn-primary',
						"title": 'Update',
						  click: function () {
							  var display_name = $('input[name="ReportsFieldType[table_display_name]"]').val();
							  $('.table_display_html_name_'+unique_value).html(display_name);
							  var table = JSON.parse($('input[name="table_lists['+unique_value+']"').val());
							  $(table).each(function(key,value){
								  table[key].table_full_name=display_name;
							  });
							  $('input[name="table_lists['+unique_value+']"').val(JSON.stringify(table));
							  $(this).dialog('destroy').remove();											  
						  }
				  }
		]
	});
}	 

/**
 * save report
 */
function edit_report_user_saved()
{
	var keys = $('#report-user-saved-grid').yiiGridView('getSelectedRows');
	 if(keys==''){
		alert("Please select a record to perform this action."); hideLoader(); return false;
	 }
	 
	 var newkeys = keys.toString().split(",");
	 var str = [];
	 for(var i=0;i<newkeys.length;i++){
		str.push(newkeys[i]);
	 }
	 if(str.length > 1){
		alert("Please select only 1 record to perform this action.");
		return false;
	 } else {
	
	$.ajax({
		type: 'post',
		url:baseUrl+'saved-report/save-report-popup',
		data: 'report_saved_id='+str,
		beforeSend:function (data) {showLoader();},
		success:function(response){
		hideLoader();
		if($('body').find('#save-report-access-popup').length == 0)
		{
			$('body').append('<div class="dialog" id="save-report-access-popup" title="Save Report"></div>');
		}
		$('#save-report-access-popup').html('').html(response);		
		$('#save-report-access-popup').dialog({ 
			modal: true,
			width:'80em',
			height:692,
			title:'Edit Saved Report Access',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                $('.ui-dialog-titlebar-close').attr("title", "Close");
                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			},
			close:function(){
				$(this).dialog('destroy').remove();
			},
			beforeClose: function(event){
				if(event.keyCode!=9) trigger = '';
				if(trigger!='Update') checkformstatus(event);
			},
			buttons: [
					{ 
						text: "Cancel", 
						"class": 'btn btn-primary',
						"title": 'Cancel',
						click: function () { 
							trigger = 'Cancel';
							$(this).dialog('close');
						} 
					},
					{ 
						text: "Update", 
						"class": 'btn btn-primary',
						"title": 'Update',
						click: function () { 
							trigger = 'Update';
							var save_report_to = $('#save-report-access-popup #report-save-to');
							var sharereport = $('#save-report-access-popup #share-report-by');
							var sharedchk = $('#save-report-access-popup #show_by_content').find('.chk:checked');
							var userId = $('#userId').val();
							var has_error = false;
							if(save_report_to.val() == 2 && sharereport.val() == '') {
								sharereport.closest('div.form-group').removeClass('has-success').addClass('has-error');
								sharereport.parent().parent().parent().find('.help-block').html('Share Report cannot be blank.');
								has_error = true;
							} 
							if(save_report_to.val() == 2 && sharedchk.length == 0) {
								$('.field-reportsusersaved-show_by').removeClass('has-success').addClass('has-error');
								$('.field-reportsusersaved-show_by').find('.help-block').addClass('clear').html('Option cannot be blank.');
								has_error = true;
							}
							if(save_report_to.val() == 2 && sharedchk.length > 0) {
								$('.field-reportsusersaved-show_by').removeClass('has-error').addClass('has-success');
								$('.field-reportsusersaved-show_by').find('.help-block').addClass('clear').html('');
							}
							if(!has_error){
								var reportdata = $('#report-type-format-dates').serialize();
								var usersaveddata = $('#frm_popup_ReportsUserSaved').serialize();
								$.ajax({
									url:baseUrl+'saved-report/save-report',
									type:'post',
									data:reportdata+'&'+usersaveddata,
									success:function(response){
										var obj = JSON.parse(response);
										if(obj.reports_saved_id!='' && obj.reports_saved_id!=0) {
											$.pjax.reload('#report-user-saved-grid', $.pjax.defaults);
											$('#report_saved_id').val(obj.reports_saved_id);
											$('button#save').hide();
											$('#save-report-access-popup').dialog('destroy').remove();
										} else {
											alert('Oops! something went wrong.');
											return false;
										}
									}
								});	
							}
						}
					}
				]
		});
	},complete:function(){
		setTimeout(function(){
			console.log('here');
			$('#show_by_data .inner_chk:checked').each(function(){
			if (!$(this).closest('div.content').is(':visible')) {
				$(this).closest('div.content').css('display','block');
				$(this).closest('div.content').prev('div').addClass('myheader-selected-tab');
			}
			});
		},1000);
	}
});
	 }
}
function filterfield_pop_up(id,report_type_id){
	if(report_type_id == undefined){
		report_type_id =0;
	}
	var filter=$('#reportypefilter_'+id).val();
	var grp=$('#reportypegrp_'+id).val();
	var field_name=$.trim($('#field_name_'+id).html());
	$.ajax({
			url:baseUrl+'report-management/add-report-fieldfilter&id='+id,
			beforeSend:function (data) {showLoader();},
			type:"POST",
			data:{report_type_id:report_type_id,filter:filter,grp:grp,field_name:field_name},			
			success:function(response){
			hideLoader();
			
			if($('body').find('#filter-field').length == 0){
				$('body').append('<div class="dialog" id="filter-field" title="Add Filter"></div>');
			}
			$('#filter-field').html('').html(response);							
			$('#filter-field').dialog({ 
			modal: true,
			width:'60em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
				$('.ui-dialog-titlebar-close').prop('title', 'Close');
				$('.ui-dialog-titlebar-close').prop('aria-label', 'Close');
			},
			close:function(){
				$(this).dialog('destroy').remove();
			},
			buttons: [
						{ 
						  text: "Clear", 
						  "class": 'btn btn-primary',
						  "title": 'Clear',
						  click: function () { 
							$('#formula').val(null);  
							$("#grp").attr('checked',false);
							$("#grp").prev().removeClass('checked');
						  } 
					   },
					   { 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog("close");
						  } 
					   },
					   { 
						  text: "Add", 
							"class": 'btn btn-primary',
							"title": 'Add',
							click: function () { 
								$('#reportypefilter_'+id).val($('#formula').val());	
								$('#reportypegrp_'+id).val(0);				
								if($("#grp").is(":checked")){
									$('#reportypegrp_'+id).val(1);
								}	
								$("#atagfiletr_"+id).find('em').removeClass('text-primary').addClass('text-danger');
								$(this).dialog("close");			
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
function remove_dialog_report_relationship_field_data(id,table_name,field_name,report_type_id,module)
{
	if(report_type_id == undefined){
		report_type_id =0;
	}
	if(module == undefined){
		module ='';
	}
	if(confirm("Are you sure you want to delete Field:"+table_name+"->"+field_name+"?"))
	{
		if(report_type_id > 0 && module != "")
		{
			jQuery.ajax({
						url: baseUrl +'/report-management/delete-'+module+'&report_type_id='+report_type_id+'&id='+id+'&table_name='+table_name,
						type: 'GET',
						beforeSend:function (data) {showLoader();},
						success: function (data) 
						{
							//console.log(data);return false;
							if(data == "OK")
							{
								hideLoader();
								$('#ReportsReportType #is_change_form').val('1'); // change flag to 1
								$('#is_change_form_main').val('1'); // change flag to 1
								$('.field_raw_'+id).remove();		
							}
							else
							{
								hideLoader();
								alert("This report type field is already used in a saved report which cannot be deleted!");
							}
						}
				});
		}
		else
		{
			$('.field_raw_'+id).remove();
		}
	}
}

function remove_grid_report_relationship_table(obj,table_display_name,table_name,report_type_id,module)
{
	if(report_type_id == undefined){
		report_type_id =0;
	}
	if(module == undefined){
		module ='';
	}
	if(confirm("Are you sure you want to delete Table:"+table_display_name+"?"))
	{
		if(report_type_id >0 && module != "")
		{
			jQuery.ajax({
						url: baseUrl +'/report-management/delete-'+module+'&report_type_id='+report_type_id+'&table_name='+table_name,
						type: 'GET',
						beforeSend:function (data) {showLoader();},
						success: function (data) 
						{
							if(data == "OK")
							{
								hideLoader();
								$('#ReportsReportType #is_change_form').val('1'); // change flag to 1
								$('#is_change_form_main').val('1'); // change flag to 1
								if($('tr[data-key="'+table_name+'"]').length)
								{
									$('tr[data-key="'+table_name+'"]').remove();
								}
								$(obj).closest('tr').remove();
								//console.log($('#get-report-type-grid').find('tbody tr').length);
								if($('#get-report-type-grid').find('tbody tr').length == 0)
								{
									$('.table_name_action_th span.fa-plus').show();
								} 
								else 
								{
									$('.table_name_action_th span.fa-plus').hide();
								}
							}
							else
							{
								hideLoader();
								alert("This report type table is already used in a saved report which cannot be deleted!");
							}
						}
				});
		}
		else
		{
			if($('tr[data-key="'+table_name+'"]').length)
			{
				$('tr[data-key="'+table_name+'"]').remove();
			}
			$(obj).closest('tr').remove();
			//console.log($('#get-report-type-grid').find('tbody tr').length);
			if($('#get-report-type-grid').find('tbody tr').length == 0)
			{
				$('.table_name_action_th span.fa-plus').show();
			} 
			else 
			{
				$('.table_name_action_th span.fa-plus').hide();
			}
		}
		
	}
}
