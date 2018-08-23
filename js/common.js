function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 46) {
			return false;
	}
	return true;
}
if (!Array.prototype.findIndex) {
  Object.defineProperty(Array.prototype, 'findIndex', {
    value: function(predicate) {
     // 1. Let O be ? ToObject(this value).
      if (this == null) {
        throw new TypeError('"this" is null or not defined');
      }

      var o = Object(this);

      // 2. Let len be ? ToLength(? Get(O, "length")).
      var len = o.length >>> 0;

      // 3. If IsCallable(predicate) is false, throw a TypeError exception.
      if (typeof predicate !== 'function') {
        throw new TypeError('predicate must be a function');
      }

      // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
      var thisArg = arguments[1];

      // 5. Let k be 0.
      var k = 0;

      // 6. Repeat, while k < len
      while (k < len) {
        // a. Let Pk be ! ToString(k).
        // b. Let kValue be ? Get(O, Pk).
        // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
        // d. If testResult is true, return k.
        var kValue = o[k];
        if (predicate.call(thisArg, kValue, k, o)) {
          return k;
        }
        // e. Increase k by 1.
        k++;
      }

      // 7. Return -1.
      return -1;
    }
  });
}
var trigger;
$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) {
		$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
		$('.ui-dialog-titlebar-close').attr("title", "Close");
		$('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	}
});

/**
 * onbefore unload event @return
 */
window.onbeforeunload=checkunloadform;
function checkunloadform(e)
{
	var form_id = $('#active_form_name').val(); // get form name
	var is_change_form_main = $("#is_change_form_main").val();
	if(form_id != '' && form_id != undefined)
		is_change_form_main = $("#"+form_id+" #is_change_form_main").val(); // Assign

	if(is_change_form_main > 0) {
		var clk = document.activeElement.title;
		if(clk != 'Logout' && clk != 'Add' && clk != 'Update' && clk != 'Submit' && clk != 'Save' && clk != 'ReSubmit' && clk != 'Post' && clk != 'Delete' && clk != 'Export' && clk != 'PDF Export' && clk != 'Run' && clk != 'Export Chart Report'  && clk != 'Export Tabular Report') {
                	hideLoader(); // hide loader
			return "This page is asking you to confirm that you want to leave - data you have entered may not be saved";
		}
	}
}


/*$(function (){
	$( ".datepickers" ).datepicker({dateFormat: 'yy-mm-dd'});
});*/
function reorderFormBuilder(id)
{
	var sorder="";
    // Added Date: 12-1-15, remove new added TR in popup
    $("."+id).remove();
	$("#form_builder_panel ol li").each(function(){ // new code for sorting
		if($(this).html=="")
			$(this).remove();
		if($(this).attr('data-id')){
			if(sorder=="")
				sorder=$(this).attr('data-id');
			else
				sorder= sorder + "," +$(this).attr('data-id');
		}
});
$('#sort_order').val('').val(sorder);
}

function allowOnly(e)
{
	var unicode=e.charCode? e.charCode : e.keyCode
	//*f(unicode == 39 || unicode == 34) return false;*/
	return true;
}
function textToLable(obj) {
    id = $(obj).attr('id');
    if (id !== undefined)
    {
        id_val = $(obj).val();
        $('#form_builder_panel ol li a').each(function (i, e) {
            if (e.id == id.replace("_text", ""))
                $(e).html(id_val);
        });
    }
}
function dailogConfirmed(title,msg,method_name,arg){
	var dynamicDialog = $('<div id="dailogConfirmed">'+msg+'</div>');
	dynamicDialog.dialog({
		title: title,
        modal: true,
        width:'40em',
        create: function(event, ui) {
			 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
		},
        buttons: [
                  {
                	  text: "Yes",
                	  "title":"Yes",
                	  "class": 'btn btn-primary',
                	  click: function () {
                		  $(this).dialog("close");
                		//Create the function

                  		if(arg.length > 0){
                  			var fn = window[method_name];
                  			//Call the function
                			fn.apply(this, arg);
                		}
                		else{
                			eval(method_name+'()');
                		}
                	  }
                  },
                  {
                	  text: "No",
                	  "title":"No",
                	  "class": 'btn btn-primary',
                	  click: function () {
                		  $(this).dialog("close");
                	  }
                  }
        ]
    });
}
function dailogAlert(title,msg){
	var dynamicDialog = $('<div id="dailogConfirmed">'+msg+'</div>');
	dynamicDialog.dialog({
		title: title,
        modal: true,
        width:'40em',
        create: function(event, ui) {
			 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
		},
        buttons: [
                  {
                	  text: "Ok",
                	  "title":"Ok",
                	  "class": 'btn btn-primary',
                	  click: function () {
                		  $(this).dialog("close");
                	  }
                  }
        ]
    });
}


function clearForm(form){
	$(':input', form).each(function() {
		var type = this.type;
		var tag = this.tagName.toLowerCase(); // normalize case
		// it's ok to reset the value attr of text inputs,
		// password inputs, and textareas
		if (type == 'text' || type == 'password' || tag == 'textarea')
		  this.value = "";
		// checkboxes and radios need to have their checked state cleared
		// but should *not* have their 'value' changed
		else if (type == 'checkbox' || type == 'radio')
		  this.checked = false;
		// select elements need to have their 'selectedIndex' property set to -1
		// (this works for both single and multiple select elements)
		else if (tag == 'select')
		  this.selectedIndex = -1;
	});
}
function commonAjax(url,updateID){
	jQuery.ajax({
	    url: url,
	    cache: false,
	    type: 'get',
	    beforeSend:function (data) {
			showLoader();
		},
	    success: function (data) {

	    	hideLoader();
	 	   jQuery('#'+updateID).html(data);


	    }
	});
}
function showLoader(){
	jQuery('#loader').show();
}
function hideLoader(){
	jQuery('#loader').hide();
}
function noAlphabets(event) {
    var charCode = (event.which) ? event.which : event.keyCode
    if ((charCode >= 97) && (charCode <= 122) || (charCode >= 65)
&& (charCode <= 90))
        return false;

    return true;
}
function setTitle(iconclass, titletext){
	var emtitle=strip_tags(titletext);
	jQuery('#page-title').html('<em title="'+emtitle+'" class="fa '+iconclass+'"></em> <span> '+titletext+'</span>');
}
function ManageDropdownSubmitAjaxForm(form_id,btn,successFunc) {
	var form = $('form#'+form_id);
	$.ajax({
        url    : form.attr('action'),
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
        	$(btn).attr('disabled','disabled');
        },
        success: function (response){
        	if(response == 'OK'){
				eval(successFunc+'()');
			}else{
				$('#form_div').html(response);
        		$(btn).removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}
function SubmitAjaxForm(form_id,btn,successFunc,targetid){
	var form = $('form#'+form_id);
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
				eval(successFunc);
			}else{
				$('#'+targetid).html(response);
        		$(btn).removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}

$(document).ready(function() {
	$('.filter_number_only').on('keydown', function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,110])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});
	$(document).on('pjax:end',   function(xhr, textStatus, options) {
		console.log(xhr);
            $('input').customInput();

            $('.media-tr input[type="checkbox"]').each(function(){
				$(this).parent().remove();
			});

            $('.all_filter').hide();
            if ($(xhr.target).attr('data-filter') !== undefined) {
                if($(xhr.target).attr('data-filter') == 1){
                    $('.all_filter').show();
                }
				else if($('#dynamic_filter') && $('#dynamic_filter').val()==1) {
					$('.all_filter').show();
				}
            }
	    /* start: custom code to dynamically resize grid height according to screen resolution */
	    if($('.table-responsive').length>0 && $('.kv-panel-pager').length>0)
	    {
		var grid_height = $('.table-responsive').height()-$('.kv-panel-pager').height()-5;
		$('.kv-grid-container').height(grid_height);
	    }
	    /* end: custom code to dynamically resize grid height according to screen resolution */
	    var interval=setInterval(function(){
			if($("[sortfocus]")){
				if(!$("[sortfocus]").is(":focus")){
					//$("[sortfocus]").focus();
					clearInterval(interval)
				}
			}
		},100);
		if($('.table-responsive .filters').find('select[data-krajee-select2]').length > 0){
			$('.table-responsive .filters').find('select[data-krajee-select2]').each(function () {

				var $el = $(this), settings = window[$el.attr('data-krajee-select2')] || {};
				$('.select2-container').removeClass('select2-container--open');
				$('.select2-container').removeClass('select2-container-'+$el.attr('id')+'-open');
				if ($el.data('select2')) {
					$el.select2('destroy');
				}
				$.when($el.select2(settings)).done(function () {
					initS2Loading($el.attr('id'), '.select2-container--krajee'); // jshint ignore:line
				});
			});
		}
	    //setTimeout(function() {   $("[sortfocus]").focus();  }, 1000);
		$('.filter_number_only').on('keydown', function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,110])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});
        });
        $(document).on('pjax:success',   function(xhr, textStatus, options) {
			var interval=setInterval(function(){
			if($("[sortfocus]")){
				if(!$("[sortfocus]").is(":focus")){
					$("[sortfocus]").focus();
					clearInterval(interval)
				}
			}
			},1000);
		});
	$(document).on('change', '.select-on-check-all', function() {
				//alert('working for pjax working');
		var main_id = $(this).attr('id');
		var mainContainerDiv = $(document).find('div.kv-grid-container');
		//console.log(mainContainerDiv.html());
		var chk_select_all = $('.select-on-check-all').is(':checked');
		$(mainContainerDiv).find('input[type="checkbox"]').not('.select-on-check-all').each(function(){
			$(this).prop('checked',chk_select_all);
			//console.log($(this).next('label'));
			//alert('dsgs');
			$('label[for="'+$(this).attr('id')+'"]').removeClass('checked');
			if(chk_select_all){
				$('label[for="'+$(this).attr('id')+'"]').addClass('checked');
			}
		});
	});
	$(document).on('change', 'input[custominput=""]', function() {
		var chk_select_all = $(this).is(':checked');
		if(!chk_select_all){
			$('.select-on-check-all').next('label').removeClass('checked');
		}else{
			var mainContainerDiv = $(document).find('div.kv-grid-container');
			var allchkbox_count = $(mainContainerDiv).find('input[type="checkbox"]').not('.select-on-check-all').length;
			var checked_chkbox=$(mainContainerDiv).find('input[type="checkbox"]:checked').not('.select-on-check-all').length;
			if(checked_chkbox==allchkbox_count){
				$('.select-on-check-all').next('label').addClass('checked');
			}

		}
	});
	$('#submitMediaDataType').on('click', function(){
	    var old_password = $('#options-old_password').val();
	    if(typeof(old_password)!="undefined" && old_password!="") {
		var form_error = false;
		$.ajax({
			url : baseUrl+'/site/passwords',
			type : 'post',
			data : {
			'old_password' : old_password
			},
			success: function(response){
				if(response == 0){
					$('#options-old_password').parent().parent().parent().addClass("has-error");
					$('#options-old_password').siblings().html("This does not match your current/old password.");
					form_error=true;
				}else{
					$('#options-old_password').parent().parent().parent().removeClass("has-error");
					$('#options-old_password').siblings().html("");
					form_error=false;
				}
				if(!form_error)
				    $('#Options').submit();
			},
			});
	    }
	    else
	    {
		$('#Options').submit();
	    }
	});
	$('#options-old_password').on('change',function(){
		var old_password = $(this).val();
		$.ajax({
			url : baseUrl+'/site/passwords',
			type : 'post',
			data : {
			'old_password' : old_password
			},
			success: function(response){
				if(response == 0){
					$('#options-old_password').parent().parent().parent().addClass("has-error");
					$('#options-old_password').siblings().html("This does not match your current/old password.");
				}else{
					$('#options-old_password').parent().parent().parent().removeClass("has-error");
					$('#options-old_password').siblings().html("");
				}
			},
			});
	});

	$(document).on("keydown", '.numeric-field-qu',function (e) {
		//alert('here 123');
  		var keylist = [46, 8, 9, 27, 13, 116];
  		var isint = false;
  		if($(this).hasClass('integer')){
  			var isint = true;
  		}

  		var isnegative = false;
  		if($(this).hasClass('negative-key')){
  			var isnegative = true;
  		}

  		var dotcount = ($(this).val().match(/\./g) || []).length;
  		var negcount = ($(this).val().match(/\-/g) || []).length;

		if(!$(this).hasClass('billing_units')) 
		{ 
			var text = $(this).val();
			if ((text.indexOf('.') != -1) &&
			(text.substring(text.indexOf('.')).length > 2) &&
			(e.keyCode != 0 && e.keyCode != 8 && e.keyCode != 9 && e.keyCode != 37 && e.keyCode != 39) &&
			($(this)[0].selectionStart >= text.length - 2)) {
				e.preventDefault();
			}
		}

  		//console.log("in : "+isnegative);
  		if ($.inArray(e.keyCode, keylist) !== -1 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 40) || (dotcount==0 && (e.keyCode == 110 || e.keyCode == 190) && !isint) || (negcount==0 && (e.keyCode == 109 || e.keyCode == 173 || e.keyCode == 189) && isnegative)){//console.log("negative in");
  			return;
  		}
  		if ((e.keyCode == 190 || e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) || (dotcount>1 && (e.keyCode == 110 || e.keyCode == 190)) || (negcount>1 && (e.keyCode == 109 || e.keyCode == 173)))
  		{//console.log("prevent in");
  			e.preventDefault();
  		}

	  });
	  $(document).on("paste", '.numeric-field-qu',function (e) {
		//var text = e.originalEvent.clipboardData.getData('Text');
		clipboardData = e.clipboardData || window.clipboardData;
		var text = clipboardData.getData('Text');
		if ($.isNumeric(text)) {
			if ((text.substring(text.indexOf('.')).length > 3) && (text.indexOf('.') > -1)) {
				e.preventDefault();
				$(this).val(text.substring(0, text.indexOf('.') + 3));
		   }
		}
		else {
				e.preventDefault();
			 }
		});

  	$(document).on("blur", '.numeric-field-qu', function () {
  		if($(this).val().charAt(0) == '.') {
  			$(this).val('0'+$(this).val());
  		}
  		if($(this).val().replace(/\s+/g, '') != '' && !isValidnumber($(this).val()))
  		{
  			if($(this).hasClass('integer') && !isValidInteger($(this).val())){
  				$(this).val('');
  			} else if($(this).val().charAt($(this).length) == '.'){
  				$(this).val('');
  			} else if (!$(this).hasClass('integer')) {
  				$(this).val('');
  			}
  	        return;
  		}
  	});
});

function isValidnumber(number){
	var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
	return numberRegex.test(number);

}

function isValidInteger(number){
	var numberRegex = /^[+-]?\d?([eE][+-]?\d+)?$/;
	return numberRegex.test(number);

}

function historyPushState(targetUrl, targetTitle){
	window.history.pushState({url: "" + targetUrl + ""}, targetTitle, targetUrl);
}

/*workflow toggle*/
function WorkflowToggle(){
	var url = $('#module-url').val();
	var pajax_container = $('#pajax_container').val();
	if($('#'+pajax_container).length){
		$.pjax.defaults.url =url;
		$.pjax.defaults.push= false;
		//$.pjax.reload('#'+pajax_container, $.pjax.defaults);

	}
	jQuery('#maincontainer').toggleClass('slide-close');

	setTimeout(function() {
		$('#'+pajax_container).find('.kv-thead-float').css('left', $('#'+pajax_container).find('.kv-grid-container').offset().left);
		$('#'+pajax_container).find('.kv-thead-float').css('width', $('#'+pajax_container).find('.kv-grid-table:eq( 1 )').css('width'));
	},300);
}

function changeTodaysActivity(){
	$('#activity_offset').val('0');
	var offset = parseInt($('#activity_offset').val());
	setTimeout(function(){
	$.ajax({
		url:baseUrl + "site/ajaxprocessactivity",
	    type:"post",
	    async: false,
	    beforeSend:function(){

			showLoader();

	    	//$('#tabs_33_activity').html('<center>Loading...<br/><img src="<?php echo $tUrl; ?>/images/ajaxloader.gif" alt="Loading..." /></center>');
	    },
	   data:{'YII_CSRF_TOKEN':"<?php echo Yii::$app->request->csrfToken ?>",'offset':offset},
	   success:function(mydata){
			hideLoader();
			if(mydata=="")
				mydata = '<span>No records found.</span>';
			   $('#activity-log-dynamic').html(null).html(mydata);
			   
			$('#kv-grid-demo-container').on('scroll',function(){
				var obj=this;
			   if( obj.scrollTop == (obj.scrollHeight - (obj.offsetHeight))) {
				   if(parseInt($('#noactivities').val())==0){
					   yHandler();
				   }
			   }
		   });
		   function yHandler(){
			   $('#activity_offset').val(parseInt($('#activity_offset').val()) + parseInt(100));
			   var offset = parseInt($('#activity_offset').val());
			   $.ajax({
				   url:baseUrl + "site/ajaxprocessactivity",
				   type:"post",
				   async: false,
				   beforeSend:function(){
					   showLoader();
				   },
				  data:{'YII_CSRF_TOKEN':"<?php echo Yii::$app->request->csrfToken ?>",'offset':offset},
				  success:function(mydata){
					   hideLoader();
					   if($.trim(mydata)!=""){
						   $('#activity-log-dynamic').append(mydata);
						  }else{
						   $('#noactivities').val(1);	
					   }
				  }, error: function(mydata) { 
								  return false;
				  }
			   });
		   }  
	   }, error: function(mydata) {
                       return false;
       }
	});
	},100);
}
function validateFormBuilder(){
	var has_error=false;
	$(document).find('.form-builder-ol .required-entry').filter(':input').each(function(){
		if ($(this).is("input:checkbox") || $(this).is("input:radio")){
			var name = $(this).attr('name');
			var $myLabel = ($('label[for="'+ name.replace('[]','') +'"]').text());
			if($('input[name="'+name+'"]:checked').length == 0){
				if(!$(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().addClass('has-error');
					$(this).parent().parent().parent().addClass('has-error');
					$(this).parent().parent().parent().append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				has_error=true;
			}
			else{
				if($(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().removeClass('has-error');
					$(this).parent().parent().parent().removeClass('has-error');
					$(this).parent().parent().parent().find('.help-block').html(null);
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
function validateBillingFormBuilder(){
	var has_error=false;
	$(document).find('.form-builder-ol .required-entry').filter(':input').each(function(){
		if ($(this).is(":checkbox") || $(this).is(":radio")){
			var name = $(this).attr('name');
			var $myLabel = ($('label[data-for-name="'+ name.replace('[]','') +'"]').text());

			if($('input[name="'+name+'"]:checked').length == 0){
				if(!$(this).closest('div.block').hasClass('has-error')){
					$(this).closest('div.block').addClass('has-error');
					$(this).closest('div.block').append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				has_error=true;
			}
			else{
				if($(this).closest('div.block').parent().hasClass('has-error')){
					$(this).closest('div.block').removeClass('has-error');
					$(this).closest('div.block').find('.help-block').html(null);
				}
			}
		}
		else if ($(this).is("select")){
			if($(this).val()=="0"){
				var name = $(this).attr('name');
				var $myLabel = ($('label[data-for-name="'+ name +'"]').text());console.log(name);
				if(!$(this).closest('div.block').hasClass('has-error')){
				    $(this).closest('div.block').addClass('has-error');
				    $(this).closest('div.block').append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				has_error=true;
			}else{
				if($(this).closest('div.block').hasClass('has-error')){
					$(this).closest('div.block').removeClass('has-error');
					$(this).closest('div.block').find('.help-block').html(null);
				}
			}
		}
		else{
			if($(this).val()==""){
				var name = $(this).attr('name');
				var $myLabel = ($('label[data-for-name="'+ name +'"]').text());
				if(!$(this).closest('div.block').hasClass('has-error')){
					$(this).closest('div.block').addClass('has-error');
					$(this).closest('div.block').append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				has_error=true;
			}else{
				if($(this).closest('div.block').hasClass('has-error')){
					$(this).closest('div.block').removeClass('has-error');
					$(this).closest('div.block').find('.help-block').html(null);
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
	if(has_error === false){
		var has_value="N";
		var has_element="N";
		if($("#billing_data_div .form-builder-task .user_input").length > 0){
			has_element="Y";
			$("#billing_data_div .form-builder-task .user_input").each(function(){
				//console.log($(this).attr('type'));
				if ($(this).is(":checkbox") || $(this).is(":radio")){
					if($('input:checked').length > 0){
						has_value="Y";
					}
				}
				if ($(this).is("select") && has_value=="N"){
					if($(this).val()!="0"){
						has_value="Y";
					}

				}
				if(($(this).is("textarea") || $(this).is('input:text') ) && has_value=="N"){
					if($.trim($(this).val())!=""){
						has_value="Y";
					}
				}
			});
		}
		if($("#billing_data_div #T-list").html()!=""){
			has_value="Y";
		}
		if($("#billing_data_div .billing_units").length > 0 && has_value=="N"){
			has_element="Y";
			$("#billing_data_div .billing_units").each(function(){
				if($.trim($(this).val())!=""){
					has_value="Y";
				}
			});
		}
		//console.log(has_value + " => " + has_element);
		if(has_value == "N" && has_element=="Y"){
			alert('Please enter a value to perform this action.');
			return true;
		}else{
			return false;
		}
	}
	return has_error;
}
function stripHTML(dirtyString) {
	  var container = document.createElement('div');
	  var text = document.createTextNode(dirtyString);
	  container.appendChild(text);
	  return container.innerHTML; // innerHTML will be a xss safe string
	}
/*Start: Add Instruction Notes*/
function AddInstrcutionNotes(servicetask_id,task_id){
	if(!$( "#add-instrcution-notes" ).length){
		$('body').append("<div id='add-instrcution-notes'></div>");
	}
	$( "#add-instrcution-notes" ).dialog({
		  title:"Add Instruction Notes",
	      autoOpen: false,
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
					trigger = 'Cancel';
	                $( this ).dialog( "close" );
	            }
	        },
	        {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
					trigger = 'Update';
	            	$('#TaskInstructNotes').submit();
	            }
	        }
	    ],
	    beforeClose: function(event) {
			if(event.keyCode == 27)	trigger = 'esc';
			if(trigger != 'Update')	checkformstatus(event);
		},
	    close: function() {
	    	$(this).dialog('destroy').remove();
	        // Close code here (incidentally, same as Cancel code)
	    }
	    });
		$.ajax({
		url:baseUrl + "track/instruction-notes&servicetask_id="+servicetask_id+"&task_id="+task_id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	       $('#add-instrcution-notes').html(null).html(mydata);hideLoader();$( "#add-instrcution-notes" ).dialog("open");
		},
		complete: function () {
			$('#TaskInstructNotes').ajaxForm({
				beforeSubmit : function(arr, $form, options){
					showLoader();
				},
		        success: SubmitSuccesfulInstructionNotesForm,
		    });
		}
		});
}

/* Remove The Aditional Task Instructions */
function DeleteInstrcutionNotes(servicetask_id,task_id,obj){
	name = $(obj).data('name');
	if (confirm('Are you sure you want to Delete the Additional Task Instructions?')) { // '+name+'
		$.ajax({
			url:baseUrl + "track/deletetaskinstruction&servicetask_id="+servicetask_id+"&task_id="+task_id,
		    type:"get",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
			    hideLoader();
			    if(mydata=='OK'){
					$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
		        }
			}
		});
	}
}


function SubmitSuccesfulInstructionNotesForm(responseText, statusText) {
	hideLoader();
	if(responseText == 'OK'){
		$('#add-instrcution-notes').dialog('close');
		$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
		//location.reload();
	}else{
		$("#add-instrcution-notes").html(responseText);
	}
}
/*End: Add Instruction Notes*/
/*Start: Add Todo*/
function AddTodo(servicetask_id,task_id,team_loc,taskunit_id){
	if(!$( "#add-todo" ).length){
		$('body').append("<div id='add-todo'></div>");
	}
	$( "#add-todo" ).dialog({
		  title:"Add ToDo Item",
	      autoOpen: false,
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
					trigger = 'Cancel';
	                $( this ).dialog( "close" );
	            }
	        },
	        {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
                        trigger = 'Update';
                        var textarea = $('#tasksunitstodos-todo').val();
                        if($.trim(textarea) != ''){
                                showLoader();
                                $('#TasksUnitsTodos').submit();
                        }
	            }
	        }
	    ],
	    beforeClose: function(event){
			if(event.keyCode == 27)	trigger = 'esc';
			if(trigger != 'Update')	checkformstatus(event);
		},
	    close: function() {
	    	$(this).dialog('destroy').remove();
                $( this ).dialog( "close" );
	        // Close code here (incidentally, same as Cancel code)
	    }
	    });
	$.ajax({
		url:baseUrl + "track/addtodo&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	       $('#add-todo').html(null).html(mydata);hideLoader();$( "#add-todo" ).dialog("open");
		},
		complete: function () {
			$('#TasksUnitsTodos').ajaxForm({
		        success: SubmitSuccesfulTODO,
		    });
		}
		});
}
function SubmitSuccesfulTODO(responseText, statusText) {
		hideLoader();
		if(responseText == 'OK'){
                    $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
                    //$('#add-todo').dialog('close');
                    $('#add-todo').remove();
//                    location.reload();
		}else{
			$("#add-todo").html(responseText);
		}
}
/*Start: Edit Todo*/
function EditTodo(servicetask_id,task_id,team_loc,taskunit_id,todo_id){
	if(!$( "#add-todo" ).length){
		$('body').append("<div id='add-todo'></div>");
	}
	$( "#add-todo" ).dialog({
		  title:"Edit ToDo Item",
	      autoOpen: false,
		  resizable: false,
	      width: "50em",
	      modal: true,
		  buttons: [
	        {
	            text: "Cancel",
	            "title":"Cancel",
	            "class": 'btn btn-primary',
	            click: function() {
					trigger = 'Cancel';
	                $( this ).dialog( "close" );
	            }
	        },
	        {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
					trigger = 'Update';
					var todo = $('#tasksunitstodos-todo').val();
	            	if($.trim(todo) != '') {
						showLoader();
	            		$('#TasksUnitsTodos').submit();
					}
	            }
	        }
	    ],
	    beforeClose: function(event) {
			if(event.keyCode == 27)	trigger = 'esc';
			if(trigger != 'Update')	checkformstatus(event);
		},
	    close: function() {
	    	$(this).dialog('destroy').remove();
	        // Close code here (incidentally, same as Cancel code)
	    }
	    });
	$.ajax({
			url:baseUrl + "track/editodo&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id+"&todo_id="+todo_id,
		    type:"get",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
		       $('#add-todo').html(null).html(mydata);hideLoader();$( "#add-todo" ).dialog("open");
			},
			complete: function () {
				$('#TasksUnitsTodos').ajaxForm({
			        success: SubmitSuccesfulTODO,
			    });
			}
		});
}
/*Start: Complete Todo*/
function CompleteTodo(servicetask_id,task_id,team_loc,taskunit_id,todo_id,team_id,case_id){
	$.ajax({
		url:baseUrl + "track/completetodo&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id+"&todo_id="+todo_id+"&team_id="+team_id+"&case_id="+case_id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	       if(mydata=='OK'){
	    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
	    	//   location.reload();
	       }else{
	    	   alert(mydata);
	       }
		}
	});
}
/*Start: ReOpen Todo*/
function ReOpenTodo(servicetask_id,task_id,team_loc,taskunit_id,todo_id,team_id,case_id){
	$.ajax({
		url:baseUrl + "track/reopentodo&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id+"&todo_id="+todo_id+"&team_id="+team_id+"&case_id="+case_id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	       if(mydata=='OK'){
	    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
			//location.reload();
	       }else{
	    	   alert(mydata);
	       }
		}
	});
}
/*Start: Delete Todo*/
function DeleteTodo(id,team_id,case_id,team_loc,task_id){
	name = $('#todo_'+id).data('name');
	if (confirm('Are you sure you want to Delete this ToDo item?')) { //  '+name+'
		$.ajax({
			url:baseUrl + "track/deletetodo&todo_id="+id+"&team_id="+team_id+"&case_id="+case_id+"&team_loc="+team_loc+"&task_id="+task_id,
		    type:"get",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
		    	hideLoader();
		       if(mydata=='OK'){
		    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
		    //location.reload();
		       }else{
		    	   alert(mydata);
		       }
			}
		});
	}
}

/*Start: Assign / Transit Todo*/
function AssignTransitTodo(servicetask_id,task_id,team_loc,taskunit_id,todo_id,team_id,case_id,asssigned){
	if(!$( "#add-assign-transition" ).length){
		$('body').append("<div id='add-assign-transition'></div>");
	}
	var title='Assign ToDo';
	if(asssigned >0)
		title='Transition ToDo';
	$.ajax({
		url:baseUrl + "track/assign-transit-todo&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id+"&todo_id="+todo_id+"&team_id="+team_id+"&case_id="+case_id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	       hideLoader();
	       if(mydata.match(/<(\w+)((?:\s+\w+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/)){
			   $('#add-assign-transition').html(null).html(mydata);
			   $( "#add-assign-transition" ).dialog("open");
			}else{
				alert(mydata);
				return false;
			}

		}
		});
	$( "#add-assign-transition" ).dialog({
		  title:title,
	      autoOpen: false,
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
				id: "track-transit-update",
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
	            	if($('#add-assign-transition li.active').length){
	            		user_id=$('#add-assign-transition li.active').data('id');
	            		$.ajax({
	            			url:baseUrl + "track/assign-transit-todo&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id+"&todo_id="+todo_id+"&team_id="+team_id+"&case_id="+case_id,
	            		    type:"post",
	            		    data:{user_id:user_id},
	            		    beforeSend:function(){
	            				showLoader();
	            		    },
	            		    success:function(mydata){
	            		    	hideLoader();
	            		       if(mydata=='OK'){
	            		    	   //$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
									location.reload();
	            		       }else{
	            		    	   alert(mydata);
	            		       }
	            			}
	            		});
	            	}
	            	//showLoader();
	            }
	        }
	    ],
	    close: function() {
	    	$(this).dialog('destroy').remove();
	        // Close code here (incidentally, same as Cancel code)
	    }
	    });

}
/*Change Task unit status*/
function changeStaus(servicetask_id,task_id,taskunit_id,status){
	var status_arr={0:" Not Started",1:"Started",2:"On Pause",3:"On Hold",4:"Completed"};
	//if (confirm('Are you sure you want to change the Status to '+status_arr[status]+'?')) {
		$.ajax({
			url:baseUrl + "track/changetaskstatus&servicetask_id="+servicetask_id+"&task_id="+task_id+"&taskunit_id="+taskunit_id+"&status="+status,
		    type:"post",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
		    	hideLoader();
		       if(mydata=='OK'){
				$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
				setTimeout(function(){
					$( "#filtertrackproject" ).select2({
						theme: "krajee"
					});
					$('input').customInput();
				 }, 200);
				//window.location.reload(false);
				//, $.pjax.defaults
		       }else{
		    	   alert(mydata);
		       }
			}
		});

	//}
}

window.pjax_load = function()
{
  console.log('on pjax load');
}

/*Unassign Task unit status*/
function UnassignTask(servicetask_id,task_id,team_loc,taskunit_id){
	if(confirm('Are you sure you want to Unassign #'+task_id+'?  The Task and any Incomplete ToDos will also be Unassigned.')){
		$.ajax({
			url:baseUrl + "track/unassigntask&servicetask_id="+servicetask_id+"&task_id="+task_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id,
		    type:"post",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
		    	hideLoader();
		       if(mydata=='OK'){
				   $( "#add-assign-transition" ).dialog('close');
		    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
				// location.reload();

		       }else{
		    	   alert(mydata);
		       }
			}
		});
	}
}
function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		if(key!='r'){
			vars[key] = value;
		}

	});
	return vars;
}
/*Assign Task unit status*/
function AssignTaskAll(task_id,case_id,team_id,team_loc){
	 var sel_row = jQuery('.grid-view').yiiGridView('getSelectedRows');
	 if(!sel_row.length){
 		alert('Please select at least 1 record to perform this action.');
 	}else{
 		var option=jQuery('#filtertrackproject').val();
 		var params=getUrlVars();
 		$.ajax({
			url:baseUrl + "track/assigntask",
			data:{ids:sel_row,task_id:task_id,case_id:case_id,team_id:team_id,team_loc:team_loc,params:params},
		    type:"get",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
		    	hideLoader();
		    	var obj = jQuery.parseJSON(mydata);
		    	if (obj.error!=""){
		    	   alert(obj.error);
		        }else{
		    	    if(!$( "#add-assign-transition" ).length){
		    			$('body').append("<div id='add-assign-transition'></div>");
		    		}
		    	   	$( "#add-assign-transition" ).html(obj.data);
		    		$( "#add-assign-transition" ).dialog({
		    			  title:"Assign/Transition Task(s)",
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
		    		            	if($('#add-assign-transition li.active').length){
		    		            		user_id=$('#add-assign-transition li.active').data('id');
		    		            		service_ids=$("#services").val();
		    		            		taskunit_ids=$('#taskunits').val();

			    		            	$.ajax({
			    		        			url:baseUrl + "track/assigntask&ids="+sel_row+"&task_id="+task_id+"&case_id="+case_id+"&team_id="+team_id+"&team_loc="+team_loc,
			    		        			data:{user_id:user_id,service_ids:service_ids,taskunit_ids:taskunit_ids},
			    		        		    type:"post",
			    		        		    beforeSend:function(){
			    		        				showLoader();
			    		        		    },
			    		        		    success:function(mydata){
			    		        		    	hideLoader();
			    		        		    	if(mydata=='OK'){
			    		        		    	   $( "#add-assign-transition" ).dialog('close');
			    		        		    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
													// location.reload();
			    		        		       } else {
			    		        		    	   alert(mydata);
			    		        		       }
			    		        			}
			    		        		});
		    		            	}else{
		    		            		alert('Please select a User to perform this action.');
		    		            	}
		    		            }
		    		        }
		    		    ],
		    		    close: function() {
		    		    	$(this).dialog('destroy').remove();
		    		        // Close code here (incidentally, same as Cancel code)
		    		    },
						open: function() {
		    		    	$('.search-result').css('height', parseInt($('#add-assign-transition').height())-(parseInt($('.search-box').height())+20));
		    		    }
		    		    });
		       }
			}
		});
 	}
}
/*Transit Task Unit Status*/
function TransitTask(task_id,case_id,team_id,team_loc,servicetask_id,taskunit_id,access){
        $.ajax({
		url:baseUrl + "track/assigntask",
		data:{servicetask_id:servicetask_id,task_id:task_id,case_id:case_id,team_id:team_id,team_loc:team_loc,taskunit_id:taskunit_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
            	hideLoader();
	    	var obj = jQuery.parseJSON(mydata);
	    	if (obj.error!=""){
	    	   alert(obj.error);
	        }else{
				btn=[{
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
	    		                $( this ).dialog( "close" );
	    		            }
	    		        }];
			   if(access=='all'){
				btn=[
	    		        {
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
	    		                $( this ).dialog( "close" );
	    		            }
	    		        },
	    		        {
							text: "UnAssign",
	    		            "title":"UnAssign",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								UnassignTask(servicetask_id,task_id,team_loc,taskunit_id);
	    		            }
						},
	    		        {
	    		            text: "Update",
	    		            "title":"Update",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
	    		            	if($('#add-assign-transition li.active').length){
	    		            		user_id=$('#add-assign-transition li.active').data('id');
	    		            		service_ids=$("#services").val();
	    		            		taskunit_ids=$('#taskunits').val();

		    		            	$.ajax({
		    		        			url:baseUrl + "track/assigntask&servicetask_id="+servicetask_id+"&task_id="+task_id+"&case_id="+case_id+"&team_id="+team_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id,
		    		        			data:{user_id:user_id,service_ids:service_ids,taskunit_ids:taskunit_ids},
		    		        		    type:"post",
		    		        		    beforeSend:function(){
		    		        				showLoader();
		    		        		    },
		    		        		    success:function(mydata){
                                                              	hideLoader();
		    		        		    	 if(mydata=='OK'){
		    		        		    		$( "#add-assign-transition" ).dialog('close');
		    		        		    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
											   //location.reload();
		    		        		       }else{
		    		        		    	   alert(mydata);
		    		        		       }
		    		        			}
		    		        		});
	    		            	}else{
	    		            		alert('Please select a User to perform this action.');
	    		            	}

	    		            }
	    		        }
	    		    ];
			   }else if(access=='assign'){
					btn=[
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
	    		            	if($('#add-assign-transition li.active').length){
	    		            		user_id=$('#add-assign-transition li.active').data('id');
	    		            		service_ids=$("#services").val();
	    		            		taskunit_ids=$('#taskunits').val();

		    		            	$.ajax({
		    		        			url:baseUrl + "track/assigntask&servicetask_id="+servicetask_id+"&task_id="+task_id+"&case_id="+case_id+"&team_id="+team_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id,
		    		        			data:{user_id:user_id,service_ids:service_ids,taskunit_ids:taskunit_ids},
		    		        		    type:"post",
		    		        		    beforeSend:function(){
		    		        				showLoader();
		    		        		    },
		    		        		    success:function(mydata){
                                                              	hideLoader();
		    		        		    	 if(mydata=='OK'){
		    		        		    		$( "#add-assign-transition" ).dialog('close');
		    		        		    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
											   //location.reload();
		    		        		       }else{
		    		        		    	   alert(mydata);
		    		        		       }
		    		        			}
		    		        		});
	    		            	}else{
	    		            		alert('Please select a User to perform this action.');
	    		            	}

	    		            }
	    		        }
	    		    ];
			   }
			   else if(access=='unassign'){
					btn=[
	    		        {
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
	    		                $( this ).dialog( "close" );
	    		            }
	    		        },
	    		        {
							text: "UnAssign",
	    		            "title":"UnAssign",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								UnassignTask(servicetask_id,task_id,team_loc,taskunit_id);
	    		            }
						}
	    		    ];
			   }
	    	   if(!$( "#add-assign-transition" ).length){
	    			$('body').append("<div id='add-assign-transition'></div>");
	    		}
	    	   	$( "#add-assign-transition" ).html(obj.data);
	    		$( "#add-assign-transition" ).dialog({
	    			  //title:"Transition Task",
	    			  title:"Update Task Assignment",
	    		      autoOpen: true,
	    			  resizable: false,
	    		      width: "50em",
	    		      height:456,
	    		      modal: true,
	    			  buttons: btn,
					  close: function() {
					  	$(this).dialog('destroy').remove();
					  },
					  open: function() {
					  	$('.search-result').css('height', parseInt($('#add-assign-transition').height())-(parseInt($('.search-box').height())+20));
					  }
				});
	       }
		}
	});
}
/*Transfer Task*/
function TransferTask(task_id,case_id,team_id,team_loc,servicetask_id,taskunit_id){
	$.ajax({
		url:baseUrl + "track/transfertask",
		data:{servicetask_id:servicetask_id,task_id:task_id,case_id:case_id,team_id:team_id,team_loc:team_loc,taskunit_id:taskunit_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	var obj = jQuery.parseJSON(mydata);
	    	if (obj.error!=""){
	    	   alert(obj.error);
	        }else{
	    	   if(!$( "#add-transfer-task" ).length){
	    			$('body').append("<div id='add-transfer-task'></div>");
	    		}
	    	   	$( "#add-transfer-task" ).html(obj.data);
	    		$( "#add-transfer-task" ).dialog({
	    			  title:"Transfer Task Location",
	    		      autoOpen: true,
	    			  resizable: false,
	    		      width: "50em",
	    		      height:302,
	    		      modal: true,
	    		      beforeClose: function(event){
						  if(event.keyCode==27) trigger = 'esc';
						  if(trigger!='Update') checkformstatus(event);
					  },
	    			  buttons: [
	    		        {
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								trigger = 'Cancel';
	    		                $( this ).dialog( "close" );
	    		            }
	    		        },
	    		        {
	    		            text: "Update",
	    		            "title":"Update",
	    		            "class": 'btn btn-primary',
	    		            click: function() {

    		            		loc=$('#location').val();
    		            		if(loc!=""){
    		            			trigger = 'Update';
	    		            	$.ajax({
	    		        			url:baseUrl + "track/transfertask&servicetask_id="+servicetask_id+"&task_id="+task_id+"&case_id="+case_id+"&team_id="+team_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id,
	    		        			data:{loc:loc},
	    		        		    type:"post",
	    		        		    beforeSend:function(){
	    		        				showLoader();
	    		        		    },
	    		        		    success:function(mydata){
	    		        		    	hideLoader();
	    		        		    	if(mydata=='OK'){
											$( "#add-transfer-task" ).dialog('close');
	    		        		    		$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
											//location.reload();
	    		        		       }else{
	    		        		    	   alert(mydata);
	    		        		       }
	    		        			}
	    		        		});
	    		            	}else{
	    		            		$('.help-block').append('Location cannot be blank.');
						if(!$('#location').parent().parent().hasClass('has-error')){
						    $('#location').parent().parent().addClass('has-error');
						}
	    		            	}

	    		            }
	    		        }
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		        // Close code here (incidentally, same as Cancel code)
	    		    }
	    		    });
	       }
		}
	});
}
/*change status of selected service task*/
function onlyUnique(value, index, self) {
	    return self.indexOf(value) === index;
	}
function ChangeStatusAll(task_id,case_id,team_id,team_loc){
	 var sel_row = jQuery('.grid-view').yiiGridView('getSelectedRows');
	 if(!sel_row.length){
 		alert('Please select at least 1 record to perform this action.');
 	}else{
 		var team_idss= new Array();
 		$('input:checked').each(function(){
 			if(!$(this).hasClass('select-on-check-all')){
 			id = $(this).data('team_id');
 			console.log(id);
 			team_idss.push(id);
 			}
 		});
 		if(team_idss.length > 0){
 			var team_idss = team_idss.filter( onlyUnique );
 			if(team_idss.length > 1){
 				alert('The selected Tasks cannot be Bulk Change Status because the selected Tasks are from different Teams.');
 				return false;
 			}
 		}
 		var params=getUrlVars();
 		$.ajax({
			url:baseUrl + "track/change-multiple-task-status",
			data:{ids:sel_row,task_id:task_id,case_id:case_id,team_id:team_id,team_loc:team_loc,params:params},
		    type:"get",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){
		    	hideLoader();
		    	var obj = jQuery.parseJSON(mydata);
		    	if (obj.error!=""){
		    	   alert(obj.error);
		        }else{
		    	   if(!$( "#change-statuses-details" ).length){
		    			$('body').append("<div id='change-statuses-details'></div>");
		    		}
		    	   	$( "#change-statuses-details" ).html(obj.data);
		    		$( "#change-statuses-details" ).dialog({
		    			  title:"Change Task(s) Statuses",
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
		    		                $( this ).dialog( "close" );
		    		            }
		    		        },
		    		        {
		    		            text: "Update",
		    		            "title":"Update",
		    		            "class": 'btn btn-primary',
		    		            click: function() {
								task_status   = $("#multi_status").val();
								var status_arr={0:" Not Started",1:"Started",2:"On Pause",3:"On Hold",4:"Completed"};
								if(task_status !=""){
								if (confirm('Are you sure you want to change the selected Tasks Statuses to '+status_arr[task_status]+' ?')) {

		    		            		services = $('#services').val();
		    		            		taskunits = $('#taskunits').val();
		    		            		$.ajax({
		    		            			url:baseUrl + "track/change-multiple-task-status&ids="+sel_row+"&task_id="+task_id+"&case_id="+case_id+"&team_id="+team_id+"&team_loc="+team_loc,
		    		            			data:{status:task_status,services:services,taskunits:taskunits,params:params},
		    		            			type:"post",
			    		        		    beforeSend:function(){
			    		        				showLoader();
			    		        		    },
			    		        		    success:function(mydata){
			    		        		       hideLoader();
			    		        		       if(mydata=='OK'){
			    		        		    	   $( "#change-statuses-details" ).dialog('close');
												$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
												setTimeout(function(){
													$( "#filtertrackproject" ).select2({
														theme: "krajee"
													});
													$('input').customInput();
												 }, 200);
											//	location.reload();
			    		        		       }else{
			    		        		    	   alert(mydata);
			    		        		       }
			    		        			}
		    		            		});
		    		            	}
									}
									else{
		    		            		alert('Please select a Status to perform this action.');
										}
		    		            }
		    		        }
		    		    ],
		    		    close: function() {
		    		    	$(this).dialog('destroy').remove();
		    		        // Close code here (incidentally, same as Cancel code)
		    		    }
		    		});
		       }
			}
		});
 	}
}

/**
 * IRT 159
 * changes done
 * Data Statistic Attachment
 **/
function EditDataItemAttachment(id)
{
	if(!$( "#edit-data-field-attachment" ).length){
		$('body').append("<div id='edit-data-field-attachment'></div>");
	}
   	$( "#edit-data-field-attachment" ).dialog({
		  title:"Edit Data Field Attachment",
	      autoOpen: false,
		  resizable: false,
	      width: "50em",
	      modal: true,
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
	                $( this ).dialog( "close" );
	            }
	        },
	        {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
	            	trigger = 'Update';
	            	$("#TasksUnitsDataAttachment").attr('action', baseUrl+"track/editdatafieldattachment&id="+id);
					$("#TasksUnitsDataAttachment").submit();
	            }
	        }
	    ],
	    close: function() {
	    	$(this).dialog('destroy').remove();
	    }
	});

   	/* Ajax Edit Data Field Attachment */
	$.ajax({
		url:baseUrl + "track/editdatafieldattachment&id="+id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	       $('#edit-data-field-attachment').html(null).html(mydata);hideLoader();$( "#edit-data-field-attachment" ).dialog("open");
		},complete: function () {
			$('#TasksUnitsDataAttachment').ajaxForm({
		        success: SubmitSuccesfulAttachment,
		    });
		}
	});
}

/** IRT 159 **/
function SubmitSuccesfulAttachment(responseText, statusText)
{
	hideLoader();
	if(responseText=='OK'){
		 $('#edit-data-field-attachment').dialog('close');
		 $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
	}else{
		 $('#edit-data-field-attachment').html(null).html(responseText);
	}
}


/*Add Billing Data Service task track project*/
function AddBilling(task_id,case_id,teamId,team_loc,servicetask_id,taskunit_id){
	$.ajax({
		url:baseUrl + "track/addbillableitems",
		data:{task_id:task_id,case_id:case_id,teamId:teamId,team_loc:team_loc,servicetask_id:servicetask_id,taskunit_id:taskunit_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	var obj = jQuery.parseJSON(mydata);
	    	if (obj.error!=""){
	    	   alert(obj.error);
	        }else{
	        	if(!$( "#add-billing-item" ).length){
		    		$('body').append("<div id='add-billing-item'></div>");
	    		}
	    	   	$( "#add-billing-item" ).html(obj.data);
	    		$( "#add-billing-item" ).dialog({
	    			  title:"Enter Task Details",
	    		      autoOpen: true,
	    			  resizable: false,
	    		      width: "80em",
	    		      height:692,
	    		      modal: true,
					  create: function(event, ui) {
							$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
							$('.ui-dialog-titlebar-close').attr("title", "Close");
							$('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					  },
	    		      beforeClose: function(event){
                                    if(event.keyCode == 27) trigger = 'esc';
									if(trigger != 'Update') checkformstatus(event);

                            },
	    			  buttons: [
	    		        {
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								trigger = 'Cancel';
	    		                $( this ).dialog( "close" );
	    		            }
	    		        },
	    		        {
	    		            text: "Update",
	    		            "title":"Update",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
                                        trigger = 'Update';
	    		            	if(!validateBillingFormBuilder()) {

									$("#frm_billing_data_div").attr('action', baseUrl + "track/addbillableitems&task_id="+task_id+"&case_id="+case_id+"&teamId="+teamId+"&team_loc="+team_loc+"&servicetask_id="+servicetask_id+"&taskunit_id="+taskunit_id);
									$("#frm_billing_data_div").submit();

									/*$.ajax({
	    		            			url:baseUrl + "track/addbillableitems&task_id="+task_id+"&case_id="+case_id+"&teamId="+teamId+"&team_loc="+team_loc+"&servicetask_id="+servicetask_id+"&taskunit_id="+taskunit_id,
	    		            			// data:$('#billing_data_div :input').serialize(),
	    		            			data:$('#frm_billing_data_div').serialize(),
	    		            			type:"post",
		    		        		    beforeSend:function() {
		    		        				showLoader();
		    		        		    },
		    		        		    success:function(mydata) {
		    		        		    	hideLoader();
		    		        		    	if(mydata=='OK') {
		    		        		    	    $( "#add-billing-item").dialog('close');
		    		        		    	    setTimeout(function() {
		    		        		    		   $('input').customInput();
		    		        		    		   $('.media-tr input[type="checkbox"]').each(function(){
														$(this).parent().remove();
		    		        		    		   });
												},200);
		    		        		    	   	$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
											} else {
		    		        		    	   alert(mydata);
		    		        		        }
		    		        			}
	    		            		});*/
	    		            	}
	    		            }
	    		        }
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		        // Close code here (incidentally, same as Cancel code)
	    		    }
	    		});
	        }
	    },complete:function() {
			$('input').customInput();
	    	$('#frm_billing_data_div').ajaxForm({
	    		beforeSubmit : function(arr, $form, options){
					showLoader();
				},
		        success: SubmitSuccesfulDataStatisticsForm,
		    });
	    }
	});
}

/* Successful Instruction Notes FormSet */
function SubmitSuccesfulDataStatisticsForm()
{
	hideLoader();
	$( "#add-billing-item").dialog('close');
    setTimeout(function() {
	   $('input').customInput();
	   $('.media-tr input[type="checkbox"]').each(function(){
			$(this).parent().remove();
	   });
	},200);
   	$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
}

/*Delete Billing*/
function DeleteBilling(billing_id,billing_name){
	 if (confirm('Are you sure you want to Delete '+billing_name+'?'))
     {

		 $.ajax({
		        type: "POST",
		        url: httpPath + "track/deletebillable&id=" + billing_id,
		        data: {'YII_CSRF_TOKEN': $("#token").val()},
		        cache: false,
		        beforeSend:function(){
    				showLoader();
    		    },
    		    success:function(mydata){
    		    	hideLoader();
		        	if(mydata=='OK'){
     		    	   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
		    //location.reload();
     		        }else{
     		    	   alert(mydata);
     		        }
		        }
		    });
     }
}
/*Delete Task Unit Data*/
function DeleteDataItem(data_id,data_lable){
	if (confirm('Are you sure you want to Delete '+data_lable+'?')) {
		 $.ajax({
		        type: "POST",
		        url: httpPath + "track/deletedataitem&id=" + data_id,
		        data: {'YII_CSRF_TOKEN': $("#token").val()},
		        cache: false,
		        beforeSend:function(){
    				showLoader();
    		    },
    		    success:function(mydata){
    		    	hideLoader();
		        	if(mydata=='OK'){
		        		$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
			//location.reload();
	  		        }else{
	  		    	   alert(mydata);
	  		        }
		        }
		    });
	}
}
/*Edit Billing Item*/
function EditBilling(billing_id){
	if(!$( "#edit-billing-item" ).length){
		$('body').append("<div id='edit-billing-item'></div>");
	}
   	$( "#edit-billing-item" ).dialog({
		  title:"Edit Billing Item",
	      autoOpen: false,
		  resizable: false,
	      width: "50em",
	      modal: true,
	      beforeClose: function(event){
			  if(event.keyCode == 27) trigger = 'esc';
			  if(trigger!='Update') checkformstatus(event);
		  },
		  buttons: [
	        {
	            text: "Cancel",
	            "title":"Cancel",
	            "class": 'btn btn-primary',
	            click: function() {
					trigger = 'Cancel';
	                $( this ).dialog( "close" );
	            }
	        },
	        {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
					trigger = 'Update';
	            	$.ajax({
	            		url:baseUrl + "track/editbilling&id="+billing_id,
	            	    type:"post",
	            	    data:$('#TasksUnitsBilling').serialize(),
	            	    beforeSend:function(){
	            			showLoader();
	            	    },
	            	    success:function(mydata){
	            	    	hideLoader();
	            	    	if(mydata=='OK'){
	            	    		$('#edit-billing-item').dialog('close');
	    		        		$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
					//location.reload();
	    	  		        }else{
	    	  		        	$('#edit-billing-item').html(null).html(mydata);
	    	  		        }
	            		}
	            		});
	            }
	        }
	    ],
	    close: function() {
	    	$(this).dialog('destroy').remove();
	        // Close code here (incidentally, same as Cancel code)
	    }
	});
	$.ajax({
		url:baseUrl + "track/editbilling&id="+billing_id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	       $('#edit-billing-item').html(null).html(mydata);hideLoader();$( "#edit-billing-item" ).dialog("open");
		}
		});
}
/*Edit Unit  Data*/
function EditDataItem(id){
	if(!$( "#edit-data-field" ).length){
		$('body').append("<div id='edit-data-field'></div>");
	}
   	$( "#edit-data-field" ).dialog({
		  title:"Edit Data Field",
	      autoOpen: false,
		  resizable: false,
	      width: "50em",
	      modal: true,
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
	                $( this ).dialog( "close" );
	            }
	        },
	        {
	            text: "Update",
	            "title":"Update",
	            "class": 'btn btn-primary',
	            click: function() {
	            	if(!validateDataFiled()){
						trigger = 'Update';
	            		$.ajax({
	            		url:baseUrl + "track/editdatafield&id="+id,
	            	    type:"post",
	            	    data:$('#TasksUnitsData').serialize(),
	            	    beforeSend:function(){
	            			showLoader();
	            	    },
	            	    success:function(mydata){
	            	    	hideLoader();
	            	    	if(mydata=='OK'){
	            	    		$('#edit-data-field').dialog('close');
	    		        		$.pjax.reload('#trackproject-pajax', $.pjax.defaults);
					//location.reload();
	    	  		        }else{
	    	  		        	$('#edit-data-field').html(null).html(mydata);
	    	  		        }
	            		}
	            		});
	            	}
	            }
	        }
	    ],
	    close: function() {
	    	$(this).dialog('destroy').remove();
	        // Close code here (incidentally, same as Cancel code)
	    }
	});
	$.ajax({
		url:baseUrl + "track/editdatafield&id="+id,
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	       $('#edit-data-field').html(null).html(mydata);hideLoader();$( "#edit-data-field" ).dialog("open");
		}
		});
}
/*Validate Data Filed*/
function validateDataFiled(){
	var has_error=false;
	$(document).find('.required-entry').filter(':input').each(function(){
		if ($(this).is("input:checkbox") || $(this).is("input:radio")){
			var name = $(this).attr('name');
			var $myLabel = $('#datafield_label').text();
			if(!$('input[name="'+name+'"]:checked').length){
				if(!$(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().addClass('has-error');
					$(this).parent().append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
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
				var $myLabel = $('#datafield_label').text();
				if(!$(this).parent().parent().hasClass('has-error')){
				    $(this).parent().parent().addClass('has-error');
				    $(this).parent().append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				has_error=true;
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
				var $myLabel = $('#datafield_label').text();
				if(!$(this).parent().parent().hasClass('has-error')){
				    $(this).parent().parent().addClass('has-error');
				    $(this).parent().append("<div class='help-block'>"+$myLabel.replace('*','').replace(':','')+" cannot be blank.</div>");
				}
				has_error=true;
			}else{
				if($(this).parent().parent().hasClass('has-error')){
					$(this).parent().parent().removeClass('has-error');
					$(this).parent().find('.help-block').html(null);
				}
			}
		}
	});
	return has_error;
}
/* Task Unit Assign */
function AssignTask(servicetask_id,task_id,case_id,team_id,team_loc,taskunit_id){
	 	$.ajax({
			url:baseUrl + "track/assigntask",
			data:{servicetask_id:servicetask_id,task_id:task_id,case_id:case_id,team_id:team_id,team_loc:team_loc,taskunit_id:taskunit_id},
		    type:"get",
		    beforeSend:function(){
				showLoader();
		    },
		    success:function(mydata){ // success
		    	hideLoader();
		    	var obj = jQuery.parseJSON(mydata);
		    	if (obj.error!=""){
		    	   alert(obj.error);
		        }else{
		    	   if(!$( "#add-assign-transition" ).length){
		    			$('body').append("<div id='add-assign-transition'></div>");
		    		}
		    	   	$( "#add-assign-transition" ).html(obj.data);
		    		$( "#add-assign-transition" ).dialog({
		    			  title:"Assign Task",
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
		    		            	if($('#add-assign-transition li.active').length){
		    		            		user_id=$('#add-assign-transition li.active').data('id');
		    		            		service_ids=$("#services").val();
		    		            		taskunit_ids=$('#taskunits').val();
			    		            	$.ajax({
			    		        			url:baseUrl + "track/assigntask&servicetask_id="+servicetask_id+"&task_id="+task_id+"&case_id="+case_id+"&team_id="+team_id+"&team_loc="+team_loc+"&taskunit_id="+taskunit_id,
			    		        			data:{user_id:user_id,service_ids:service_ids,taskunit_ids:taskunit_ids},
			    		        		    type:"post",
			    		        		    beforeSend:function(){
			    		        				showLoader();
			    		        		    },
			    		        		    success:function(mydata){
			    		        		    	hideLoader();
			    		        		    	if(mydata=='OK'){
			    		        		    	   $( "#add-assign-transition" ).dialog('close');
												   $.pjax.reload('#trackproject-pajax', $.pjax.defaults);
													//location.reload();
												   }else{
													   alert(mydata);
												   }
			    		        			}
			    		        		});
		    		            	}else{
		    		            		alert('Please select a User to perform this action.');
		    		            	}

		    		            }
		    		        }
		    		    ],
		    		    close: function() {
		    		    	$(this).dialog('destroy').remove();
		    		        // Close code here (incidentally, same as Cancel code)
		    		    },
				    open: function() {
		    		    	$('.search-result').css('height', parseInt($('#add-assign-transition').height())-(parseInt($('.search-box').height())+20));
		    		    }
		    		    });

		       }
			}
		});
}

//on window resize run function
$(window).resize(function () {
    fluidDialog();
});

// catch dialog if opened within a viewport smaller than the dialog width
$(document).on("dialogopen", ".ui-dialog", function (event, ui) {
    fluidDialog();
});

function fluidDialog() {
    var $visible = $(".ui-dialog:visible");
    // each open dialog
    $visible.each(function () {
        var $this = $(this);
        var dialog = $this.find(".ui-dialog-content").data("ui-dialog");
        // if fluid option == true
        if (dialog.options.fluid) {
        	dialog.option("max-height","100%");
            var wWidth = $(window).width();
            // check window width against dialog width
            if (wWidth < (parseInt(dialog.options.maxWidth) + 50))  {
                // keep dialog from filling entire screen
                $this.css("max-width", "90%");
            } else {
                // fix maxWidth bug
                $this.css("max-width", dialog.options.maxWidth + "px");
            }
            //reposition dialog
            dialog.option("position", dialog.options.position);
        }
    });

}

/*Edit Comment*/
function EditComment(id,task_id,case_team_id,type){
	var Url=baseUrl + "case-projects/edit-comment"
	if(type=='team'){
		Url=baseUrl + "team-projects/edit-comment"
	}
	$.ajax({
		url:Url,
		data:{id:id,task_id:task_id,case_team_id:case_team_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	   if(!$( "#edit-comment" ).length){
	    			$('body').append("<div id='edit-comment'></div>");
	    		}
	    	   	$( "#edit-comment" ).html(mydata);
	    		$( "#edit-comment" ).dialog({
	    			  title:"Edit Comment",
	    		      autoOpen: true,
	    		      width: "60em",
	    		      height: "auto",
	    		      modal: true,
	    		      fluid: true,
	    		      resizable: false,
	    			  buttons: [
						{
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								trigger = 'Cancel';
	    		                $( this ).dialog( "close" );
	    		            }
	    		        },
	    		        {
						    text: "Update",
						    "title":"Update",
						    "class": 'btn btn-primary',
						    click: function() {
								trigger = 'Update';
						    	comment = $("#edit_comment_"+id).val();
						    	comment = strip_tags(comment.replace(/&nbsp;/g, ''));
						    	if(comment!=""){
						    		$("#edit-comments-form-"+id).submit();
						    	}else{
						    		alert('Comment cannot be blank.');
						    	}
						    }
						}

	    		    ],
	    		    beforeClose: function(event){
						if(event.keyCode==27) trigger='esc';
						if(trigger!='Update') checkformstatus(event);
					},
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		        // Close code here (incidentally, same as Cancel code)
	    		    }
	    		});

		},complete:function(){
			$("#edit-comments-form-"+id).ajaxForm({
			   	beforeSubmit: function() {
					showLoader();
			    },
			   	success: function(responseText, statusText) {
					$('div#comments').html(responseText);
			   		hideLoader();
			   		$("#edit-comments-form-"+id+" #is_change_form").val('0');
					$("#edit-comments-form-"+id+" #is_change_form_main").val('0');
					$("#edit-comment").dialog('destroy').remove();
			   		//window.location.reload();
				},
			  });
		}
	});
}
/*Edit Comment*/
/*Reply Comment*/
function ReplyComment(id,task_id,case_team_id,type){
	var Url=baseUrl + "case-projects/reply-comment"
	if(type=='team'){
		Url=baseUrl + "team-projects/reply-comment"
	}
	$.ajax({
		url:Url,
		data:{id:id,task_id:task_id,case_team_id:case_team_id},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	   if(!$( "#add-comment" ).length){
	    			$('body').append("<div id='add-comment'></div>");
	    		}
	    	   	$( "#add-comment" ).html(mydata);
	    		$( "#add-comment" ).dialog({
	    			  title:"Add Comment Reply",
	    		      autoOpen: true,
	    			  resizable: false,
	    		      width: "60em",
	    		      modal: true,
	    			  buttons: [
						{
	    		            text: "Cancel",
	    		            "title":"Cancel",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								trigger = 'Cancel';
	    		                $( this ).dialog( "close" );
	    		            }
	    		        },
	    		        {
						    text: "Update",
						    "title":"Update",
						    "class": 'btn btn-primary',
						    click: function() {
								trigger = 'Update';
						    	comment = $("#add_comment_"+id).val();
								if(comment!=""){
						    		$("#add-comments-form-"+id).submit();
						    	}else{
						    		alert('Comment cannot be blank.');
						    	}
						    }
						}
	    		    ],
	    		    beforeClose: function(event){
						if(event.keyCode==27) trigger='esc';
						if(trigger!='Update') checkformstatus(event);
					},
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		        // Close code here (incidentally, same as Cancel code)
	    		    }
	    		    });

		},complete:function(){
			$("#add-comments-form-"+id).ajaxForm({
			   	beforeSubmit: function() {
					showLoader();
			    },
			   	success: function(responseText, statusText) {
					$('div#comments').html(responseText);
			   		hideLoader();
			   		$("#add-comments-form-"+id+" #is_change_form").val('0');
					$("#add-comments-form-"+id+" #is_change_form_main").val('0');
					$("#add-comment").dialog('destroy').remove();

			   		//window.location.reload();
				},
			  });
		}
	});
}
/*Reply Comment*/
function escapeHtml(text) {
  var map = {
     '&amp;':'&',
     '&lt;':'<',
    '&gt;': '>',
    '&quot;': '"',
    '&#039;': "'"
  };

  return text.replace(/[&amp;&lt&gt;&quot;&#039;]/g, function(m) { return map[m]; });
}
/*Delete Comment*/
function DeleteComment(id,msg,type,comment){
	var Url=baseUrl + "case-projects/delete-comment"
	if(type=='team'){
		Url=baseUrl + "team-projects/delete-comment"
	}
	if(comment==undefined){
		comment = 'this Comment';
	}else{
		var comment = $("<div/>").html(comment).text();
	}
	var msg_conf="Are you sure you want to Delete "+comment+"?";
	if(msg=="parent"){
		msg_conf="Are you sure you want to Delete "+comment+"?";
	}
	if(confirm(msg_conf)){
		var url     = Url;
		$.ajax({
            type: "GET",
            url: url,
            data: {id:id,msg:msg},
            dataType: 'html',
            cache: false,
            beforeSend:function (data) {showLoader();},
            success: function (data) {
            	hideLoader();
                if (data == "NA") {
                	alert('The Comment cannot be Deleted because it has been replied to.');
                }else{
                	window.location.reload();
                }
            }
        });
	}
}
/*Delete Comment*/
/* */

/* To Get the service Location For Project By Team service */
            function filterbyserviceLocations(filterserv,status,teamlocs){
            if ($('#chkprocessteamloc').is(':checked')){
	        	$("#displayteamloc ul.by_teamloc_sub").html('<center>Loading...<br/></center>');
	            $('#displayteamloc').show();
	        	var start_date = $('#start_date').val();
	    		var end_date = $('#end_date').val();
	    		var datedropdown = $('.SelectDataprocessDropDown').val();
	    		var teamlocs = teamlocs;
	    		var error="";
	    		var value = "";
	    		if(filterserv != ""){
	    			value = filterserv;
	        	}else{
	            	$('#projteamservUl li .processclientteams').each(function(){
	                	if($(this).is(":checked")){
	                		if(value!=""){
	                			value += "," + $(this).val();
	                    	} else {
	                    		value = $(this).val();
	                    		//$('#displayteamloc ul.by_teamloc_sub').html("<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>");
	                        }
	                    }
	                });
	        	}

	        	var projectstatus = "";
	        	if(status != ""){
	        		projectstatus = status;
	        	}else{
	            	$('#projstatusul li .tostatus').each(function(){
	                	if($(this).is(":checked")){
	                		if(projectstatus!=""){
	                			projectstatus += "," + $(this).val();
	                    	} else {
	                    		projectstatus = $(this).val();
	                        }
	                    }
	                });
	        	}
	        	if((start_date!="" && end_date!="") || datedropdown != 0) {
		        	$.ajax({
		                type: "POST",
		                url: baseUrl+"status-report/getteamlocbyservicetaskcriteria",
		                data: {'filterservice':value,'projectstatus':projectstatus,'filterLoc':teamlocs, start_date:start_date, end_date:end_date, datedropdown:datedropdown},
		                dataType:'json',
		                cache: false,
		                success:function(data){
		                	$('#displayteamloc ul.by_teamloc_sub').html('');
		                    if(data.TeamLoc == ""){
		                    	data.TeamLoc="<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>";
		                    }
		                	$('#displayteamloc ul.by_teamloc_sub').html(data.TeamLoc);
		                },complete:function(){
							$('input').customInput();
						}
		            });
	    		} else {
	    			if(start_date==''){
					   $('#start_date_error').html('Select project submit start date or date range is required field ');
					   $('#start_date_error').parent().addClass('has-error');
  			           error+="- Select project submit start date or date range is required field. <br>";
	  			    }
	  			    if(end_date==''){
					  $('#end_date_error').html('Select project submit end date or date range is required field. ');
					  $('#end_date_error').parent().addClass('has-error');
	  			      error+="- Select project submit end date or date range is required field. <br>";
	  			    }
	  			    if(error!=""){
		                $('#errorContent').html(error);
		                $("#projectstatuss").attr("checked",false);
		                $('#displayteamloc').hide();
		  			    $("input[name='chkprocessteamlocs']").prop('checked', false);
			            $(".teamlocall").prop('checked', false);
		            }
	  				//$('#displayteamloc ul.by_teamloc_sub').html("<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>");
		    	}
	        } else {
	            $('#displayteamloc').hide();
	            $("input[name='teamlocs[]']").prop('checked', false);
	            $(".teamlocall").prop('checked', false);
	        }
	    }
/* End code */

/* For Get Client and Clientcase */
    function filterRevenueData(filter_data,criteria,appendul){

		$("#"+appendul).html('<center>Loading...<br/></center>');
		$.ajax({
			type: "POST",
			url: baseUrl+"status-report/getrevenuecriteria",
			data: {'YII_CSRF_TOKEN':$("#token").val(),'criteria':criteria,'filter_data':filter_data},
			dataType:'html',
			cache: false,
			success:function(data){
				$("#"+appendul).html('');
				if(data != ""){
					$("#"+appendul).append(data);
				} else {
					$("#"+appendul).append("<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>");
				}
			},complete:function(){
				$('input').customInput();
				$('#selectall').siblings().removeClass('checked');
				$('#checkcases_all').siblings().removeClass('checked');
			}
		});
	}
	/* End */
/* Start : code to set dynamic height for grid as per screen resolution */
$(window).resize(function() {
    if($('.table-responsive').length>0 && $('.kv-panel-pager').length>0)
    {
        var grid_height = $('.table-responsive').height()-$('.kv-panel-pager').height()-5;
        $('.kv-grid-container').height(grid_height);
    }
});
$('.numeric-field-qu').on("keydown", function (e) {
		//alert('here');
		var keylist = [46, 8, 9, 27, 13, 116];
		var isint = false;
		if($(this).hasClass('integer')){
			var isint = true;
		}

		var isnegative = false;
		if($(this).hasClass('negative-button')){
			var isnegative = true;
		}

		var dotcount = ($(this).val().match(/\./g) || []).length;
		var negcount = ($(this).val().match(/\-/g) || []).length;
		alert(dotcount);
		if ($.inArray(e.keyCode, keylist) !== -1 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 40) || (dotcount==0 && (e.keyCode == 110 || e.keyCode == 190) && !isint) || (negcount==0 && (e.keyCode == 109 || e.keyCode == 173 || e.keyCode == 189) && isnegative)){
			return;
		}
		if ((e.keyCode == 190 || e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) || (dotcount>1 && (e.keyCode == 110 || e.keyCode == 190)) || (negcount>1 && (e.keyCode == 109 || e.keyCode == 173)))
		{
			e.preventDefault();
		}
	});
/* End : code to set dynamic height for grid as per screen resolution */


function strip_tags (input, allowed) { // eslint-disable-line camelcase
	  //  discuss at: http://locutus.io/php/strip_tags/
	  // original by: Kevin van Zonneveld (http://kvz.io)
	  // improved by: Luke Godfrey
	  // improved by: Kevin van Zonneveld (http://kvz.io)
	  //    input by: Pul
	  //    input by: Alex
	  //    input by: Marc Palau
	  //    input by: Brett Zamir (http://brett-zamir.me)
	  //    input by: Bobby Drake
	  //    input by: Evertjan Garretsen
	  // bugfixed by: Kevin van Zonneveld (http://kvz.io)
	  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
	  // bugfixed by: Kevin van Zonneveld (http://kvz.io)
	  // bugfixed by: Kevin van Zonneveld (http://kvz.io)
	  // bugfixed by: Eric Nagel
	  // bugfixed by: Kevin van Zonneveld (http://kvz.io)
	  // bugfixed by: Tomasz Wesolowski
	  //  revised by: Rafał Kukawski (http://blog.kukawski.pl)
	  //   example 1: strip_tags('<p>Kevin</p> <br /><strong>van</strong> <em>Zonneveld</i>', '<em><strong>')
	  //   returns 1: 'Kevin <strong>van</strong> <em>Zonneveld</em>'
	  //   example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <em>Zonneveld</em></p>', '<p>')
	  //   returns 2: '<p>Kevin van Zonneveld</p>'
	  //   example 3: strip_tags("<a href='http://kvz.io'>Kevin van Zonneveld</a>", "<a>")
	  //   returns 3: "<a href='http://kvz.io'>Kevin van Zonneveld</a>"
	  //   example 4: strip_tags('1 < 5 5 > 1')
	  //   returns 4: '1 < 5 5 > 1'
	  //   example 5: strip_tags('1 <br/> 1')
	  //   returns 5: '1  1'
	  //   example 6: strip_tags('1 <br/> 1', '<br>')
	  //   returns 6: '1 <br/> 1'
	  //   example 7: strip_tags('1 <br/> 1', '<br><br/>')
	  //   returns 7: '1 <br/> 1'

	  // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	  allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('')

	  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi
	  var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi

	  return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
	    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
	  })
}
/* Form Builder Dropdown,checkbox and radio Validation*/
function form_validation(){
	var drop_flag = true;
	 $('#form_builder_panel select').each(function () {
                var dropdown_length = $(this).find('option').length;
                if(dropdown_length == 1){
                    drop_flag = false;
                    return false;
                }
	   });
	   if(drop_flag == false){
		alert('At least one option needs to be added to Dropdown.');
		return false;
	   }
	   var total_count = $('#form_builder_panel .form-builder-ol li input:radio').length;
	   var total_option_count = 0;

	   $('#form_builder_panel .form-builder-ol li input:radio').each(function () {
		   var radio_html = $(this).next().html();
		   if($.trim(radio_html) != ''){
			total_option_count++;
		   }
	   });
	   var total_option_count_chk = 0;
	   var total_chk_count = $('#form_builder_panel .form-builder-ol li input:checkbox').length;
	    $('#form_builder_panel .form-builder-ol li input:checkbox').each(function () {
		   var checkbox_html = $(this).next().html();
		   if($.trim(checkbox_html) != ''){
			total_option_count_chk++;
		   }
	    });
	   if(total_count != total_option_count){
		alert('At least one option needs to be added to Radio.');
		return false;
	   }
	   if(total_chk_count != total_option_count_chk){
		alert('At least one option needs to be added to Checkbox.');
		return false;
	   }
	   return true;
}
/*
 * For Task Name Popup on Track
 */
 function TaskInstructPopup(task_id,teamId,team_loc,servicetask_id,sort_order,taskunit_id,case_id,team_id){
	 $.ajax({
			type: "POST",
			url: baseUrl+"track/gettaskpopupdata",
			data: {'task_id':task_id,'teamId':teamId,'team_loc':team_loc,'servicetask_id':servicetask_id,'sort_order':sort_order,'taskunit_id':taskunit_id,'case_id':case_id,'team_id':team_id},
			success: function (data) {
				  $('#task_instruction_popup').html(data);
				  $('#task_instruction_popup').dialog({
					title: 'Task Instructions',
					autoOpen: true,
					resizable: false,
					width: "50em",
					height:302,
					modal: true,
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
								$('#task_instruction_popup').dialog("close");
							}
						},
					],
				});
			}
		});
 }

/**
 * Checkformstatus popup message
 */
function checkformstatus(event)
{
	var form_id = $('#active_form_name').val(); // get form name

	/* definedform with flag */
	var is_change_form = $("#is_change_form").val();
	var changeflag = $("#is_change_form");
	var changeflagmain = $("#is_change_form_main");

	/* form Id */
	if(form_id!='' && form_id != undefined){
		is_change_form = $("#"+form_id+" #is_change_form").val();
		changeflag = $("#"+form_id+" #is_change_form");
		changeflagmain = $("#"+form_id+" #is_change_form_main");
	}

	/* is change form */
	if(is_change_form == 1) {
		var str = confirm("This page is asking you to confirm that you want to leave - data you have entered may not be saved"); // confirm box
		if(str==true) { // change flag check
			changeflag.val('0'); changeflagmain.val('0');
			return str;
		} else {
			event.preventDefault(); return false;
		}
	} else {
		return true;
	}
}


 /* Datepicker ChangeFlag */
 function changeflag(){
	$('#is_change_form').val('1'); // change flag
	$('#is_change_form_main').val('1'); // change flag
 }

 /* Change flag for timer popup */
 function changeformstatustimer(){
	 var form_id = $('#active-form-name').val(); // form id
	 if(form_id != '' && form_id != undefined){
		$('#'+form_id+' #is_change_form').val('0');
		$('#'+form_id+' #is_change_form_main').val('0');
	 } else {
		$('#is_change_form').val('0');
		$('#is_change_form_main').val('0');
	 }
 }
/* Javascript wrapper of php number_format function */
 function number_format (number, decimals, decPoint, thousandsSep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
  var n = !isFinite(+number) ? 0 : +number
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
  var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
  var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
  var s = ''
  var toFixedFix = function (n, prec) {
    var k = Math.pow(10, prec)
    return '' + (Math.round(n * k) / k)
      .toFixed(prec)
  }
  // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }
  return s.join(dec)
}

// Warning Duplicate IDs
/*$('document').ready(function(){
	$('[id]').each(function(){
	  var ids = $('[id="'+this.id+'"]');
	  //console.log(this.id);
	  if(ids.length>1 && ids[0]==this){
		   console.log(this);
			console.log('Multiple IDs #'+this.id);
		}
	});
});*/
