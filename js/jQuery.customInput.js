/**
 * --------------------------------------------------------------------
 * jQuery customInput plugin
 * Author: Maggie Costello Wachs maggie@filamentgroup.com, Scott Jehl, scott@filamentgroup.com
 * Copyright (c) 2009 Filament Group 
 * licensed under MIT (filamentgroup.com/examples/mit-license.txt)
 * --------------------------------------------------------------------
 */

(function ($) {

	$.fn.customInput = function(){
		return $(this).each(function(){	
			if($(this).is('[type=checkbox],[type=radio]')){
				var input = $(this);
				//console.log(input.attr('id'));
				// get the associated label using the input's id
				var label = $('label[for="'+input.attr('id')+'"]');
				if($.trim(label.html())==''){
                                    var titleContent = '';                                    
                                    if($(input).attr('title') === undefined){										
										if($(input).parent().attr('title') === undefined){																						
											//console.log('nelson'+input.attr('id'));
										}else{											
											titleContent = $(input).parent().attr('title');
										}
                                    }else{
                                        titleContent = $(input).attr('title');
                                    }
                                    if(titleContent != ''){
										label.html('&nbsp;<span class="sr-only">'+titleContent+'</span>');
									}
					
				}
				
				// wrap the input + label in a div 
				if($(input).parent().hasClass('custom-'+ input.attr('type') +'')){
				}else{
				input.add(label).wrapAll('<div class="custom-'+ input.attr('type') +'"></div>');
				}
				// necessary for browsers that don't support the :hover pseudo class on labels
				label.hover(
					function(){ $(this).addClass('hover'); },
					function(){ $(this).removeClass('hover'); }
				);
				input.unbind('keyup').bind('keyup',function(e){
					 var code = e.keyCode || e.which;
					 if(code == 13 || code == 32) {
					   $(this).trigger('updateState'); 
					   e.preventDefault;
					 }
				});
				
				//bind custom event, trigger it, bind click,focus,blur events					
				input.bind('updateState', function(){
					//console.log(input.is(':checked'));
					input.is(':checked') ? label.addClass('checked') : label.removeClass('checked checkedHover checkedFocus'); 
				})
				.trigger('updateState')
				.click(function(){ 
					$('input[name="'+ $(this).attr('name') +'"]').trigger('updateState'); 
				})
				.focus(function(){ 
					label.addClass('focus'); 
					if(input.is(':checked')){  $(this).addClass('checkedFocus'); } 
				})
				.blur(function(){ label.removeClass('focus checkedFocus'); });
			}
		});
	};

}(jQuery));


	
	
