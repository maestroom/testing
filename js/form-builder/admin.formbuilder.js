$.extend($.ui.dialog.prototype.options, {
	create: function(event, ui) { 
		 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                 $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
	} 
});
var hpath=AdminFormBuilderbaseUrl;
var Admin = {}; // Stripped from Admin System
var tinyMCE = false; // Placeholder until tinyMCE is loaded at end of DOM.

Admin.formbuilder = {
		BASEURL: hpath+'/formBuilder/formbuilder.php',
		PREVIEWURL: hpath+'/formBuilder/preview.php',
		init: function()
		{
			//console.log('init');
			Admin.formbuilder.layout('body');
			//$('input').customInput();
			// Admin.formbuilder.tinymce();
		},
		layout: function(e)
		{
			var $active_layout = $(e);
			
			$active_layout.find('form[id=""]').each(function(){
				$(this).attr('id','f'+randomString(50)); // an ID for every	// form.
			});
			
			$active_layout.find('.last-child').removeClass('last-child'); // meh,
																			// safety
																			// dance
			
			$active_layout.find('ul,ol').each(function(){
				$(this).children('li:last').addClass('last-child');
			});
			
			$active_layout.children('li:last').addClass('last-child'); 
			
			$active_layout.find("#form_builder_toolbox li").unbind("click").bind("click",function(i){
				var into = $("#form_builder_panel ol");
				var type = $(this).attr('id');
				var e = this;
				$(this).addClass('loading');
				showLoader();
				$.get( hpath+'/formBuilder/formbuilder.php?action=element&type='+type+'&nocache='+Math.random(),function(result){
					hideLoader();
					$(e).removeClass('loading');
					$(into).append(result);// $(into).prepend(result);
					var $newrow = $(into).find('li:last');// find('li:first');
					
					// style
					Admin.formbuilder.editors();
					Admin.formbuilder.properties($newrow);
					Admin.formbuilder.layout($newrow);
					
					// show
					$newrow.hide().slideDown('slow');
					$(into).sortable("refresh");
					var sorder="";
					$("#form_builder_panel ol li").each(function(){ // new
																	// code
																	// for
																	// sorting
							if($(this).html=="")
								$(this).remove();	
							if($(this).attr('data-id'))
							{
								if(sorder=="")
									sorder=$(this).attr('data-id');
								else
									sorder= sorder + "," +$(this).attr('data-id');
							}
					});
					$('#sort_order').val('').val(sorder);
					$('.datepickers').each(function(e){
						var datepicker_id = $(this).attr('id');
						var formElements={};
						formElements[datepicker_id] = "%m/%d/%Y";
						datePickerController.createDatePicker({formElements: formElements });	
					});
					delete result;
				});
					
			});
			
			$active_layout.find("#form_builder_toolbox1 li").unbind("click").bind("click",function(i){
			
				var into = $("#form_builder_panel ol.ui-sortable");
				var element_list = [];
				into.find('li').each(function(){ element_list.push($(this).data('id')); });
				//var element_list = list_li.join(",");
				
				var type = $(this).attr('id');
				
				var formtype = $('#formtype').val();
				var e = this;
				$(this).addClass('loading');
				showLoader();
				if(type == 'copy'){ // Copy a Field
					
					// Load Existing Fields : getFieldsByTypes [Types: Custodian / Workflow]
					$.ajax({
                                                url:baseUrl + "system/get-fields-by-types",
                                                data:{formtype:formtype, element_list:element_list},
                                                type:"get",
                                                beforeSend:function(){
                                                        showLoader();
                                                },
                                                success:function(mydata){
                                                  //  console.log();
							if($('#copy-field-dialog').length == 0){
								$('body').append("<div id='copy-field-dialog'></div>");
							}
							
							$('#copy-field-dialog').html(mydata);
							
							$( "#copy-field-dialog" ).dialog({
								//title:"Transition Task",
								title:"Select Fields",
								autoOpen: true,
								resizable: false,
								width: "50em",
								height:456,
								modal: true,
								open: function (){
									hideLoader();
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
											var element_id = $('input[name="form_field[]"]:checkbox:checked').map(function(){
												return this.value;
											}).get().join(',');
											if(element_id==''){
												alert('Please select any field to copy');
												return false;
											}
											
											$.ajax({
												url:baseUrl + "system/get-element-by-field-id",
												data:{element_pkid:element_id},
												type:"get",
												beforeSend:function(){
													showLoader();
												},
												success:function(response){
													var Url = Admin.formbuilder.BASEURL+'?action=element_copy';
													$('body').append(response);
													var firstcopiedfield = '';
													$.ajax({
														url: Url,
														type:"post",
														cache: false,
														dataType:'json',
														data:$('form#copy-form-edit').serialize(),
														success:function(result){
															$(e).removeClass('loading');
															$.each(result,function(key,val){
																if(firstcopiedfield == '')
																	firstcopiedfield = key;
																	
																$(into).append(val);
																//$(into).prepend(val);
																var $newrow = $(into).find('li:last');// find('li:first');
																// style
																
																Admin.formbuilder.init();
																Admin.formbuilder.editors();
																Admin.formbuilder.properties($newrow);
																Admin.formbuilder.layout($newrow);
																// show
																$newrow.hide().slideDown('slow');
																$(into).sortable("refresh");
															});
															var sorder="";
															$("#form_builder_panel ol.ui-sortable li").each(function(){
																if($(this).html=="")
																	$(this).remove();
																if($(this).attr('data-id'))
																{
																	if(sorder=="")
																		sorder=$(this).attr('data-id');
																	else
																		sorder= sorder + "," +$(this).attr('data-id');
																}
															});
															$('#sort_order').val('').val(sorder);
															delete result;
														},
														complete:function(){
															$('.datepickers').each(function(e){
																var datepicker_id = $(this).attr('id');
																var formElements={};
																formElements[datepicker_id] = "%m/%d/%Y";
																datePickerController.createDatePicker({formElements: formElements });	
															});
															hideLoader();
															if($('.appended-copy-element').length > 0)
																$('.appended-copy-element').remove();
															$("#copy-field-dialog").dialog('close');
															if(firstcopiedfield != ''){
																$('#form_builder_panel li[data-id="'+firstcopiedfield+'"]').find('a.properties').trigger('click');
																firstcopiedfield = '';
															}
														}
													});
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
						}
					});
				} else {
					//alert(type); return false;
					$.get(Admin.formbuilder.BASEURL+'?action=element&type='+type+'&nocache='+Math.random()+'&form_type='+formtype,function(result){
						hideLoader();
						$(e).removeClass('loading');
						$(into).append(result);// $(into).prepend(result);
						var $newrow = $(into).find('li:last');// find('li:first');
						// style
						Admin.formbuilder.init();
						Admin.formbuilder.editors();
						Admin.formbuilder.properties($newrow);
						Admin.formbuilder.layout($newrow);
						// show
						$newrow.hide().slideDown('slow');
						$(into).sortable("refresh");
						var sorder="";
						$("#form_builder_panel ol.ui-sortable li").each(function(){
								if($(this).html=="")
									$(this).remove();
								if($(this).attr('data-id'))
								{
									if(sorder=="")
										sorder=$(this).attr('data-id');
									else
										sorder= sorder + "," +$(this).attr('data-id');
								}
						});
						$('#sort_order').val('').val(sorder);
						$('.datepickers').each(function(e){
							var datepicker_id = $(this).attr('id');
							var formElements={};
							formElements[datepicker_id] = "%m/%d/%Y";
							datePickerController.createDatePicker({formElements: formElements });	
						});
						delete result;
					});
				}
			});
			$active_layout.find("#form_builder_panel ol:first").sortable({
				cursor: 'ns-resize',
				axis: 'y',
				handle: '.handle',
				start: function(e,ui) {
					$('.wysiwyg').each(function(){
						var name = $(this).attr('name');
						if (name) {
						// if (tinyMCE.get(name)) {
							// tinyMCE.execCommand('mceRemoveControl',
							// false, name);
						// }
						}
					});
				},
				change: function(e,ui){
					// change flag status
					$("#is_change_form").val('1'); 
					$("#is_change_form_main").val('1');
				},
				stop: function(e,ui) { 
					var sorder="";
					$("#form_builder_panel ol.ui-sortable li").each(function(){ // new code
							// for
							// sorting
							if($(this).attr('data-id'))
							{
								if(sorder=="")
									sorder=$(this).attr('data-id');
								else
									sorder= sorder + "," +$(this).attr('data-id');
							}
					});
					//console.log('coming here console');
					document.getElementById('sort_order').value="";
					document.getElementById('sort_order').value=sorder;
					
					Admin.formbuilder.editors();
				}
			});
			
			$active_layout.find('div.dialog').each(function(){
				
				// $.metadata.setType("class");
				var w = 400;
				var h = 200;
				
				$(this).dialog({
					modal: true,
					zIndex: 400000, /*
									 * TinyMCE grief. Their default is literally
									 * 300000... Fail
									 */
					autoOpen: false,
					shadow: false,
					width: (w?parseInt(w, 10):400),
					height: (h?parseInt(h, 10):'auto'),
					title: $(this).attr('title'),
					dragStart: function(event, ui) {
						$(this).find('iframe').hide();
					},
					dragStop: function(event, ui) {
						$(this).find('iframe').show();
					},
					resizeStart: function(event, ui) {
						$(this).find('iframe').hide();
					},
					resizeStop: function(event, ui) {
						$(this).find('iframe').show();
					}
				});
			});
			
			$active_layout = null; // destroy
		},
		properties: function(e)
		{
			$(e).find('a.properties').unbind("click").bind("click",function(){
				$('body').find('div.dialog').remove();
				$('body').find('ul#form_builder_properties').remove();
				$('body').append('<ul id="form_builder_properties"></ul>');
				//$(e).on("click",'a.properties',function(){
                
                $(e).find('span.properties').focus();
                            
				$('#form_builder_properties').html("");
				// mohsin
				$('.wrapper').css('opacity',0.40);
				var id = $(this).parents('label:first').attr('for');
				if(id == undefined)
				{
					var id=$(this).find('em').attr('for');
				}
				
				// mohsin//
				$('#form_builder_properties').html('<span class="icon loading">Loading...</span>');
				// var label=$(this).find('span').find('span').html();
				var title = $(this).attr('rel');
				var label=$(this).parent().parent().parent().find('label span.properties').html();
				//if(title=='text'){
				//	var label=$(this).parent().parent().parent().find('label').html();
				//}
				if(label==undefined)
					label=$('#'+id).html();
				
				var new_label = strip_tags(label,'<span>');
				label = encodeURIComponent(new_label.replace('*', ''));
				
				var req=1;
				var qau=0;
				var desc='';
				var val='';
				var vals=''; // alert($('#no_load_prev_'+id).val());
                                // Added data:8-1-15
                var fieldType ='';
                
                var formtype = '';
				if($('#formtype').length > 0)
					formtype = $('#formtype').val();
					
                var sync_prod = '';
                var optionChk = '';
                var optionValue = '';
                
				if($('#'+id+'_values') != undefined){
					vals=$('#'+id+'_values').val();
					
				}
				if($('#'+id+'_value') != undefined){
					val=$('#'+id+'_value').val();
				}
				if($('#description_'+id) != undefined){
					desc = htmlEntities($('#description_'+id).val());
					desc = encodeURIComponent(desc.replace('*', ''));
				}
				if($('#required_'+id) != undefined)
					req=$('#required_'+id).val();
				if($('#qareportuse_'+id) != undefined)
					qau=$('#qareportuse_'+id).val();
				if(qau==undefined && $('input[name="properties['+id+'][qareportuse]"]') != undefined)
					qau=$('input[name="properties['+id+'][qareportuse]"]').val();
				
				
				if($('#no_load_prev_'+id) != undefined)
					no_load_prev=$('#no_load_prev_'+id).val();
				// Added data:8-1-15
				if($('#sync_prod_'+id) != undefined)
					sync_prod=$('#sync_prod_'+id).val();    
				/*if($('#field_type_'+id) != undefined)
					fieldType=$('#field_type_'+id).val();    */  
				if($('#optionchk_'+id) != undefined)
					optionChk=$('#optionchk_'+id).val();   
				
				if($('input[name="properties['+id+'][values]"]') != undefined){
					optionValue=$('input[name="properties['+id+'][values]"]').val();
				}
				
				var qareport=0;
                if(formtype == 'dataform' && (title == 'dropdown' || title == 'radio')){
                	qareport=1;
                }
				
				var form = "";
				if($("#form").val() != "")
					form = $("#form").val();    
								
				$('#form_builder_panel li.on').removeClass('on');
				var old_li_content =  encodeURIComponent($(this).closest('li').html());
				// showLoader();
				// alert(label);
				
				$.get(Admin.formbuilder.BASEURL+'?action=properties&type='+title+'&id='+id+'&label='+label+'&req='+req+'&no_load_prev='+no_load_prev+'&desc='+desc+'&sync_prod='+sync_prod+'&optionchk='+optionChk+'&optionValue='+optionValue+'&vals='+vals+'&val='+val+'&form='+form+'&qareport='+qareport+'&qau='+qau+'&formtype='+formtype+'&nocatch='+Math.random(),function(result){
					
					if(title != 'text'){
						var dialog_title = 'Edit ' + htmlDecode(new_label); 
					}else{
						var dialog_title = 'Edit';
					}
					
					if(result.replace(/\s+/g, ' ') != "")
					{
					
					// console.log(result);	// comment
					$('#form_builder_properties').html(null).html(result);
					
					$('#form_builder_properties').dialog({
                                            resizable: false,
                                            height:692,
					    width: "80em",
					    modal: true,
					    title: unescape(dialog_title),
					    create: function(event, ui) {
							$('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
							$('.ui-dialog-titlebar-close').attr("title", "Close");   
							$('.ui-dialog-titlebar-close').attr("aria-label", "Close");   
						},	
						close: function(event, ui) {
							//console.log($('#form_builder_properties').find('div#old_content'));
							if($(event.currentTarget).length > 0){
								var old_content = $('#old_content').html();
								var obj_li = $('li[data-id="'+id+'"]');
								$('li[data-id="'+id+'"]').html(decodeURIComponent(old_content));
								Admin.formbuilder.init();
								Admin.formbuilder.editors();
								Admin.formbuilder.properties(obj_li);
								Admin.formbuilder.layout(obj_li);
							}
							$('#form_builder_properties').html("");
						},
						dialogClass:'edit',
						buttons: {
							"Cancel":{
								"text": "Cancel",
								"title":"Cancel",
								"class": 'btn btn-primary',
								click: function() {
									var old_content = $('#old_content').html();
									var obj_li = $('li[data-id="'+id+'"]');
									$('li[data-id="'+id+'"]').html(decodeURIComponent(old_content));
									Admin.formbuilder.init();
									Admin.formbuilder.editors();
									Admin.formbuilder.properties(obj_li);
									Admin.formbuilder.layout(obj_li);
									$(this).dialog("close");
								}
							}
							,"Update":{
								"text": "Update",
								"title":"Update",
								"class": 'btn btn-primary',
								click: function() {
							                if(title == 'radio') {
										var optTextVal = $('input[name="optionchk_name"]:checked').closest('tr').find('td:first').text();
										$("#form_builder_panel .cls_"+id).removeAttr("checked");
										$("#form_builder_panel .cls_"+id).each(function(){
                                                                                    if(htmlEntities($(this).val()) == htmlEntities(optTextVal)){
                                                                                        $(this).attr("checked","checked");
                                                                                        $(this).prop("checked",true);
                                                                                    }
										});
									} else if(title == 'dropdown') {
										var optTextVal = $('input[name="optionchk_name"]:checked').closest('tr').find('td:first').text();
										//$("#form_builder_panel select[name='"+id+"']").val(0);
										if(optTextVal.replace(/\s/g, '') != "") {
                                                                                    $("#form_builder_panel select[name='"+id+"'] > option").each(function() {
                                                                                        $(this).removeAttr('selected');
                                                                                        if(htmlEntities($(this).text())==htmlEntities(optTextVal)) {
                                                                                            $(this).attr('selected','selected');
                                                                                            $('#form_builder_panel select[name="'+id+'"]').val($(this).val());
                                                                                        }
                                                                                    });
										}
                                                                            //$("#form_builder_panel select[name='"+id+"']").val(optTextVal);
									} else if(title == 'checkbox') {
										var selvalues = '';
										$('.ui-dialog').each(function(){
                                                                                    if($(this).is(":visible")){
                                                                                        $(this).find('#form_builder_properties input[name="optionchk_name"]').each(function(){
                                                                                            if($(this).attr('id') == id && $(this).is(':checked')){
                                                                                                if(selvalues != ""){
                                                                                                    selvalues += ',' +   htmlDecode($(this).val());
                                                                                                } else {
                                                                                                    selvalues = htmlDecode($(this).val());
                                                                                                }
                                                                                            }
                                                                                        });
                                                                                    }
										});
                                                                            //    console.log(selvalues);
										$("#form_builder_panel .cls_"+id).removeAttr("checked");
										if(selvalues != ""){
											var t = selvalues.split(",");
											for (i = 0; i < t.length; i++) {
												$("#form_builder_panel .cls_"+id).each(function(){
													if(htmlEntities($(this).val()) == htmlEntities(t[i])){
														$(this).attr("checked","checked");
														if(!$(this).is(':checked')){
															$(this).attr("checked",true);
															if(!$(this).is(':checked')) {
																$(this).prop("checked",true);	
															}
														}
													}
												});
											}
										}
									}
                                                                    /* change form flag to 1 */
                                                                    $("#is_change_form").val('1'); 
                                                                    $("#is_change_form_main").val('1');  
                                                                    /* end */

                                                                    /* Nelson Code Start */
                                                                    var $lblTxt = 'No Label';
																	if($( "input[name='label']").length){
                                                                    	if($( "input[name='label']").val() != '') 
                                                                        	$lblTxt = htmlEntities($( "input[name='label']").val());
																	}

																	///alert($lblTxt);
                                                                    $('.form_label #'+id).html($lblTxt);
                                                                    $('#sr_only_'+id).html($lblTxt);

																	if($( "input[name='description']").length){
																		if($( "input[name='description']").val() != ''){
																			$descriptiontext = htmlEntities($( "input[name='description']").val());
																			$('.note[class~='+id+']').html($descriptiontext)
																		}
																	}

																	if(title=='text'){
																		var $lblTxt = 'No Text Heading';
																		if($('#editor_'+id+'_text').length){
																			if($( '#editor_'+id+'_text').text() != ''){ 
                                                                        		$lblTxt = htmlEntities($( '#editor_'+id+'_text').text());
																			}
																		}
																		$('.form_label #'+id).html($lblTxt);
                                                                    	$('#sr_only_'+id).html($lblTxt);
																	}
																	/** (28-July-2017) Required Checkbox **/
                                                                    if($('#chk-required-'+id).val() == '0' && !$('label[for="lbl-'+id+'"] span').hasClass('required')) {
                                                                        $('label[for="lbl-'+id+'"]').append('<span class="required" aria-label="required">*</span>');
                                                                        $('#lbl-'+id).attr("required","required");                                                      
                                                                        if($('#lbl-'+id).hasClass('datepickers'))
                                                                            $('#lbl-'+id).attr("Title","This Field is required");   
                                                                    }
                                                                    /* End Code */

                                                                    /* Nelson Code Ends */                                                                                                               //$('input[name="optionchk_name"]').trigger('change');
                                                                    $(this).dialog("close");
								}
							},
						},
						open: function() {
							$('#form_builder_properties').append("<div id='old_content' aria-hidden='true' style='display:none;visibility:hidden;' tab-index='-1'>"+old_li_content+"</div>");
							$('.ui-dialog-buttonpane').find('button:contains("Cancel")').focus();
						    setTimeout(function(){ 
								addMCE(id+'_text');
								setTimeout(function(){
									$('#editor_'+id+'_text').focus();
									//console.log('done')
								},500);
							},500);
                                                        /*if($('input[name="properties['+id+'][field_type]"]').val() == 1){
                                                                $('#form_builder_properties li select[name="default_unit"]').closest('[data-id="'+id+'"]').show();
                                                        } else {
                                                                $('#form_builder_properties li select[name="default_unit"]').closest('[data-id="'+id+'"]').hide();
                                                        }*/
						}
					});
					
					if($('textarea[name='+id+']').length)
					{
						str=$('textarea[name='+id+']').val();
						var find = '<br>';
						var re = new RegExp(find, 'g');
						str = str.replace(re, '\n');
						$('textarea[name='+id+']').val('').val(str);
					}
					$( "input[name='label']").focus(); /* focus to label */
					/* label value force to a html */
					// $( "input[name='label']" ).die("keyup");
					/* label value force to a html */
					
					if($('#form_builder_properties_edit') != undefined)
					{
                                            $('#form_builder_properties_edit').append(result);
                                            Admin.formbuilder.attr.get(id);
                                            Admin.formbuilder.layout('#form_builder_properties');
                                            $('#form_builder_properties_edit li *:input').unbind("keyup").bind("keyup",function(){
                                                //	console.log(result);
                                                Admin.formbuilder.attr.update(this);
                                            });
					}
					Admin.formbuilder.attr.get(id);
					Admin.formbuilder.layout('#form_builder_properties');
					
					$('#form_builder_properties li *:input').unbind("keyup").bind("keyup",function(){
                                            Admin.formbuilder.attr.update(this);
                                            if($('#form_builder_properties_edit') != undefined)
                                            {
                                                $('#form_builder_properties_edit li *:input').unbind("keyup").bind("keyup",function(){
                                                    Admin.formbuilder.attr.update(this);
                                                });
                                            }
					});
					
					// mohsin
					$('#tinymce').unbind("keyup").bind("keyup",function(){
						Admin.formbuilder.attr.update(this);
					});
					
					$('#form_builder_properties li *:input').unbind("change").bind("change",function(){
						Admin.formbuilder.attr.update(this);
						if($('#form_builder_properties_edit') != undefined)
						{
							$('#form_builder_properties_edit li *:input').unbind("change").bind("change",function(){
								Admin.formbuilder.attr.update(this);
							});
						}
					});
					$('#form_builder_properties li input[type=checkbox]').bind("click",function(){
						if(this.checked){
							if($(this).attr('name')=='required')
								$('#required_'+id).val(0);
							if($(this).attr('name')=='no_load_prev')
								$('#no_load_prev_'+id).val(1);
							if($(this).attr('name')=='qareportuse')
								$('#qareportuse_'+id).val(1);
						}
						if(!this.checked)
						{
							if($(this).attr('name')=='required')
								$('#required_'+id).val(1);
							if($(this).attr('name')=='no_load_prev')
								$('#no_load_prev_'+id).val(0);
							if($(this).attr('name')=='qareportuse')
								$('#qareportuse_'+id).val(0);
						}
					});
					/* additional features add by MB */
					// $("input[name='label']").die('keyup');
					$("input[name='label']").on('keyup',function(){
						$($(this).attr('rel')).html('').html($(this).val());
						if($(this).val().replace(/\s/g, '')=="")
						{
							$($(this).attr('rel')).html('').html('No Label');
						}
						// find attr and set value if not exist
						if($('input[name="properties['+id+'][label]"]').length)
						{
							if($(this).val().replace(/\s/g, '')=="")
								$('input[name="properties['+id+'][label]"]').val('No Label');
							else
								$('input[name="properties['+id+'][label]"]').val(htmlEntities($(this).val()));
						}
						else
						{
							
							$('.attrs clear '+id).append('<input type="hidden" value="'+htmlEntities($(this).val())+'" class="label" name="properties['+id+'][label]">');
						}
						$('input[name="properties['+id+'][label]"]').val(htmlEntities($(this).val()));
						Admin.formbuilder.attr.update(this);
					});
					// $("input[name='description']").die('keyup');
					$("input[name='description']").on('keyup',function(){

						//alert(htmlEntities($(this).val()));
						
						$($(this).attr('rel')).text('').text(($(this).val()));
						if($(this).val().replace(/\s/g, '')=="")
						{
							$($(this).attr('rel')).text('');
						}
						if($('input[name="properties['+id+'][description]"]').length)
						{
							if($(this).val().replace(/\s/g, '')=="")
								$('input[name="properties['+id+'][description]"]').val('');
							else
								$('input[name="properties['+id+'][description]"]').val(htmlEntities($(this).val()));
						}
						else
						{
							$('.attrs clear '+id).append('<input type="hidden" value="'+htmlEntities($(this).val())+'" class="description" id="description_'+id+'" name="properties['+id+'][description]">');
						}
						Admin.formbuilder.attr.update(this);
					});
					
					$("[name='default_answer']").on('change',function(){
					   var id = $(this).attr('data-element-id');
						$('#form_builder_panel ol li[data-id="'+id+'"] [name="'+id+'"]').val($(this).val());
						if(title == 'textbox'){
							$('#form_builder_panel ol li[data-id="'+id+'"] [name="'+id+'"]').attr('value',($(this).val()));
						}
						if(title == 'textarea'){
							$('#form_builder_panel ol li[data-id="'+id+'"] [name="'+id+'"]').text($(this).val());
						}
						Admin.formbuilder.attr.update(this);
					});
					
					/*$("select[name='field_type']").on('change',function(){
						var id   = $(this).attr('data-element-id');
						//console.log($('#form_builder_properties li select[name="default_unit"]').closest('li[data-id="'+id+'"]').html());
						$('#form_builder_properties li select[name="default_unit"]').val(0);
						if($('input[name="properties['+id+'][field_type]"]').val() == 1){
							$('#form_builder_properties li select[name="default_unit"]').closest('[data-id="'+id+'"]').show();
						} else {
							$('#form_builder_properties li select[name="default_unit"]').closest('[data-id="'+id+'"]').hide();
						}
					});  */
					
					// $("textarea[name='values']").die('keyup');
					$("textarea[name='values']").on('keyup',function(){
						if($('input[name="properties['+id+'][values]"]').length)
						{
							if($(this).val().replace(/\s/g, '')=="")
								$('input[name="properties['+id+'][values]"]').val('');
							else
								$('input[name="properties['+id+'][values]"]').val($(this).val());
						}
						else
						{
							// alert(id+" length "+ id.length);
							// $('.attrs clear '+id).append('<input type="hidden" name="properties['+id+'][values]" class="values" value="'+$(this).val()+'">');
						}
						Admin.formbuilder.attr.update(this);
					});
					// $('textarea[name='+id+']').die('keyup');
					$('textarea[name='+id+']').on('keyup',function(){
						if($(this).attr('class')=='wysiwyg')
						{
							str=$(this).val();
							var find = '\n';
							var re = new RegExp(find, 'g');
							str = str.replace(re, '<br>');
							$('#'+id).each(function(){
								if($(this).get(0).tagName=='span')
									$(this).html('').html(str);
							});
						}	
					});
					/* End of additional features add by MB */
					if(!$(".tbodyClass_"+id).hasClass('ui-sortable')){
						//alert('hello');
						var fixHelper = function(e, ui) {
						ui.children().each(function() {
							$(this).width($(this).width());
						});
						return ui;
						};
					$(".tbodyClass_"+id).sortable({
						helper: fixHelper,
						stop: function(e,ui) { 
							var sorder="";
							var sort_arr=new Array();
							var checked_arr = new Array();
							$(".tbodyClass_"+id+" > tr ").each(function(i){ // new
																			// code
																			// for
																			// sorting
																			
									sort_arr[i]=$(this).find('td:nth-child(1)').html();
									checked_arr[i] = $(this).find('td:nth-child(2)').find('input').attr('checked');
									// BY HNL sort_arr[i]=$(this).find('td:nth-child(2)').html();
									if(sorder == "")
										sorder = htmlDecode($(this).find('td:nth-child(1)').html());
									else
										sorder = sorder + ';'  + htmlDecode($(this).find('td:nth-child(1)').html());
									
							});
							
							$('input[name="properties['+id+'][values]"]').val(sorder);
							var type = $('input[name="properties['+id+'][type]"]').val();
							
							if(type == 'checkbox' || type == 'radio'){
								$('.cls_'+id).each(function(i){
									$(this).val(sort_arr[i]);
									$(this).next().html(sort_arr[i]);
									if(checked_arr[i] == 'checked'){
										$(this).prop("checked",true);
									}else{
										$(this).prop("checked",false);
									}
								});
							}
							
							if(type == 'dropdown'){
								$('[name='+id+'] option').each(function(i){
									if(i != 0){
										$(this).val(i);
										$(this).text(sort_arr[i-1]);
										$(this).attr('description',sort_arr[i-1]);
										if(checked_arr[i-1] == 'checked'){
											$(this).attr("selected","selected");
										}else{
											$(this).removeAttr("selected");
										}
									}	
								});
							}
						}
					}).disableSelection();
				}
					Admin.formbuilder.commonFunc();
			    	// mohsin
					delete result;
				}
				if(title=='text'){
					var interval=setInterval(function(){
						if($('#'+id+'_text').css('display')=='none'){
							clearInterval(interval);
							hideLoader();
						}
					},500);
				}else{
					hideLoader();
				}
			});
				
				return false;
			});
		},
		tinymce: function(e)
		{/*
			 * //alert($(e)); //if (!tinyMCE) {
			 * 
			 * tinyMCE.init({ // General options mode : "textareas", theme :
			 * "advanced", plugins :
			 * "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			 *  // Theme options theme_advanced_buttons1 :
			 * "bold,italic,underline,|,fontsizeselect,",//$service_task
			 * theme_advanced_buttons2 : "", theme_advanced_buttons3 : "",
			 * theme_advanced_buttons4 : "", theme_advanced_toolbar_location :
			 * "bottom", theme_advanced_toolbar_align : "left",
			 * theme_advanced_statusbar_location : "bottom",
			 * theme_advanced_resizing : true, onchange_callback :
			 * "myCustomOnChangeHandler", editor_selector : "wysiwyg",
			 * editor_deselector : "wysiwygNoEditor",
			 * 
			 * 
			 *  // Skin options skin : "o2k7", skin_variant : "silver",
			 *  // Example content CSS (should be your site CSS) content_css :
			 * "css/example.css",
			 *  // Drop lists for link/image/media/template dialogs
			 * template_external_list_url : "js/template_list.js",
			 * external_link_list_url : "js/link_list.js",
			 * external_image_list_url : "js/image_list.js",
			 * media_external_list_url : "js/media_list.js",
			 *  // Replace values for the template plugin
			 * 
			 * });
			 * 
			 * //}); }
			 * 
			 */},
		remove: function(e)
		{
			Admin.formbuilder.confirm("Really remove this element?",function(options){
				$('label[for='+options.rel+']').parents('li').slideUp('slow',function(){
					$(this).remove();
					$('#form_builder_properties').dialog('close');
				});
			},{rel: $(e).attr('rel')});
		},
		editors: function()
		{
			$('.wysiwyg').each(function(){
				var name = $(this).attr('name');
				if (name) {
                                    // if (!tinyMCE.get(name))
                                    // tinyMCE.execCommand('mceAddControl', false, name);
				}
			});
		},
		attr: {
			get: function(id)
			{
				$('.attrs.'+id+' input').each(function(){
					var val = $(this).val();
					var id = $(this).attr('class');
					if (val && id!="optionchk") {
						$('#form_builder_properties input[name='+id+']').val(val);
						$('#form_builder_properties textarea[name='+id+']').val(val);
                        $('#form_builder_properties select[name='+id+']').val(val);
					}
				});
			},
			update: function(e)
			{
                var $element = $(e);
				var name = $element.attr('name');
				var id = $element.parents('li:not(.sub):first').data('id');
				var rel = $element.attr('rel');
				var value = $element.val();
				//alert(value);
				var type = $element.attr('class');
				var found = false;
                                var required_field =  $('#chk-required-'+id).val();
                                
				$('body').data(rel,{'name':name,'value':value});
				
				$('div.attrs.'+id+' input').each(function(){
					if ($(this).attr('name') == "properties["+id+"]["+name+"]")
					{
						
						$(this).val(value);
						$element.val(value);
						// alert($element.val());
						found = true;
					}
				});
				
				// $('select[name='+name+']').val(0);
				
				if (!found) {
					// alert($('div.attrs.'+id).attr('name'));
					if(name=='required')
						$('div.attrs.'+id).append("<input type='hidden' class='new_property "+name+"' name='properties["+id+"]["+name+"]' id='"+name+"_"+id+"'/>");
					else if(name=='no_load_prev')
						$('div.attrs.'+id).append("<input type='hidden' class='new_property "+name+"' name='properties["+id+"]["+name+"]' id='"+name+"_"+id+"'/>");
					else
						$('div.attrs.'+id).append("<input type='hidden' class='new_property "+name+"' name='properties["+id+"]["+name+"]'/>");
					
					$('.new_property').removeClass('new_property').val(value);
				}
				
				switch (type)
				{
					case 'dropdown':
						value = htmlDecode(value).split(';');
					break;
					case 'checkbox':
						value = htmlDecode(value).split(';');
					break;
					case 'radio':
						value = htmlDecode(value).split(';');
					break;
					default: break;
				}
				
				if (rel && value) {
					
					if (!$.isArray(value)) {
						var block = $(rel).not(':input').length;
						
						if (block == 0) $(rel).val(value);
						else $(rel).html(value);
						//console.log(rel);
					} else {
						// its an array, oh dear!
                                                var required = '';
                                                if(required_field == 0)
                                                    required += 'required="required" title="This field is required"';    
                                                
						switch (type)
						{
							case 'dropdown':
								var newc = '';
								var def = '<option value="0">Please Select</option>';
								for (i in value){
									j = parseInt(i) + 1;
									if(newc != ""){
										if(value[i] != ""){	
											newc += '<option value='+j+' description="'+value[i]+'">'+htmlEntities(value[i]).substring(0,65);+'</option>';
										}
										else if(newc == ""){
											newc = def;
										}	
									} else {
										if(value[i] != ""){
											newc = def+'<option value='+j+' description="'+value[i]+'">'+htmlEntities(value[i]).substring(0,65);+'</option>';
										}else{
											newc = def;
										}
									}
								}
								$(rel).html(newc);
								break;
							case 'radio':
								var newc = '';
								for (i in value) newc += '<input type="radio" class="cls_'+id+'" '+required+' aria-label="'+value[i]+'" value="'+value[i]+'" name="temp['+name+'][]"><label for="lbl_chkbx_'+id+'" class="form_label fbg-label"><span class="cls_value_'+id+'"> '+htmlEntities(value[i])+' </span></label> <br/>';
								$(rel).html(newc);
								break;
							case 'checkbox':
								var newc = '';
								for (i in value) newc += '<input type="checkbox" class="cls_'+id+'" '+required+' aria-label="'+value[i]+'" value="'+value[i]+'" name="temp['+name+'][]"><label for="lbl_chkbx_'+id+'" class="form_label fbg-label"><span class="cls_value_'+id+'">'+htmlEntities(value[i])+'</span></label> <br/>';
								$(rel).html(newc);
								break;
							default: break;
						}
					}
				}
				
			}
		},
		preview: function()
		{
			$('.wrapper').css('opacity',0.30);
			$('.ui-widget-overlay').hide();
			// $('textarea.wysiwyg').each(function(){
                            // var name = $(this).attr('name');
                            // alert(name);
                            // if (name) {
                                // var contents =
                                // $('#'+name+'_ifr').contents().find("body").html();//tinyMCE.get(name).getContent();
                            // }
                            // $(this).val(contents);
			// });
			if($('#form_builder_preview').length == 0){
                            $('body').append('<div id="form_builder_preview"></div>');
			}
			var data =$('#admin_right').find('form').serialize();
                            // $('#form_builder_panel form').serialize();
			$.post(Admin.formbuilder.PREVIEWURL,data,function(result){
                            $('#form_builder_preview').html(result);
                            Admin.formbuilder.dialog('form_builder_preview');
			});
			$('#form_builder_preview').dialog({
                            title: 'Preview',
                            width: "50em",
                            height: 456,
                            'modal' : true,
                        // dialogClass:'preview',
                        buttons: {
                            "Close":{
                                text: "Close",
                                "title":"Close",
                                "class": 'btn btn-primary',
                                'aria-label': "Close",
                                click:function() {
                                    $('.wrapper').css('opacity',1);
                                    $(this).dialog('close');
                                }
                        }
                        /*,
                    "Test":{
                        text: "Update",
                        "title":"Update",
                        "class": 'btn btn-primary',
                        'aria-label': "Update",
                        click:function() {
                        	$('#form_builder_preview form').submit();
                        }
					}*/
				},
				open: function() {
					/*$('.ui-dialog-buttonpane').find('button:contains("Cancel")').attr('class','btn btn-primary');
					$('.ui-dialog-buttonpane').find('button:contains("Cancel")').attr('title','Cancel');
		            $('.ui-dialog-buttonpane').find('button:contains("Test")').attr('class','btn btn-primary');
		            $('.ui-dialog-buttonpane').find('button:contains("Test")').attr('title','Test');*/
		        }
				});
		},
		dialog: function(rel,link)
		{
			var external = $("#"+rel).hasClass('external');
			if (external) {
				
				$("#"+rel).show().html("<iframe src='"+link+"' name='"+rel+"' width='100%' height='100%' frameborder='0' border='0'></iframe>").dialog('open');
				return;
			}
			if (link) {
                            if (link.indexOf('http') >= 0) {
                                $("#"+rel).html("");
                                $.get(link,function(result){
                                    $("#"+rel).html(result).show().dialog('open');
                                    Admin.formbuilder.layout("#"+rel);
                                    delete result;
                                });
                                return;
                            }
			}
			$("#"+rel).show().dialog('open');
		},
		confirm: function(msg,callback,options)
		{
			var id = 'confirm_'+Math.ceil(100*Math.random());
			$('body').append('<div id="'+id+'"><p></p></div>');
			$('#'+id+' p').html(msg).dialog({
				modal: true,
				overlay: { 
					opacity: 0.5, 
					background: "black" 
				},
				title: 'Confirm',
				buttons: { 
                            "Confirm":{
                                text: "Confirm",
                                "title":"Confirm",
                                "class": 'btn btn-primary',
                                'aria-label': "Confirm",
                                click: function() { 
                                        if (callback) callback(options);
                                        $(this).dialog("close");
                                        $('.ui-dialog-titlebar .ui-widget-header .ui-corner-all .ui-helper-clearfix').dialog("close");
                                        $(this).parents('div:first').remove();
                                    }
                                }, 
                                "Cancel": {
                                    text: "Cancel",
                                    "title":"Cancel",
                                    "class": 'btn btn-primary',
                                    'aria-label': "Cancel",
                                    click:function() {
                                        $(this).dialog("close");
                                        $(this).parents('div:first').remove();
                                    }
                                } 
                        },
			open: function() {
		            $('.ui-dialog-buttonpane').find('button:contains("Cancel")').attr('class','btn btn-primary');
		            $('.ui-dialog-buttonpane').find('button:contains("Confirm")').attr('class','btn btn-primary');
		        }
                    });
		},
		edit: function(e)
		{
			var $element = $(e);
		},
		commonFunc: function(e)
		{
			 $('input[name="optionchk_name"]').unbind('change').bind('change',function(){
				    var optTextVal = ""; 	
				    if($(this).attr('class') == 'radio_btn' || $(this).attr('class') == 'dropdown_btn'){
				    	var elementId   = $(this).attr('id');
				    	if($(this).is(':checked')){
				    		$('input[name="optionchk_name"]').not(this).removeAttr('checked');
				            var $tr         = $(this).closest('tr');
				            // BY HNL var optTextVal  = $tr.find('td:nth-child(2)').html();
				            var optTextVal  = htmlDecode($tr.find('input').val());
				            //alert(optTextVal);
				            $("#form_builder_panel #optionchk_"+elementId).val(optTextVal);
				        } else {
				            $("#form_builder_panel #optionchk_"+elementId).val("");
				        }
				    } else {
				    	var elementId   = $(this).attr('id');
				        var $tr         = $(this).closest('tr');
				        var optTextVal  = $tr.find('td:nth-child(2)').html();
				        var oldvalue    = $("#form_builder_panel #optionchk_"+elementId).val();
				        var olderArr    = oldvalue.split(',');
				        var selvalues = "";
				        $('.ui-dialog').each(function(){
				        	if($(this).is(":visible")){
				                $(this).find('#form_builder_properties input[name="optionchk_name"]').each(function(){
						        	if($(this).attr('id') == elementId && $(this).is(':checked')){
					            		if(selvalues != "") {
					            			selvalues += ',' +   $(this).val();
					            		} else {
					            			selvalues = $(this).val();
					            		}
					            	}
					            });
				        	}
				        });
				        
				        $("#form_builder_panel #optionchk_"+elementId).val(selvalues);
				    }
				    
				    if($(this).attr('class') == 'radio_btn') {
				    	$("#form_builder_panel .cls_"+elementId).removeAttr("checked");
				    	$("#form_builder_panel .cls_"+elementId).each(function(){
							if(htmlEntities($(this).val()) == htmlEntities(optTextVal)){
								$(this).attr("checked","checked");
				    			$(this).prop("checked",true);
				    		}
				    	});
				    } else if($(this).attr('class') == 'dropdown_btn') {
				    	$("#form_builder_panel select[name='"+elementId+"']").val(0);
				    	if(optTextVal.replace(/\s/g, '') != "") {
							
							$("#form_builder_panel select[name='"+elementId+"'] > option").each(function(){
								$(this).removeAttr('selected');
								if(htmlEntities($(this).text())==htmlEntities(optTextVal)) { 
									$(this).attr('selected','selected');
									//$(this).parent('select').val($(this).val());
									$("#form_builder_panel select[name='"+elementId+"']").val($(this).val());
								}
							});
				    	}
				    } else {
				    	$("#form_builder_panel .cls_"+elementId).removeAttr("checked");
				    	
				    	if(selvalues != ""){
				    		var t = selvalues.split(",");
				    		for (i = 0; i < t.length; i++) {
					        	$("#form_builder_panel .cls_"+elementId).each(function(){
					        		if(htmlEntities($(this).val()) == htmlEntities(t[i])){
					        			$(this).attr("checked","checked");
					        			if(!$(this).is(':checked')){
					        				$(this).attr("checked",true);
					        				if(!$(this).is(':checked')) {
					        					$(this).prop("checked",true);	
					        				}
					        			}
					        		}
					        	});
				    		}
				    	}
				    	
				    }
				});
				$(".removeOptionText").unbind('click').bind("click",function(){
				    var typeClassStr = $(this).closest("tr").find("td:eq(1)").find('input').attr('class');
				    var newStr      = typeClassStr.split('_');
				    var typeClass   = newStr[0];
				    var tdvalue     = $(this).closest("tr").find("td:eq(1)").html();
				    var elementId   = $(this).attr('id');
				    var strArr      = $('input[name="properties['+elementId+'][values]"]').val().split(";");
				    var optionArr   = $("#optionchk_"+elementId).val().split(',');
				    var typeArr     = $("."+typeClass).val().split(";");
				    
				    // remove append tr
				    $(this).parent().parent().remove();
				    
				    // remove match selected option
				    var selvalues = "";
				    var allvalues = "";
				    
				    $('.ui-dialog').each(function(){
				    	if($(this).is(":visible")){
					        $(this).find('#form_builder_properties input[name="optionchk_name"]').each(function(){
					        	if($(this).attr('id') == elementId && $(this).attr('checked') == "checked") {
					        		if(selvalues != ""){
					        			selvalues += ',' + $(this).val();
					        		} else {
					        			selvalues = $(this).val();
					        		}
					        	}
					        	if(allvalues != "") {
					        		allvalues += ';' + $(this).val();
					    		} else {
					    			allvalues = $(this).val();
					    		}
					        });
				    	}
				    });    
				    $("#form_builder_panel #optionchk_"+elementId).val(selvalues);
				    //console.log(selvalues);
				    // $("#optionchk_"+elementId).val(allvalues);
				   
				    // remove match textarea values
				    $("."+typeClass).val(allvalues);
				    
				    $("."+typeClass).trigger("keyup");
				    
				    // append empty tr
				    $(".optionvalueClass tbody").filter(function () {
				        return !$(this).children().length;
				    }).append('<tr class="removeTr"><td style="min-width:20px !important;"></td><td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;">&nbsp;</td><td style="min-width:40px !important;">&nbsp;</td></tr>');
				});
		}
		
};
function randomString(lengt)
{
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = lengt;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}
function removeTinyMCE (fieldID) {
	/*
	 * if (tinyMCE.get(fieldID)){ tinyMCE.execCommand('mceFocus', false,
	 * fieldID); tinyMCE.execCommand('mceRemoveControl', false, fieldID); }
	 */
}

function addMCE(fieldID) {
	
	if($('#form_builder_properties_edit').length == 0) {
	if($('#'+fieldID).is("textarea"))
	{
		 var textareaid=fieldID.replace('_text','');
		 $("#"+fieldID+"_text").val($(textareaid).html());
		 $("#"+fieldID).val($("#"+textareaid).html());
		 var storedVal = $("#"+fieldID+'_val').val();
		 $("#"+fieldID).parent().append('<div class="x_panel custom-wording-editer">'+
                 '<div class="x_content">'+
                 '<div id="alerts"></div>'+
                 '<div class="btn-toolbar editor toolbar-justified" data-role="editor-toolbar" aria-hidden="true" data-target="#editor_'+fieldID+'">'+
                     '<div class="btn-group">'+
                         '<a class="btn dropdown-toggle" data-toggle="dropdown" title="Font"><em class="fa icon-font"></em><strong class="caret"></strong></a>'+
                         '<ul class="dropdown-menu">'+
                         '</ul>'+
                     '</div>'+
                     '<div class="btn-group">'+
                         '<a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><em class="icon-text-height"></em>&nbsp;<strong class="caret"></strong></a>'+
                         '<ul class="dropdown-menu">'+
                             '<li><a data-edit="fontSize 5"><p style="font-size:17px">Huge</p></a>'+
                             '</li>'+
                             '<li><a data-edit="fontSize 3"><p style="font-size:14px">Normal</p></a>'+
                             '</li>'+
                             '<li><a data-edit="fontSize 1"><p style="font-size:11px">Small</p></a>'+
                             '</li>'+
                         '</ul>'+
                     '</div>'+
                     '<div class="btn-group">'+
                         '<a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><em class="icon-bold"></em></a>'+
                         '<a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><em class="icon-italic"></em></a>'+
                         '<a class="btn" data-edit="strikethrough" title="Strikethrough"><em class="icon-strikethrough"></em></a>'+
                         '<a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><em class="icon-underline"></em></a>'+
                     '</div>'+
                     '<div class="btn-group">'+
                         '<a class="btn" data-edit="insertunorderedlist" title="Bullet list"><em class="icon-list-ul"></em></a>'+
                         '<a class="btn" data-edit="insertorderedlist" title="Number list"><em class="icon-list-ol"></em></a>'+
                         '<a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><em class="icon-indent-left"></em></a>'+
                         '<a class="btn" data-edit="indent" title="Indent (Tab)"><em class="icon-indent-right"></em></a>'+
                     '</div>'+
                     '<div class="btn-group">'+
                         '<a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><em class="icon-align-left"></em></a>'+
                         '<a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><em class="icon-align-center"></em></a>'+
                         '<a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><em class="icon-align-right"></em></a>'+
                         '<a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><em class="icon-align-justify"></em></a>'+
                     '</div>'+
                     '<div class="btn-group">'+
                         '<a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><em class="icon-link"></em></a>'+
                         '<div class="dropdown-menu input-append">'+
                             '<input class="span2" placeholder="URL" type="text" data-edit="createLink" />'+
                             '<button class="btn" type="button">Add</button>'+
                         '</div>'+
                    '</div>'+
                     '<div class="btn-group">'+
                         '<a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><em class="icon-undo"></em></a>'+
                         '<a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><em class="icon-repeat"></em></a>'+
                     '</div>'+
                 '</div>'+
                 '<div tab-index="1" class="editor-container" id="editor_'+fieldID+'">'+storedVal+'</div>'+
             '</div></div>');
		 initToolbarBootstrapBindings();
		 $("#"+fieldID).hide();
		 $('#editor_'+fieldID).wysiwyg();
		 $('#editor_'+fieldID).unbind('DOMSubtreeModified').bind('DOMSubtreeModified', function(event) {
			 $('#'+fieldID+'_text').html($(this).html());
			 edit_val=$(this).html();
			 var textareaid=fieldID.replace('_text','');
			 $("#"+textareaid+"_text_val").val(edit_val);
			 $("#"+textareaid).html(edit_val);    
		 });
	}
	else{ // edit time
		if($('#form_builder_properties #'+fieldID).is("textarea"))
		{
			$("#"+fieldID).parent().append('<div class="x_panel custom-wording-editer">'+
	                 '<div class="x_content">'+
	                 '<div id="alerts"></div>'+
	                 '<div class="btn-toolbar editor toolbar-justified"  aria-hidden="true" data-role="editor-toolbar" data-target="#editor_'+fieldID+'">'+
	                     '<div class="btn-group">'+
	                         '<a class="btn dropdown-toggle" data-toggle="dropdown" title="Font"><em class="fa icon-font"></em><strong class="caret"></strong></a>'+
	                         '<ul class="dropdown-menu">'+
	                         '</ul>'+
	                     '</div>'+
	                     '<div class="btn-group">'+
	                         '<a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><em class="icon-text-height"></em>&nbsp;<strong class="caret"></strong></a>'+
	                         '<ul class="dropdown-menu">'+
	                             '<li><a data-edit="fontSize 5"><p style="font-size:17px">Huge</p></a>'+
	                             '</li>'+
	                             '<li><a data-edit="fontSize 3"><p style="font-size:14px">Normal</p></a>'+
	                             '</li>'+
	                             '<li><a data-edit="fontSize 1"><p style="font-size:11px">Small</p></a>'+
	                             '</li>'+
	                         '</ul>'+
	                     '</div>'+
	                     '<div class="btn-group">'+
	                         '<a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><em class="icon-bold"></em></a>'+
	                         '<a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><em class="icon-italic"></em></a>'+
	                         '<a class="btn" data-edit="strikethrough" title="Strikethrough"><em class="icon-strikethrough"></em></a>'+
	                         '<a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><em class="icon-underline"></em></a>'+
	                     '</div>'+
	                     '<div class="btn-group">'+
	                         '<a class="btn" data-edit="insertunorderedlist" title="Bullet list"><em class="icon-list-ul"></em></a>'+
	                         '<a class="btn" data-edit="insertorderedlist" title="Number list"><em class="icon-list-ol"></em></a>'+
	                         '<a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><em class="icon-indent-left"></em></a>'+
	                         '<a class="btn" data-edit="indent" title="Indent (Tab)"><em class="icon-indent-right"></em></a>'+
	                     '</div>'+
	                     '<div class="btn-group">'+
	                         '<a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><em class="icon-align-left"></em></a>'+
	                         '<a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><em class="icon-align-center"></em></a>'+
	                         '<a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><em class="icon-align-right"></em></a>'+
	                         '<a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><em class="icon-align-justify"></em></a>'+
	                     '</div>'+
							'<div class="btn-group">'+
	                         '<a class="btn" title="Insert picture (or just drag & drop)" id="pictureBtn"><em class="icon-picture"></em></a>'+
	                         '<input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage" />'+
	                     '</div>'+
	                     '<div class="btn-group">'+
	                         '<a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><em class="icon-link"></em></a>'+
	                         '<div class="dropdown-menu input-append">'+
	                             '<input class="span2" placeholder="URL" type="text" data-edit="createLink" />'+
	                             '<button class="btn" type="button">Add</button>'+
	                         '</div>'+
	                    '</div>'+
	                     '<div class="btn-group">'+
	                         '<a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><em class="icon-undo"></em></a>'+
	                         '<a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><em class="icon-repeat"></em></a>'+
	                     '</div>'+
	                 '</div>'+
	                 '<div tab-index="1" class="editor-container" id="editor_'+fieldID+'">'+storedVal+'</div>'+
	             '</div></div>');
			 initToolbarBootstrapBindings();
			 $("#"+fieldID).hide();
			 $('#editor_'+fieldID).wysiwyg();
			 $('#editor_'+fieldID).unbind('DOMSubtreeModified').bind('DOMSubtreeModified', function(event) {
				 $('#'+fieldID+'_text').html($(this).html());
				 edit_val=$(this).html();
				 var textareaid=fieldID.replace('_text','');
				 $("#"+textareaid+"_text_val").val(edit_val);
				 $("#"+textareaid).html(edit_val);    
				 $("#form_builder_properties_edit #"+fieldID).val(edit_val); 
			 });
		}
	}
}

}
function initToolbarBootstrapBindings() {
	var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
'Times New Roman', 'Verdana'],
		fontTarget = $('[title=Font]').siblings('.dropdown-menu');
	$.each(fonts, function (idx, fontName) {
		fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
	});
	// $('a[title]').tooltip({
// container: 'body'
// });
	$('.dropdown-menu input').click(function () {
			return false;
		})
		.change(function () {
			$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');
		})
		.keydown('esc', function () {
			this.value = '';
			$(this).change();
		});

	$('[data-role=magic-overlay]').each(function () {
		var overlay = $(this),
			target = $(overlay.data('target'));
		overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
	});
	if ("onwebkitspeechchange" in document.createElement("input")) {
		var editorOffset = $('#editor').offset();
		$('#voiceBtn').css('position', 'absolute').offset({
			top: editorOffset.top,
			left: editorOffset.left + $('#editor').innerWidth() - 35
		});
	} else {
		$('#voiceBtn').hide();
	}
};

function GetElementInsideContainer(containerID, childID) {
    var elm = {};
    var elms = document.getElementById(containerID).getElementsByTagName("*");
    for (var i = 0; i < elms.length; i++) {
        if (elms[i].id === childID) {
            elm = elms[i];
            break;
        }
    }
    return elm;
}

function myCustomOnChangeHandler(inst) {
	idss=$(inst).attr('id');// .val();
	idss=idss.replace("_text","");
	$('#'+idss+'_text_val').val(inst.getBody().innerHTML);
	$('label[for='+idss+'] span').html(inst.getBody().innerHTML);
}
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function htmlDecode(inp){
  var replacements = {'&lt;':'<','&gt;':'>','&sol;':'/','&quot;':'"','&apos;':'\'','&amp;':'&','&laquo;':'«','&raquo;':'»','&nbsp;':' ','&copy;':'©','&reg;':'®','&deg;':'°'
                     };
  for(var r in replacements){
    inp = inp.replace(new RegExp(r,'g'),replacements[r]);
  }
  return inp.replace(/&#(\d+);/g, function(match, dec) {
    return String.fromCharCode(dec);
  });
}

function addOption(elementId,typeClass,chk2) {
	// alert(typeClass);
    // var optionTxt = $(':hidden.optionTextbox'+typeClass).val();
	
	$('.ui-dialog').each(function(){
    	if($(this).is(":visible")){
	        var optionTxt = htmlEntities($(this).find('#form_builder_properties #optionTextboxAddVal').val());
	        if (optionTxt.replace(/\s/g, '') == '') {
	            alert("Please enter Option Text to perform this action")
	        } else if (optionTxt.replace(/\s/g, '') == 'undefined') {
	            alert("Please enter Option Text to perform this action")
	        } else {
	        	var allvalues = "";
	        	var error = 0; 
	        	var selvalues = "";
		        $(this).find('#form_builder_properties input[name="optionchk_name"]').each(function(){
		        	if(optionTxt == $(this).val()){
		        		error = 1;
		        		return false;
		        	}
		        	if($(this).attr('id') == elementId && $(this).attr('checked') == "checked"){
	            		if(selvalues != ""){
	            			selvalues += ',' +   $(this).val();
	            		} else {
	            			selvalues = $(this).val();
	            		}
	            	}
		    		if(allvalues != "") {
		        		allvalues += ';' + $(this).val();
		    		} else {
		    			allvalues = $(this).val();
		    		}
		        });
        	
	        	if(error == 1){
	        		alert(optionTxt+" already exists. Please enter a unique value to perform this action.");
	        		return false;
	        	} else {
		        	if(allvalues != "") {
		        		$(this).find("#form_builder_properties ."+typeClass).val(allvalues+";"+htmlDecode(optionTxt));
		        	} else {
		        		$(this).find("#form_builder_properties ."+typeClass).val(htmlDecode(optionTxt));
		        	}
		        	
		        	$(this).find("#form_builder_properties ."+typeClass).trigger("keyup");
		            
		        	$(this).find("#form_builder_properties .removeTr").remove();
		        	
		        	var rowCount = $('#form_builder_properties .tbodyClass_'+elementId+' tr').length;
		        	
		        	if(rowCount % 2 == 0){
						var odd_even = 'odd';
					}else{
						var odd_even = 'even';
					}
		        	
		            
		            var optionValueHtml = '<tr id="newTr" class="'+elementId+' '+odd_even+'">\n\<td style="width: 85%; word-wrap: break-word;" align="left">' + optionTxt + '</td>\n\<td style="min-width:40px !important;text-align:center;">\n\<input type="checkbox" value="'+optionTxt+'" name="optionchk_name" class="'+typeClass+'_btn" id="'+elementId+'"></td>\n<td style="min-width:20px !important;"><a href="javascript:void(0);" style="cursor: pointer;" class="removeOptionText icon-set" id='+elementId+'><em class="fa fa-close text-primary"></em></a></td>\n\</tr>';
		            $(this).find("#form_builder_properties .tbodyClass_"+elementId).append(optionValueHtml);
	        	}
	        }
	        if($('input[name="optionchk_name"]').attr('class') == 'radio_btn') {
	        	$("#form_builder_panel .cls_"+elementId).removeAttr("checked");
	        	$("#form_builder_panel .cls_"+elementId).each(function(){
	        		if(htmlEntities($(this).val()) == htmlEntities(selvalues)){
	        			$(this).attr("checked","checked");
	        		}
	        	});
	        } else if($('input[name="optionchk_name"]').attr('class') == 'dropdown_btn') {
	        	if(selvalues.replace(/\s/g, '') != "") {
	        		$("#form_builder_panel select[name='"+elementId+"'] > option:contains(" + htmlEntities(selvalues) + ")").attr('selected','selected');
	        	} else {
	        		$("#form_builder_panel select[name='"+elementId+"']").val(0);
	        	}
	        } else {
	        	$("#form_builder_panel .cls_"+elementId).removeAttr("checked");
	        	if(selvalues != ""){
	        		var t = selvalues.split(",");
	        		for (i = 0; i < t.length; i++) { 
			        	$("#form_builder_panel .cls_"+elementId).each(function(){
			        		if(htmlEntities($(this).val()) == htmlEntities(t[i])){
			        			$(this).attr("checked","checked");
			        		}
			        	});
	        		}
	        	}
	        }        
            // Remove option textbox value
            $(this).find('#form_builder_properties #optionTextboxAddVal').val("");
            $(".optionTextboxdropdown").val("");
            $(this).find('#form_builder_properties #optionTextboxAddVal').focus();
            Admin.formbuilder.commonFunc();
                    
    	}
	});
	if(!$(".tbodyClass_"+elementId).hasClass('ui-sortable')){
			var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
			};
		$(".tbodyClass_"+elementId).sortable({
			helper: fixHelper,
			stop: function(e,ui) { 
				var sorder="";
				var sort_arr = new Array();
				$(".tbodyClass_"+elementId+" > tr ").each(function(i){ // new
																		// code
																		// for
																		// sorting
						sort_arr[i]=$(this).find('td:nth-child(2)').html();
						if(sorder == "")
							sorder = htmlDecode($(this).find('td:nth-child(2)').html());
						else
							sorder = sorder + ';'  + htmlDecode($(this).find('td:nth-child(2)').html());
				});
				
				$('input[name="properties['+elementId+'][values]"]').val(sorder);
				var type = $('input[name="properties['+elementId+'][type]"]').val();
				if(type == 'checkbox' || type == 'radio'){
					$('.cls_'+elementId).each(function(i){
						$(this).val(sort_arr[i]);
						$(this).next().html(sort_arr[i]);
					});
				}
			}
		}).disableSelection();
	}
}
$(document).ready(function () {
   
/*
 * $("select[name='sync_prod']").on('change',function(){
 * if($('input[name="properties['+id+'][sync_prod]"]').length) {
 * if($(this).val().replace(/\s/g, '')=="")
 * $('input[name="properties['+id+'][sync_prod]"]').val(''); else
 * $('input[name="properties['+id+'][sync_prod]"]').val($(this).val()); } else {
 * $('.attrs clear '+id).append('<input type="hidden"
 * name="properties['+id+'][sync_prod]" class="sync_prod"
 * value="'+$(this).val()+'">'); } Admin.formbuilder.attr.update(this); });
 */     
    
	/*$("select[name='field_type']").on('change',function(){
		var id   = $(this).attr('data-element-id');
		
		if($('input[name="properties['+id+'][field_type]"]').length)
		{
			if($(this).val().replace(/\s/g, '')==""){
				$('input[name="properties['+id+'][field_type]"]').val('');
			} else {
				$('input[name="properties['+id+'][field_type]"]').val($(this).val());
			}
		}
		else
		{
			$('#form_builder_panel form fieldset ol li[data-id="'+id+'"] div.attrs').append('<input type="hidden" name="properties['+id+'][field_type]" class="field_type" value="'+$(this).val()+'">');
		}
	});  */

	$("select[name='sync_prod']").on('change',function(){
	   var id   = $(this).attr('data-element-id');
		if($('input[name="properties['+id+'][sync_prod]"]').length)
		{
			if($(this).val().replace(/\s/g, '')=="")
				$('input[name="properties['+id+'][sync_prod]"]').val('');
			else
				$('input[name="properties['+id+'][sync_prod]"]').val($(this).val());
		}
		else
		{
			$('#form_builder_panel form fieldset ol li[data-id="'+id+'"] div.attrs').append('<input type="hidden" name="properties['+id+'][sync_prod]" class="sync_prod" value="'+$(this).val()+'">');
		}
		Admin.formbuilder.attr.update(this);
	});
	
	/*$("[name='default_answer']").on('change',function(){
	   var id = $(this).attr('data-element-id');
		if($('input[name="properties['+id+'][default_answer]"]').length)
		{
			if($(this).val().replace(/\s/g, '')=="")
				$('input[name="properties['+id+'][default_answer]"]').val('');
			else
				$('input[name="properties['+id+'][default_answer]"]').val($(this).val());
		}
		else
		{
			$('#form_builder_panel form fieldset ol li[data-id="'+id+'"] div.attrs').append('<input type="hidden" name="properties['+id+'][default_answer]" class="default_answer" value="'+$(this).val()+'">');
		}
		  console.log($('#form_builder_panel form fieldset ol li[data-id="'+id+'"] > input[name="'+id+'"]').val());
		$('#form_builder_panel form fieldset ol li[data-id="'+id+'"] > input[name="'+id+'"]').val($(this).val());
		Admin.formbuilder.attr.update(this);
	});*/
});


function showFromHelp() {
    var dialog = $('<div id="from-help" style="display:none;overflow:hidden!important"><table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="3" cellspacing="3">	<tr>	<td>Label Fields:</td>	<td>Recommended length 50 characters</td>	</tr><tr><td>Description Fields:</td><td>Recommended length 100 characters</td></tr><tr><td> Option Fields:</td><td>Separate option names with a ; <br>Do not use any ‘ or “ within an option name</td></tr></table></div>').appendTo('body');
    // open the dialog
    dialog.dialog({
		
        // add a close listener to prevent adding multiple divs to the document
        close: function (event, ui) {
            // remove div with all data and events
            dialog.remove();
        },
        modal: true,
        title: 'From Help',
        height: 250,
        width: 450,
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "explode",
            duration: 500
        },
        create: function(event, ui) { 
			 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
		},
        buttons: [
          {
              text: "Close",
              "title":"Close",
              "class": 'btn btn-primary',
              click: function() {
                  $( this ).dialog( "close" );
              }
          },
		],
    });
}

function deleteformbuilderfield(str,ids){
    var str_ids = $('#'+ids).text();
    var final = $.trim(str_ids);
    if(confirm("Are you sure you want to Remove "+final+"?")){ 
        $("#is_change_form").val('1'); // change flag to 1
        $("#is_change_form_main").val('1'); // change flag to 1
        $(str).parent().parent().parent().remove();reorderFormBuilder();
    } 
}

/** IRT 446 **/
function show_email_field(id, element_view) 
{
    var ids = $('#hide_'+id).val();
    if($('a#view_'+id+' em').hasClass('fa-eye')){
        $('a#view_'+id+' em').attr('class','fa fa-eye-slash text-primary');
        $('a#view_'+id+' em').attr('title','Will Not Appear in Email Alerts');
        $('#hide_'+id).val('0');
    } else {
        $('a#view_'+id+' em').attr('class','fa fa-eye text-primary');
        $('a#view_'+id+' em').attr('title','Will Appear in Applicable Email Alerts');
        $('#hide_'+id).val('1');
    }
}
