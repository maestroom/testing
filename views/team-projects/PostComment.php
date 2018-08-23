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
 $('#Comments').ajaxForm({ 
   	beforeSubmit: function() {
		showLoader();
    },                
   	success: SubmitSuccesful,
});
});
JS;
$this->registerJs($js);
?>
<div class="right-main-container">
    <fieldset class="one-cols-fieldset project-comments post-project-comments">
    <?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype' => 'multipart/form-data']]); ?>
     <?= IsataskFormFlag::widget(); // change flag ?>

     <!-- <div class="sub-heading">Add Comment</div>-->
     <div class="comments-area">
			 	<div class="x_panel custom-wording-editer">
                            <div class="x_content">
				 <?= $form->field($model, 'comment',['inputOptions'=>['style'=>"display:block;width:100%"],'template' => "<div class='col-md-0'><label class='sr-only' for='comments-comment'>Comment</label></div><div class='col-md-12' style='padding:0px!important;'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>2,'placeholder'=>"Leave a comment...",'aria-label'=>'Leave a comment...'])->label(false);?>
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
                                            <li>
                                                <a data-edit="fontSize 5"><p style="font-size:17px">Huge</p></a>
                                            </li>
                                            <li>
                                                <a data-edit="fontSize 3"><p style="font-size:14px">Normal</p></a>
                                            </li>
                                            <li>
                                                <a data-edit="fontSize 1"><p style="font-size:11px">Small</p></a>
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
											<label for="create_link" style="display:none">&nbsp;</label>
                                            <input id="create_link" class="span2" placeholder="URL" type="text" data-edit="createLink" />
                                            <button class="btn" type="button">Add</button>
                                        </div>
                                    </div> 
                                    <div class="btn-group">
                                        <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><em class="icon-undo"></em></a>
                                        <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><em class="icon-repeat"></em></a>
                                    </div>
                                </div>
								<?= $form->field($model, 'comment',['inputOptions'=>['style'=>"display:none;"],'template' => "<div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>2,'placeholder'=>"Leave a comment..."])->label(false);?>
                                <div id="editor" class=" editor-container" contentEditable=true data-text="Leave a comment..."></div>
                                </div>
                            </div>-->
				
                            <div class="editor-attached">
                                <div class="col-sm-12">
                                    <div class="col-sm-7">
                                        <?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}<div id='comments-attachment_list' class='MultiFile-list text-left'><small>Tip: File size cannot exceed 100 MB.</small></div>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,'id'=>uniqid(),'class'=>'multi-pt','title'=>'Choose File']) ?>
                                    </div>
                                    <div class="col-sm-5 text-right">
                                        <div class="recepients right"></div>
                                    </div>
                               </div>
                            </div>
			 </div>
			 <div class="button-set text-right">
			 
			  <button class="btn btn-primary" title="Back" type="button" onclick="GoBack();">Back</button>
			  <button class="btn btn-primary" title="Clear" type="button" onclick="clearatt();">Clear</button>
			  <button class="btn btn-primary" title="Recipients" type="button" onclick="Recipients()" id="recipients" >Recipients</button>
			  <button class="btn btn-primary" title="Post" type="button" onclick="PostComment();">Post</button>
			  <input type="hidden" id="caseteam_id" name="caseteam_id" />
			  <input type="hidden" id="team_ids" name="team_ids" />
        	  <input type="hidden" id="case_ids" name="case_ids" />
        	  <input type="hidden" id="teams_ids" name="teams_ids" />
        	  <input type="hidden" id="cases_ids" name="cases_ids" />
			  <input type="hidden" id="team_loc" name="team_loc" value="<?=$team_loc?>" />
			  <input type="hidden" id="email_send_user_ids" name="email_send_user_ids" />
			  <input type="hidden" id="fixed_emailsend_user_ids" name="fixed_email_send_user_ids" />
        	 </div>
             <?php ActiveForm::end(); ?>
		<div id="comments" class="comments left case_88">
			 <?=$this->render('_listComment', ['comment_data'=>$comment_data,'task_id'=>$task_id,'team_id'=>$team_id,'team_loc'=>$team_loc,'model'=>$model]);?>
		</div>
	</fieldset>
</div>
<script>
/* event */	
$('a.btn').on("click",function() { 
	$('#Comments #is_change_form').val('1'); 
	$('#Comments #is_change_form_main').val('1');
}); 
/*$('#editor').bind("input", function() { 
	$('#Comments #is_change_form').val('1'); 
	$('#Comments #is_change_form_main').val('1');
});*/
$('input[type=file]').change(function() {
	$('#Comments #is_change_form').val('1'); 
	$('#Comments #is_change_form_main').val('1');
});	
$('document').ready(function() { 
	$('#active_form_name').val('Comments'); // post comment
});
function GoBack() {
	window.location.href='<?=Yii::$app->getUser()->getReturnUrl();?>';
}
function remove_image(id,obj) {
	removed = $("#remove_attachments").val();
	if(removed == ""){
	  removed = id;
	}else{
	  removed = removed + ','+ id;
	}
	$("#remove_attachments").val(removed);
	$(obj).parent().remove();
}
function edit_remove_image(id,obj,comment_id) {
	removed = $("#remove_name_"+comment_id).val();
	if(removed == ""){
	  removed = id;
	}else{
	  removed = removed + ','+ id;
	}
	$("#remove_name_"+comment_id).val(removed);
	$("#attach_"+id).hide();
	//$(obj).parent().remove();
}
function PostComment() {
	var comment=$('#comments-comment').val();
	var caseteam_id=$("#caseteam_id").val();
	if(comment==""){
		alert('Comment cannot be blank.');
		return false;
	}if(caseteam_id==""){
		alert('Please select Comment Recipients to perform this action.');
	    return false;
	}else{
		$('#Comments #is_change_form').val('0'); // change flag
		$('#is_change_form_main').val('0'); // change flag
		$("#Comments").submit();
	}
}
function SubmitSuccesful(responseText, statusText) {
	$('div#comments').html(responseText);
    hideLoader();
    clearatt();
//	window.location.reload();
}

function clearatt() {
	//$('#editor').html(null);
	$("#comments-comment").jqteVal("");
	$('#comments-comment').val("");
	$('#caseteam_id').val("");
	$('#team_ids').val("");
	$('#case_ids').val("");
	$('#teams_ids').val("");
	$('#cases_ids').val("");
	$('#email_send_user_ids').val("");
	$('#fixed_emailsend_user_ids').val("");
	$('#Comment #is_change_form').val('0'); // change flag
	$('#is_change_form_main').val('0');  // change flag
	$('.recepients ').html(null);
	$('#Comments #is_change_form').val('0'); 
	$('#Comments #is_change_form_main').val('0');
	$( "#Comments .MultiFile-label" ).each(function( index ) {
		$(this).find('.MultiFile-remove').trigger('click');
	});
}
function Recipients()
{
	// show a spinner or something via css
 	if(!$( "#comment-recipients" ).length){
		$('body').append("<div id='comment-recipients'></div>");
	}
   	$( "#comment-recipients" ).dialog({
	  title:"Add Recipients to View Comment",
      autoOpen: true,
	  resizable: false,
	  height:456,
      width: "50em",
      modal: true,
	  buttons: [
        {
            text: "Cancel",
            title: "Cancel",
            "class": 'btn btn-primary',
            click: function() {
                $( this ).dialog( "close" );
            }
        },
        {
            text: "Add",
            title: "Add",
            "class": 'btn btn-primary',
            click: function() 
            {

				var team_roles="";
	  			var case_roles="";
				var team_roles_user="";
	  			var case_roles_users="";
				var show_vals="";
				var email_send_user_ids="";
				var fixed_emailsend_user_ids="";
				$("#casemanager-tree").dynatree("getRoot").visit(function(node) {
					selKeys = $.map(node.tree.getSelectedNodes(), function(node){
						if(node.childList===null)
							return node.data.key.toString();
					});
					
					if(node.isSelected() && node.data.isFolder==true) {
						if(case_roles=="")
	  						case_roles=node.data.key;
	  					else
	  						case_roles+=", "+node.data.key;
					}
					if(node.isSelected() && node.data.isFolder==false) {
						var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
						if($('#'+span_id).hasClass('fa-envelope')){
							if(fixed_emailsend_user_ids=="")
								fixed_emailsend_user_ids=span_id;
							else
								fixed_emailsend_user_ids+=","+span_id;	

							if(email_send_user_ids=="")
								email_send_user_ids=node.data.id;
							else
								email_send_user_ids+=","+node.data.id;	
						}
						if(case_roles_users=="")
	  						case_roles_users=node.data.key;
	  					else
	  						case_roles_users+=", "+node.data.key;

						
						if(show_vals=="")
							show_vals=node.data.role_name+' - '+node.data.titletext;
						else
							show_vals+=", "+node.data.role_name+' - '+node.data.titletext;	  
					}
					
				});
				$("#team-tree").dynatree("getRoot").visit(function(node) {
					selKeys = $.map(node.tree.getSelectedNodes(), function(node){
						if(node.childList===null)
							return node.data.key.toString();
					});
					if(node.isSelected() && node.data.isFolder==true) {
						if(team_roles=="")
	  						team_roles=node.data.key;
	  					else
	  						team_roles+=", "+node.data.key;
					}
					if(node.isSelected() && node.data.isFolder==false) {
						var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
						if($('#'+span_id).hasClass('fa-envelope')){
							if(fixed_emailsend_user_ids=="")
								fixed_emailsend_user_ids=span_id;
							else
								fixed_emailsend_user_ids+=","+span_id;	

							if(email_send_user_ids=="")
								email_send_user_ids=node.data.id;
							else
								email_send_user_ids+=","+node.data.id;	
						}
						if(team_roles_user=="")
	  						team_roles_user=node.data.key
	  					else
	  						team_roles_user+=", "+node.data.key

						if(show_vals=="")
							show_vals=node.data.team_name+' - '+node.data.titletext;
						else
							show_vals+=", "+node.data.team_name+' - '+node.data.titletext;
					}
					
				});
				$("#teams_ids").val(team_roles);
	  			$("#cases_ids").val(case_roles);
				$("#team_ids").val(team_roles_user);
				$("#case_ids").val(case_roles_users);
				$("#caseteam_id").val(case_roles_users+"||"+team_roles_user);
				$(".recepients").html(show_vals);
				$("#email_send_user_ids").val(email_send_user_ids);
				$("#fixed_emailsend_user_ids").val(fixed_emailsend_user_ids);
				if(case_roles_users == "" && team_roles_user == "") {
					alert('Please select Comment Recipients to perform this action.'); return false;
				} 
				$("#comment-recipients").dialog("close");
				}
	       }
    ],
    close: function() {
    	$(this).dialog('destroy').remove();
        // Close code here (incidentally, same as Cancel code)
    },
    open:function(){
    	$('.ui-dialog :button').blur();
	},
    });
     var url = baseUrl + "team-projects/newrecipients";
     var team_ids= $("#team_ids").val();
     var case_ids=	$("#case_ids").val();
     var teams_ids= $("#teams_ids").val();
     var cases_ids=	$("#cases_ids").val();
	 var fixed_emailsend_user_ids=$("#fixed_emailsend_user_ids").val();
     $.ajax({
		    type: "post",
		    data:{task_id:<?php echo $task_id?>,team_id:<?php echo $team_id?>,team_loc:<?php echo $team_loc?>,team_ids:team_ids,case_ids:case_ids,teams_ids:teams_ids,cases_ids:cases_ids,fixed_emailsend_user_ids:fixed_emailsend_user_ids},
			url: url,
			success:function(data){
				$( "#comment-recipients" ).html(data);
				$( "#comment-recipients" ).dialog('open');
		    }
     });
}
function Recipients_OLD() {
	// show a spinner or something via css
 	if(!$( "#comment-recipients" ).length){
		$('body').append("<div id='comment-recipients'></div>");
	}
        $( "#comment-recipients" ).dialog({
            title:"Step 1 : Select Case Manager Role or Team to View Comment",
            autoOpen: true,
            resizable: false,
            height:456,
            width: "50em",
            modal: true,
            buttons: [
            {
                text: "Cancel",
                title: "Cancel",
                "class": 'btn btn-primary',
                click: function() {
                    $( this ).dialog( "close" );
                }
            },
        {
            text: "Next",
            title: "Next",
            "class": 'btn btn-primary',
            click: function() 
            {
            	var case_id=$('.case_roles:checked').length;
	  			var team_id=$('.teams-role-chk:checked').length;
	  			
				if(case_id==0 && team_id==0){
	  				alert('Please select Comment Recipients to perform this action.'); return false;
	  			}else{
	  				team_roles="";
	  				case_roles="";
	  				$('.case_roles:checked').each(function(){
	  					if(case_roles=="")
	  						case_roles=$(this).val();
	  					else
	  						case_roles+=", "+($(this).val());
	  				});
	  				$('.teams-role-chk:checked').each(function(){
	  					if(team_roles=="")
	  						team_roles=$(this).val();
	  					else
	  						team_roles+=", "+($(this).val());
	  				});
	  			}

	  			/* Recipients */
	  			comment_recipients_inner(case_roles,team_roles); // inner comment recipient inner	
	  				$("#teams_ids").val(team_roles);
  					$("#cases_ids").val(case_roles);
				/*	$("#team_ids").val(team_ids);
	  				$("#case_ids").val(case_ids);
	  				$("#caseteam_id").val(case_ids+"||"+team_ids);
	  				$(".recepients").html(show_vals);
	  				$( this ).dialog( "close" );
	  				$("#recipients").blur();	*/
	  			}
	       }
    ],
    close: function() {
    	$(this).dialog('destroy').remove();
        // Close code here (incidentally, same as Cancel code)
    },
    open:function(){
    	$('.ui-dialog :button').blur();
	},
    });
     var url = baseUrl + "team-projects/recipients";
     var team_ids= $("#team_ids").val();
     var case_ids=	$("#case_ids").val();
     var teams_ids= $("#teams_ids").val();
     var cases_ids=	$("#cases_ids").val();
     $.ajax({
		    type: "post",
		    data:{task_id:<?php echo $task_id?>,team_id:<?php echo $team_id?>,team_loc:<?php echo $team_loc?>,team_ids:team_ids,case_ids:case_ids,teams_ids:teams_ids,cases_ids:cases_ids},
			url: url,
			success:function(data){
				$( "#comment-recipients" ).html(data);
				$( "#comment-recipients" ).dialog('open');
		    }
     });
}
function comment_recipients_inner(case_roles, team_roles) {
	if(!$( "#comment-recipients-inner" ).length){
		$('body').append("<div id='comment-recipients-inner'></div>");
	}	
	
	$( "#comment-recipients-inner" ).dialog({
		  title:"Step 2 : Select Users to Alert Comment",
	      autoOpen: true,
		  resizable: false,
		  height:456,
	      width: "50em",
	      modal: true,
		  buttons: [
	        {
	            text: "Cancel",
	            title: "Cancel",
	            "class": 'btn btn-primary',
	            click: function() {
	                $( this ).dialog( "close" );
	            }
	        },
	     	{
            	text: "Add",
            	title: "Add",
            	"class": 'btn btn-primary',
	            click: function() {
					var case_id=$('.case_roles_user:checked').length;
		  			var team_id=$('.teams-role-user-chk:checked').length;
					if(case_id==0 && team_id==0){
		  				alert('Please select Recipients Users to perform this action.'); return false;
		  			} else {
		  				var team_roles="";	
		  				var case_roles=""; 
		  				var show_vals="";
		  				$('.case_roles_user:checked').each(function(){
		  					if(case_roles=="")
		  						case_roles=$(this).val();
		  					else 
		  						case_roles+=", "+($(this).val());
	  						
		  					if(show_vals=="")
		  						show_vals=(this.title);
		  					else
		  						show_vals+=", "+(this.title);
		  				});
		  				$('.teams-role-user-chk:checked').each(function(){
		  					if(team_roles=="")
		  						team_roles=$(this).val();
		  					else 
		  						team_roles+=", "+($(this).val());

		  					if(show_vals=="")
		  						show_vals=(this.title);
		  					else
		  						show_vals+=", "+(this.title);
		  				});
		  			}
	            	$("#team_ids").val(team_roles);
	  				$("#case_ids").val(case_roles);
	  				$("#caseteam_id").val(case_roles+"||"+team_roles);
	  				$(".recepients").html(show_vals);

	  				//$( this ).dialog( "close" );
	  				$("#comment-recipients").dialog("close");
	  				$("#comment-recipients-inner").dialog("close");
	  				$("#recipients").blur();
	            } 
	     	}
		],
		close: function() {
	    	$(this).dialog('destroy').remove();
	        // Close code here (incidentally, same as Cancel code)
	    },
	    open:function() {
	    	$('.ui-dialog :button').blur();
		},
	});
    
    var url = baseUrl + "team-projects/recipients-users";
    var team_ids= $("#team_ids").val();
    var case_ids = $("#case_ids").val();
    $.ajax({
	    type: "post",
	    data:{case_roles:case_roles, team_roles:team_roles, team_ids:team_ids, case_ids:case_ids },
		url: url,
		success:function(data){
			$( "#comment-recipients-inner" ).html(data);
			$( "#comment-recipients-inner" ).dialog('open');
	    }
    });
}

$(function () {
	$("#comments-comment").jqte({source: false});
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
		$('#comments-comment').text($(this).html());
		document.getElementById("comments-comment").value = $(this).html();
	});*/
});
//document.getElementById("editor").focus();
</script>
<noscript></noscript>
