$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) {
            $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
            $('.ui-dialog-titlebar-close').attr("title", "Close");
            $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	}
});

jQuery(document).ready(function()
{
	//$('#exp_finalize_inv_li').hide();
	//$('#delete_finalize_inv_li').hide();
	var host = window.location.href; //.hostname
	var httPpath = "";
	if (host.indexOf('index.php')) {
		httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
	}
	jQuery(".billingModulesFinalize").on('click',function(){
		var module=jQuery(this).data('module');
		jQuery('.billingModulesFinalize').removeClass('active');
		if(module == 'finalized_invoices'){
			loaderfinalinvoice();
			jQuery(this).addClass('active');
			//$('#exp_finalize_inv_li').show();
    		//$('#delete_finalize_inv_li').show();
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}

		// preview finalized invoice
		if(module=='preview_finalized_invoices'){
			previewfinlizedinvoice();
			jQuery(this).addClass('active');
			var topleftheader = $('.page-header');
			topleftheader.find('span.pull-right').html(null);
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}

		// delete finalized invoice
		if(module=='delete_finalized_invoices'){
			removefinlizedinvoice();
			jQuery(this).addClass('active');
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}

		// Export finalized invoice
		if(module=='export_finalized_invoices'){
			bulkexportrequest_pdf();
			jQuery(this).addClass('active');
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}

		// close finalized invoice
		if(module=='close_finalized_invoices'){
					var bulkcloseinvoicedialog = $('#bulkcloseinvoice-closed-dialog');
					if (bulkcloseinvoicedialog.hasClass('hide')) {
							bulkcloseinvoicedialog.removeClass('hide');
					}
					if($('#close-invoice-form').length){
							$('#close-invoice-form').html(null);
							$('#close-invoice-form').html($('#final_invocies_gird').find('.table-responsive').find('.filters').html());
					}
					closedInvoices(bulkcloseinvoicedialog);

		}

		// merge finalized invoice
		if(module=='merge_finalized_invoices'){
			var mergeinvoicedialog = $('#mergeinvoice-dialog');
			if (mergeinvoicedialog.hasClass('hide')) {
				mergeinvoicedialog.removeClass('hide');
			}
			mergeInvoices(mergeinvoicedialog);

}

	});

	jQuery(".billingModulesClose").on('click',function(){
		var module=jQuery(this).data('module');
		jQuery('.billingModulesClose').removeClass('active');
		if(module == 'closed_invoices'){
			loaderclosedinvoice();
			jQuery(this).addClass('active');
			//$('#exp_finalize_inv_li').show();
    		//$('#delete_finalize_inv_li').show();
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}

		// preview finalized invoice
		if(module=='preview_finalized_invoices'){
			previewfinlizedinvoice();
			jQuery(this).addClass('active');
			var topleftheader = $('.page-header');
			topleftheader.find('span.pull-right').html(null);
			if(!$('h1 span#header-right-client').hasClass('hide')){
				$('h1 span#header-right-client').addClass('hide');
			}
			if(!$('h1 span#header-right-clientcase').hasClass('hide')){
				$('h1 span#header-right-clientcase').addClass('hide');
			}
		}



		// Export closed invoice
if(module=='export_closed_invoices'){
	bulkexportrequestclosed_pdf();
	jQuery(this).addClass('active');
	if(!$('h1 span#header-right-client').hasClass('hide')){
		$('h1 span#header-right-client').addClass('hide');
	}
	if(!$('h1 span#header-right-clientcase').hasClass('hide')){
		$('h1 span#header-right-clientcase').addClass('hide');
	}
}
		// reopen finalized invoice
		if(module=='reopen_finalized_invoices'){
					var bulkreopeninvoicedialog = $('#bulkreopeninvoice-reopen-dialog');
					if (bulkreopeninvoicedialog.hasClass('hide')) {
							bulkreopeninvoicedialog.removeClass('hide');
					}
					if($('#reopen-invoice-form').length){
							$('#reopen-invoice-form').html(null);
							$('#reopen-invoice-form').html($('#closed_invoices_grid').find('.table-responsive').find('.filters').html());
					}
					reopenInvoices(bulkreopeninvoicedialog);

		}

	});
});

/**
 * Preview finalized invoice
 */
 function previewfinlizedinvoice(){
	var keys = $('#final-invoiced-grid').yiiGridView('getSelectedRows');
	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	} else {
		if(keys.length > 1){
			alert('Please select a single record to perform this action.');
		}else{
			showLoader();
			location.href = baseUrl +'billing-finalized-invoice/preview-invoice&invoiced_id='+keys;
		}
	}
 }

/**
 * Final invoiced grid page
 */
function loaderfinalinvoice(){
	var targetUrl = baseUrl +'billing-finalized-invoice/finalized-invoices';
	var targetTitle = "Finalized Invoices";
    historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-money');
	topleftheader.find('em').attr('title','Finalized Invoices');
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title","Finalized Invoices");
	topleftheader.find('span.top-left-header').html('Finalized Invoices');
	jQuery('.billingModulesFinalize').removeClass('active');
	showLoader();
    location.href = baseUrl +'billing-finalized-invoice/finalized-invoices';


	/*jQuery.ajax({
	       url: targetUrl,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
			   jQuery('.billingModulesFinalize[data-module="finalized_invoices"]').addClass('active');
	       }
	});*/
}

/**
 * Final invoiced grid page
 */
function loaderclosedinvoice(){
	var targetUrl = baseUrl +'billing-closed-invoice/closed-invoices';
	var targetTitle = "Closed Invoices";
    historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-money');
	topleftheader.find('em').attr('title','Closed Invoices');
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title","Closed Invoices");
	topleftheader.find('span.top-left-header').html('Closed Invoices');
	jQuery('.billingModulesClose').removeClass('active');
	showLoader();
    location.href = baseUrl +'billing-closed-invoice/closed-invoices';

}

/**
 * Remove finalized invoice single row
 * @return
 */
 function removefinalizedinvoicesingle(keys)
 {
	$.ajax({
		type: "POST",
		url: baseUrl+"billing-finalized-invoice/chkhasaccuinvoice",
		data:'invoiceid='+keys,
		cache: false,
		success:function(data){
				if(data.replace(/^\s+|\s+$/g,"")=="done")
				{
					if(confirm('Are you sure you want to Delete '+keys+'?')){
					{
						$.ajax({
							type: "POST",
							url: baseUrl+"billing-finalized-invoice/deletefinalinvoice",
							data:'invoice_id='+keys,
							cache: false,
							success:function(data){
								if(data.replace(/^\s+|\s+$/g,"")=="done")
								{
									hideLoader();
									loaderfinalinvoice();
									//$.pjax.reload('#final-invoiced-grid', $.pjax.defaults);
								}
							}
						});
					}
				}
			} else {
				alert(data);
				return false;
			}
		}
	});
}

/**
 * Remove finalized invoiced from grid
 * @return
 */
function removefinlizedinvoice()
{
	var keys = $('#final-invoiced-grid').yiiGridView('getSelectedRows');

	if(!keys.length){
		alert('Please select at least 1 record to perform this action.');
	} else {
		var newkeys = keys.toString().split(",");
		var str = [];var str_val;
		for(var i=0;i<newkeys.length;i++){
			var val = JSON.parse(decodeURIComponent($( '.chk_finalized_invoiced_'+newkeys[i] ).val()));
			str_val =  " "+val['client_id'];
			str.push(str_val);
		}
		$.ajax({
				type: "POST",
				url: baseUrl+"billing-finalized-invoice/chkhasaccuinvoice",
				data:'invoiceid='+keys,
				cache: false,
				success:function(data){
					if(data.replace(/^\s+|\s+$/g,"")=="done")
					{
						if(confirm('Are you sure you want to Delete the selected '+i+' record(s): '+str+'?'))
						{
							$.ajax({
								type: "POST",
								url: baseUrl+"billing-finalized-invoice/deletefinalinvoice",
								data:'invoice_id='+keys,
								cache: false,
								success:function(data){
									if(data.replace(/^\s+|\s+$/g,"")=="done")
									{
										hideLoader();
										loaderfinalinvoice();
										//$.pjax.reload('#final-invoiced-grid', $.pjax.defaults);
									}
								}
							});
						}
					}
					else
					{
						alert(data);
						return false;
					}
				}
		});
	}
}

/**
 * Bulk Close finalized invoiced from grid
 * @return
 */
 function closedInvoices(bulkcloseinvoicedialog) {

     var team_id = jQuery('#team_id').val();
     var team_loc = jQuery('#team_loc').val();
     var keys = $('.grid-view').yiiGridView('getSelectedRows');
     var count = keys.length;
     var sel_row = "";


     bulkcloseinvoicedialog.dialog({
         title: 'Close Invoices',
         autoOpen: true,
         resizable: false,
         width: "50em",
         height: 302,
         modal: true,
         create: function (event, ui) {
             $('#bulkcloseinvoice-closed-dialog').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
             $('#bulkcloseinvoice-closed-dialog').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
             $('#bulkcloseinvoice-closed-dialog').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
         },
         buttons: [
             {
                 text: "Cancel",
                 "title": "Cancel",
                 "class": 'btn btn-primary',
                 click: function () {
                     bulkcloseinvoicedialog.dialog("close");
                 }
             },
             {
                 text: "Update",
                 "title": "Update",
                 "class": 'btn btn-primary',
                 click: function () {
                     var invoiceoperation = $('#bulkcloseinvoice-closed-dialog input[type="radio"]:checked').val();
                     // console.log(taskoperation);return false;
                     var postdata = '';
                     if (invoiceoperation == 'selectedinvoice') {
                         var selected_type = "selected";
                         var msg = "Are you sure you want to Bulk Close the selected " + count + " Invoices in the grid?";

                         $('#final-invoiced-grid input:checkbox:checked("input[name="selection"]")').each(function () {
                             if ($(this).attr("name") != 'selection_all') {
															 var check_val = JSON.parse($(this).val());
                                 if (sel_row == "") {
                                     sel_row = check_val.client_id;
                                 } else {
                                     sel_row += "," + check_val.client_id;
                                 }
                             }
                         });
                         postdata = {'invoiceIds': sel_row, 'type': selected_type};
                     } else {
                         var selected_type = "all";
                         var msg = "Are you sure you want to Bulk Close All record(s)?";

                          postdata = '&type=' + selected_type;
                         postdata = $('#close-invoice-form').serialize() + postdata;
                     }
                    /* if (confirm(msg))
                     {*/
                         jQuery.ajax({
                             url: baseUrl + 'billing-finalized-invoice/closefinalinvoices',
                             data: postdata,
                             type: 'post',
                             dataType: 'json',
                             beforeSend: function (data) {
                                 showLoader();
                             },
                             success: function (data) {
                                 if (data.finalresult != 'OK') {
                                     alert(data.error);
                                 }
                                 bulkcloseinvoicedialog.dialog("close");
								 //location.reload();
								 location.href=baseUrl + 'billing-closed-invoice/closed-invoices';
                             },
                             complete: function (data) {
                                 hideLoader();
                             }
                         });
                   //  }
                 }
             }
         ],
         open: function () {

             bulkcloseinvoicedialog.find('#allbulkcloseinvoice').html($('#final-invoiced-pajax .summary b#totalItemCountinvoice').text());

             if (count == 0) {
                 bulkcloseinvoicedialog.find('#rdo_bulkcloseinvoice').prop('checked', true);
                 bulkcloseinvoicedialog.find('label[for="rdo_bulkcloseinvoice"]').addClass('checked');
                 bulkcloseinvoicedialog.find('#rdo_selectedcloseinvoice').prop('disabled', true);
                 bulkcloseinvoicedialog.find('label[for="rdo_selectedcloseinvoice"]').addClass('disabled');
                 bulkcloseinvoicedialog.find('#rdo_selectedcloseinvoice').prop('checked', false);
                 bulkcloseinvoicedialog.find('label[for="rdo_selectedcloseinvoice"]').removeClass('checked');

                 bulkcloseinvoicedialog.find('label[for="rdo_selectedcloseinvoice"]').removeClass('focus');
                 bulkcloseinvoicedialog.find('#rdo_bulkcloseinvoice').focus();
                 bulkcloseinvoicedialog.find('label[for="rdo_bulkcloseinvoice"]').addClass('focus');
             } else {
                 bulkcloseinvoicedialog.find('#rdo_selectedcloseinvoice').prop('disabled', false);
                 bulkcloseinvoicedialog.find('label[for="rdo_selectedcloseinvoice"]').removeClass('disabled');
                 bulkcloseinvoicedialog.find('#rdo_selectedcloseinvoice').prop('checked', true);
                 bulkcloseinvoicedialog.find('label[for="rdo_selectedcloseinvoice"]').addClass('checked');
                 bulkcloseinvoicedialog.find('#rdo_bulkcloseinvoice').prop('checked', false);
                 bulkcloseinvoicedialog.find('label[for="rdo_bulkcloseinvoice"]').removeClass('checked');

                 bulkcloseinvoicedialog.find('label[for="rdo_bulkcloseinvoice"]').removeClass('focus');
                 bulkcloseinvoicedialog.find('#rdo_selectedcloseinvoice').focus();
                 bulkcloseinvoicedialog.find('label[for="rdo_selectedcloseinvoice"]').addClass('focus');
             }
             bulkcloseinvoicedialog.find('#selectedinvoice').html(count);
         }
     });

 }
 /**
  * Bulk Reopen finalized invoiced from grid
  * @return
  */
  function reopenInvoices(bulkreopeninvoicedialog) {

      var team_id = jQuery('#team_id').val();
      var team_loc = jQuery('#team_loc').val();
      var keys = $('.grid-view').yiiGridView('getSelectedRows');
      var count = keys.length;
      var sel_row = "";

      bulkreopeninvoicedialog.dialog({
          title: 'ReOpen Invoices',
          autoOpen: true,
          resizable: false,
          width: "50em",
          height: 302,
          modal: true,
          create: function (event, ui) {
              $('#bulkreopeninvoice-reopen-dialog').prev().find('.ui-dialog-titlebar-reopen').append('<span class="ui-button-icon-primary ui-icon"></span>');
              $('#bulkreopeninvoice-reopen-dialog').prev().find('.ui-dialog-titlebar-reopen').attr("title", "ReOpen");
              $('#bulkreopeninvoice-reopen-dialog').prev().find('.ui-dialog-titlebar-reopen').attr("aria-label", "ReOpen");
          },
          buttons: [
              {
                  text: "Cancel",
                  "title": "Cancel",
                  "class": 'btn btn-primary',
                  click: function () {
                      bulkreopeninvoicedialog.dialog("close");
                  }
              },
              {
                  text: "Update",
                  "title": "Update",
                  "class": 'btn btn-primary',
                  click: function () {
                      var invoiceoperation = $('#bulkreopeninvoice-reopen-dialog input[type="radio"]:checked').val();
                      // console.log(taskoperation);return false;
                      var postdata = '';
                      if (invoiceoperation == 'selectedinvoice') {
                          var selected_type = "selected";
                          var msg = "Are you sure you want to Bulk ReOpen the selected " + count + " Invoices in the grid?";

                          $('#closed-invoiced-grid input:checkbox:checked("input[name="selection"]")').each(function () {
                              if ($(this).attr("name") != 'selection_all') {
 															 var check_val = JSON.parse($(this).val());
                                  if (sel_row == "") {
                                      sel_row = check_val.client_id;
                                  } else {
                                      sel_row += "," + check_val.client_id;
                                  }
                              }
                          });
                          postdata = {'invoiceIds': sel_row, 'type': selected_type};
                      } else {
                          var selected_type = "all";
                          var msg = "Are you sure you want to Bulk ReOpen All record(s)?";

                           postdata = '&type=' + selected_type;
                          postdata = $('#reopen-invoice-form').serialize() + postdata;
                      }
                    /*  if (confirm(msg))
                      {*/
                          jQuery.ajax({
                              url: baseUrl + 'billing-closed-invoice/reopenfinalinvoices',
                              data: postdata,
                              type: 'post',
                              dataType: 'json',
                              beforeSend: function (data) {
                                  showLoader();
                              },
                              success: function (data) {
                                  if (data.finalresult != 'OK') {
                                      alert(data.error);
                                  }
                                  bulkreopeninvoicedialog.dialog("close");
								 // location.reload();
								 location.href=baseUrl + 'billing-finalized-invoice/finalized-invoices';
                              },
                              complete: function (data) {
                                  hideLoader();
                              }
                          });
                    //  }
                  }
              }
          ],
          open: function () {

              bulkreopeninvoicedialog.find('#allbulkreopeninvoice').html($('#closed-invoiced-pajax .summary b#totalItemCountinvoice').text());

              if (count == 0) {
                  bulkreopeninvoicedialog.find('#rdo_bulkreopeninvoice').prop('checked', true);
                  bulkreopeninvoicedialog.find('label[for="rdo_bulkreopeninvoice"]').addClass('checked');
                  bulkreopeninvoicedialog.find('#rdo_selectedreopeninvoice').prop('disabled', true);
                  bulkreopeninvoicedialog.find('label[for="rdo_selectedreopeninvoice"]').addClass('disabled');
                  bulkreopeninvoicedialog.find('#rdo_selectedreopeninvoice').prop('checked', false);
                  bulkreopeninvoicedialog.find('label[for="rdo_selectedreopeninvoice"]').removeClass('checked');

                  bulkreopeninvoicedialog.find('label[for="rdo_selectedreopeninvoice"]').removeClass('focus');
                  bulkreopeninvoicedialog.find('#rdo_bulkreopeninvoice').focus();
                  bulkreopeninvoicedialog.find('label[for="rdo_bulkreopeninvoice"]').addClass('focus');
              } else {
                  bulkreopeninvoicedialog.find('#rdo_selectedreopeninvoice').prop('disabled', false);
                  bulkreopeninvoicedialog.find('label[for="rdo_selectedreopeninvoice"]').removeClass('disabled');
                  bulkreopeninvoicedialog.find('#rdo_selectedreopeninvoice').prop('checked', true);
                  bulkreopeninvoicedialog.find('label[for="rdo_selectedreopeninvoice"]').addClass('checked');
                  bulkreopeninvoicedialog.find('#rdo_bulkreopeninvoice').prop('checked', false);
                  bulkreopeninvoicedialog.find('label[for="rdo_bulkreopeninvoice"]').removeClass('checked');

                  bulkreopeninvoicedialog.find('label[for="rdo_bulkreopeninvoice"]').removeClass('focus');
                  bulkreopeninvoicedialog.find('#rdo_selectedreopeninvoice').focus();
                  bulkreopeninvoicedialog.find('label[for="rdo_selectedreopeninvoice"]').addClass('focus');
              }
              bulkreopeninvoicedialog.find('#selectedinvoice').html(count);
          }
      });

  }
 /**
 * Merge finalized invoices from grid
 * @return
 */
function mergeInvoices(mergeinvoicedialog) {

	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	var count = keys.length;
	var sel_row = "";
	if(count == 0)
	{
		alert('Please select at least 1 record to perform this action.');
		return false;
	}
	else if(count > 1)
	{
		alert('Please select only one Finalized Invoice.');
		return false;
	}
	var checked_row = JSON.parse($('#final-invoiced-grid input:checkbox:checked("input[name="selection"]")').val());
			
	var sel_inv_id = checked_row.client_id;
	var clientid = checked_row.clientid;
	var caseid = checked_row.caseid;
	var displaytype = checked_row.displaytype;

	jQuery.ajax({
		url: baseUrl +'/billing-finalized-invoice/get-invoices-to-merge',
		type: 'post',
		dataType: 'json',
		data   : {'client_id':clientid,'client_case_id':caseid,'display_type':displaytype, 'invoice_id': sel_inv_id},
		beforeSend:function (response) {showLoader();},
		success: function (response) {
			hideLoader();
			var invoice_data = response;
			if(invoice_data.invoice_count==0)
			{
				alert('No invoices found to merge.');
				return false;
			}
			else
			{
				$('#mergeinvoices').html(invoice_data.html_text);

				$('input').customInput();

				mergeinvoicedialog.dialog({
					title: 'Select Invoices to Merge Into Master',
					autoOpen: true,
					resizable: false,
					width: "50em",
					height: 302,
					modal: true,
					create: function (event, ui) {
						$('#mergeinvoice-dialog').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
						$('#mergeinvoice-dialog').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
						$('#mergeinvoice-dialog').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					},
					buttons: [
						{
							text: "Cancel",
							"title": "Cancel",
							"class": 'btn btn-primary',
							click: function () {
								mergeinvoicedialog.dialog("close");
							}
						},
						{
							text: "Merge",
							"title": "Merge",
							"class": 'btn btn-primary',
							click: function () {
								var postdata = '';
								var chk_row = "";
								
								$('#mergeinvoice-dialog input:checkbox:checked("input[name="merge_invoice"]")').each(function () {
									if($(this).attr('name')!='select_all')
									{
										if (chk_row == "") {
											chk_row = $(this).val();
										} else {
											chk_row += "," + $(this).val();
										}
									}
								});
								if(chk_row=="")
								{
									alert('Please select at least 1 Invoice to Merge Into Master.');
								}
								else
								{
									postdata = {'maininvoiceid': sel_inv_id, 'mergeinvoiceIds': chk_row};
								
									jQuery.ajax({
										url: baseUrl + 'billing-finalized-invoice/mergefinalinvoices',
										data: postdata,
										type: 'post',
										dataType: 'json',
										beforeSend: function (data) {
											showLoader();
										},
										success: function (data) {
											if (data.finalresult != 'OK') {
												alert(data.error);
											}
											mergeinvoicedialog.dialog("close");
											//location.reload();
											location.href=baseUrl + 'billing-finalized-invoice/finalized-invoices';
										},
										complete: function (data) {
											hideLoader();
										}
									});
								}	
							}
						}
					],
					open: function () {

						$('#mergeinvoice-dialog input:checkbox[name=select_all]').click(function() {
							var checkedStatus = this.checked;
							$('#mergeinvoice-dialog input:checkbox[name=merge_invoice]').each(function() {
							$(this).prop('checked', checkedStatus);
							if(checkedStatus)
								$(this).next('label').addClass('checked');
							else
								$(this).next('label').removeClass('checked');
							});
						});
						
					}
				});
		 	}
		}
	});
} 
 /**
  * Edit invoice
  */
function edit_invoice(invoice_id,flag){
	var targetUrl = baseUrl +'billing-finalized-invoice/edit-invoice&invoice_id='+invoice_id+"&flag="+flag;
	var targetTitle = "Finalized Invoice - Edit";
    historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-money');
	topleftheader.find('em').attr('title','Finalized Invoice - Edit');
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title","Finalized Invoice - Edit");
	topleftheader.find('span.top-left-header').html('Finalized Invoice - Edit');
	topleftheader.find('span.pull-right').html(null);
	jQuery('.billingModulesFinalize').removeClass('active');
	showLoader();
    //location.href = baseUrl +'billing-finalized-invoice/finalized-invoices';
	jQuery.ajax({
	       url: targetUrl,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
			   $('#exp_finalize_inv_li').hide();
			   $('#delete_finalize_inv_li').hide();
				 $('#close_finalize_inv_li').hide();
				 $('#merge_finalize_inv_li').hide();
			   // jQuery('.billingModulesFinalize[data-module="finalized_invoices"]').addClass('active');
	       }
	});
	//showLoader();
	//location.href = baseUrl +'billing-finalized-invoice/edit-invoice&invoice_id='+invoice_id+"&flag="+flag;
}

/**
* Export request in bulk from finalized grid
*/
function bulkexportrequest_pdf()
{
	var keys = $('#final-invoiced-grid').yiiGridView('getSelectedRows');
	if(keys.length==""){
		alert("Please select a record to perform this action.");
		return false;
	}
	$.ajax({
		url:baseUrl+'billing-finalized-invoice/export-invoice&invoice_id='+keys,
		beforeSend:function (data) {showLoader();},
		success:function(response){
		hideLoader();
		if($('body').find('#availabl-price-points').length == 0){
			$('body').append('<div class="dialog" id="availabl-price-points" title="Select Export Format"></div>');
		}
		$('#availabl-price-points').html('').html(response);
		$('#availabl-price-points').dialog({
		modal: true,
		width:'40em',
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
						  text: "Export",
						  "class": 'btn btn-primary',
						  "title": 'Export',
						  click: function () {
							 var showsummarynote = $("input[name=export]:checked").val();
							 if(showsummarynote == 0 || showsummarynote == 1){
								hideLoader();
								location.href = baseUrl +'pdf/pdf-invoice&invoice_id='+keys+'&showsummarynote='+showsummarynote;
								$(this).dialog('destroy').remove();
							 }
							 if(showsummarynote == 2 || showsummarynote == 3){
								hideLoader();
								location.href = baseUrl +'export-excel/excel-invoice&invoice_id='+keys+'&showsummarynote='+showsummarynote;
								$(this).dialog('destroy').remove();
							 }
						  }
					  }
				]
			});
		}
	});
}

/**
* Export request in bulk from closed invoice grid
*/
function bulkexportrequestclosed_pdf()
{
	var keys = $('#closed-invoiced-grid').yiiGridView('getSelectedRows');
	if(keys.length==""){
		alert("Please select a record to perform this action.");
		return false;
	}
	$.ajax({
		url:baseUrl+'billing-closed-invoice/export-invoice&invoice_id='+keys,
		beforeSend:function (data) {showLoader();},
		success:function(response){
		hideLoader();
		if($('body').find('#availabl-price-points').length == 0){
			$('body').append('<div class="dialog" id="availabl-price-points" title="Select Export Format"></div>');
		}
		$('#availabl-price-points').html('').html(response);
		$('#availabl-price-points').dialog({
		modal: true,
		width:'40em',
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
						  text: "Export",
						  "class": 'btn btn-primary',
						  "title": 'Export',
						  click: function () {
							 var showsummarynote = $("input[name=export]:checked").val();
							 if(showsummarynote == 0 || showsummarynote == 1){
								hideLoader();
								location.href = baseUrl +'pdf/pdf-invoice&invoice_id='+keys+'&showsummarynote='+showsummarynote;
								$(this).dialog('destroy').remove();
							 }
							 if(showsummarynote == 2 || showsummarynote == 3){
								hideLoader();
								location.href = baseUrl +'export-excel/excel-invoice&invoice_id='+keys+'&showsummarynote='+showsummarynote;
								$(this).dialog('destroy').remove();
							 }
						  }
					  }
				]
			});
		}
	});
}

/**
* Export request
*/
function exportrequest_pdf(invoice_id)
{
	$.ajax({
		url:baseUrl+'billing-finalized-invoice/export-invoice&invoice_id='+invoice_id,
		beforeSend:function (data) {showLoader();},
		success:function(response){
		hideLoader();
		if($('body').find('#availabl-price-points').length == 0){
			$('body').append('<div class="dialog" id="availabl-price-points" title="Select Export Format"></div>');
		}
		$('#availabl-price-points').html('').html(response);
		$('#availabl-price-points').dialog({
		modal: true,
		width:'40em',
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
						  text: "Export",
						  "class": 'btn btn-primary',
						  "title": 'Export',
						  click: function () {
							 var showsummarynote = $("input[name=export]:checked").val();
							 if(showsummarynote == 0 || showsummarynote == 1){
								hideLoader();
								location.href = baseUrl +'pdf/pdf-invoice&invoice_id='+invoice_id+'&showsummarynote='+showsummarynote;
								$(this).dialog('destroy').remove();
							 }
							 if(showsummarynote == 2 || showsummarynote == 3){
								hideLoader();
								location.href = baseUrl +'export-excel/excel-invoice&invoice_id='+invoice_id+'&showsummarynote='+showsummarynote;
								$(this).dialog('destroy').remove();
							 }
						  }
					  }
				]
			});
		}
	});
}

/**
 * Updated Invoice
 */
 function update_invoice(){
	 var frm_efin = $('#edit_finalized_invoice');
	 //frm_efin.submit();
	 var invoice_id = $('#InvoiceFinal_id').val();
	 jQuery.ajax({
	       url: frm_efin.attr('action'),
	       type: 'post',
		   data:frm_efin.serialize(),
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
			   if($.trim(data)=='preview'){
					preview(invoice_id);
			   }else{
				 	loaderfinalinvoice();
			   }
	    	   //jQuery('.right-side').html(data);
			   //$('#admin_main_container').html(data);
			   //$('input').customInput();
			   //$('#exp_finalize_inv_li').hide();
			   //$('#delete_finalize_inv_li').hide();
			   // jQuery('.billingModulesFinalize[data-module="finalized_invoices"]').addClass('active');
	       }
	});

 }

/**
 * Cancel invoice from edit invoice form
 */
function cancel_invoice(){
	var flag = $('#flag').val(); var invoice_id = $('#InvoiceFinal_id').val();
	if(flag=='preview'){
		preview(invoice_id);
		//location.href = baseUrl +'billing-finalized-invoice/preview-invoice&invoice_id='+invoice_id+'&flag=preview';
	} else {
		loaderfinalinvoice();
		//location.href = baseUrl +'billing-finalized-invoice/finalized-invoices';
	}
}

/**
 * Preview icon show preview page
**/
function preview(key,mod){
	if(mod=='undefined')
			mod = 'finalized';
	var targetUrl = baseUrl +'billing-finalized-invoice/preview-invoice&invoice_id='+key+'&flag=preview';
	var targetTitle = "Finalized Invoice - Preview";
	if(mod=='closed')
	{
		targetUrl = baseUrl +'billing-closed-invoice/preview-invoice&invoice_id='+key+'&flag=preview';
		targetTitle = "Closed Invoice - Preview";
	}

    historyPushState(targetUrl,targetTitle);
	var topleftheader = $('.page-header');
	topleftheader.find('em').attr('class','fa fa-money');
	topleftheader.find('em').attr('title',targetTitle);
	topleftheader.find('span.top-left-header').attr("tabindex","0");
	topleftheader.find('span.top-left-header').attr("title",targetTitle);
	topleftheader.find('span.top-left-header').html(targetTitle);
	topleftheader.find('span.pull-right').html(null);
	if(mod=='closed')
	{
		jQuery('.billingModulesClose').removeClass('active');
	}
	else {
		jQuery('.billingModulesFinalize').removeClass('active');
	}

	showLoader();
    //location.href = baseUrl +'billing-finalized-invoice/finalized-invoices';
	jQuery.ajax({
	       url: targetUrl,
	       type: 'get',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   //jQuery('.right-side').html(data);
			   $('#admin_main_container').html(data);
			   $('input').customInput();
				 if(mod=='closed')
			 	{
					$('#exp_closed_inv_li').hide();
 				 	$('#reopen_finalize_inv_li').hide();
			 	} else {
				 $('#exp_finalize_inv_li').hide();
			   $('#delete_finalize_inv_li').hide();
				 $('#close_finalize_inv_li').hide();
				 $('#merge_finalize_inv_li').hide();
			 	}


			   // jQuery('.billingModulesFinalize[data-module="finalized_invoices"]').addClass('active');
	       }
	});
	//showLoader();
	//location.href = baseUrl +'billing-finalized-invoice/preview-invoice&invoice_id='+key+'&flag=preview';

}
