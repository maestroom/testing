<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Comments;
use app\models\Options;
$attachment_array=array();
?>
<?php
				   	if ((new User)->checkAccess(4.08)) {
				   		if(!empty($comment_data)){
				   			foreach ($comment_data as $da){?>
				   			<ul>
				   			<?php if((new Comments)->checkAccess($da)){?>
				   			<li>
				   			<div class="comment_box row">
				   			
		   						<div class="col-sm-9">
		   										<div class="name">
			      									<strong><?= $da->createdUser->usr_first_name." ".$da->createdUser->usr_lastname;?></strong>
			      									<?php if($da->parent_id!=0){?>
			      									<div class="right">
			      										<em class="fa fa-long-arrow-right" title="Comment username"></em>
	  													<span style="color:gray"><?= $da->parent->createdUser->usr_first_name." ".$da->parent->createdUser->usr_lastname;?></span>
													</div>
			      									<?php }?>
		      									</div>
		   										<div class="date">
		   											<div id="defualt_<?=$da->Id?>" class="defualt"><?=Html::encode($da->comment)?></div>
				      							</div>
		      						</div>	
		      							
		      						<div class="col-sm-3">
		   									<div class="type  text-right">
				      								<div><?php  
									                	$attachment="";
										                if (!empty($da->attachments)) {
											                foreach ($da->attachments as  $at) {
											                    if ($attachment == "")
											                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em></a>';
											                    else
											                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em></a>';
											                }
										               }echo "&nbsp;".$attachment; 
										               echo (new Options)->ConvertOneTzToAnotherTz($da->created,'UTC',$_SESSION['usrTZ'],"comment");
										               ?>
										            </div>
				      								<div class='text_alright'><em> <?php echo (new Comments)->getRecipients($da);?></em></div>
				      							</div>
		   						</div>
		   						<div class="col-sm-12">
		   										<div class="action text-right">
				       									<?php if($da->parent_id==0){?>
				       									<a href="javascript:void(0)" onclick="$('.Edit_<?php echo $da->Id?>').hide();$('.reply_<?php echo $da->Id?>').toggle();" title="Reply" class=" btn btn-primary btn-xs">Reply</a>
				       									<?php if ((new User)->checkAccess(4.0801)) { ?>
				       										<a href="javascript:void(0)" onclick="EditComment(<?php echo $da->Id?>);" title="Edit" class=" btn btn-primary btn-xs">Edit</a>
				       									<?php } ?>
				       									<a href="javascript:void(0)" onclick="DeleteComment(<?php echo $da->Id?>,'parent');" title="Delete" class=" btn btn-primary btn-xs">Delete</a>
				       									
				       									<?php }else{?>
				       									<?php if ((new User)->checkAccess(4.0801)) { ?>
				       										<a href="javascript:void(0)" onclick="EditComment(<?php echo $da->Id?>);" title="Edit" class=" btn btn-primary btn-xs">Edit</a>
				       									<?php } ?>
				       									<a href="javascript:void(0)" onclick="DeleteComment(<?php echo $da->Id?>,'reply');" title="Delete" class=" btn btn-primary btn-xs">Delete</a>
														<?php }?>
			       									</div>
													<?php if ((new User)->checkAccess(4.0801)) { ?>
	       											<div class="Edit_<?php echo $da->Id?>" style="display: none;">
	       												<?php $form = ActiveForm::begin(['id' => 'edit-comments-form-'.$da->Id,'action'=>Yii::$app->urlManager->createUrl(["case-projects/edit-comment",'id'=>$da->Id,'task_id'=>$task_id,'case_id'=>$case_id]),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
			       											<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" /> 		
					       									<div>
					       									<?= $form->field($model, 'comment',['template' => "<div class='col-md-12'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>2,'placeholder'=>"Leave a comment...",'value'=>Html::encode($da->comment)]);?>
					       									<input type="hidden" id="hdn_org_comment_<?php echo $da->Id?>" value="<?php echo nl2br($da->comment);?>">
					       									</div>
					       									<div class="clear"></div>
		       												<div class="button-set text-right">
		       												<div class="col-sm-7">
		       												<div class=" MultiFile-title">
																<span> 
																<?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}<div id='comments-attachment_list' class='MultiFile-list text-left'>(File Size can't exceed 100MB)</div>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,"id"=>uniqid(), 'class'=>'multi-pt','title'=>'Choose File']) ?>
																<?php
																$attachment_array['T'.$da->Id]='T'.$da->Id.'-list';
																if (!empty($da->attachments)) {
													               foreach ($da->attachments as $filename) {
													               ?>
													               <div class="MultiFile-label selected-file-list text-left attach_<?php echo $da->Id?>" id="attach_<?php echo $filename->id; ?>">
													                   <a href="#T7" class="MultiFile-remove" onclick="edit_remove_image('<?php echo $filename->id; ?>', this,'<?php echo $da->Id?>');" title="Delete attachment"><em title="Delete" class="fa fa-close text-danger"></em></a>
													                   <span title="File selected: " class="MultiFile-title">
													                       <?php echo $filename->fname;?>
													                   </span>
													               </div>
													               <?php
													                   } 
													               }
													               ?>  
																<input type="hidden" name="remove_name_<?php echo $da->Id?>" id="remove_name_<?php echo $da->Id?>" />
																</span> 
															</div>
																</div>
																<div class="col-sm-5">
																<a href="javascript:void(0)" class="btn btn-primary" onclick="EditMyComment(<?php echo $da->Id?>);" title="Update">Update</a>
																<a href="javascript:void(0)" class="btn btn-primary" onclick="EditComment(<?php echo $da->Id?>);"   title="Cancel">Cancel</a>
																</div>
															</div>
	       												<?php ActiveForm::end(); ?>
	      											</div>
	      											<?php } ?>
	      											<div class="reply_<?php echo $da->Id?>" style="display:none;">
		       											<?php $form = ActiveForm::begin(['id' => 'add-comments-form-'.$da->Id,'action'=>Yii::$app->urlManager->createUrl(["case-projects/reply-comment","id"=>$da->Id,"task_id"=>$task_id,"case_id"=>$case_id]),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
													<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
		       										<div class="row">
		       											<?= $form->field($model, 'comment',['template' => "<div class='col-md-12'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>2,'placeholder'=>"Leave a comment...",'id'=>"comment_area_".$da->Id]);?>
		       										</div>
		       										<div class="button-set text-right">
		       											<div class="col-sm-7">
		       											<?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}<div id='comments-attachment_list' class='MultiFile-list text-left'>(File Size can't exceed 100MB)</div>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,'id'=>uniqid(),'class'=>'multi-pt','title'=>'Choose File']) ?>
														</div>
														<div class="col-sm-5">
															<a href="javascript:void(0)" onclick="ReplyComment(<?php echo $da->Id?>);" class="btn btn-primary"  title="Send">Send</a>
															<a href="javascript:void(0)" onclick="cancelComment(<?php echo $da->Id?>);" class="btn btn-primary" title="Cancel">Cancel</a>
														</div>
													</div>
		       								<?php ActiveForm::end(); ?>
		       								</div>
		   						</div>
		   						</div>
		   						
		   						<?php 
								/*Second level loop*/
								if(!empty($da->childs)){ ?>
								<ul>
									<?php foreach ($da->childs as $dachilds){
										?>
										<li>
										<div class="comment_box row">
										<div class="col-sm-9">
		   										<div class="name">
			      									<strong><?= $dachilds->createdUser->usr_first_name." ".$dachilds->createdUser->usr_lastname;?></strong>
			      									<?php if($dachilds->parent_id!=0){?>
			      									
			      										<em class="fa fa-share" aria-hidden="true" title="Comment username"></em>
	  													<span style="color:gray"><?= $dachilds->parent->createdUser->usr_first_name." ".$dachilds->parent->createdUser->usr_lastname;?></span>
													<?php }?>
		      									</div>
		   										<div class="date">
		   											<div id="defualt_<?=$dachilds->Id?>" class="defualt"><?=nl2br($dachilds->comment)?></div>
				      							</div>
		      						</div>
		      						<div class="col-sm-3">
		   									<div class="type  text-right">
				      								<div><?php  
									                	$attachment="";
										                if (!empty($dachilds->attachments)) {
											                foreach ($dachilds->attachments as  $at) {
											                    if ($attachment == "")
											                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em></a>';
											                    else
											                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em></a>';
											                }
										               }echo "&nbsp;".$attachment; 
										               echo (new Options)->ConvertOneTzToAnotherTz($da->created,'UTC',$_SESSION['usrTZ'],"comment");
										               ?>
										            </div>
				      							</div>
		   						</div>
		   						<div class="col-sm-12">
		   									<div class="action text-right">
		       								<?php if ((new User)->checkAccess(4.0801)) { ?>
		       									<a href="javascript:void(0)" onclick="EditComment(<?php echo $dachilds->Id?>);" title="Edit" class=" btn btn-primary btn-xs">Edit</a>
		       								<?php } ?>
		       								<?php if($da->parent_id==0){?>
		       									<a href="javascript:void(0)" onclick="DeleteComment(<?php echo $dachilds->Id?>,'parent');" title="Delete" class=" btn btn-primary btn-xs">Delete</a>
		       								<?php }else{?>
		       									<a href="javascript:void(0)" onclick="DeleteComment(<?php echo $dachilds->Id?>,'reply');" title="Delete" class=" btn btn-primary btn-xs">Delete</a>
											<?php }?>
		       								</div>
		       								<?php if ((new User)->checkAccess(4.0801)) { ?>
	       									<div class="Edit_<?php echo $dachilds->Id?>" style="display: none;">
	       												<?php $form = ActiveForm::begin(['id' => 'edit-comments-form-'.$dachilds->Id,'action'=>Yii::$app->urlManager->createUrl(["case-projects/edit-comment",'id'=>$dachilds->Id,'task_id'=>$task_id,'case_id'=>$case_id]),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
			       											<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" /> 		
					       									<div>
					       									<?= $form->field($model, 'comment',['template' => "<div class='col-md-12'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>2,'placeholder'=>"Leave a comment...",'value'=>Html::encode($dachilds->comment)]);?>
					       									<input type="hidden" id="hdn_org_comment_<?php echo $da->Id?>" value="<?php echo Html::encode($dachilds->comment);?>">
					       									</div>
					       									<div class="clear"></div>
		       												<div class="button-set text-right">
		       												<div class="col-sm-7">
		       												<div class=" MultiFile-title">
																<span> 
																<?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}<div id='comments-attachment_list' class='MultiFile-list text-left'>(File Size can't exceed 100MB)</div>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,"id"=>uniqid(),'class'=>'multi-pt','title'=>'Choose File']) ?>
																<?php
																$attachment_array['T'.$dachilds->Id]='T'.$dachilds->Id.'-list';
																if (!empty($dachilds->attachments)) {
													               foreach ($dachilds->attachments as $filename) {
													               ?>
													               <div class="MultiFile-label selected-file-list text-left attach_<?php echo $dachilds->Id?>" id="attach_<?php echo $filename->id; ?>">
													                   <a href="#T7" class="MultiFile-remove" onclick="edit_remove_image('<?php echo $filename->id; ?>', this,'<?php echo $dachilds->Id?>');" title="Delete attachment"><em title="Remove" class="fa fa-close text-danger"></em></a>
													                   <span title="File selected: " class="MultiFile-title">
													                       <?php echo $filename->fname;?>
													                   </span>
													               </div>
													               <?php
													                   } 
													               }
													               ?>  
																<input type="hidden" name="remove_name_<?php echo $dachilds->Id?>" id="remove_name_<?php echo $dachilds->Id?>" />
																</span> 
															</div>
																</div>
																<div class="col-sm-5">
																<a href="javascript:void(0)" class="btn btn-primary" onclick="EditMyComment(<?php echo $dachilds->Id?>);" title="Update">Update</a>
																<a href="javascript:void(0)" class="btn btn-primary" onclick="EditComment(<?php echo $dachilds->Id?>);" title="Cancel">Cancel</a>
																</div>
															</div>
	       												<?php ActiveForm::end(); ?>
	      											</div>
	      									<?php } ?>
		   						</div>
		   						</div>
		   									
		       						</li>
									<?php }?>
								</ul>
								<?php }
								/*Second level loop*/
								?>
				   			</li>
				   		<?php }?>
						</ul>
				<?php 	}
				   		}
				   }
				?>