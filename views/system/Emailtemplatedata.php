<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
\app\assets\SystemCustomWordingAsset::register($this);
?>
<?php $form = ActiveForm::begin(['id' => $template->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<div class="sub-heading"><a href="javascript:void(0);" title="<?= $template->email_name?>" class="tag-header-black"><?= $template->email_name?></a></div>
			  <fieldset class="one-cols-fieldset">
			   <div class="email-template-confrigration">	
				<?php if(isset($template->email_custom_subject) && $template->email_custom_subject!=""){ $template->email_custom_subject = $template->email_custom_subject; } else {  $template->email_custom_subject = $template->email_subject;}?>
				<?= $form->field($template, 'email_custom_subject',['template' => '<div class="row"><div class="col-md-12">{label}</div><div class="col-md-12">{input}{error}{hint}</div></div>'])->textInput(['maxlength'=>$Emailtmpl_len['email_custom_subject']])->label($template->getAttributeLabel('email_subject'), ['class'=>'form_label']);?>
				<div class="row">
				 <div class="form-group">
				  <div class="col-md-12"><label class="form_label required" for="EmailBody">Email Body</label></div>
			  	  <div class="col-md-12 col-sm-12 col-xs-12">	
					<div class="custom-wording-editer">
                          
					<?php if(isset($template->email_custom_body) && $template->email_custom_body!=""){ $template_email_body = (html_entity_decode(($template->email_custom_body))); } else {  $template_email_body = (html_entity_decode(($template->email_body)));} 
							$template->email_custom_body=$template_email_body;
							echo $form->field($template, 'email_custom_body',['inputOptions'=>['style'=>"display:block;width:100%;"]])->textArea(['rows'=>5,'aria-label'=>'Custom Body','maxlength'=>$Emailtmpl_len['email_custom_body']])->label(false);
					?>
						
					</div>
					<!--<div class="x_panel email-temp-config">
                            <div class="x_content">

                                <div id="alerts"></div>
                                <div class="btn-toolbar editor toolbar-justified toolbar-email-config" data-role="editor-toolbar" data-target="#editor">
                                    <div class="btn-group">
                                        <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font"><em class="fa icon-font"></em><strong class="caret"></strong></a>
                                        <ul class="dropdown-menu">
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><em class="icon-text-height"></em>&nbsp;<strong class="caret"></strong></a>
                                        <ul class="dropdown-menu">
                                            <li><a data-edit="fontSize 5"><p style="font-size:17px">Huge</p></a>
                                            </li>
                                            <li><a data-edit="fontSize 3"><p style="font-size:14px">Normal</p></a>
                                            </li>
                                            <li><a data-edit="fontSize 1"><p style="font-size:11px">Small</p></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><em class="icon-bold"></em></a>
                                        <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><em class="icon-italic"></em></a>
                                        <a class="btn" data-edit="strikethrough" title="Strikethrough"><em class="icon-strikethrough"></em></a>
                                        <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><em class="icon-underline"></em></a>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><em class="icon-list-ul"></em></a>
                                        <a class="btn" data-edit="insertorderedlist" title="Number list"><em class="icon-list-ol"></em></a>
                                        <a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><em class="icon-indent-left"></em></a>
                                        <a class="btn" data-edit="indent" title="Indent (Tab)"><em class="icon-indent-right"></em></a>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><em class="icon-align-left"></em></a>
                                        <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><em class="icon-align-center"></em></a>
                                        <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><em class="icon-align-right"></em></a>
                                        <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><em class="icon-align-justify"></em></a>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><em class="icon-link"></em></a>
                                        <div class="dropdown-menu input-append">
                                            <input class="span2" placeholder="URL" type="text" data-edit="createLink" id="heyper-span2" aria-label="URL" />
                                            <button class="btn" type="button">Add</button>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><em class="icon-undo"></em></a>
                                        <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><em class="icon-repeat"></em></a>
                                    </div>
                                </div>
									<?php if(isset($template->email_custom_body) && $template->email_custom_body!=""){ $template_email_body = (html_entity_decode(($template->email_custom_body))); } else {  $template_email_body = (html_entity_decode(($template->email_body)));} ?>
                                <div id="editor" class="editor-container" aria-label="Email Body"><?php echo html_entity_decode($template_email_body);?></div>
                                	<?php 
                                		$template->email_custom_body=$template_email_body;
										echo $form->field($template, 'email_custom_body',['inputOptions'=>['style'=>"display:none;"]])->textArea(['rows'=>5,'aria-label'=>'Custom Body','maxlength'=>$Emailtmpl_len['email_custom_body']])->label(false);
	 								?>
                            </div>
                        </div>
                    
					
					END -->
					</div>
				 </div>
				</div>
				
				<div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12"><label class="form_label required" for="EmailRecipients">Email To: Recipients</label></div>
                                            <div class="col-md-12">
                                                <div class="email-recipients-box">
                                    <fieldset>
                                        <legend class="sr-only">Email To: Recipients</legend>       
				    <ul class="email-recipients-list1">
				    <?php
                                    $disabled = "";
				    $disabled_class = "";
					if(!empty($email_recipients)){
						foreach ($email_recipients as $recipient){
							$disabled = "";
							$disabled_class = "";

							if(in_array($template->email_sort,array(10,24,25))  && $recipient->id  >2){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(1,4,8,9,20))  && $recipient->id  >3){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(5,7,12,13))  && $recipient->id  >4){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(15))  && $recipient->id  >4){
								if($recipient->id != 6){$disabled = 'disabled="disabled"';$disabled_class = "disabled";}
							}
							if(in_array($template->email_sort,array(16,18))  && $recipient->id  >2){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(19,21))  && $recipient->id !=1){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(17))  && $recipient->id  >5){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(22))  && $recipient->id >7){
								$disabled = 'disabled="disabled"';
								$disabled_class = "disabled";
							}
							if(in_array($template->email_sort,array(6))){
								if($recipient->id  >4 && $recipient->id!=9){
									$disabled = 'disabled="disabled"';
									$disabled_class = "disabled";
								}
							}
							if(in_array($template->email_sort,array(2,23))){
								if($recipient->id==1 || $recipient->id==8){
									$disabled = "";
									$disabled_class = "";
								}else{
									$disabled_class = "disabled";
									$disabled = 'disabled="disabled"';
								}
							}	
							
							/**
							 * New Media Received
							 * Date : 23-8-2016	 
							 */
							if(in_array($template->email_sort,array(3))){
								if($recipient->id==1 || $recipient->id==8)
								{
									$disabled = "";
									$disabled_class = "";
								}else{
									$disabled_class = "disabled";
									$disabled = 'disabled="disabled"';
								}
							}	
							//
							if(in_array($template->email_sort,array(14))){
								if($recipient->id==8 || $recipient->id==9){
									$disabled_class = "disabled";
									$disabled = 'disabled="disabled"';}
							}
							if($recipient->id == 8){
						 ?>
						 	<li>
						 		<input <?php if(isset($template->email_custom_recipients) && $template->email_custom_recipients!=""){ if(in_array($recipient->id,explode(",",$template->email_custom_recipients))) { echo 'checked="checked"';} } else if(isset($template->email_recipients)){ if(in_array($recipient->id,explode(",",$template->email_recipients))) { echo 'checked="checked"';} } ?> type="checkbox" id="emailrecipient_<?php echo $recipient->id?>" value="<?php echo $recipient->id?>" class="email_recipients_parent_8" name="emailrecipients[]" <?php echo $disabled?>/><label for="emailrecipient_<?php echo $recipient->id?>" class="form_label email_recipients_parent_8 <?php echo $disabled_class ?>"><?php echo $recipient->email_recipients?></label>
						 		<!-- Case Roles need to show here -->
						 		<?php //if($disabled_class != 'disabled'){ ?>
						 		<ul>
						 		<?php foreach($getcaseRole as $role  => $value){ ?>
									<li>
										<input <?php if(isset($template->email_caserole) && $template->email_caserole!=""){ if(in_array($role,explode(",",$template->email_caserole))) { echo 'checked="checked"'; } }?> type="checkbox" id="email_caserole_<?php echo $role; ?>" name="email_caserole[]" <?php echo $disabled?>  value="<?php echo $role; ?>" class="email_recipients_child_8">
										<label class="form_label email_recipients_child_8 <?php echo $disabled_class ?>" for="email_caserole_<?php echo $role; ?>"><?php echo $value; ?></label>
									</li>
								<?php } ?>	
								</ul>
								<?php //} ?>
						 	</li>
						 	<?php  } if($recipient->id == 9){ ?>
						 	<li>
						 		<input <?php if(isset($template->email_custom_recipients) && $template->email_custom_recipients!=""){ if(in_array($recipient->id,explode(",",$template->email_custom_recipients))) { echo 'checked="checked"';} } else if(isset($template->email_recipients)){ if(in_array($recipient->id,explode(",",$template->email_recipients))) { echo 'checked="checked"';} }?> type="checkbox" id="emailrecipient_<?php echo $recipient->id?>" value="<?php echo $recipient->id?>"  class="email_recipients_parent_9" name="emailrecipients[]" <?php echo $disabled?>/><label for="emailrecipient_<?php echo $recipient->id?>" class="form_label email_recipients_parent_9 <?php echo $disabled_class; ?>"><?php echo $recipient->email_recipients?></label>
						 		<!-- Teservice need to show here -->
						 		<ul>
								<?php foreach($getTeamservices as $teamservices){ ?>
									<li>
										<input <?php if(isset($template->email_teamservice) && $template->email_teamservice!=""){ if(in_array($teamservices->id,explode(",",$template->email_teamservice))) { echo 'checked="checked"';} }	?>type="checkbox" name="email_teamservice[]" id="email_teamservice_<?php echo $teamservices->id; ?>" <?php echo $disabled?> value="<?php echo $teamservices->id; ?>" class="email_recipients_child_9">
										<label class="form_label email_recipients_child_9 <?php echo $disabled_class;  ?>" for="email_teamservice_<?php echo $teamservices->id; ?>"><?php echo $teamservices->team->team_name.' - '.$teamservices->service_name; ?></label>
									</li>
								<?php } ?>		
						 		</ul>
						 	</li>	
						 <?php }else if ($recipient->id != 8 && $recipient->id != 9){ ?>
						 	<li><input <?php if(isset($template->email_custom_recipients) && $template->email_custom_recipients!=""){ if(in_array($recipient->id,explode(",",$template->email_custom_recipients))) { echo 'checked="checked"';} } else  if(isset($template->email_recipients)){ if(in_array($recipient->id,explode(",",$template->email_recipients))) { echo 'checked="checked"';} }?> type="checkbox" aria-label="<?php echo $recipient->email_recipients?>" id="emailrecipient_<?php echo $recipient->id?>" value="<?php echo $recipient->id?>" name="emailrecipients[]" <?php echo $disabled?>/><label for="emailrecipient_<?php echo $recipient->id?>" class="form_label <?php echo $disabled_class ?>"><?php echo $recipient->email_recipients?></label></li>
						 <?php } ?>
					 <?php 
						}
					 }?>
					 <!--  <li><input type="checkbox" id="checkbox1" name="checkbox1"/><label for="checkbox1">All Case members project was submitted under</label></li>-->
					 
					</ul>
                                           </fieldset>
				   </div>
				  </div>
				 </div>
				</div>
                               <div class="row">
                                   <div class="form-group">                                        
                                        <div class="col-md-12">
                                            <?= $form->field($template, 'bcc_email_recipients',['labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '6','maxlength'=>$Emailtmpl_len['bcc_email_recipients']]); ?>
                                            <p>To add more than one email address, separate emails with semicolons and no spaces.</p>
                                        </div>
                                   </div>
                               </div>
			  </div>
			  </fieldset>
			  
			  <div class="button-set text-right">
			  	 <?= Html::button('Default', ['title'=>"Default",'class' => 'btn btn-primary pull-left','id'=>'RestoreEmailTemplate']);?>
				 <?= Html::button('Fields', ['title'=>"Fields",'class' => 'btn btn-primary','onclick'=>'fieldsDialog('.$template->email_sort.');']);?>
				 <?= Html::button('Preview', ['title'=>"Preview",'class' => 'btn btn-primary','onclick'=>'emailpreview('.$template->id.');']);?>
				 <?= Html::button('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'UpdateEmailTemplate']);?>				 
			  </div>
<?php ActiveForm::end(); ?>
<script>
$(function() {
  $('input').customInput();
  /** Settings Email Templates **/	
  $('#active_form_name').val('SettingsEmailTemplate');
  
  $('input').bind('input', function(){
		$('#SettingsEmailTemplate #is_change_form').val('1'); 
		$('#SettingsEmailTemplate #is_change_form_main').val('1');
  }); 
  $('.btn-toolbar .btn').click(function(){ 
		$('#SettingsEmailTemplate #is_change_form').val('1'); 
		$('#SettingsEmailTemplate #is_change_form_main').val('1');
  }); 
  $(':checkbox').change(function(){ 
		$('#SettingsEmailTemplate #is_change_form').val('1'); 
		$('#SettingsEmailTemplate #is_change_form_main').val('1');
  });
  $('#editor').bind("input",function(){
		$('#SettingsEmailTemplate #is_change_form').val('1'); 
		$('#SettingsEmailTemplate #is_change_form_main').val('1');
  });
});
</script>
<noscript></noscript>
<script>
	/**
	 * email child
	 */
	$('.email_recipients_child_8').click(function(){
		if($('.email_recipients_child_8').is(':checked')){
			$('.email_recipients_parent_8').prop('checked',true);
			$('.email_recipients_parent_8').addClass('checked');
		} else {
			$('.email_recipients_parent_8').prop('checked',false);
			$('.email_recipients_parent_8').removeClass('checked');
		}
	});
	
	/**
	 * email recipients parent
	 */
	$('.email_recipients_parent_8').click(function(){
		if($('.email_recipients_parent_8').is(':checked')){
			$('.email_recipients_child_8').prop('checked',true);
			$('.email_recipients_child_8').addClass('checked');
		} else {
			$('.email_recipients_child_8').prop('checked',false);
			$('.email_recipients_child_8').removeClass('checked');
		}
	});
	
	/**
	 * email child
	 */
	$('.email_recipients_child_9').click(function(){
		if($('.email_recipients_child_9').is(':checked')){
			$('.email_recipients_parent_9').prop('checked',true);
			$('.email_recipients_parent_9').addClass('checked');
		} else {
			$('.email_recipients_parent_9').prop('checked',false);
			$('.email_recipients_parent_9').removeClass('checked');
		}
	});
	
	/**
	 * email recipients parent
	 */
	$('.email_recipients_parent_9').click(function(){
		if($('.email_recipients_parent_9').is(':checked')){
			$('.email_recipients_child_9').prop('checked',true);
			$('.email_recipients_child_9').addClass('checked');
		} else {
			$('.email_recipients_child_9').prop('checked',false);
			$('.email_recipients_child_9').removeClass('checked');
		}
	});
	
	
	$(function () {
		$("#settingsemailtemplate-email_custom_body").jqte({source: false});
		/*function initToolbarBootstrapBindings() {
			var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
		'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
		'Times New Roman', 'Verdana'],
				fontTarget = $('[title=Font]').siblings('.dropdown-menu');
			$.each(fonts, function (idx, fontName) {
				fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
			});
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

		function showErrorAlert(reason, detail) {
			var msg = '';
			if (reason === 'unsupported-file-type') {
				msg = "Unsupported format " + detail;
			} else {
				console.log("error uploading file", reason, detail);
			}
			$('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
				'<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
		};
		initToolbarBootstrapBindings();
		$('#editor').wysiwyg({
			fileUploadError: showErrorAlert
		});
		$('#editor').wysiwyg('document').keypress(function(e) {			
			// This will limit the charctors to maxlength
			var maxlength = parseInt($('#settingsemailtemplate-email_custom_body').attr("maxlength"));		 
			var editor_lenth = parseInt($('.editor-container').html().length);
			allowed_keys = [8, 37, 38, 39, 40, 46];						
			if(editor_lenth >= maxlength){				
				if($.inArray(e.keyCode, allowed_keys) == -1){             			
					e.preventDefault();
					e.stopPropagation();
				}
			}
		 });
	//	$('#editor').html(null).html('<?php //echo html_entity_decode($template_email_body); ?>');
		window.prettyPrint && prettyPrint();
		$('#editor').unbind('DOMSubtreeModified').bind('DOMSubtreeModified', function(event) {
			$('#settingsemailtemplate-email_custom_body').html($(this).html());
		});
		*/
		$('#UpdateEmailTemplate').bind('click',function(){
			submitForm('update');
		});
		$('#RestoreEmailTemplate').bind('click',function(){
			if (confirm('Are you sure you want to restore the system default settings?')) {
				submitForm('restore');
			}
		});
		
		function submitForm(btnValue){
			jQuery.ajax({
				   url: jQuery('form#<?php echo $template->formName()?>').attr('action'),
				   type: 'post',
				   data   : jQuery('form#<?php echo $template->formName()?>').serialize()+'&btnSubmit='+btnValue,
				   beforeSend:function (data) { 
//                                       showLoader(); $('.btn').attr('disabled','disabled');
                                   },
				   success: function (data) {
					   if(data=='OK') {
						   showEmailTemplate(<?=$template->id?>,<?=$template->email_sort?>);
					   } else {
                                                $('.btn').removeAttr('disabled','disabled');
                                                if(data.bcc_email_recipients.length != 0){
                                                    $('.field-settingsemailtemplate-bcc_email_recipients').addClass('has-error');
                                                    $('.field-settingsemailtemplate-bcc_email_recipients .help-block').html(data.bcc_email_recipients[0]);                                                    
                                                }                                                
					   }
				   }
			});
		}
	});
	
	function emailpreview(id){
		jQuery.ajax({
		       url: baseUrl +'/system/emailpreview&id='+id,
		       type: 'post',
		       data   : jQuery('form#<?php echo $template->formName()?>').serialize(),
		       beforeSend:function (data) {showLoader();},
		       success: function (data) {
		    	   hideLoader();
		    	   jQuery('#email_preview').html(null).html(data);
		    	   $( "#preview-dialog" ).dialog({
		    		      width: "60em",
		    		      modal: true,
		    		      buttons: [
							{
		    		            text: "Close",
		    		            "class": 'btn btn-primary',
		    		            click: function() {
		    		                $( this ).dialog( "close" );
		    		            }
		    		        },
		    		    ],
		    		    close: function() {
		    		        // Close code here (incidentally, same as Cancel code)
		    		    }
		    		    });
		       }
		  });
	}
	function fieldsDialog(email_sort)
	{
		jQuery.ajax({
		       url: baseUrl +'/system/emailfields&email_sort='+email_sort,
		       type: 'post',
		       data   : jQuery('form#<?php echo $template->formName()?>').serialize(),
		       beforeSend:function (data) {showLoader();},
		       success: function (data) {
		    	   hideLoader();
		    	   jQuery('#fields_content').html(null).html(data);
		    	   $( "#field-dialog" ).dialog({
		    		      width: "50em",
		    		      height:456,
		    		      title:'Select Fields',
		    		      modal: true,
		    		      buttons: [
							{
							    text: "Cancel",
							    title:"Cancel",
							    "class": 'btn btn-primary',
							    click: function() {
								    $( this ).dialog( "close" );
							    }
							},
							{
							    text: "Add To Subject",
							    title:"Add To Subject",
							    "class": 'btn btn-primary',
							    click: function() {
								    var selected_fields="";
								    $('.fieldschk:checked').each(function(){
									    if(selected_fields==""){selected_fields=this.value}else{selected_fields=selected_fields+'-'+this.value}
									});
									if(selected_fields!=""){
										$('#settingsemailtemplate-email_custom_subject').val($('#settingsemailtemplate-email_custom_subject').val()+'-'+selected_fields);
										$('#SettingsEmailTemplate #is_change_form').val('1');
										$('#is_change_form_main').val('1');
										$( this ).dialog( "close" );
									}
								}
							},
							{
							    text: "Add To Body",
							    title: "Add To Body",
							    "class": 'btn btn-primary',
							    click: function() {
								 	var selected_fields="";
									trigger = 'Body';
								    $('.fieldschk:checked').each(function(){
									    if(selected_fields==""){ selected_fields=this.value }else{ selected_fields=selected_fields+'-'+this.value }
									});
								    if(selected_fields!=""){
									   // $('#settingsemailtemplate-email_custom_body').append(selected_fields);
									    //updated_val=$("#settingsemailtemplate-email_custom_body").text()+" "+selected_fields;
										updated_val=$('.jqte_editor').html()+" "+selected_fields;
									    $("#settingsemailtemplate-email_custom_body").jqteVal(updated_val);
									    $('#SettingsEmailTemplate #is_change_form').val('1');
									    $('#is_change_form_main').val('1');
									    $( this ).dialog( "close" );
									}
							    }
							},   
		    		    ],
		    		    close: function() {
						    // Close code here (incidentally, same as Cancel code)
		    		    }
		    		    });
		       }
		  });
	}
</script>
<noscript></noscript>
