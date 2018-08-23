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

	jQuery(".billingModulesInvoice").on('click',function(){
		var module=jQuery(this).data('module');
		jQuery('.billingModulesInvoice').removeClass('active');
		if(module == 'invoice_criteria'){
			var topleftheader = $('.page-header');
			topleftheader.find('em').attr('class','fa fa-pie-chart');
			topleftheader.find('em').attr('title','Generate Invoices');
			loadergenerateinvoice();
			jQuery(this).addClass('active');
			if(!$('h1 span#header-right-client').hasClass('hide')) {
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')) {
				$('h1 span#header-right-clientcase').addClass('hide');
			}
			topleftheader.find('span.top-left-header').attr("tabindex","0");
			topleftheader.find('span.top-left-header').attr("title","Generate Invoices");
			topleftheader.find('span.top-left-header').html('Generate Invoices');
			topleftheader.find('span.pull-right').html(null);
		}
		if(module=='delete_saved_invoice'){
			var topleftheader = $('.page-header');
			topleftheader.find('em').attr('class','fa fa-pie-chart');
			topleftheader.find('em').attr('title','Saved Invoices');
			loaderdeletesavedinvoice();
			jQuery(this).addClass('active');
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
			topleftheader.find('span.top-left-header').attr("tabindex","0");
			topleftheader.find('span.top-left-header').attr("title","Saved Invoices");
			topleftheader.find('span.top-left-header').html('Saved Invoices');
			topleftheader.find('span.pull-right').html(null);
		}
		if(module=='final_invoice'){
			finalizeinvprocess(0);
			jQuery(this).addClass('active');
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}
		if(module=='display_saved_invoice'){
			var topleftheader = $('.page-header');
			topleftheader.find('em').attr('class','fa fa-pie-chart');
			topleftheader.find('em').attr('title','Saved Invoices');
			savedinvoice();
			jQuery(this).addClass('active');
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
			topleftheader.find('span.top-left-header').attr("tabindex","0");
			topleftheader.find('span.top-left-header').attr("title","Saved Invoices");
			topleftheader.find('span.top-left-header').html('Saved Invoices');
			topleftheader.find('span.pull-right').html(null);
		}
	});
});

function savedinvoice(){
	var targetUrl = baseUrl +'billing-generate-invoice/saved-invoice';
	var targetTitle = "Generate Invoices";
	historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-pie-chart');
	topleftheader.find('em').attr('title','Saved Invoices');
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title","Saved Invoices");
	topleftheader.find('span.top-left-header').html('Saved Invoices');
	jQuery('.billingModulesInvoice').removeClass('active');
	showLoader();
	//location.href = baseUrl +'billing-generate-invoice/saved-invoice';
	jQuery.ajax({
	       url: targetUrl,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
			   $('#finalizedinvoice_li').hide();
			   $('#deletesavedinvoice_li').show();
			   jQuery('.billingModulesInvoice[data-module="display_saved_invoice"]').addClass('active');
	       }
	});
}


/*
 * Loader generate invoice page
 */
function loadergenerateinvoice(){
	var targetUrl = baseUrl + 'billing-generate-invoice/billing-invoice-management';
	var targetTitle = "Generate Invoices";
	historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-pie-chart');
	topleftheader.find('em').attr('title','Generate Invoices');
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title","Generate Invoices");
	topleftheader.find('span.top-left-header').html('Generate Invoices');
	jQuery('.billingModulesInvoice').removeClass('active');
	showLoader();
	//location.href = baseUrl +'billing-generate-invoice/billing-invoice-management';
	jQuery.ajax({
	       url: targetUrl,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
			   $('#deletesavedinvoice_li').hide();
			   $('#finalizedinvoice_li').hide();
			   jQuery('.billingModulesInvoice[data-module="invoice_criteria"]').addClass('active');
	       }
	});
}

/*
 * Loader saved invoice
 */
function loadereditsavedinvoice(tasks){
    var targetUrl = baseUrl + 'billing-generate-invoice/billing-invoice-management&batchId='+tasks;
	var targetTitle = "Generate Invoices";
	historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-pie-chart');
	topleftheader.find('em').attr('title','Generate Invoices');
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title","Generate Invoices");
	topleftheader.find('span.top-left-header').html('Generate Invoices');
	jQuery('.billingModulesInvoice').removeClass('active');
	showLoader();
	//location.href = baseUrl +'billing-generate-invoice/billing-invoice-management';
	jQuery.ajax({
	       url: targetUrl,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
			   $('#deletesavedinvoice_li').hide();
			   $('#finalizedinvoice_li').hide();
			   jQuery('.billingModulesInvoice[data-module="invoice_criteria"]').addClass('active');
	       }
	});
	//location.href = baseUrl +'billing-generate-invoice/billing-invoice-management&batchId='+tasks;
}

/*
 * Delete saved invoice from generate invoice
 * @return mixed
 */
function loaderdeletesavedinvoice(){
	var tasks = $('#saved-invoice-grid').yiiGridView('getSelectedRows');
	if(tasks.length==0){
		alert("Please select a record to perform this action.");
		return false;
	}else{
		var newkeys = tasks.toString().split(",");
		var str = [];var str_val;
		for(var i=0;i<newkeys.length;i++){
			var val = JSON.parse(decodeURIComponent($( '.chk_invoice_id_'+newkeys[i] ).val()));
			str_val =  "#"+val['id'];
			str.push(str_val);
		}
		var str_count = str.length;
		if(confirm("Are you sure you want to Delete the selected "+str_count+" record(s): "+str+"?")){
			jQuery.ajax({
			       url: baseUrl +'/billing-generate-invoice/deletegenerateinvoice&batchId='+tasks,
			       type: 'get',
			       beforeSend:function (data) {showLoader();},
			       success: function (data) {
			    	   hideLoader();
					   savedinvoice();
			    	   //$.pjax.reload({container:"#saved-invoice-grid"});
			       }
			});
		}
	}
}

/**
 * Delete single saved invoice from grid
 */
function removesavedinvoices(id){
	if(confirm('Are you sure you want to Delete Batch #'+id+'?')){
		jQuery.ajax({
		       url: baseUrl +'/billing-generate-invoice/deletegenerateinvoice&batchId='+id,
		       type: 'get',
		       beforeSend:function (data) {showLoader();},
		       success: function (data) {
		    	   hideLoader();
				   savedinvoice();
		    	   //$.pjax.reload({container:"#saved-invoice-grid"});
		       }
		});
	}
}

/*
 * previous button display generated invoice
 * @returns
 */
function previousinvoicebtn(){
     //$("#display-generate-invoice-form").submit();
	 var targetUrl = baseUrl + 'billing-generate-invoice/billing-invoice-management';
		showLoader();
		//location.href = baseUrl +'billing-generate-invoice/billing-invoice-management';
		jQuery.ajax({
			url:  targetUrl,
			type: 'post',
			data: $('#display-generate-invoice-form').serialize(),
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				//jQuery('.right-side').html(data);
				$('#admin_main_container').html(data);
				$('input').customInput();
				$('#deletesavedinvoice_li').hide();
			   	$('#finalizedinvoice_li').hide();
			}
		});

}

/**
 * Add invoice display generated invoice
 * @returns
 */
function savedinvoicebackbtn(){
	var str = $('#filter_data').val();
	jQuery.ajax({
        url: baseUrl +'/billing-generate-invoice/add-invoice-details',
        type: 'post',
        data: {filter_data:str},
        beforeSend:function (data) {showLoader();},
        success: function (data) {
			hideLoader();
			if($.trim(data)=='OK'){
				savedinvoice();
			}

            //$.pjax.reload({container:"#saved-invoice-grid"});
            //location.href = baseUrl +'billing-generate-invoice/saved-invoice';

        }
    });
}

/**
 * Generate Invoice Percent Dialogue box
 */
function generateInvoiceedit(key, client_case_id)
{
	// form serialize data of generated invoice
	var str = $('#final_units_data_'+client_case_id+'_'+key).val();
	var type = $('#display_type').val();
	//var accum = $('#accumulated_'+client_case_id+'_'+key).val();

	// accumulated check
	/*if(accum=='fail'){
		alert("Please Select Non-Accumulated Record to Perform This action.");
		hideLoader();
		return false;
	}*/

	$.ajax({
		url:baseUrl+'billing-generate-invoice/edit-invoice',
		type: 'post',
		data: {'final_units':str,'display_type':type},
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('#edit-invoice').length == 0){
				$('#admin_right').append('<div class="dialog" id="edit-invoice" title="Edit Price Point"></div>');
			}
			$('#edit-invoice').html('').html(response);
			$('#edit-invoice').dialog({
				modal: true,
				width:'50em',
				height:456,
				create: function(event, ui) {
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close: function( event, ui ) {
					$(this).dialog('destroy').remove();
				},
				buttons:[
					  {
						  text: "Cancel",
						  "class": 'btn btn-primary',
						  "title": "Cancel",
						  click: function () {
							  $(this).dialog('destroy').remove();
						  }
					  },
					  {
						  text: "Update",
						  "class": 'btn btn-primary',
						  "title": "Update",
						  // click for update button
						  click: function () {
								var rate = $('#rate').val();
								var quantity = $('#quantity').val();
								var pre_rate = $('#pre_rate').val();
								var isnonbillable='';
								if($('#non_billable').is(':checked'))
									isnonbillable=2;

								var description ="";
								if($('#description').val() != undefined){
								var description = $('#description').val();
								}
								var billing_unit_id = $('#billing_unit_id').val();
								if(rate==''){alert("Please enter a Rate.");return false;}
								if(quantity==''){alert("Please enter a Quantity.");return false;}

								jQuery.ajax({
									url : baseUrl +'/billing-generate-invoice/update-invoice',
									type : 'post',
									data : {
										'temp_rate':rate,
										'quantity':quantity,
										'billing_unit_id':billing_unit_id,
										'desc':description,
										'isnonbillable':isnonbillable,
										'pre_rate':pre_rate
									},
									beforeSend:function (response) {showLoader();},
									success: function (response) {
										if(response == 'OK'){
											var filter_data = $('#filter_data').val();
											$.ajax({
												url:baseUrl+'billing-generate-invoice/display-generate-invoice',
												type: 'post',
												data: {'filter_data':filter_data,'flag':'reload'},
												beforeSend:function (data) {showLoader();},
												success:function(response){
													hideLoader();
													$('#admin_main_container').html(response);
													$('#edit-invoice').dialog( "close" );
												}
											});
										} else {
											hideLoader();
										}
									}
								});
						  }
					 }
				 ]
			});
		}
	});
}

/**
 * Generate Invoice Percent Dialogue box
 */
function generateInvoiceeditNew(key, client_case_id,invoicetype,replace_id)
{
	// form serialize data of generated invoice
	var str = $('#final_units_data_'+client_case_id+'_'+key).val();
	var type = $('#display_type').val();
	$.ajax({
		url:baseUrl+'billing-generate-invoice/edit-invoice',
		type: 'post',
		data: {'final_units':str,'display_type':type},
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('#edit-invoice').length > 0){
				$('.ui-dialog:has(#edit-discount)').empty().remove();
			}
			if($('#edit-invoice').length == 0){
				$('#admin_right').append('<div class="dialog" id="edit-invoice" title="Edit Price Point"></div>');
			}
			$('#edit-invoice').html('').html(response);
			$('#edit-invoice').dialog({
				modal: true,
				width:'50em',
				height:456,
				create: function(event, ui) {
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close: function( event, ui ) {
					$(this).dialog('destroy').remove();
				},
				buttons:[
					  {
						  text: "Cancel",
						  "class": 'btn btn-primary',
						  "title": "Cancel",
						  click: function () {
							  $(this).dialog('destroy').remove();
						  }
					  },
					  {
						  text: "Update",
						  "class": 'btn btn-primary',
						  "title": "Update",
						  // click for update button
						  click: function () {
								var rate = $('#rate').val();
								var quantity = $('#quantity').val();
								var pre_rate = $('#pre_rate').val();
								var isnonbillable='';
								if($('#non_billable').is(':checked'))
									isnonbillable=2;

								var description ="";
								if($('#description').val() != undefined){
								var description = $('#description').val();
								}
								var billing_unit_id = $('#billing_unit_id').val();
								if(rate==''){alert("Please enter a Rate.");return false;}
								if(quantity==''){alert("Please enter a Quantity.");return false;}

								jQuery.ajax({
									url : baseUrl +'/billing-generate-invoice/update-invoice',
									type : 'post',
									data : {
										'temp_rate':rate,
										'quantity':quantity,
										'billing_unit_id':billing_unit_id,
										'desc':description,
										'isnonbillable':isnonbillable,
										'pre_rate':pre_rate
									},
									beforeSend:function (response) {showLoader();},
									success: function (response) {
										if(response == 'OK'){
											var filter_data = $('#filter_data').val();
											var expandUrl="";
											if(invoicetype=='I'){
												expandUrl=baseUrl+'billing-generate-invoice/billing-itemized-invoice';
											}else{
												if(invoicetype=='C')
													expandUrl=baseUrl+'billing-generate-invoice/billing-consolidated-invoice';
											}
											$.ajax({
												url:expandUrl,
												type: 'post',
												data: {'filter_data':filter_data,'flag':'reload','expandRowKey':replace_id},
												beforeSend:function (data) {showLoader();},
												success:function(response){
													hideLoader();
													//$('#admin_main_container').html(response);
													$('.kv-expanded-row[data-key="'+replace_id+'"]').html(response);
													$('#edit-invoice').dialog( "close" );
												}
											});
										} else {
											hideLoader();
										}
									}
								});
						  }
					 }
				 ]
			});
		}
	});
}

/**
 * Final invoice Submit
 **/
function finalizeinvprocess(t){
		showLoader();
        setTimeout(function () {
		if (t == 0) {
			selectAccumExist(true);
		} else {
			finalize_invoice();
		}
		if (t == 0) {
			t++;
			finalizeinvprocess(t);
		}
     }, 1000);
}

/**
 * Check Accumulated invoice exist or not
 */
function selectAccumExist(stachecked){
	$('.servicetask_items:checkbox:checked').each(function(){
		if($(this).hasClass('Nonbillable') == false){
			var accuclass = "accu_"+$(this).attr('client-id')+"_"+$(this).attr('pricing-id');
			checkAccuIfAny(accuclass,stachecked,$(this).attr('team-loc'));
		}
	});
}

/**
 * Check Accumulated invoice exist or not (if exist checked the checkbox of accumulated invoice)
 */
function checkAccuIfAny(accuclass,ischecked,loc){
	var acculist = $('.'+accuclass+'[team-loc="'+loc+'"]');
	acculist.prop('checked',ischecked);
	if(ischecked){
		acculist.next('label').addClass('checked');
		acculist.attr('onclick','return false');
		acculist.next('label').attr('onclick','return false');
	} else {
		acculist.next('label').removeClass('checked');
		acculist.next('label').removeAttr('onclick');
		acculist.removeAttr('onclick');
	}
}
function unserialize(serialize) {
	let obj = {};
	serialize = serialize.split('&');
	for (let i = 0; i < serialize.length; i++) {
		thisItem = serialize[i].split('=');
		obj[decodeURIComponent(thisItem[0])] = decodeURIComponent(thisItem[1]);
	};
	return obj;
};
/**
 * Final invoice Validation & submission
 */
function finalize_invoice(){
	var form = $('#add-finalized-invoice');
	var str = form.serialize();
	if(!$('.inner_invoice2:checked').length){
		alert("Please select a record to perform this action.");
		$('Nonbillable').siblings().removeClass('checked');
		hideLoader();
		return false;
	}
	if($('.Nonbillable:checked').length == $('.inner_invoice2:checked').length){
		alert("Please select 1+ Billable Item to proceed with the Finalize Invoice process.");
		$('Nonbillable').siblings().removeClass('checked');
		hideLoader();
		return false;
	}
	var formdata = form.find('.inner_invoice2:checked');
	var display_type = form.find('#display_type').val();
	var i = 0;
	var clientcasedata = {};
	var diffclientcase = 0;
	var diffclient=0;
	formdata.each(function() {
		if(i==0)
		{
			clientcasedata.clientid = $(this).attr('client-id');
			clientcasedata.clientcaseid = $(this).attr('case-id');
		}
		else {
			if(display_type=='Itemized')
			{
				if($(this).attr('client-id')!=clientcasedata.clientid && $(this).attr('case-id')!=clientcasedata.clientcaseid)
				{
					hideLoader();
					diffclientcase = 1;
					return false;
				}
			}
			else if(display_type == 'Consolidated')
			{
				if($(this).attr('client-id')!=clientcasedata.clientid)
				{
					hideLoader();
					diffclient = 1;
					return false;
				}
			}
		}

		i++;
	});
	if(diffclientcase==1)
	{
		alert('Please select price points for only one Client-Case.');
		return false;
	}
	else if(diffclient==1)
	{
		alert('Please select price points for only one Client.');
		return false;
	}
	if($('#existinginvoices').length>0)
	{
		jQuery.ajax({
			url: baseUrl +'/billing-generate-invoice/get-existing-invoices',
			type: 'post',
			dataType: 'json',
			data   : {'client_id':clientcasedata.clientid,'client_case_id':clientcasedata.clientcaseid,'display_type':display_type},
			beforeSend:function (response) {showLoader();},
			success: function (response) {
				hideLoader();
				var invoice_data = response;
				var inv_count = invoice_data.invoice_count;
				if(inv_count > 0){

					if(inv_count == 1)
					{
						$('#existinginvoices').html(invoice_data.html_text);
					}
					else
					{
						$('#existinginvoices').find('#selinvoices').html(invoice_data.html_text);
					}

					var finalizeinvoicedialog = $('#finalizeinvoice-dialog');

					finalizeinvoicedialog.dialog({
							title: 'Finalize Invoice',
							autoOpen: true,
							resizable: false,
							width: "50em",
							height: 302,
							modal: true,
							create: function (event, ui) {
									finalizeinvoicedialog.prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
									finalizeinvoicedialog.prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
									finalizeinvoicedialog.prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
							},
							buttons: [
									{
											text: "Cancel",
											"title": "Cancel",
											"class": 'btn btn-primary',
											click: function () {
													finalizeinvoicedialog.dialog("close");
											}
									},
									{
											text: "Finalize",
											"title": "Finalize",
											"class": 'btn btn-primary',
											click: function () {
													var invoiceoperation = $('#finalizeinvoice-dialog input[type="radio"]:checked').val();
													// console.log(taskoperation);return false;
													if(invoiceoperation=='existinginvoice')
													{
														form.find('#finalize_type').val(1);
														if(inv_count == 1)
														{
															form.find('#existing_invoice_id').val($('#finalizeinvoice-dialog #selected_invoice').val());
														} else {
															form.find('#existing_invoice_id').val($('#finalizeinvoice-dialog #selinvoices').val());
														}
													}
													//if(confirm("Are you sure you want to Finalize Invoice?")){
														form.submit();
													/*} else {
														hideLoader();
														selectAccumExist(false);
														return false;
													}*/
											}
									}
							],
							open: function () {

													hideLoader();
													if (finalizeinvoicedialog.hasClass('hide')) {
														finalizeinvoicedialog.removeClass('hide');
													}
													finalizeinvoicedialog.find('#rdo_newinvoice').prop('checked', true);
													finalizeinvoicedialog.find('label[for="rdo_newinvoice"]').addClass('checked');
													finalizeinvoicedialog.find('#rdo_newinvoice').focus();
													finalizeinvoicedialog.find('#rdo_existinginvoice').prop('checked', false);
													finalizeinvoicedialog.find('label[for="rdo_existinginvoice"]').removeClass('checked');

													$('#rdo_newinvoice').click(function() {
														$('#existinginvoices').hide();
													});
													$('#rdo_existinginvoice').click(function() {
														$('#existinginvoices').show();
														if($('#existinginvoices').find('select'))
															$('#existinginvoices').find('select').select2();
													});

							},
							close: function() {
								finalizeinvoicedialog.find('#existinginvoices').hide();
								finalizeinvoicedialog.addClass('hide');
								finalizeinvoicedialog.dialog('destroy');
							}
					});
				}
				else {
					//if(confirm("Are you sure you want to Finalize Invoice?")){
						form.submit();
				/*	} else {
						hideLoader();
						selectAccumExist(false);
						return false;
					}*/
				}
			}
		});
	}
	else {
			form.submit();

	}
}


/**
 * Generated Invoice Edit dialogue box
 */
function generateInvoicepercent(key,client_case_id)
{
	// form serialize data of generated invoice
	var str = $('#final_units_data_'+client_case_id+'_'+key).val();
	var invoiced = $('#invoiced_'+client_case_id+'_'+key).val();
	var display_type = $('#display_type').val();
	var filter_data = $('#filter_data').val();

	$.ajax({
		url:baseUrl+'billing-generate-invoice/add-discount-invoice',
		data: {'final_units':str,'display_type':display_type},
		type: 'post',
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('#apply-discount').length > 0){
				$('.ui-dialog:has(#apply-discount)').empty().remove();

			}
			if($('#apply-discount').length == 0){
				$('#admin_right').append('<div class="dialog" id="apply-discount" title="Apply Discount"></div>');
			}
			$('#apply-discount').html('').html(response);
			$('#apply-discount').dialog({
				modal: true,
				width:'50em',
				height:302,
				create: function(event, ui) {
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				buttons: [
					  {
						  text: "Cancel",
						  "class": 'btn btn-primary',
						  "title": "Cancel",
						  click: function () {
							  $(this).dialog('destroy').remove();
						  }
					  },
					  {
						  text: "Update",
						  "class": 'btn btn-primary',
						  "title": "Update",
						  // click for Apply Discount
						  click: function () {
								var discount = $('#discount').val();
								var discount_reason = $('#discount_reason').val();
								var billing_unit_id = $('#billing_unit_id').val();
								if(discount==''){alert("Please enter a Discount.");return false;}
								jQuery.ajax({
									url: baseUrl +'/billing-generate-invoice/update-discount',
									type: 'post',
									//data   : 'temp_discount='+discount+'&temp_discount_reason='+discount_reason+'&billing_unit_id='+billing_unit_id,
									data   : {'temp_discount':discount,'temp_discount_reason':discount_reason,'billing_unit_id':billing_unit_id},
									beforeSend:function (response) {showLoader();},
									success: function (response) {
										hideLoader();
										if(response == 'OK'){
											if(response == 'OK'){
												var filter_data = $('#filter_data').val();
												$.ajax({
													url:baseUrl+'billing-generate-invoice/display-generate-invoice',
													type: 'post',
													data: {'filter_data':filter_data,'flag':'reload'},
													beforeSend:function (data) {showLoader();},
													success:function(response){
														hideLoader();
														$('#admin_main_container').html(response);
														$('#apply-discount').dialog( "close" );
													}
												});
											} else {
												hideLoader();
											}
										}
									}
								});
						  }
					  }
				 ]
			});
		}
	});
}

/**
 * Generated Invoice Edit dialogue box
 */
function generateInvoicepercentNew(key,client_case_id,invoicetype,replace_id)
{
	// form serialize data of generated invoice
	var str = $('#final_units_data_'+client_case_id+'_'+key).val();
	var invoiced = $('#invoiced_'+client_case_id+'_'+key).val();
	var display_type = $('#display_type').val();
	var filter_data = $('#filter_data').val();

	$.ajax({
		url:baseUrl+'billing-generate-invoice/add-discount-invoice',
		data: {'final_units':str,'display_type':display_type},
		type: 'post',
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('#apply-discount').length > 0){
				$('.ui-dialog:has(#apply-discount)').empty().remove();
			}
			if($('#apply-discount').length == 0){
				$('#admin_right').append('<div class="dialog" id="apply-discount" title="Apply Discount"></div>');
			}
			$('#apply-discount').html('').html(response);
			$('#apply-discount').dialog({
				modal: true,
				width:'50em',
				height:302,
				create: function(event, ui) {
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close: function( event, ui ) {
					$(this).dialog('destroy').remove();
				},
				buttons: [
					  {
						  text: "Cancel",
						  "class": 'btn btn-primary',
						  "title": "Cancel",
						  click: function () {
							  $(this).dialog('destroy').remove();
						  }
					  },
					  {
						  text: "Update",
						  "class": 'btn btn-primary',
						  "title": "Update",
						  // click for Apply Discount
						  click: function () {
								var discount = $('#discount').val();
								var discount_reason = $('#discount_reason').val();
								var billing_unit_id = $('#billing_unit_id').val();
								if(discount==''){alert("Please enter a Discount.");return false;}
								jQuery.ajax({
									url: baseUrl +'/billing-generate-invoice/update-discount',
									type: 'post',
									//data   : 'temp_discount='+discount+'&temp_discount_reason='+discount_reason+'&billing_unit_id='+billing_unit_id,
									data   : {'temp_discount':discount,'temp_discount_reason':discount_reason,'billing_unit_id':billing_unit_id},
									beforeSend:function (response) {showLoader();},
									success: function (response) {
										hideLoader();
										if(response == 'OK'){
											if(response == 'OK'){
												var filter_data = $('#filter_data').val();
												var expandUrl="";
												if(invoicetype=='I') {
													expandUrl=baseUrl+'billing-generate-invoice/billing-itemized-invoice';
												} else {
													if(invoicetype=='C')
														expandUrl=baseUrl+'billing-generate-invoice/billing-consolidated-invoice';
												}
												$.ajax({
													url:expandUrl,
													type: 'post',
													data: {'filter_data':filter_data,'flag':'reload','expandRowKey':replace_id},
													beforeSend:function (data) {showLoader();},
													success:function(response){
														hideLoader();
														//$('#admin_main_container').html(response);
														$('.kv-expanded-row[data-key="'+replace_id+'"]').html(response);
														$('#apply-discount').dialog( "close" );
													}
												});
											} else {
												hideLoader();
											}
										}
									}
								});
						  }
					  }
				 ]
			});
		}
	});
}
