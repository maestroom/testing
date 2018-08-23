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
	/* Start : Billing-PriceList */
	jQuery('.acordian-main').on('click','a.billingModules',function(event){ 
		var chk_status = checkformstatus(event); // check form status flag
		if(chk_status == true){ // check
			var module=jQuery(this).data('module'); 
			jQuery('.billingModules').removeClass('active');
			var topleftheader = $('.page-header');
			topleftheader.find('em').attr('class','fa fa-money');
			topleftheader.find('span.pull-right').html(null);
			if(module == 'internal_team_pricing'){
				showLoader();
				loadInternalTeamPricing();
				topleftheader.find('span.top-left-header').attr("tabindex","0");
				topleftheader.find('span.top-left-header').attr("title","Price List - Internal Team Pricing");
				topleftheader.find('span.top-left-header').html('Price List - Internal Team Pricing');
				topleftheader.find('em').attr('title','Price List - Internal Team Pricing');
			}
			
			if(module == 'internal_shared_pricing'){
				showLoader();
				loadSharedPricing();
				topleftheader.find('span.top-left-header').attr("tabindex","0");
				topleftheader.find('span.top-left-header').attr("title","Price List - Internal Shared Pricing");
				topleftheader.find('span.top-left-header').html('Price List - Internal Shared Pricing');
				topleftheader.find('em').attr('title','Price List - Internal Shared Pricing');
			}
			
			if(module == 'client_preferred_pricing'){
				showLoader();
				clientPreferredPricing();
				topleftheader.find('span.top-left-header').attr("tabindex","0");
				topleftheader.find('span.top-left-header').attr("title","Price List - Client Preferred Pricing");
				topleftheader.find('span.top-left-header').html('Price List - Client Preferred Pricing');
				topleftheader.find('em').attr('title','Price List - Client Preferred Pricing');
			}
			if(module == 'case_preferred_pricing'){
				showLoader();
				clientcasePreferredPricing();
				topleftheader.find('span.top-left-header').attr("tabindex","0");
				topleftheader.find('span.top-left-header').attr("title","Price List - Case Preferred Pricing");
				topleftheader.find('span.top-left-header').html('Price List - Case Preferred Pricing');
				topleftheader.find('em').attr('title','Price List - Case Preferred Pricing');
				$('h1 span#header-right-clientcase select').val(0);
				$('h1 span#header-right-clientcase').removeClass('hide');
			}
			jQuery(this).addClass('active');
		}
	});
	/* End : Billing-PriceList */
	
	$('body').on('change','#container_clientcase_toclonefrom input[type="checkbox"]',function(){
		$('.clientcasecheckbox').not('input[value="'+this.value+'"]').prop('checked',false);
		$('.clientcasecheckbox').not('input[value="'+this.value+'"]').next('label').removeClass('checked');
	});
	
	$('body').on('click','input[name="PricingRates[rate_type]"]',function(){
		var form = jQuery('#PricingRates');
		var rate_type_added=form.find('input[name="rate_type_added"]').val();
		if (rate_type_added != '' && rate_type_added != form.find('input[name="PricingRates[rate_type]"]:checked').val()) {
			alert("Please select 1 Rate Type as a Price Point cannot have multiple Rate Types.");
			form.find('input[name="PricingRates[rate_type]"]').each(function(){
				if(rate_type_added==2){
					if(this.value==2){
						$(this).prop('checked',true);
						$(this).next('label').addClass('checked');
						$('form#PricingRates .ifistiered').show();
						$('.field-pricingrates-tiered').show();
					}
					if(this.value==1){
						$(this).prop('checked',false);
						$(this).next('label').removeClass('checked');
					}
				}
				if(rate_type_added==1){
					if(this.value==1){
						$(this).prop('checked',true);
						$(this).next('label').addClass('checked');
					}
					if(this.value==2){
						$(this).prop('checked',false);
						$(this).next('label').removeClass('checked');
						$('form#PricingRates .ifistiered').hide();
						$('.field-pricingrates-tiered').hide();
					}
				}
			});
			return false;
		}
		if($(this).val() == 2) {
			$('form#PricingRates .ifistiered').show();
			$('.field-pricingrates-tiered').show();
			$('#pricingrates-tier_from').val('');
			$('#pricingrates-tier_to').val('');
		} else {
			$('form#PricingRates .ifistiered').hide();
			$('.field-pricingrates-tiered').hide();
		}
	});
});

/* Start : To Load Right container on click of Pricing List */
function loadTeamPricingBilling(id){
	var chk_status = checkformstatus('event');
	if(chk_status==true)
			loadTeamPricing(id);
}
/* End : To Load Right container on click of Pricing List */

/* Start : To Load Right container on click of Pricing List */
function loadTeamPricing(id)
{
	jQuery.ajax({
       url: baseUrl +'/billing-pricelist/load-team-pricing&team_id='+id,
       type: 'get',
       beforeSend:function (data) {showLoader();jQuery(".teamlist").removeClass('active');},
       success: function (data) {
    	   hideLoader();
    	   jQuery('#team_id').val(id);
    	   jQuery("li[data-id='"+id+"']").addClass('active');
    	   jQuery('#admin_right').html(data);
    	   jQuery.pjax.reload({container:'#internal-price-point-grid-pajax', replace: false, url: baseUrl +'/billing-pricelist/load-team-pricing&team_id='+id});

       }
	});
}
/* End : To Load Right container on click of Pricing List */

/* Start : PriceList - toggle*/
function PriceListToggle(){
	var url = $('#module-url').val();
	var pajax_container = $('#pajax_container').val();
	if($('#'+pajax_container).length){
		$.pjax.defaults.url =url;
		$.pjax.defaults.push= false;
		$.pjax.reload('#'+pajax_container, $.pjax.defaults);
	}
	jQuery('#maincontainer').toggleClass('slide-close');
}
/* End : PriceList - toggle*/

/* Start : Add Internal Pricepoint */
function AddPricePoint(){
	var team_id = $('#team_id').val();
	jQuery.ajax({
       url: baseUrl +'/billing-pricelist/create&team_id='+team_id,
       type: 'get',
       beforeSend:function (data) {showLoader();},
       success: function (data) {
    	   hideLoader();
    	   jQuery('#admin_right').html(data);
       } 
	});
}
/* End : Add Internal Pricepoint */

/* Start : Manage Pricing Rate on click of Rate button while Adding / Editing Pricing */
/** 
 * @param pricing_id (int)
 */
function managePricingRate(pricing_id,team_id){
	
	var table = $('form#Pricing').find('table#pricing_rate_table');
	var tableWrap = table.html();
		
	var manageratedialog = $('body').find('div#manage-rate-dialog');
	if(manageratedialog.length == 0){
		$('body').append('<div id="manage-rate-dialog" class="task-details-popup"></div>');
		var manageratedialog = $('body').find('div#manage-rate-dialog');
	}
	
	jQuery.ajax({
		url: baseUrl +'billing-pricelist/load-pricing-rate&pricing_id='+pricing_id+'&team_id='+team_id+'&type=team',
		type: 'post',
		data: {'data':tableWrap},
		beforeSend:function (data) { showLoader();},
		success: function (data) {
			manageratedialog.dialog({
		        title: 'Add Rate',
		        autoOpen: true,
		        resizable: false,
		        height:692,
		        width:"80em",
		        modal: true,
					open: function(){
						manageratedialog.html(data);
						if($('.price-range-width').css('display') != 'none'){
							$( "input[name='PricingRates[rate_type]']" ).each(function(){
								if(this.value==2){
									$(this).prop('checked',true);
									$(this).next('label').addClass('checked');
									$(".field-pricingrates-tiered").css('display','inline-block');
								}else{
									$(this).prop('checked',false);
									$(this).next('label').removeClass('checked');
									$(".field-pricingrates-tiered").css('display','none');
								}
							});
					}
				},
		        close: function(){
					manageratedialog.dialog('destroy').remove();
				},
				buttons: [ 
		            {
			            text: "Cancel",
			            "title":"Cancel",
			            "class": 'btn btn-primary',
			            click: function() {
							manageratedialog.dialog("close");
			            }
		            },
		            {
			            text: "Update",
			            "title":"Update",
			            "class": 'btn btn-primary',
			            click: function() {
							/*if($.trim($('#pricingrates-rate_amount').val())!="" || $('input[name="PricingRates[team_loc][]"]:checked').length >0){
								AddPricingRange();
								return false;
							}*/
							var form = $('#PricingRates');
			            	var table = form.find('table#pricing_rate_table');
			        		$("form#Pricing #pricing-pricing_rate").val(table.html());
							
			        		if(table.find('tbody tr').length > 0) {
								$.ajax({
			        				url: baseUrl +'billing-pricelist/load-services&team_id='+team_id,
			        				type: 'post',
			        				data: form.serialize(),
			        				beforeSend:function (data) {showLoader();},
			        				success: function (data) {
										var tablehtml = table.html();
										var tablewrap = '<table summary="Added Pricing Rates" class="display dataTable no-footer" id="pricing_rate_table">'+tablehtml+'</table>';
						        		manageratedialog.dialog("close");
						        		$("form#Pricing .field-pricing-rate-table div.col-md-9").html(tablewrap);
			        					$('form#Pricing #pricing-service_task').html('');
			        					$('#Pricing #is_change_form').val('1'); // change flag to 1
			        					$('#is_change_form_main').val('1');  // change flag to 1
			        					$('form#Pricing #pricing-service_task').html(data);
			        				},
			        				complete: function(){hideLoader();}
			        			});
							} else {
			        			manageratedialog.dialog("close");
			        			$('form#Pricing #pricing-service_task').html('<div class="col-sm-12"><label class="form_label">First add Rate(s), to then see and select Service Task(s)</label></div>');
			        			$("form#Pricing .field-pricing-rate-table div.col-md-9").html('');
			        		}
		            	}
		            }
		        ]
		    });
		},
		complete: function () {
			hideLoader();
			manageratedialog.find('#pricingrates-rate_amount').focus();
			$('#PricingRates').ajaxForm({
				beforeSubmit:function(arr, $form, options) { showLoader();return true},
				success: submitPricingRate,
				complete:function() { hideLoader();},
			});
		}
	});
}

function submitPricingRate(responseText, statusText){
	if(responseText == 'OK'){
		var form = jQuery('#PricingRates');
		if (form.find('input[name="rate_type_added"]').val() != '' && form.find('input[name="rate_type_added"]').val() != form.find('input[name="PricingRates[rate_type]"]:checked').val()) {
			alert("Please select 1 Rate Type as a Price Point cannot have multiple Rate Types.");
			return false;
		} else {
			form.find('input[name="rate_type_added"]').val(form.find('input[name="PricingRates[rate_type]"]:checked').val());
			AddPricingRateInTable(form);
		}
	}else{
		for (var key in responseText) {
			$("#manage-rate-dialog #"+key).next().html(responseText[key]);
			$("#manage-rate-dialog #"+key).parent().parent().parent().addClass('has-error');
		}
	}
}
/* Start : It will validate user entered data & on success call addPricingRateRow method */
function AddPricingRateInTable(form){
	
	var table = form.find('table#pricing_rate_table');
	var tablerow = form.find('table#pricing_rate_table tbody tr');
	var rate_type = form.find('input[name="PricingRates[rate_type]"]:checked').val();
	
	if(rate_type == 1){
		var success = true;
		if(tablerow.length > 0){
			tablerow.each(function(){
				var tablerowloc = $(this).find('.team_loc').val();
				form.find('input[name="PricingRates[team_loc][]"]:checked').each(function() {
					if($(this).val() == tablerowloc){
						success = false;
					}
				});
			});
		}
		if(success){
			form.find('input[name="PricingRates[team_loc][]"]:checked').each(function() {
				var team_loc_id = $(this).val();
				var team_loc = $(this).next('label').text();
				var rate_amount = form.find('input[name="PricingRates[rate_amount]"]').val();
				var cost_amount = form.find('input[name="PricingRates[cost_amount]"]').val();
				var tier_from_tier_to = '';
				addPricingRateRow(table, rate_type, team_loc_id, team_loc, rate_amount, cost_amount, 0, 0, tier_from_tier_to);
			});
			$('#pricingrates-rate_amount').val(null);
			$('#pricingrates-cost_amount').val(null);
			$('input[name="PricingRates[team_loc][]"]:checked').each(function(){
				$(this).prop("checked",false);
				$(this).next('label').removeClass('checked');
			});
			return table;
		} else {
			alert("A Rate has already been applied to this Team Location.");
			return false;
		}
	} 
	if(rate_type == 2){
		var success = true;
		var enteredFrom = form.find('input[name="PricingRates[tier_from]"]').val();
		var enteredTo = form.find('input[name="PricingRates[tier_to]"]').val();
		
		if(parseInt(enteredFrom) == parseInt(enteredTo)){
			alert("Please adjust your Tiered Range as the 'Tiered From' value and Tiered To' value cannot be the same.");
			return false;
		} else if (parseInt(enteredFrom) > parseInt(enteredTo)) {
			alert("Please adjust your Tiered Range as the 'Tiered From' value should not be greater than the 'Tiered To' value.");
			return false;
		} else {
			if(tablerow.length > 0){
				tablerow.each(function(){
					var team_loc = $(this).find('.team_loc').val();
					var tier_from = $(this).find('.tier_from').val();
					var tier_to = $(this).find('.tier_to').val();
					
					form.find('input[name="PricingRates[team_loc][]"]:checked').each(function() {
						if(parseInt($(this).val()) == parseInt(team_loc) && (((parseFloat(enteredFrom) <= parseFloat(tier_from) && parseFloat(enteredFrom) <= parseFloat(tier_to)) && (parseFloat(enteredTo) >= parseFloat(tier_from) && parseFloat(enteredTo) >= parseFloat(tier_to))) || (parseFloat(enteredFrom) >= parseFloat(tier_from) && parseFloat(enteredFrom) <= parseFloat(tier_to)) || (parseFloat(enteredTo) >= parseFloat(tier_from) && parseFloat(enteredTo) <= parseFloat(tier_to)) || parseFloat(tier_from) == parseFloat(enteredFrom) || parseFloat(tier_to) == parseFloat(enteredFrom) || parseFloat(tier_from) == parseFloat(enteredTo) || parseFloat(tier_to) == parseFloat(enteredTo))){ 
							//WORKING CONDITION
							success = false;
						}
					});
				});
			}
			
			if(success) {
				form.find('input[name="PricingRates[team_loc][]"]:checked').each(function() {
					var team_loc_id = $(this).val();
					var team_loc = $(this).next('label').text();
					var rate_amount = form.find('input[name="PricingRates[rate_amount]"]').val();
					var cost_amount = form.find('input[name="PricingRates[cost_amount]"]').val();
					var enteredFrom = form.find('input[name="PricingRates[tier_from]"]').val();
					var enteredTo = form.find('input[name="PricingRates[tier_to]"]').val();
					var tier_from_tier_to = enteredFrom+'-'+enteredTo;
					addPricingRateRow(table, rate_type, team_loc_id, team_loc, rate_amount, cost_amount, enteredFrom, enteredTo, tier_from_tier_to);
				});
				$('#pricingrates-rate_amount').val(null);
				$('#pricingrates-cost_amount').val(null);
				$('#pricingrates-tier_from').val(null);
				$('#pricingrates-tier_to').val(null);
				$('input[name="PricingRates[team_loc][]"]:checked').each(function(){
					$(this).prop("checked",false);
					$(this).next('label').removeClass('checked');
				});
				return table;
			} else {
				alert("Please adjust your Tiered Range as there is at least one Tier that has an overlapping Range with another Tier.");
				return false;
			}
		}
	}
}
/* End : It will validate user entered data & on success call addPricingRateRow method */
/* Start : It will add a Row into Pricing Rate Table */
function addPricingRateRow(table, rate_type, team_loc_id, team_loc, rate_amount, cost_amount, tier_from, tier_to, tier_from_tier_to)
{
	var tableBody = table.find('tbody');
	var trClass = 'even';	
	if(tableBody.find('tr').length == 0){
		var trClass = 'odd';	
	} else if(tableBody.find('tr').length > 0 && tableBody.find('tr').length % 2 == 0){
		var trClass = 'odd';
	}
	
	if(tableBody.find('tr.no-rows').length > 0){
		tableBody.find('tr.no-rows').remove();
	}
	var style = '';
	
	if(rate_type != 2){
		style = 'style="display:none;"';
	}
	
	if(cost_amount == ''){
		cost_amount = 0.00;
	}
	
	//tableBody.append('<tr class="'+trClass+' newTr"><td class="text-left skip-export kv-align-center kv-align-middle price-location-width">'+team_loc+'<input type="hidden" name="PricingRatesAvail[rate_type][]" class="rate_type" value="'+rate_type+'"/><input type="hidden" name="PricingRatesAvail[team_loc][]" class="team_loc" value="'+team_loc_id+'"/></td><td class="skip-export kv-align-center kv-align-middle price-bill-width">$ '+rate_amount+'<input type="hidden" name="PricingRatesAvail[rate_amount][]" class="rate_amount" value="'+rate_amount+'"/></td><td class="skip-export kv-align-center kv-align-middle price-cost-width">$ '+cost_amount+'<input type="hidden" name="PricingRatesAvail[cost_amount][]" class="cost_amount" value="'+cost_amount+'"/></td><td class="text-left skip-export kv-align-center kv-align-middle ifistiered price-range-width" '+style+'>'+tier_from_tier_to+'<input type="hidden" name="PricingRatesAvail[tier_from][]" class="tier_from" value="'+tier_from+'"/><input type="hidden" name="PricingRatesAvail[tier_to][]" class="tier_to" value="'+tier_to+'"/></td><td class="skip-export kv-align-center kv-align-middle"><a class="icon-set removePricingRate" title="Remove Rate" href="javascript:void(0);"><em class="fa fa-close text-primary"></em></a></td></tr>');
	tableBody.append('<tr><td class="text-left skip-export kv-align-middle price-location-width">'+team_loc+'<input type="hidden" name="PricingRatesAvail[rate_type][]" class="rate_type" value="'+rate_type+'"/><input type="hidden" name="PricingRatesAvail[team_loc][]" class="team_loc" value="'+team_loc_id+'"/></td><td class="skip-export  kv-align-middle price-bill-width">$'+parseFloat(rate_amount).toFixed(2)+'<input type="hidden" name="PricingRatesAvail[rate_amount][]" class="rate_amount" value="'+rate_amount+'"/></td><td class="skip-export  kv-align-middle price-cost-width">$'+parseFloat(cost_amount).toFixed(2)+'<input type="hidden" name="PricingRatesAvail[cost_amount][]" class="cost_amount" value="'+cost_amount+'"/></td><td class="text-left skip-export  kv-align-middle ifistiered price-range-width" '+style+'>'+tier_from_tier_to+'<input type="hidden" name="PricingRatesAvail[tier_from][]" class="tier_from" value="'+tier_from+'"/><input type="hidden" name="PricingRatesAvail[tier_to][]" class="tier_to" value="'+tier_to+'"/></td><td class="skip-export  kv-align-middle third-td text-center"><a class="icon-set removePricingRate" title="Remove Rate" href="javascript:void(0);"><em class="fa fa-close text-primary" title="Remove"></em></a></td></tr>');
	return true;
}
/* End : It will add a Row into Pricing Rate Table */
/* Start : It will validate & on success it will call submitPricingRate method */
function AddPricingRange()
{
	$('#PricingRates').submit();
}
/* End : It will validate & on success it will call submitPricingRate method */
/* Start : It will remove a row of pricing rate from html table */
$(document).on('click', 'form#PricingRates a.removePricingRate', function(event){
	var current_tr = $(this).parent('td').parent('tr');
	var table = $(this).parent('td').parent('tr').parent('tbody').parent('table');
	current_tr.remove();
	$('#is_change_form').val('1'); //  change flag to 1
	$('#is_change_form_main').val('1');  //  change flag to 1
	var tablerow = table.find('tbody tr');
	
	if(tablerow.length == 0){
		//$("form#Pricing").table.remove();
		$("form#Pricing #pricing-pricing_rate").val('');
		table.find('thead #rate_type_added').val('');
		table.find('tbody').append('<tr class="odd no-rows"><td colspan="4" style="text-align:center;">No Rates added yet</td></tr>');
	}
});
/* End : It will remove a row of pricing rate from html table */
/* Start : It will remove a row of pricing rate from html table */
$(document).on('click', 'form#Pricing a.removePricingRate', function(){
	var team_id = $('#team_id').val();
	var current_tr = $(this).parent('td').parent('tr');
	var table = $(this).parent('td').parent('tr').parent('tbody').parent('table');
	current_tr.remove();
	$('#is_change_form').val('1'); // change flag to 1
	$('#is_change_form_main').val('1'); // change flag to 1
	var tablerow = table.find('tbody tr');
	
	var form = $('#Pricing');
	if(tablerow.length > 0) {
		$.ajax({
			url: baseUrl +'billing-pricelist/load-services&team_id='+team_id,
			type: 'post',
			data: form.serialize(),
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				$('form#Pricing #pricing-service_task').html('');
				$('form#Pricing #pricing-service_task').html(data);
			},
			complete: function(){hideLoader();}
		});
	} else {
		$('form#Pricing #pricing-service_task').html('<div class="col-sm-12"><label class="form_label">First add Rate(s), to then see and select Service Task(s)</label></div>');
		$("form#Pricing #pricing-pricing_rate").val('');
		table.find('thead #rate_type_added').val('');
		table.find('tbody').append('<tr class="odd no-rows"><td colspan="4" style="text-align:center;">No Rates added yet</td></tr>');
	}
});
/* End : It will remove a row of pricing rate from html table */
/* End : Manage Pricing Rate on click of Rate button while Adding / Editing Pricing */

/* Start : Edit Internal Pricepoint */
function EditPricepoint(pricing_id,source,destination)
{
	if(source == 'team') {
		var team_id = $('#team_id').val();
		var url = baseUrl +'/billing-pricelist/update&team_id='+team_id+'&pricing_id='+pricing_id;
	} else {
		var url = baseUrl +'/billing-pricelist/update-shared-pricing&pricing_id='+pricing_id;
	}
	jQuery.ajax({
       url: url,
       type: 'get',
       beforeSend:function (data) {showLoader();},
       success: function (data) {
    	   hideLoader();
    	   jQuery(destination).html(data);
       } 
	});
}
/* End : Edit Internal Pricepoint */

/* Start : Update Internal Pricepoint */
function UpdatePricepoint(form_id,btn,successFunc,targetid,team_id,pricing_id){
	var form = $('form#'+form_id);
	$.ajax({
        type: "POST",
        url: baseUrl +'/billing-pricelist/validate-pricing&team_id='+team_id+'&pricing_id='+pricing_id,
        data: form.serialize(),
        dataType: 'JSON',
        cache: false,
        beforeSend:function(){showLoader();$(btn).attr('disabled','disabled');},
        success: function (responseText) {
        	if(responseText == 'OK'){
				$.ajax({
			        type: "POST",
			        url: baseUrl +'/billing-pricelist/chklocandservicetask&team_id='+team_id+'&pricing_id='+pricing_id,
			        data: form.serialize(),
			        dataType: 'html',
			        cache: false,
			        beforeSend:function(){showLoader();$(btn).attr('disabled','disabled');},
			        success: function (data) {
						//alert(successFunc);
			        	if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft' || data.replace(/^\s+|\s+$/g, "") == 'accumalateditemsleft'){
				  			var error="The deselected Task(s) must remain with the Price Point because it has outstanding Billing Items.  Please Finalize Invoices related to the Task(s) before removing the Task from the Price Point.";
			    			alert(error);
			    			$(btn).attr('disabled',false);
			    			hideLoader();
			    			return false;
			        	} else if (data.replace(/^\s+|\s+$/g, "") == 'billableitemsleftforloc' || data.replace(/^\s+|\s+$/g, "") == 'accumalateditemsleftforloc') {
			        		var error="The Team Location cannot be removed from the Price Point because it has outstanding Billing Items.  Please Finalize Invoices related to the Price Point Team Location before removing the Team Location from the Price Point.";
			    			alert(error);
			    			$(btn).attr('disabled',false);
			    			hideLoader();
			    			return false;
			        	} else if (data.replace(/^\s+|\s+$/g, "") == 'billableitemsleftforteam'){
			        		var error="The Team cannot be removed from the Price Point because it has outstanding Billing Items.  Please Finalize Invoices related to the Price Point Team before removing the Team from the Price Point.";
			    			alert(error);
			    			$(btn).attr('disabled',false);
			    			hideLoader();
			    			return false;
			        	} else {
			        		$.ajax({
			        	        url    : form.attr('action'),
			        	        cache: false,
			        	        type   : 'post',
			        	        data   : form.serialize(),
			        	        beforeSend : function() {
			        				$(btn).attr('disabled','disabled');
			        	        },
			        	        success: function (response){
			        	        	if(response == 'OK'){
										if(form_type_name == 'sharedpricing_div'){
											loadSharedPricing();
											//loadTeamPricing(team_id);
										} else { 
											eval(successFunc);
										}
			        					//eval(successFunc);
			        				}else{
			        					$('#'+targetid).html(response);
			        	        		$(btn).removeAttr("disabled");
			        	        	}
			        	        },
			        	        complete: function(){
			        	        	hideLoader();
			        	        },
			        	        error  : function (){
			        	            console.log('internal server error');
			        	        }
			        	    });
			        	}
					}
				});
	        } else {
	        	for (var key in responseText) {
	    			$("form#Pricing #"+key).next().html(responseText[key]);
	    			$("form#Pricing #"+key).parent().parent().parent().addClass('has-error');
	    		}
	        	$(btn).removeAttr("disabled");
	        	hideLoader();
	        	return false;
	        }
		}
	});	
}
/* End : Update Internal Pricepoint */

/* Start : Remove Pricepoint */
function DeletePricepoint(team_id, pricing_id, price_point, typefrom){
	$.ajax({
        type: "post",
        url: baseUrl + "/billing-pricelist/chkcanremovepricepoint",
        data:{'pricing_id[]':pricing_id},
        dataType: 'html',
        cache: false,
        beforeSend:function(){showLoader();},
        success:function(data){            
        	if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft' || data.replace(/^\s+|\s+$/g, "") == 'accumalateditemsleft'){
	  			var error="The Price Point cannot be removed because it has outstanding Billing Items. Please Finalize Invoices related to the Price Point before removing the Price Point from the application.";
    			alert(error);
    			hideLoader();
    			return false;
        	} else {
        		if(confirm("Are you sure you want to Remove Price Point "+price_point+"?")){
        			$.ajax({
        		        type: "post",
        		        url: baseUrl + "/billing-pricelist/delete-pricepoint",
        		        data:{'pricing_id[]':pricing_id},
        		        dataType: 'html',
        		        cache: false,
        		        beforeSend:function(){showLoader();},
        		        success:function(data){
        		        	if(data.replace(/^\s+|\s+$/g, "") == 'OK') {
        		        		if(typefrom == 'team'){
        		        			loadTeamPricing(team_id);
        		        		} else { 
        		        			loadSharedPricing();
        		        		}
        		        	} else {
        		        		alert("Data could not be removed, something must be wrong.");
        		        	}
        		        },
        		        complete:function(){ hideLoader(); }
        			});
        		}else{
        			hideLoader();
        			return false;
        		}
        	}
		}
	});
}
/* End : Remove Pricepoint */

/* Start : Remove Bulk Pricepoint */
function DeleteBulkPricepoint(team_id,typefrom){
	
	if(typefrom=='shared'){
		var pricing_id = $('#shared-price-point-grid').yiiGridView('getSelectedRows');
	}else{
		var pricing_id = $('#internal-price-point-grid').yiiGridView('getSelectedRows');
	}
	if(pricing_id.length > 0){
		var newkeys = pricing_id.toString().split(",");
		var price_point = [];var str_val;
		for(var i=0;i<newkeys.length;i++){
			var val = JSON.parse(decodeURIComponent($( '.chk_price_point_'+newkeys[i] ).val()));
			str_val = val['price_point'];
			price_point.push(str_val);
		}
		var str_count = price_point.length;
		var strprice_point = price_point.join(', ');
		$.ajax({
	        type: "post",
	        url: baseUrl + "/billing-pricelist/chkcanremovepricepoint",
	        data:{'pricing_id[]':pricing_id},
	        dataType: 'html',
	        cache: false,
	        beforeSend:function(){showLoader();},
	        success:function(data){
	        	if(data.replace(/^\s+|\s+$/g, "") == 'billableitemsleft' || data.replace(/^\s+|\s+$/g, "") == 'accumalateditemsleft'){
		  			var error="The Price Point cannot be removed because it has outstanding Billing Items. Please Finalize Invoices related to the Price Point before removing the Price Point from the application.";
	    			alert(error);
	    			hideLoader();
	    			return false;
	        	} else {
	        		if(confirm("Are you sure you want to Remove the selected "+str_count+" record(s): "+strprice_point+"?")){
	        			$.ajax({
	        		        type: "post",
	        		        url: baseUrl + "/billing-pricelist/delete-pricepoint",
	        		        data:{'pricing_id':pricing_id},
	        		        dataType: 'html',
	        		        cache: false,
	        		        beforeSend:function(){showLoader();},
	        		        success:function(data){
	        		        	if(data.replace(/^\s+|\s+$/g, "") == 'OK'){
	        		        		if(typefrom == 'team'){
	        		        			loadTeamPricing(team_id);
	        		        		} else { 
	        		        			loadSharedPricing();
	        		        		}
	        		        	} else {
	        		        		alert("Data could not be removed, something must be wrong.");
	        		        	}
	        		        },
	        		        complete:function(){hideLoader();}
	        			});
	        		}else{
	        			hideLoader();
	        			return false;
	        		}
	        	}
			}
		});
	} else {
		alert("Please select at least 1 record to perform this action.");
		return false;
	}
}
/* End : Remove Bulk Pricepoint */


/* Start : Load Internal Team Pricing List */
function loadInternalTeamPricing()
{
	var targetUrl = baseUrl +'billing-pricelist/internal-team-pricing';
	var targetTitle = "Internal Shared Pricing"; 
	historyPushState(targetUrl,targetTitle);
    commonAjax(targetUrl,'admin_main_container');
}
/* End : Load Internal Team Pricing List */

/* Start : Load Shared Pricing List */
function loadSharedPricing()
{
	var targetUrl = baseUrl +'billing-pricelist/internal-shared-pricing';
	var targetTitle = "Internal Shared Pricing"; 
	historyPushState(targetUrl,targetTitle);
    //commonAjax(targetUrl,'admin_main_container');
	jQuery.ajax({
	      url: targetUrl,
	      type: 'get',
	      beforeSend:function (data) {showLoader();},
	      success: function (data) {
	    	  hideLoader();
	    	  jQuery('#admin_main_container').html(data);	    	  
	    	  jQuery.pjax.reload({container:'#shared-price-point-transaction-pjax', replace: false, url: targetUrl});   	   
	      } 
	});
}
/* End : Load Shared Pricing List */

/* Start : Load Add Shared Pricepoint form */
function AddSharedPricePoint()
{
	var team_id = $('#team_id').val();
	jQuery.ajax({
       url: baseUrl +'/billing-pricelist/create-shared-pricing',
       type: 'get',
       beforeSend:function (data) {showLoader();},
       success: function (data) {
    	   hideLoader();
    	   jQuery('.right-main-container').html(data);
       } 
	});
}
/* End : Load Add Shared Pricepoint form */

/* Start : Load View of Client Preferred Pricing */
function clientPreferredPricing()
{
	var targetUrl = baseUrl +'billing-pricelist/get-preferred-pricing&type=client';
	var targetTitle = "Client Preferred Pricing"; 
	historyPushState(targetUrl,targetTitle);
	commonAjax(targetUrl,'admin_main_container');
}
/* End : Load View of Client Preferred Pricing */

/* Start : Load View of Case Preferred Pricing */
function clientcasePreferredPricing()
{
	var targetUrl = baseUrl +'billing-pricelist/get-preferred-pricing&type=case';
	var targetTitle = "Case Preferred Pricing"; 
	historyPushState(targetUrl,targetTitle);
	commonAjax(targetUrl,'admin_main_container');
}
/* End : Load View of Case Preferred Pricing */

/** 
 * Start : Load View of Preferred Pricing 
 * @param client_id int
 * @param client_case_id int
 * @param type string client/case
 */

function getTemplatesByID(client_id, client_case_id, type)
{
	commonAjax(baseUrl +'/billing-pricelist/get-preferred-pricing&client_id='+client_id+'&client_case_id='+client_case_id+'&type='+type,'admin_main_container');
}
/* End : Load View of Case Preferred Pricing */

/* Start : Load Remaining Pricepoints Teams */
function loadRemainingPricePoint(client_id, client_case_id, type)
{
	var clientpricepointdialog = $('body').find('div#client-pricepoint-dialog');
	if(clientpricepointdialog.length == 0){
		$('body').append('<div id="client-pricepoint-dialog" class="task-details-popup"></div>');
		var clientpricepointdialog = $('body').find('div#client-pricepoint-dialog');
	}
	
	jQuery.ajax({
		url: baseUrl +'billing-pricelist/load-remaining-pricepoints&client_id='+client_id+'&client_case_id='+client_case_id+'&type='+type,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			clientpricepointdialog.dialog({
		        title: 'Add Price Points',
		        autoOpen: true,
		        resizable: false,
		        height:456,
		        width: "50em",
		        modal: true,
		        close: function(){
					clientpricepointdialog.dialog('destroy').remove();
				},
		        buttons: [ 
		            {
			            text: "Cancel",
			            "title":"Cancel",
			            "class": 'btn btn-primary',
			            click: function() {
		            		clientpricepointdialog.dialog("close");
			            }
		            },
		            {
			            text: "Add",
			            "title":"Add",
			            "class": 'btn btn-primary',
			            click: function() {
			            	var form = jQuery('#reamining-preferred-pricing');
			            	var clone_id = 0;
			            	if($(form).find('input[name="clone_client_id"]:checked').length > 0){
			            		var clone_id = $(form).find('input[name="clone_client_id"]:checked').val();
			            	}
			            	if($(form).find('input[name="clone_client_case_id"]:checked').length > 0){
			            		var clone_id = $(form).find('input[name="clone_client_case_id"]:checked').val();
			            	}
			            	
			            	var data = form.serialize() + '&clone_id='+clone_id;
			            	if($(form).find('input[type="checkbox"]:checked').length == 0 && clone_id == 0)
			            	{
			            		if(type == 'client')
			            			alert('Please clone or select 1+ Price Point to perform this action.');	
			            		if(type == 'case')
			            			alert('Please clone or select 1+ Price Point to perform this action.');
			  					return false;
			            	} else {
			            		if(type == 'client')
			            			var addpricepointurl = baseUrl +'billing-pricelist/add-remaining-client-pricepoint&client_id='+client_id;
			            		if(type == 'case')
			            			var addpricepointurl = baseUrl +'billing-pricelist/add-remaining-case-pricepoint&client_id='+client_id+'&client_case_id='+client_case_id;
			            		jQuery.ajax({
			            			url: addpricepointurl,
			            			type: 'post',
			            			data: data,
			            			cache:false,
			            			beforeSend:function () {showLoader();},
			            			success: function (data) {
		            					clientpricepointdialog.dialog("close");
		            					getTemplatesByID(client_id,client_case_id,type);
		            					return false;
			            			}
			            		});
			            	}
		            	}
		            }
		        ],
		        open:function(){
					clientpricepointdialog.html(data);
		        	clientpricepointdialog.find('a:first').focus();
		        }
		    });
		},
		complete: function () {
			hideLoader();
		}
	});
}
/* End : Load Remaining Client Pricepoints Teams */

/* Start : Load Remaining Client Pricepoints by team & client */
function loadreamainingpricepoints(team_id)
{
	//if(isClientCaseSelectedtoClone()){
		$container = $('#container_'+team_id);
		$container.slideToggle(500, function() {});
		$myheader = $container.prev('.myheader');
		
		if($myheader.hasClass('myheader-selected-tab')){
			$myheader.removeClass('myheader-selected-tab');
		}else{
			$myheader.addClass('myheader-selected-tab');
		}
	//}
}
/* End : Load Remaining Client Pricepoints by team & client */

/* Start : It will expand clientcases to clone from */
function loadclientcasestoclonefrom(){
	var $container = $('#container_clientcase_toclonefrom');
	$container.slideToggle(500, function() {});
	$myheader = $container.prev('.myheader');
	
	if($myheader.hasClass('myheader-selected-tab')){
		$myheader.removeClass('myheader-selected-tab');
	}else{
		$myheader.addClass('myheader-selected-tab');
	}
}
/*End : It will expand clientcases to clone from */

/* Start : select parent Team when remaining Pricepoint selected */
function selectParentTeam(parent_id, child_id, child_state)
{
	var form = jQuery('#reamining-preferred-pricing');
	var obj = $(form).find('input[name="clone_client_id"]');
	var objchecked = $(form).find('input[name="clone_client_id"]:checked');
	if(obj.length == 0){
		var obj = $(form).find('input[name="clone_client_case_id"]');
		var objchecked = $(form).find('input[name="clone_client_case_id"]:checked');
	}
	
	if(obj.length > 0 && objchecked.length > 0){
		objchecked.next('label').removeClass('checked');
		objchecked.removeAttr('checked');
	}
	
	var selchildlen = $('[id^="child_'+parent_id+'_"]:checked').not('[id^="child_'+parent_id+'_'+child_id+'"]').length; 
	
	if(child_state == false && selchildlen == 0){
		$('[id="parent_'+parent_id+'"]').prop('checked',child_state);
		if($('label[for="parent_'+parent_id+'"]').hasClass('checked')){
			$('[id="parent_'+parent_id+'"]').next().removeClass('checked');
		}
	}
	
	if(child_state == true) {
		$('[id="parent_'+parent_id+'"]').prop('checked',child_state);
		if($('label[for="parent_'+parent_id+'"]').hasClass('checked') == false){
			$('label[for="parent_'+parent_id+'"]').addClass('checked');
		}
	}

}
/* End : select parent Team when remaining Pricepoint selected */

/* Start : select all child pricepoints when Team selected */
function selectChildContent(parent_id,parent_state)
{
	var form = jQuery('#reamining-preferred-pricing');
	var obj = $(form).find('input[name="clone_client_id"]');
	var objchecked = $(form).find('input[name="clone_client_id"]:checked');
	if(obj.length == 0){
		var obj = $(form).find('input[name="clone_client_case_id"]');
		var objchecked = $(form).find('input[name="clone_client_case_id"]:checked');
	}
	
	if(obj.length > 0 && objchecked.length > 0){
		objchecked.next('label').removeClass('checked');
		objchecked.removeAttr('checked');
	}
	
		if(parent_state == true){
			$('[for^="child_'+parent_id+'_"]').each(function(){
				var id = $(this).attr('for');
				$('#'+id).prop('checked',parent_state);
				if($(this).hasClass('checked') == false){
					$(this).addClass('checked');
				}
			});
		} else {
			$('[for^="child_'+parent_id+'_"]').each(function(){
				var id = $(this).attr('for');
				$('#'+id).prop('checked',parent_state);
				if($(this).hasClass('checked') == true){
					$(this).removeClass('checked');
				}
			});
		}
	
}
/* End : select all child pricepoints when Team selected */

/* Start : Function will check whether Clone Client/Case dropdown has selected or not, If Yes then it will disable all Teams & pricepoints */
function isClientCaseSelectedtoClone(){
	var form = jQuery('#reamining-preferred-pricing');
	var clone_id = 0;
	if($(form).find('input[name="clone_client_id"]:checked').length > 0){
		var clone_id = $(form).find('input[name="clone_client_id"]:checked').length;
	}
	if($(form).find('input[name="clone_client_case_id"]:checked').length > 0){
		var clone_id = $(form).find('input[name="clone_client_case_id"]:checked').length;
	}
	
	if($(form).find('input[type="checkbox"]').not('input[name="clone_client_case_id"]').not('input[name="clone_client_id"]').next('label').hasClass('checked')){
		$(form).find('input[type="checkbox"]').not('input[name="clone_client_case_id"]').not('input[name="clone_client_id"]').next('label').removeClass('checked');
	}
	$(form).find('input[type="checkbox"]').not('input[name="clone_client_case_id"]').not('input[name="clone_client_id"]').prop('checked', false);
			
}
/* End : Function will check whether Clone Client/Case dropdown has selected or not, If Yes then it will disable all Teams & pricepoints */

/** Start : Remove Template 
 * @param templated_id int
 * @param id int client_id/client_case_id
 * @param type string client/case
 * */
function RemoveTemplate(template_id,client_id,client_case_id,type){
	if(template_id != '' && template_id != 0)
	{
		var data = '';
		if(type == 'client')
			data = $('select[name="client_id"] option:selected').text(); 
		else 
			data = $('select[name="client_case_id"] option:selected').text(); 
		
		if(confirm("Are you sure you want to Delete "+data+"?")){
			$.ajax({
				url: baseUrl +'billing-pricelist/remove-template',
				type: 'post',
				data: {'template_id':template_id},
				cache:false,
				beforeSend:function () {showLoader();},
				success: function (data) {
					getTemplatesByID(client_id,client_case_id,type);
					return false;
				}
			});
		}
	}
}
/** End : Remove Template */

/** Start : Remove Pricepoint by template 
 * @param templated_id int
 * @param id int client_id/client_case_id
 * @param type string client/case
 * */
function RemovePricepointByTemplate(template_id,client_id,client_case_id,type,pricepoint_id,pricepoint){
	if(template_id != '' && template_id != 0)
	{
		var data = '';
		if(type == 'client')
			data = $('select[name="client_id"] option:selected').text(); 
		else 
			data = $('select[name="client_case_id"] option:selected').text(); 
		
		var price_point = [];
		var pricing_id = [];
		if(pricepoint_id!=0){
			price_point.push(pricepoint);
			pricing_id.push(pricepoint_id);
		} else {
			$('.pricing-sub-grid').each(function(){
				var newkeys = [];
				$('#'+$(this).parent('div').attr('id')+' input[type="checkbox"]:checked').each(function(){
					price_point.push($(this).val());
					pricing_id.push($(this).attr('data-id'));
				});
			});
		}
		var str_count = price_point.length;
		var strprice_point = price_point.join(',');
		if(pricing_id.length>0){
			if(confirm("Are you sure you want to Delete the selected "+str_count+" record(s): "+strprice_point+"?")){
				$.ajax({
					url: baseUrl +'billing-pricelist/remove-pricepoint-by-template',
					type: 'post',
					data: {'template_id':template_id, 'pricepoint':pricing_id},
					cache:false,
					beforeSend:function () {showLoader();},
					success: function (data) {
						getTemplatesByID(client_id,client_case_id,type);
						return false;
					}
				});
			}
		} else {
			alert("Please select a record to perform this action.");
			return false;
		}
	}
}
/** End : Remove Pricepoint by id(client_id/case_id) & pricing_id */

/** Start : Adjust pricing rate by client/case 
 * @param pricing_id int
 * @param client_id int 
 * @param client_case_id int
 * @param team_id int
 * @param type string client/case
 * */
function AdjustPricepointByClientcase(pricing_id, client_id, client_case_id, team_id, type){
	
	var pricingratedialog = $('body').find('div#pricingrate-dialog');
	if(pricingratedialog.length == 0){
		$('body').append('<div id="pricingrate-dialog" class="task-details-popup"></div>');
		var pricingratedialog = $('body').find('div#pricingrate-dialog');
	}
	var id = client_id;
	var title = 'Add Client Rate';
	if(type == 'case'){
		var id = client_case_id;
		var title = 'Add Case Rate';
	}
	
	jQuery.ajax({
		url: baseUrl +'billing-pricelist/load-pricing-rate&pricing_id='+pricing_id+'&team_id='+team_id+'&id='+id+'&type='+type,
		type: 'get',
		beforeSend:function () {showLoader();},
		success: function (data) {
			pricingratedialog.dialog({
		        title: title,
		        autoOpen: true,
		        resizable: false,
		        height:692,
		        width: "80em",
		        modal: true,
		        open:function(){
					pricingratedialog.html(data);
		        },
		        close: function(){
					pricingratedialog.dialog('destroy').remove();
				},
				beforeClose: function(event){
					if(event.keyCode==27) trigger = 'esc';
					if(trigger != 'Update') checkformstatus(event);
				},
			    buttons: [
					{
			            text: "Cancel",
			            "title":"Cancel",
			            "class": 'btn btn-primary',
			            click: function() {
							trigger = 'Cancel';
							pricingratedialog.dialog("close");
			            }
		            },
		            {
			            text: "Update",
			            "title":"Update",
			            "class": 'btn btn-primary',
			            click: function() {
							trigger = 'Update';
							var form = $('form#PricingRates');
							var data = form.serialize();
	            			pricingratedialog.dialog("close");
			            	$.ajax({
		        				url: baseUrl +'billing-pricelist/add-preferred-pricing-rate&pricing_id='+pricing_id+'&client_id='+client_id+'&client_case_id='+client_case_id+'&type='+type,
		        				type: 'post',
		        				data: data,
		        				beforeSend:function (data) {showLoader();},
		        				success: function (data) {
									getTemplatesByID(client_id,client_case_id,type);
		    						return false;
		        				}
		        			});
		            	}
		            }
		        ]
		    });
		},
		complete: function () {
			hideLoader();
			pricingratedialog.find('#pricingrates-rate_amount').focus();
			$('#PricingRates').ajaxForm({
				beforeSubmit:function(arr, $form, options) { showLoader();return true; },
				success: submitPricingRate,
				complete:function() { hideLoader();},
			});
		}
	});
}
/** End : Load Adjust pricing rate by client/case */
