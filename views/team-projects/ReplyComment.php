<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Comments;
use app\models\Options;
use app\components\IsataskFormFlag;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js',['depends' => [\yii\web\JqueryAsset::className()],'position'=>\yii\web\View::POS_HEAD]);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js',['depends' => [\yii\web\JqueryAsset::className()],'position'=>\yii\web\View::POS_HEAD]);
\app\assets\SystemCustomWordingAsset::register($this);

$js = <<<JS
// get the form id and set the event
$(function() {
  $('.multi-pt').MultiFile({
    STRING: { 
      remove:'<em class="fa fa-close text-danger" title="Remove"></em>', 
    },
	maxsize:102400	
  });
});
JS;
$this->registerJs($js);
?>

			<fieldset class="one-cols-fieldset project-comments post-project-comments">
			<?php $form = ActiveForm::begin(['id' => 'add-comments-form-'.$id,'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
			 <?= IsataskFormFlag::widget(); // change flag ?>
			 <!-- <div class="sub-heading">Add Comment</div>-->
			 <div class="comments-area">
				 <div class="x_panel custom-wording-editer">
                    <div class="x_content">
						<?= $form->field($model, 'comment',['inputOptions'=>['style'=>"display:block;width:100%;"],'template' => "<div class='col-md-0'>{label}</div><div class='col-md-12'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['id'=>'add_comment_'.$id,'rows'=>2,'placeholder'=>"Leave a comment..."])->label(false);?>
					</div>
				</div>
			 	<!--<div class="x_panel custom-wording-editer">
                            <div class="x_content">
                                <div id="alerts"></div>
                                <div class="btn-toolbar editor toolbar-justified" data-role="editor-toolbar" data-target="#editor_<?=$id?>">
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
                                        <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><em class="icon-undo"></em></a>
                                        <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><em class="icon-repeat"></em></a>
                                    </div>
                                </div>
								<?= $form->field($model, 'comment',['inputOptions'=>['style'=>"display:none;"],'template' => "<div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['id'=>'add_comment_'.$id,'rows'=>2,'placeholder'=>"Leave a comment..."])->label(false);?>
                                <div id="editor_<?=$id?>" class=" editor-container" contentEditable=true data-text="Leave a comment..."></div>
                                </div>
                        </div>-->
				
			 		<div class="editor-attached">
				 		<div class="col-sm-12">
				  			<?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}<div id='comments-attachment_list' class='MultiFile-list text-left'>(File Size can't exceed 100MB)</div>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,"id"=>uniqid(), 'class'=>'multi-pt','title'=>'Choose File']) ?>
						</div>
				   </div>
			   </div>
			 </div>
			 <?php ActiveForm::end(); ?>
	</fieldset>
</div>
<script>
$('document').ready(function(){
	$('#active_form_name').val('add-comments-form-<?= $id ?>'); // active form name
});
$('a.btn').on("click",function(){ 
	$('#add-comments-form-<?=$id?> #is_change_form').val('1'); 
	$('#add-comments-form-<?=$id?> #is_change_form_main').val('1');
});
/*$('#editor_<?=$id?>').bind('input',function(){
	$('#add-comments-form-<?=$id?> #is_change_form').val('1'); 
	$('#add-comments-form-<?=$id?> #is_change_form_main').val('1');
});*/
$('input[type=file]').change(function() {
	$('#add-comments-form-<?=$id?> #is_change_form').val('1'); 
	$('#add-comments-form-<?=$id?> #is_change_form_main').val('1');
});
$(function () {
	$('#add_comment_<?php echo $id?>').jqte({source: false});
	/*function initToolbarBootstrapBindings() {
		var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
	'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
	'Times New Roman', 'Verdana'],
			fontTarget = $('[title=Font]').siblings('.dropdown-menu');
		$.each(fonts, function (idx, fontName) {
			fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
		});
		//$('a[title]').tooltip({
//			container: 'body'
//		});
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
			var editorOffset = $('#editor_<?=$id?>').offset();
			$('#voiceBtn').css('position', 'absolute').offset({
				top: editorOffset.top,
				left: editorOffset.left + $('#editor_<?=$id?>').innerWidth() - 35
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
	$('#editor_<?=$id?>').wysiwyg({
		fileUploadError: showErrorAlert
	});
	window.prettyPrint && prettyPrint();
	$('#editor_<?=$id?>').unbind('DOMSubtreeModified').bind('DOMSubtreeModified', function(event) {
		$('#add_comment_<?php echo $id?>').text($(this).html());
		document.getElementById("add_comment_<?php echo $id?>").value = $(this).html();
	});*/
});
//document.getElementById("editor_<?php echo $id?>").focus();
</script>
<noscript></noscript>
