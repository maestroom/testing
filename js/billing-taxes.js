$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                $('.ui-dialog-titlebar-close').attr("title", "Close");
                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	}
});

jQuery(document).ready(function()
{
	var host = window.location.href; //.hostname
	var httPpath = "";
	if (host.indexOf('index.php')) {
		httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
	}
	
	jQuery(".billingModulesTax").on('click',function(event) {
		var module=jQuery(this).data('module'); 
		jQuery('.billingModulesTax').removeClass('active');
		if(module == 'tax_classes'){
		//	setTitle('fa-briefcase','Tax Classes');
			var topleftheader = $('.page-header');
			topleftheader.find('em').attr('class','fa fa-pie-chart');
			topleftheader.find('em').attr('title','Tax Management - Tax Classes');
			loaderclasspage();
			jQuery(this).addClass('active');
			topleftheader.find('span.top-left-header').attr("tabindex","0");
			topleftheader.find('span.top-left-header').attr("title","Tax Management - Tax Classes");
			topleftheader.find('span.top-left-header').html('Tax Management - Tax Classes');
			topleftheader.find('span.pull-right').html(null);
//			if(!$('h1 span#header-right-client').hasClass('hide')){
//				$('h1 span#header-right-client').addClass('hide');
//			}
//			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
//				$('h1 span#header-right-clientcase').addClass('hide');
//			}
		}
		if(module == 'tax_codes'){
		//	setTitle('fa-briefcase','Tax Codes');
			var topleftheader = $('.page-header');
			topleftheader.find('em').attr('class','fa fa-pie-chart');
			topleftheader.find('em').attr('title','Tax Management - Tax Codes');
			loadercodepage();
			jQuery(this).addClass('active');
			topleftheader.find('span.top-left-header').attr("tabindex","0");
			topleftheader.find('span.top-left-header').attr("title","Tax Management - Tax Codes");
			topleftheader.find('span.top-left-header').html('Tax Management - Tax Codes');
			topleftheader.find('span.pull-right').html(null);
//			if(!$('h1 span#header-right-client').hasClass('hide')){
//				$('h1 span#header-right-client').addClass('hide');
//			}
//			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
//				$('h1 span#header-right-clientcase').addClass('hide');
//			}
		}
	});
});

function loaderclasspage()
{
	var targetUrl = baseUrl +'billing-taxes/tax-classes';
	var targetTitle = "Tax Management - Tax Classes"; 
	historyPushState(targetUrl,targetTitle);
	showLoader();
	//location.href = baseUrl +'billing-taxes/tax-classes';
	jQuery.ajax({
	       url: baseUrl +'/billing-taxes/tax-classes',
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
	       } 
		});
}

function loadercodepage()
{
	showLoader();
	var targetUrl = baseUrl +'billing-taxes/tax-codes';
	var targetTitle = "Tax Management - Tax Codes"; 
	historyPushState(targetUrl,targetTitle);
	//location.href = baseUrl +'billing-taxes/tax-codes';
	//addtaxcode();
	jQuery.ajax({
		url: baseUrl +'/billing-taxes/tax-codes',
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			//jQuery('.right-side').html(data);
			$('#admin_main_container').html(data);
			$('input').customInput();
		} 
	});
}

/**
 * Add Tax Code Form
 */
function addtaxcode(){
	jQuery.ajax({
	       url: baseUrl +'/billing-taxes/add-tax-codes',
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery('.right-side').html(data);
	       } 
		});
}

/*
 * Add Tax Class Form
 */
function addtaxclass()
{
	jQuery.ajax({
       url: baseUrl +'/billing-taxes/add-tax-classes',
       type: 'get',
       beforeSend:function (data) {showLoader();},
       success: function (data) {
    	   hideLoader();
    	   jQuery('.right-side').html(data);
       } 
	});
}

/**
 * Delete Tax Code From Grid
 */
function removetaxcode(){
	var keys = $('#tax-codes-grid').yiiGridView('getSelectedRows');
	
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	} else {
		
		var newkeys = keys.toString().split(",");
		var str = [];var str_val;
		for(var i=0;i<newkeys.length;i++){
			var val = JSON.parse(decodeURIComponent($( '.chk_tax_code_'+newkeys[i] ).val()));
			str_val =  ""+val['tax_code'];
			str.push(str_val);
		}
		var str_count = str.length;
		if(confirm('Are you sure you want to Delete the selected '+str_count+' record(s): '+str+'?')){
			showLoader();
			jQuery.ajax({
			       url: baseUrl +'/billing-taxes/delete-tax-codes',
			       type: 'post',
			       data: 'keys='+keys,
			       beforeSend:function (data) {showLoader();},
			       success: function (data) {
			    	   hideLoader();
					   if(data=='FINALINVOICE') {
							alert('Can not delete selected tax code one of them is used in Final Invoice.');
							return false;
					   } else {
							loadercodepage();
					   }
	               	   //$.pjax.reload('#tax-codes-grid', $.pjax.defaults);
					   
			       } 
			});
		}
	}
}

/**
 * Remove single tax class from grid in tax class
 */
 function removetaxclassrow(keys,class_name){
	 if(confirm('Are you sure you want to Delete '+class_name+'?')){
		showLoader();
		jQuery.ajax({
			   url: baseUrl +'/billing-taxes/delete-tax-classes',
			   type: 'post',
			   data: 'keys='+keys,
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {	
				   var response = jQuery.parseJSON(data);				   
				   hideLoader();			   				   
				   if(response.status == 'fail'){
						alert(class_name+" is already used in "+response.tax_code+".");
						return false;
				   } else {
						loaderclasspage();
						//$.pjax.reload('#tax-classes-grid', $.pjax.defaults);
				   }
			   } 
		});
	}	
 }
 
 /**
 * Remove single tax class from grid in tax code
 */
 function removetaxcoderow(keys,tax_code){
	 if(confirm('Are you sure you want to Delete '+tax_code+'?')){
		showLoader();
		jQuery.ajax({
			   url: baseUrl +'/billing-taxes/delete-tax-codes',
			   type: 'post',
			   data: {'keys':keys},
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   hideLoader();
				   //console.log(tax_code);
				   //$.pjax.reload('#tax-codes-grid', $.pjax.defaults);
				   if(data=='FINALINVOICE') {
						alert('Can not delete tax code it is used in Final Invoice.');
						return false;
				   } else {
						loadercodepage();
				   }
			   } 
		});
	}
 }

/*
 * Delete Tax Classes from Grid
 */
function removetaxclass()
{
	var keys = $('#tax-classes-grid').yiiGridView('getSelectedRows');
	
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	} else {
		var newkeys = keys.toString().split(",");
		var str = [];var str_val;
		for(var i=0;i<newkeys.length;i++){
			var val = JSON.parse(decodeURIComponent($( '.chk_class_name_'+newkeys[i] ).val()));
			str_val =  " "+val['class_name'];
			str.push(str_val);
		}
		var str_count = str.length;
		if(confirm('Are you sure you want to Delete the selected '+str_count+' record(s): '+str+'?')){
			showLoader();
			jQuery.ajax({
			       url: baseUrl +'/billing-taxes/delete-tax-classes',
			       type: 'post',
			       data: 'keys='+keys,
			       beforeSend:function (data) {showLoader();},
			       success: function (data) {
					    var response = jQuery.parseJSON(data);				   
					   hideLoader();			   				   
					   if(response.status == 'fail'){
							alert("Tax Class is already used in Tax Code.");
							return false;
					   } else {
						   loaderclasspage();
							//$.pjax.reload('#tax-classes-grid', $.pjax.defaults);
					   }			    	  
			       } 
			});
		}
	}
}

/**
 * Edit Tax Code From Grid
 */
function edittaxcode(keys){
	jQuery.ajax({
		url: baseUrl +'/billing-taxes/update-tax-codes&id='+keys,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('.right-side').html(data);
		} 
	});
}
/*
 * Edit Tax classes from Grid
 */
function edittaxclass(keys)
{
	jQuery.ajax({
		url: baseUrl +'/billing-taxes/update-tax-classes&id='+keys,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			jQuery('.right-side').html(data);
		} 
	});
}

/**
* get selected clients in tax code add/edit page
*/
function selectedclients(id)
{
		$.ajax({
			url:baseUrl+'billing-taxes/get-taxcode-clients-lists&id='+id,
			beforeSend:function (data) { showLoader(); },
			success:function(response){
			hideLoader();
			if($('body').find('#availabl-price-points').length == 0){
				$('body').append('<div class="dialog" id="availabl-price-points" title="Add Available Clients"></div>');
			}
			$('#availabl-price-points').html('').html(response);		
			$('#availabl-price-points').dialog({ 
			modal: true,
			height:456,
			width:'50em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                $('.ui-dialog-titlebar-close').attr("title", "Close");
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
							  $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
						  text: "Add", 
							"class": 'btn btn-primary',
							"title": 'Add',
							click: function () { 
								$('#TaxCode #is_change_form').val('1'); // change flag to 1
								$('#TaxCode #is_change_form_main').val('1');  // change flag to 1
								$('.client-tax-code:checked').each(function(){
									var price_point_id = this.value;
									var teams = $(this).data('client');
									if($('#form-tax-codes').find('.clients_'+price_point_id).length == 0){
										$('#form-tax-codes').append('<tr class="clients_'+price_point_id+'"><input class="clients" type="hidden" name="clients[]" value='+price_point_id+' /><td>'+teams+'</td><td><a href="javascript:void(0);" onClick="remove_clientcode('+price_point_id+');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
									}
								});
								$('input').customInput();
								$(this).dialog('destroy').remove(); 
							}
					  }
				]
			});	
		}
	});
}
// remove price point	
function remove_clientcode(id){
	var rs = $('#form-tax-codes').find('.clients_'+id);
	$('#TaxCode #is_change_form').val('1'); //  change flag to 1
	$('#is_change_form_main').val('1'); //  change flag to 1
	rs.remove();
}

/**
* get selected price points in tax class add/edit page
*/
function selectedpricepoint(id)
{    
		$.ajax({
			url:baseUrl+'billing-taxes/get-price-point-lists&id='+id,
			beforeSend:function (data) {showLoader();},
			success:function(response){
			hideLoader();
			if($('body').find('#availabl-price-points').length == 0){
				$('body').append('<div class="dialog" id="availabl-price-points" title="Add Available Price Points"></div>');
			}
			$('#availabl-price-points').html('').html(response);		
			$('#availabl-price-points').dialog({ 
			modal: true,
			height:456,
			width:'50em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                $('.ui-dialog-titlebar-close').attr("title", "Close");
                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
			},
			close:function(){
				$(this).dialog('destroy').remove();
				$('#pricepoint-tax-classes').focus();
			},
			beforeClose: function(event){
				if(event.keyCode==27) trigger = '';
				if(trigger != 'Add') checkformstatus(event);
			},
			buttons: [
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () {
							  trigger = 'Cancel';
							  $(this).dialog('destroy').remove();
							  $('#pricepoint-tax-classes').focus();
						  } 
					   },
					   { 
						  text: "Add", 
						  "class": 'btn btn-primary',
							"title": 'Add',
							  click: function () { 
									trigger = 'Add';
									$('.team-price-point:checked').each(function(event){
										$('#TaxClass #is_change_form').val('1'); // change flag to 1
										$('#TaxClass #is_change_form_main').val('1'); // change flag to 1
										var price_point_id = this.value;
										var teams = $(this).data('teams');
										$('.price-point:checked').each(function(){
											var teams_name = $(this).data('team');
											if(price_point_id == teams_name){
												var inner_point_id = this.value;
												var price_point = $(this).data('pricepoint');
												if($('#form-tax-classes').find('.teamspricepoint_'+price_point_id+'_'+inner_point_id).length == 0){
													$('#form-tax-classes').append('<tr class="teamspricepoint_'+price_point_id+'_'+inner_point_id+'"><input class="pricepointlist" type="hidden" name="pricepointlist[]" value='+inner_point_id+' /><td>'+teams+'</td><td>'+price_point+'</td><td><a href="javascript:void(0);" onClick="remove_pricepoint('+price_point_id+','+inner_point_id+');"><em class="fa fa-close text-primary" title="Delete"></em></a></td></tr>');
												}
											}
										});
									});
									$('input').customInput();
									$(this).dialog('destroy').remove(); 
							  }
					  }
				]
			});	
		}
	});
}
	
// remove price point	
function remove_pricepoint(pricepoint, inner_point_id){
	var rs = $('#form-tax-classes').find('.teamspricepoint_'+pricepoint+'_'+inner_point_id);
	$('#TaxClass #is_change_form').val('1'); // change flag to 1
	$('#TaxClass #is_change_form_main').val('1'); // change flag to 1
	rs.remove();
}
