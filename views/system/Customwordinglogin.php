<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
use app\models\User;
\app\assets\SystemCustomWordingAsset::register($this);

$js = <<<JS
// get the form id and set the event
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
   var form = $(this);
		$.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            beforeSend : function()    {
            	$('#submitCustomWordingLogin').attr('disabled','disabled');
            },
            success: function (response){
            	if(response == 'OK')
             		commonAjax(baseUrl +'/system/custom-wording-login','admin_main_container');
            	else{
                	$('#submitCustomWordingLogin').removeAttr("disabled");
            	}
            },
            error  : function (){
                console.log('internal server error');
            }
        });
		return false;
   // do whatever here, see the parameter \$form? is a jQuery Element to your form
}).on('submit', function(e){
    e.preventDefault();
});
JS;

$this->registerJs($js);
?>

		   <div class="right-main-container slide-open" id="maincontainer">
		   
			<fieldset class="two-cols-fieldset">
			<div class="administration-main-cols">
			 <div class="administration-lt-cols pull-left">
			 <button title="Expand/Collapse" id="controlbtn" class="slide-control-btn" onclick="WorkflowToggle();" aria-label="Expand or Collapse">
					<span>&nbsp;</span>
				</button>
			  <ul>
			   <li><a class="admin-main-title" href="javascript:commonAjax(baseUrl +'/system/custom-wording-login','admin_main_container');" title="Custom Wording"><em class="fa fa-folder-open text-danger" title="Custom Wording"></em> Custom Wording </a>
                                <div>
                                    <ul class="sub-links">
                                     <?php if ((new User)->checkAccess(8.0211)) { ?><li class="active customwordlogin wordingloginlinks"><a href="javascript:CustomWordingInstructionLoginSelect();" class="" title="Login Page"><em title="Login Page" class="fa fa-edit text-danger"></em> Login Page Top section</a></li><?php } ?>
                                     <?php if ((new User)->checkAccess(8.0212)) { ?><li class="customwordloginbottom wordingloginlinks"><a href="javascript:CustomWordingLoginBottomSelect();" class="" title="Login Page Bottom section"><em title="Login Page Bottom section" class="fa fa-edit text-danger"></em> Login Page Bottom section</a></li><?php } ?>
                                     <li class="customwordinstructionheader wordingloginlinks"><a href="javascript:CustomWordingInstructionSelect();" class="" title="Instruction Form - Header"><em title="Instruction Form - Header" class="fa fa-edit text-danger"></em> Instruction Form - Header</a></li>
                                     <li class="customwordinstructionfooter wordingloginlinks"><a href="javascript:CustomWordingInstructionFooterSelect();" class="" title="Instruction Form - Footer"><em title="Instruction Form - Footer" class="fa fa-edit text-danger"></em> Instruction Form - Footer</a></li>
                                     <?php if ((new User)->checkAccess(8.0213)) { ?><li class="customwordreportheader wordingloginlinks"><a href="javascript:CustomWordingReportHeaderSelect();" class="" title="Report Form - Header"><em title="Report Form - Header" class="fa fa-edit text-danger"></em> Report Form - Header</a></li><?php } ?>
                                    </ul>
				</div>
			   </li> 
			  </ul>
			 </div>
			<div class="administration-rt-cols pull-right" id="admin_right">
                            <?php if ((new User)->checkAccess(8.0211)) { ?>
                            <?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
                            <?= IsataskFormFlag::widget(); // change flag ?>
			<div class="x_panel custom-wording-editer">
                            <div class="x_content">
                                <label class="screenreader" for="settings-fieldtext" style="height:0px;padding:0px;margin:0px;">Custom Wording</label>
								<?php $model->field= 'loginpage';
									echo $form->field($model, 'field')->hiddenInput()->label(false);
									if($model->fieldtext === null){ $model->fieldtext = '';}
									echo $form->field($model, 'fieldtext',['inputOptions'=>['style'=>"display:block;width:100%"]])->textArea(['rows'=>5,'maxlength'=>$settings_length['fieldtext']])->label(false);
	 							?>
								 <div id="alerts"></div>
			</div>
			</div>					 			

			<!--<div class="x_panel custom-wording-editer">
                            <div class="x_content">
                                <div id="alerts"></div>
                                <div class="btn-toolbar editor toolbar-justified" data-role="editor-toolbar" data-target="#editor">
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
                                        <a title="Hyperlink" data-toggle="dropdown" class="btn dropdown-toggle"><em class="icon-link"></em></a>
                                        <div class="dropdown-menu input-append"><input type="text" data-edit="createLink" placeholder="URL" id="createLink_txt" class="span2"><label for="createLink_txt" class="screenrader">URL</label><button type="button" class="btn">Add</button></div>
                                    </div>
                                    

                                    <div class="btn-group">
                                        <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><em class="icon-undo"></em></a>
                                        <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><em class="icon-repeat"></em></a>
                                    </div>
                                </div>
                                
                                <div id="editor" class=" editor-container">
                                    <?= $model->fieldtext ?>
                                </div>
	 							<br />
                            </div>
                        </div>
				-->
				<div class="button-set text-right">
				<?= Html::Button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','id'=>'CancelCustomWording']) ?>	
				<?= Html::submitButton('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitCustomWordingLogin']) ?>
				</div>
				<?php ActiveForm::end(); ?>
			 </div>
			</div>
			
			<?php } ?>
			</fieldset>
			
		   </div>
		  
<script>
	/* change evnet */
	$('.btn-toolbar .btn').click(function(){ 
		$('#Settings #is_change_form').val('1'); 
		$('#Settings #is_change_form_main').val('1');
	}); 
	/*$('#editor').bind("input", function(){
		$('#Settings #is_change_form').val('1'); 
		$('#Settings #is_change_form_main').val('1');
	});*/ 
	$('document').ready(function(){
		$('#active_form_name').val('Settings');
	}); 
	$('#CancelCustomWording').click(function(event){
		var chk = checkformstatus(event);
		if(chk == true)
			commonAjax(baseUrl +'/system/custom-wording-login','admin_main_container');
	});
	$(function () {
		$("#settings-fieldtext").jqte({source: false});
		/*function initToolbarBootstrapBindings() {
			var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
		'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
		'Times New Roman', 'Verdana'],
				fontTarget = $('[title=Font]').siblings('.dropdown-menu');
			$.each(fonts, function (idx, fontName) {
				fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
			});
			//$('a[title]').tooltip({
//				container: 'body'
//			});
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
		/*initToolbarBootstrapBindings();
		$('#editor').wysiwyg({
			fileUploadError: showErrorAlert
		});
		window.prettyPrint && prettyPrint();
		$('#editor').unbind('DOMSubtreeModified').bind('DOMSubtreeModified', function(event) {
			$('#settings-fieldtext').html($(this).html());
		});
		$('#editor').wysiwyg('document').keypress(function(e) {			
			// This will limit the charctors to maxlength
			var maxlength = parseInt($('#settings-fieldtext').attr("maxlength"));		 
			var editor_lenth = parseInt($('.editor-container').html().length);			 						 			
			allowed_keys = [8, 37, 38, 39, 40, 46];						
			if(editor_lenth >= maxlength){				
				if($.inArray(e.keyCode, allowed_keys) == -1){             			
					e.preventDefault();
					e.stopPropagation();
				}
			}
		 });*/		
	});
	//document.getElementById("editor").focus();
</script>
<noscript></noscript>
