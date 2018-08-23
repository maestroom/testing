<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use app\models\ClientCase;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SearchSummaryComment */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Summary Comments';
$this->params['breadcrumbs'][] = $this->title;
$teamdropdown="";
if($case_id!=0){
	if(!empty($dropdown_widget)){
			//echo "<pre>",print_r($dropdown_widget),"</prE>";die;
			$teamdropdown='<label class="sr-only" for="team_location_dropdown">Filter Shared Team Location</label>
			<select id="team_location_dropdown" onchange="filterTeamloc(this.value);" class="form-control select2-hidden-accessible" name="TeamSearch[team_name]" title="Filter Shared Team Location">
			<option value="">Filter Shared Team Location</option>';
			if(trim($dropdown_widget['id'])=='No Records Found'){
				
			}else{
				foreach($dropdown_widget as $data){
					$selected="";
					if($shraed_teamloc==$data['id']){
						$selected="selected='selected'";
					}
					$teamdropdown.='<option '.$selected.' value="'.$data['id'].'">'.$data['team_name'].'</option>';
				}
			}
			$teamdropdown.='</select>';
	}
}?>
<div class="right-main-container">
	<fieldset class="one-cols-fieldset track-projects-fieldset" id="track-projects-fieldset">
    <?= GridView::widget([
        //'id'=>'summarycomment-grid',
        'id'=>'caseprojects-grid',
        'dataProvider' => $dataProvider,
        'layout' => '{items}',
        'columns' => [
           	['class' => '\kartik\grid\ExpandRowColumn','extraData'=>Yii::$app->request->queryParams,'detailUrl' => Url::toRoute(['summary-comment/get-comment-details','case_id'=>$case_id,'team_id'=>$team_id,'team_loc'=>$team_loc]),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>' first-td','id'=>'track_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class'=>' first-td','headers'=>'track_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw" tabindex="0"><span class="glyphicon glyphicon-plus"></span><span class="screenreader">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw" tabindex="0"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
            ['attribute' =>'comment','label'=>'Summary Comment','format'=>'raw','value'=>function($model)use($case_id,$team_id,$team_loc){
                if($case_id!=0){
                    $org="case";
                }else{
					$org="team";
                }
				return "<h6><strong>".ucwords($model->createdUser->usr_first_name." ".$model->createdUser->usr_lastname)."</h6></strong><span><small>".$model->comment."<small></span><br><strong>Shared: </strong>".$model->sharedTeamLoc($model->Id,$org);
            }],
            ['attribute' => 'comment_filter', 'header' => '<div class="select-filter-task" style="position:absolute; right:7px; top:5px; width:210px;">'.$teamdropdown.'</div>','format'=>'raw',
                          'value'=>function($model)use($case_id,$team_id,$team_loc){

							$attachment="";
										                if (!empty($model->attachments)) {
											                foreach ($model->attachments as  $at) {
											                    if ($attachment == "")
											                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											                    else
											                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											                }
										               }
							  $hundred_char = substr(strip_tags($model->comment),0,100);						   	
                              if($case_id!=0){
                                $delete_action="";
								
                                if ((new app\models\User)->checkAccess(4.0806)) {
                                        $delete_action="DeleteSummaryComment($model->Id,'parent','case','".$hundred_char."')";
                                  } else {
                                        $delete_action="alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
                                  }
                                $actions='<div class="action text-right h6">';
				       									 if($model->parent_id==0){
                                                                if ((new app\models\User)->checkAccess(4.0804) && $case_id != 0) { 
				       									            $actions.='<a href="javascript:void(0)" onclick="ReplySummaryComment('.$model->Id.','.$team_id.','.$team_loc.','.$case_id.',\'case\');" title="Reply" class=" ">Reply</a>&nbsp;&nbsp;';
                                                                }
				       									if ((new app\models\User)->checkAccess(4.0805) && $case_id != 0) { 
				       										$actions.='<a href="javascript:void(0)" onclick="EditSummaryComment('.$model->Id.','.$team_id.','.$team_loc.','.$case_id.',\'case\');" title="Edit" class=" ">Edit</a>&nbsp;&nbsp';
				       									}
                                                        $actions.='<a href="javascript:void(0)" onclick="'.$delete_action.'" title="Delete" class="">Delete</a>&nbsp;&nbsp;';
				       									}
			       									$actions.='</div>';
                              }else{
                                  $delete_action="";
								
                                if ((new app\models\User)->checkAccess(5.076)) {
                                        $delete_action="DeleteSummaryComment($model->Id,'parent','team','".$hundred_char."')";
                                  } else {
                                        $delete_action="alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
                                  }
                                $actions='<div class="action text-right h6">';
				       									 if($model->parent_id==0){
                                                                if ((new app\models\User)->checkAccess(5.074) && $team_id != 0) { 
				       									            $actions.='<a href="javascript:void(0)" onclick="ReplySummaryComment('.$model->Id.','.$team_id.','.$team_loc.','.$case_id.',\'team\');" title="Reply" class=" ">Reply</a>&nbsp;&nbsp;';
                                                                }
				       									if ((new app\models\User)->checkAccess(5.075) && $team_id != 0) { 
				       										$actions.='<a href="javascript:void(0)" onclick="EditSummaryComment('.$model->Id.','.$team_id.','.$team_loc.','.$case_id.',\'team\');" title="Edit" class=" ">Edit</a>&nbsp;&nbsp';
				       									}
                                                        $actions.='<a href="javascript:void(0)" onclick="'.$delete_action.'" title="Delete" class="">Delete</a>&nbsp;&nbsp;';
				       									}
			       									$actions.='</div>';
                              }

                                return '<div class="h6 white-space" style="">'.$attachment.'&nbsp;<strong>'.(new app\models\Options)->ConvertOneTzToAnotherTz($model->created,'UTC',$_SESSION['usrTZ']).'</strong></div>'.$actions;
                          }
                          ,'headerOptions'=>['class'=>'text-center','id'=>'track_instruction_notes'], 'format' => 'raw','contentOptions' => ['style' => 'vertical-align:top','class'=>'text-right','headers'=>'track_instruction_notes']],    
        ],
		'export'=>false,
		'floatHeader'=>true,
		'pjax'=>true,
		'responsive'=>true,
		'floatHeaderOptions' => ['top' => 'auto'],
		'pjaxSettings'=>[
				//'options'=>['id'=>'summary-comment-pajax','enablePushState' => false],
                                'options'=>['id'=>'trackproject-pajax','enablePushState' => false],
				'neverTimeout'=>true,
				'beforeGrid'=>'',
				'afterGrid'=>'',
				],
		'floatOverflowContainer'=>true,
    ]); ?>
    </fieldset>
    <div class="button-set text-right">
        <?php if ((new app\models\User)->checkAccess(4.0804) && $case_id != 0) { ?>
            <button class="btn btn-primary" title="Add Comment" onclick="addSummaryComment(<?=$case_id?>,<?=$team_id?>,<?=$team_loc?>,'case');">Add Comment</button>
			<?php $allprojects_url = Url::toRoute(['summary-comment/index', 'case_id' => $case_id]); ?>
			<?php if($comment_id!=0 || $shraed_teamloc!=0 || $comment!='') { ?>
			<?=  Html::button('All Comments',['title'=>"All Comments",'class' => 'btn btn-primary all_filter', 'onclick' => 'location.href="'.$allprojects_url.'"']) ?>
			<?php }?>

        <?php } if((new app\models\User)->checkAccess(5.074) && $team_id != 0){?>
			<button class="btn btn-primary" title="Add Comment" onclick="addSummaryCommentTeam(<?=$case_id?>,<?=$team_id?>,<?=$team_loc?>,'team');">Add Comment</button>
			<?php $allprojects_url = Url::toRoute(['summary-comment/index', 'team_id' => $team_id,'team_loc'=>$team_loc]); ?>
			<?php if($comment_id!=0 || $comment!='') { ?>
			<?=  Html::button('All Comments',['title'=>"All Comments",'class' => 'btn btn-primary all_filter', 'onclick' => 'location.href="'.$allprojects_url.'"']) ?>
			<?php }?>

		<?php }?>
	</div>
</div>
<script>
//    var isClick;
//$(document).bind('click', function() { isClick = true; })
//           .bind('keypress', function() { isClick = false; })
//           ;
//
//var focusHandler = function () {
//    if (isClick) {
//        // clicky!
//    } else {
//        // tabby!
//    }
//}
//
//$('#team_location_dropdown').focus(function() {
//    // we set a small timeout to let the click / keypress event to trigger
//    // and update our boolean
//    setTimeout(focusHandler,100);
//});
$(function(){
    //$("#team_location_dropdown").focus(function () {       
        $("#team_location_dropdown").keypress(function (e) {
            if (e.keyCode == 13) {                
                $( "#team_location_dropdown" ).trigger( "click" );
                //$( "#team_location_dropdown" ).mousedown();
//                $('#team_location_dropdown').attr('size',6);
//                alert('You pressed enter!');
            }
        });
    //});
});
function filterTeamloc(shraed_teamloc){
	var Url=baseUrl + "summary-comment/index&case_id=<?=$case_id?>";
	if(shraed_teamloc==""){
		location.href=Url;
	}else{
		location.href=Url+'&shraed_teamloc='+shraed_teamloc;
	}
}
function DeleteSummaryComment(comment_id,msg,org,comment){
	var Url=baseUrl + "summary-comment/delete&id="+comment_id;
	if(comment==undefined){
		comment = 'this Comment';
	}
	var msg_conf="Are you sure you want to Delete "+comment+"?";
	if(msg=="parent"){
		msg_conf="Are you sure you want to Delete "+comment+"?";
	}
	if(confirm(msg_conf)){
		var url     = Url;
		$.ajax({
            type: "POST",
            url: url,
            data: {comment_id:comment_id,msg:msg,org:org},
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
function addSummaryCommentTeam(case_id,team_id,team_loc,org){
	var Url=baseUrl + "summary-comment/create"
	$.ajax({
		url:Url,
		data:{case_id:case_id,team_id:team_id,team_loc:team_loc,org:org},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	   if(!$( "#add-summary-comment" ).length){
	    			$('body').append("<div id='add-summary-comment'></div>");
	    		}
	    	   	$( "#add-summary-comment" ).html(mydata);
	    		$( "#add-summary-comment" ).dialog({
	    			  title:"Add Summary Comment",
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
						    text: "Post",
						    "title":"Post",
						    "class": 'btn btn-primary',
						    click: function() {
								comment = $("#summarycomment-comment").val();
						    	comment = strip_tags(comment.replace(/&nbsp;/g, ''));
						    	if(comment!=""){
						    		$("#SummaryComment").submit();
						    	}else{
						    		alert('Comment cannot be blank.');
						    	}
						    }
						}
	    		        
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		    }
	    		});
	       
		},
	});
}
function addSummaryComment(case_id,team_id,team_loc,org){
    var Url=baseUrl + "summary-comment/create"
	$.ajax({
		url:Url,
		data:{case_id:case_id,team_id:team_id,team_loc:team_loc,org:org},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	   if(!$( "#add-summary-comment" ).length){
	    			$('body').append("<div id='add-summary-comment'></div>");
	    		}
	    	   	$( "#add-summary-comment" ).html(mydata);
	    		$( "#add-summary-comment" ).dialog({
	    			  title:"Add Summary Comment",
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
	    		            text: "Add Team",
	    		            "title":"Add Team",
	    		            "class": 'btn btn-primary',
	    		            click: function() {
								addTeamService();
							}
	    		        },
	    		        {
						    text: "Post",
						    "title":"Post",
						    "class": 'btn btn-primary',
						    click: function() {
								comment = $("#summarycomment-comment").val();
						    	comment = strip_tags(comment.replace(/&nbsp;/g, ''));
						    	if(comment!=""){
						    		$("#SummaryComment").submit();
						    	}else{
						    		alert('Comment cannot be blank.');
						    	}
						    }
						}
	    		        
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		    }
	    		});
	       
		},
	});
}
function EditSummaryComment (comment_id,team_id,team_loc,case_id,org){
	var Url=baseUrl + "summary-comment/update&id="+comment_id;
	$.ajax({
		url:Url,
		data:{comment_id:comment_id,case_id:case_id,team_id:team_id,team_loc:team_loc,org:org},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	   if(!$( "#edit-summary-comment" ).length){
	    			$('body').append("<div id='edit-summary-comment'></div>");
	    		}
	    	   	$( "#edit-summary-comment" ).html(mydata);
	    		$( "#edit-summary-comment" ).dialog({
	    			  title:"Edit Summary Comment",
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
								comment = $("#summarycomment-comment").val();
						    	comment = strip_tags(comment.replace(/&nbsp;/g, ''));
						    	if(comment!=""){
						    		$("#SummaryComment").submit();
						    	}else{
						    		alert('Comment cannot be blank.');
						    	}
						    }
						}
	    		        
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		    }
	    		});
	       
		},
	});
}
function ReplySummaryComment(comment_id,team_id,team_loc,case_id,org){
 var Url=baseUrl + "summary-comment/reply"
	$.ajax({
		url:Url,
		data:{comment_id:comment_id,case_id:case_id,team_id:team_id,team_loc:team_loc,org:org},
	    type:"get",
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
	    	   if(!$( "#reply-summary-comment" ).length){
	    			$('body').append("<div id='reply-summary-comment'></div>");
	    		}
	    	   	$( "#reply-summary-comment" ).html(mydata);
	    		$( "#reply-summary-comment" ).dialog({
	    			  title:"Add Summary Comment Reply",
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
								comment = $("#summarycomment-comment").val();
						    	comment = strip_tags(comment.replace(/&nbsp;/g, ''));
						    	if(comment!=""){
						    		$("#SummaryComment").submit();
						    	}else{
						    		alert('Comment cannot be blank.');
						    	}
						    }
						}
	    		        
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		    }
	    		});
	       
		},
	});
}
function addTeamService(){
	//alert($("#shared_team").val());
	var Url=baseUrl + "summary-comment/teamlocs"
	$.ajax({
		url:Url,
		type:"get",
		data:{shared_team:$("#shared_team").val()},
	    beforeSend:function(){
			showLoader();
	    },
	    success:function(mydata){
	    	hideLoader();
			if(!$( "#add-team-loc" ).length){
	    			$('body').append("<div id='add-team-loc'></div>");
	    		}
	    	   	$( "#add-team-loc" ).html(mydata);
	    		$( "#add-team-loc" ).dialog({
	    			  title:"Select Team to Share Summary Comment",
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
						    text: "Add",
						    "title":"Add",
						    "class": 'btn btn-primary',
						    click: function() {
								var team_locs="";
								var name = '';
								//alert($('#add-team-loc .service_checkbox:checked').length);
								$('#add-team-loc .service_checkbox:checked').each(function(){
									if(team_locs==""){
										team_locs=$(this).data('teamloc');
										name = $(this).data('name');
									}else{
										team_locs=team_locs+","+$(this).data('teamloc');
										name = name +', '+ $(this).data('name');
									}
								});
								if(team_locs==""){
									alert('please select Team Location.');
								}else{
									$('#shared_team_name').html(name);
									$("#shared_team").val(team_locs);
									$( this ).dialog( "close" );
								}
								
						    }
						}
	    		        
	    		    ],
	    		    close: function() {
	    		    	$(this).dialog('destroy').remove();
	    		    }
	    		});
			//console.log(mydata);
		}
	});
}
</script>