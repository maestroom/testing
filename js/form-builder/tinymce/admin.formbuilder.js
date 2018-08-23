var host = window.location.href;//.hostname
var hpath="";
if(host.indexOf('index.php'))
{
	hpath=host.substr(0,host.indexOf('index.php')); 
}

	var Admin = {}; //Stripped from Admin System
	var tinyMCE = false; //Placeholder until tinyMCE is loaded at end of DOM.
	
	$(document).ready(function(){
		Admin.formbuilder.init();
	});
	
	
Admin.formbuilder = {
		BASEURL: hpath+'/formBuilder/formbuilder.php',
		PREVIEWURL: hpath+'/formBuilder/preview.php',
		init: function()
		{
			Admin.formbuilder.layout('body');
			//Admin.formbuilder.tinymce();
		},
		layout: function(e)
		{
			var $active_layout = $(e);
			
			$active_layout.find('form[id=""]').each(function(){
				$(this).attr('id','f'+randomString(50)); //an ID for every form.
			});
			
			$active_layout.find('.last-child').removeClass('last-child'); //meh, safety dance
			
			$active_layout.find('ul,ol').each(function(){
				$(this).children('li:last').addClass('last-child');
			});
			
			$active_layout.children('li:last').addClass('last-child'); //incase the element itself is a ul or ol
			
			//$active_layout.find('.tooltip').tooltip({track: true, delay: 0, showURL: false, fade: 0, showBody: " - "});
			
			$active_layout.find('.datepicker').datepicker({dateFormat: 'mm-dd-yy', duration: '', showAnim: 'fold'});
			
			$active_layout.find("#form_builder_toolbox li").unbind("click").bind("click",function(i){
				var host = window.location.href;//.hostname
				var hpath="";
				if(host.indexOf('index.php'))
				{
					hpath=host.substr(0,host.indexOf('index.php')); 
				}
					
					var into = $("#form_builder_panel ol");
					var type = $(this).attr('id');
					var e = this;
					$(this).addClass('loading');
					$.get( hpath+'/formBuilder/formbuilder.php?action=element&type='+type+'&nocache='+Math.random(),function(result){
						$(e).removeClass('loading');
						$(into).append(result);//$(into).prepend(result);
						var $newrow = $(into).find('li:last');//find('li:first');
						//style
						Admin.formbuilder.editors();
						Admin.formbuilder.properties($newrow);
						Admin.formbuilder.layout($newrow);
						//show
						$newrow.hide().slideDown('slow');
						$(into).sortable("refresh");
						var sorder="";
						$("#form_builder_panel ol li").each(function(){ //new code for sorting
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
					});
					
			});
			$active_layout.find("#form_builder_toolbox1 li").unbind("click").bind("click",function(i){
				
				
				var into = $("#form_builder_panel ol");
				var type = $(this).attr('id');
				var e = this;
				$(this).addClass('loading');
				$.get(Admin.formbuilder.BASEURL+'?action=element&type='+type+'&nocache='+Math.random(),function(result){
					$(e).removeClass('loading');
					$(into).append(result);//$(into).prepend(result);
					var $newrow = $(into).find('li:last');//find('li:first');
					//style
					Admin.formbuilder.init();
					Admin.formbuilder.editors();
					Admin.formbuilder.properties($newrow);
					Admin.formbuilder.layout($newrow);
					//show
					$newrow.hide().slideDown('slow');
					$(into).sortable("refresh");
					var sorder="";
					$("#form_builder_panel ol li").each(function(){ //new code for sorting
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
				});
				
		});
		$active_layout.find("#form_builder_panel ol").sortable({
				cursor: 'ns-resize',
				axis: 'y',
				handle: '.handle',
				start: function(e,ui) {
					$('.wysiwyg').each(function(){
						var name = $(this).attr('name');
						if (name) {
							if (tinyMCE.get(name)) {
								tinyMCE.execCommand('mceRemoveControl', false, name);
							}
						}
					});
				},
				stop: function(e,ui) {
					var sorder="";
					$("#form_builder_panel ol li").each(function(){ //new code for sorting
							if($(this).attr('data-id'))
							{
								if(sorder=="")
									sorder=$(this).attr('data-id');
								else
									sorder= sorder + "," +$(this).attr('data-id');
							}
					});
					$('#sort_order').val('').val(sorder);
					Admin.formbuilder.editors();
				}
			});
			
			$active_layout.find('div.dialog').each(function(){
				
				//$.metadata.setType("class");
				var w = 400;
				var h = 200;
				
				$(this).dialog({
					modal: true,
					zIndex: 400000, /* TinyMCE grief. Their default is literally 300000... Fail*/
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
			
			$active_layout = null; //destroy
		},
		properties: function(e)
		{
			
			$(e).find('a.properties').unbind("click").bind("click",function(){
				$('#form_builder_properties').html("");
				//mohsin
				$('.wrapper').css('opacity',0.40);
				var id = $(this).parents('label:first').attr('for');
				if(id == undefined)
				{
					var id=$(this).find('span').attr('for');
				}
				
				
				//mohsin//
				$('#form_builder_properties').html('<span class="icon loading">Loading...</span>');
				var id = $(this).parents('label:first').attr('for');
				if(id == undefined)
				{
					var id=$(this).find('span').attr('for');
				}
				//alert(id);
				//var label=$(this).find('span').find('span').html();
				var label=$(this).parent().parent().parent().find('label a').html();
				//alert($(this).parent().parent().parent().find('label a').html());
				if(label==undefined)
					label=$(this).html();
				var title = $(this).attr('rel');
				var req=1;
				var desc='';
				var val='';
				var vals=''; //alert($('#no_load_prev_'+id).val());
				if($('#'+id+'_values') != undefined)
					vals=$('#'+id+'_values').val();
				if($('#'+id+'_value') != undefined)
					val=$('#'+id+'_value').val();
				if($('#description_'+id) != undefined)
					desc=$('#description_'+id).val();
				if($('#required_'+id) != undefined)
					req=$('#required_'+id).val();
				if($('#no_load_prev_'+id) != undefined)
					no_load_prev=$('#no_load_prev_'+id).val();
				$('#form_builder_panel li.on').removeClass('on');
				
				$.get(Admin.formbuilder.BASEURL+'?action=properties&type='+title+'&id='+id+'&label='+label+'&req='+req+'&no_load_prev='+no_load_prev+'&desc='+desc+'&vals='+vals+'&val='+val+'&nocatch='+Math.random(),function(result){
					if(result.replace(/\s+/g, ' ') != "")
					{	
						
					$('#form_builder_properties').html(null).html(result);
					$('#form_builder_properties').dialog({
						width: '450px',
						height:'auto',
						title: 'Edit',
						close: function(event, ui) { 
						$('.wrapper').css('opacity',1); 
						$('#form_builder_properties').html("");
						},
						dialogClass:'edit',
						buttons: {
					        'Confirm': function() {
								$(this).dialog('close');
								$('.wrapper').css('opacity',1);
							},
					        'Cancel': function() {
					        	$(this).dialog('close');
					        	$('.wrapper').css('opacity',1);
					        }
						},
						open: function() {
							$('.ui-dialog-buttonpane').find('button:contains("Cancel")').attr('class','button_small1');
				            $('.ui-dialog-buttonpane').find('button:contains("Confirm")').attr('class','button_small1');
				            setTimeout(function(){addMCE(id+'_text');},1000);
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
					$( "input[name='label']").focus(); /*focus to label*/
					/*label value force to a html*/
					$( "input[name='label']" ).die("keyup");
					/*label value force to a html*/
					if($('#form_builder_properties_edit') != undefined)
					{
						
						$('#form_builder_properties_edit').append(result);
						Admin.formbuilder.attr.get(id);
						Admin.formbuilder.layout('#form_builder_properties');
						$('#form_builder_properties_edit li *:input').unbind("keyup").bind("keyup",function(){
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
					//mohsin
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
						}
						if(!this.checked)
						{
							if($(this).attr('name')=='required')
								$('#required_'+id).val(1);
							if($(this).attr('name')=='no_load_prev')
								$('#no_load_prev_'+id).val(0);
						}
					});
					/*additional features add by MB*/
					$("input[name='label']").die('keyup');
					$("input[name='label']").live('keyup',function(){
						$($(this).attr('rel')).html('').html($(this).val());
						if($(this).val().replace(/\s/g, '')=="")
						{
							$($(this).attr('rel')).html('').html('No Label');
						}
						//find attr and set value if not exist
						if($('input[name="properties['+id+'][label]"]').length)
						{
							if($(this).val().replace(/\s/g, '')=="")
								$('input[name="properties['+id+'][label]"]').val('No Label');
							else
								$('input[name="properties['+id+'][label]"]').val($(this).val());
						}
						else
						{
							$('.attrs clear '+id).append('<input type="hidden" value="'+$(this).val()+'" class="label" name="properties['+id+'][label]">');
						}
						Admin.formbuilder.attr.update(this);
					});
					$("input[name='description']").die('keyup');
					$("input[name='description']").live('keyup',function(){
						$($(this).attr('rel')).html('').html($(this).val());
						if($(this).val().replace(/\s/g, '')=="")
						{
							$($(this).attr('rel')).html('');
						}
						if($('input[name="properties['+id+'][description]"]').length)
						{
							if($(this).val().replace(/\s/g, '')=="")
								$('input[name="properties['+id+'][description]"]').val('');
							else
								$('input[name="properties['+id+'][description]"]').val($(this).val());
						}
						else
						{
							$('.attrs clear '+id).append('<input type="hidden" value="'+$(this).val()+'" class="description" id="description_'+id+'" name="properties['+id+'][description]">');
						}
						Admin.formbuilder.attr.update(this);
					});
					$("textarea[name='values']").die('keyup');
					$("textarea[name='values']").live('keyup',function(){
						if($('input[name="properties['+id+'][values]"]').length)
						{
							if($(this).val().replace(/\s/g, '')=="")
								$('input[name="properties['+id+'][values]"]').val('');
							else
								$('input[name="properties['+id+'][values]"]').val($(this).val());
						}
						else
						{
							$('.attrs clear '+id).append('<input type="hidden" name="properties['+id+'][values]" class="values" value="'+$(this).val()+'">');
						}
						Admin.formbuilder.attr.update(this);
					});
					$('textarea[name='+id+']').die('keyup');
					$('textarea[name='+id+']').live('keyup',function(){
						if($(this).attr('class')=='wysiwyg')
						{
							str=$(this).val();
							var find = '\n';
							var re = new RegExp(find, 'g');
							str = str.replace(re, '<br>');
							$('#'+id).each(function(){
								if($(this).get(0).tagName=='A')
									$(this).html('').html(str);
							});
						}	
					});
					
					/* End of additional features add by MB*/
					
			    	//mohsin
					delete result;
				}
			});
				
				return false;
			});
		},
		tinymce: function(e)
		{
			//alert($(e));
			//if (!tinyMCE)
			{		
				
				tinyMCE.init({
					// General options
				mode : "textareas",
				theme : "advanced",
				plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
				
				// Theme options
				theme_advanced_buttons1 : "bold,italic,underline,|,fontsizeselect,",//$service_task
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_buttons4 : "",
				theme_advanced_toolbar_location : "bottom",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				onchange_callback : "myCustomOnChangeHandler",
				editor_selector : "wysiwyg",
				editor_deselector : "wysiwygNoEditor",


				
				// Skin options
				skin : "o2k7",
				skin_variant : "silver",
				
				// Example content CSS (should be your site CSS)
				content_css : "css/example.css",
				
				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "js/template_list.js",
				external_link_list_url : "js/link_list.js",
				external_image_list_url : "js/image_list.js",
				media_external_list_url : "js/media_list.js",
				
				// Replace values for the template plugin
				
				});

				//});
			} 
			
		},
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
					if (!tinyMCE.get(name)) tinyMCE.execCommand('mceAddControl', false, name);
				}
			});
		},
		attr: {
			get: function(id)
			{
				$('.attrs.'+id+' input').each(function(){
					var val = $(this).val();
					var id = $(this).attr('class');
					if (val) {
						$('#form_builder_properties input[name='+id+']').val(val);
						$('#form_builder_properties textarea[name='+id+']').val(val);
					}
				});
				
				
			},
			update: function(e)
			{
				//alert(e);
				var $element = $(e);
				
				var name = $element.attr('name');
				//alert(name);
				var id = $element.parents('li:not(.sub):first').attr('class');
				var rel = $element.attr('rel');
				var value = $element.val();
				var type = $element.attr('class');
				var found = false;
				
				$('body').data(rel,{'name':name,'value':value});
				
				
				$('div.attrs.'+id+' input').each(function(){
					if ($(this).attr('name') == "properties["+id+"]["+name+"]")
					{
						
						$(this).val(value);
						$element.val(value);
						//alert($element.val());
						found = true;
					}
				});
				
				if (!found) {
					//alert($('div.attrs.'+id).attr('name'));
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
						value = value.split(';');
					break;
					case 'checkbox':
						value = value.split(';');
					break;
					case 'radio':
						value = value.split(';');
					break;
					default: break;
				}
				
				if (rel && value) {
					//alert(value);
					if (!$.isArray(value)) {
						var block = $(rel).not(':input').length;
						
						if (block == 0) $(rel).val(value);
						else $(rel).html(value);
					} else {
						//its an array, oh dear!
						switch (type)
						{
							case 'dropdown':
								var newc = '';
								for (i in value) newc += '<option>'+value[i]+'</option>';
								$(rel).html(newc);
								break;
							case 'radio':
								var newc = '';
								for (i in value) newc += '<input type="radio" name="temp['+name+'][]"> '+value[i]+'<br/>';
								$(rel).html(newc);
								break;
							case 'checkbox':
								var newc = '';
								for (i in value) newc += '<input type="checkbox" name="temp['+name+'][]"> '+value[i]+'<br/>';
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
			$('textarea.wysiwyg').each(function(){
				var name = $(this).attr('name');
				//alert(name);
				if (name) {
					var contents = $('#'+name+'_ifr').contents().find("body").html();//tinyMCE.get(name).getContent();
					
				}
				$(this).val(contents);
			});
			
			var data = $('#form_builder_panel form').serialize();
			$.post(Admin.formbuilder.PREVIEWURL,data,function(result){
				$('#form_builder_preview').html(result);
				Admin.formbuilder.dialog('form_builder_preview');
			});
			$('#form_builder_preview').dialog({
				title: 'Preview',
				close:function(event,ui){ $('.wrapper').css('opacity',1); },
				width: '70%',
				height:'auto',
				dialogClass:'preview',
				//overlay: "background-color: red; opacity: 1",
				buttons: {
			        'Close': function() {
						$('.wrapper').css('opacity',1);
			        	$(this).dialog('close');
			        },
			        'Test': function() {
						$('#form_builder_preview form').submit();
			        }
				},
				open: function() {
					$('.ui-dialog-buttonpane').find('button:contains("Close")').attr('class','button_small');
		            $('.ui-dialog-buttonpane').find('button:contains("Test")').attr('class','button_small');
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
					"Confirm": function() { 
						if (callback) callback(options);
						$(this).dialog("close");
						$('.ui-dialog-titlebar .ui-widget-header .ui-corner-all .ui-helper-clearfix').dialog("close");
						$(this).parents('div:first').remove();
					}, 
					"Cancel": function() {
						$(this).dialog("close");
						$(this).parents('div:first').remove();
					} 
				},
				open: function() {
		            $('.ui-dialog-buttonpane').find('button:contains("Cancel")').attr('class','button');
		            $('.ui-dialog-buttonpane').find('button:contains("Confirm")').attr('class','button');
		        }
			});
		},
		edit: function(e)
		{
			var $element = $(e);
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
		if (tinyMCE.get(fieldID)){
	    tinyMCE.execCommand('mceFocus', false, fieldID);
	    tinyMCE.execCommand('mceRemoveControl', false, fieldID);
		}
	}

	function addMCE(fieldID) {
		//Admin.formbuilder.layout('body');
		//Admin.formbuilder.tinymce();
		//removeTinyMCE (fieldID);
		tinyMCE.init({
			// General options
		mode : "exact",
		theme : "advanced",
		elements : fieldID,
		plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,|,fontsizeselect,",//"bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,|,bullist,numlist,|,undo,redo",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		onchange_callback : "myCustomOnChangeHandler",
		editor_selector : "wysiwyg",
		editor_deselector : "wysiwygNoEditor",


		
		// Skin options
		skin : "o2k7",
		skin_variant : "silver",
		
		// Example content CSS (should be your site CSS)
		content_css : "css/example.css",
		
		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "js/template_list.js",
		external_link_list_url : "js/link_list.js",
		external_image_list_url : "js/image_list.js",
		media_external_list_url : "js/media_list.js",
		
		// Replace values for the template plugin
		setup : function(ed) {
			ed.onInit.add(function(ed) {
				//alert('Editor is done: ' + ed.id);
				
				tinyMCE.execCommand('mceRepaint');
			});
			}
		});
		tinyMCE.execCommand('mceAddControl', false,fieldID);
		
			
	}
	function myCustomOnChangeHandler(inst) {
		idss=$(inst).attr('id');//.val();
		idss=idss.replace("_text","");
		$('#'+idss+'_text_val').val(inst.getBody().innerHTML);
		$('label[for='+idss+'] a').html(inst.getBody().innerHTML);
	}
	function htmlEntities(str) {
	    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
