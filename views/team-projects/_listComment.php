<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Comments;
use app\models\Options;
$attachment_array=array();
				   	if ((new User)->checkAccess(5.07)) {
				   		if(!empty($comment_data)){
				   			foreach ($comment_data as $da){
							?>
				   			<ul>
				   			<?php if((new Comments)->checkAccess($da)){?>
				   			<li>
				   			<div class="comment_box">
				   			
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
		   											<div id="defualt_<?=$da->Id?>" class="defualt"><?=($da->comment)?></div>
				      							</div>
		      						</div>	
		      							
		      						<div class="col-sm-3">
		   									<div class="type  text-right">
				      								<div><?php  
									                	$attachment="";
										                if (!empty($da->attachments)) {
											                foreach ($da->attachments as  $at) {
											                    if ($attachment == "")
											                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											                    else
											                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											                }
										               }echo "&nbsp;".$attachment; 
										               echo (new Options)->ConvertOneTzToAnotherTz($da->created,'UTC',$_SESSION['usrTZ']);
										               ?>
										            </div>
				      								<div class="text_alright"><em> <?php echo (new Comments)->getRecipients($da);?></em></div>
				      							</div>
		   						</div>
		   						<div class="col-sm-12">
		   										<div class="action text-right">
				       									<?php if($da->parent_id==0){?>
				       									<a href="javascript:void(0)" onclick="ReplyComment(<?php echo $da->Id?>,<?php echo $task_id?>,<?php echo $team_id?>,'team');" title="Reply" class="">Reply</a>&nbsp;&nbsp;
				       									<?php if ((new User)->checkAccess(5.071)) { ?>
				       										<a href="javascript:void(0)" onclick="EditComment(<?php echo $da->Id?>,<?php echo $task_id?>,<?php echo $team_id?>,'team');" title="Edit" class="">Edit</a>&nbsp;&nbsp;
				       									<?php } ?>
				       									<a href="javascript:void(0)" onclick="<?php if ((new User)->checkAccess(5.072)) { ?>DeleteComment(<?php echo $da->Id?>,'parent','team');<?php } else {?>alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');<?php }?>" title="Delete" class="">Delete</a>&nbsp;&nbsp;
				       									
				       									<?php }else{?>
				       									<?php if ((new User)->checkAccess(5.071)) { ?>
				       										<a href="javascript:void(0)" onclick="EditComment(<?php echo $da->Id?>,<?php echo $task_id?>,<?php echo $team_id?>,'team');" title="Edit" class=" ">Edit</a>&nbsp;&nbsp;
				       									<?php } ?>
				       									<a href="javascript:void(0)" onclick="<?php if ((new User)->checkAccess(5.072)) { ?>DeleteComment(<?php echo $da->Id?>,'reply','team');<?php } else {?>alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');<?php }?>" title="Delete" class="">Delete</a>&nbsp;&nbsp;
														<?php }?>
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
		   											<div id="defualt_<?=$dachilds->Id?>" class="defualt"><?=($dachilds->comment)?></div>
				      							</div>
		      						</div>
		      						<div class="col-sm-3">
		   									<div class="type  text-right">
				      								<div><?php  
									                	$attachment="";
										                if (!empty($dachilds->attachments)) {
											                foreach ($dachilds->attachments as  $at) {
											                    if ($attachment == "")
											                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											                    else
											                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											                }
										               }echo "&nbsp;".$attachment; 
										               echo (new Options)->ConvertOneTzToAnotherTz($dachilds->created,'UTC',$_SESSION['usrTZ']);
										               ?>
										            </div>
				      							</div>
		   						</div>
		   						<div class="col-sm-12">
		   									<div class="action text-right">
		       								<?php if ((new User)->checkAccess(4.0801)) { ?>
		       									<a href="javascript:void(0)" onclick="EditComment(<?php echo $dachilds->Id?>,<?php echo $task_id?>,<?php echo $team_id?>,'team');" title="Edit" class=" ">Edit</a>&nbsp;&nbsp;
		       								<?php } ?>
		       								<?php if($da->parent_id==0){?>
		       									<a href="javascript:void(0)" onclick="<?php if ((new User)->checkAccess(5.072)) { ?>DeleteComment(<?php echo $dachilds->Id?>,'parent','team');<?php } else {?>alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');<?php }?>" title="Delete" class=" ">Delete</a>&nbsp;&nbsp;
		       								<?php }else{?>
		       									<a href="javascript:void(0)" onclick="<?php if ((new User)->checkAccess(5.072)) { ?>DeleteComment(<?php echo $dachilds->Id?>,'reply','team');<?php } else {?>alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');<?php }?>" title="Delete" class=" ">Delete</a>&nbsp;&nbsp;
											<?php }?>
		       								</div>
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
