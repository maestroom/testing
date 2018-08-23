<?php use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\Options;
use app\models\EvidenceCustodians;
use app\models\Servicetask;
use app\models\FormBuilder;
use app\models\ProjectSecurity;

$showsection=="N";
$attachments="";
$checkAccess = (new ProjectSecurity)->checkTeamAccess($teamId,$team_loc);

if(!empty($processTrackData['task_instructions']['formbuilder_data'])){
	foreach ($processTrackData['task_instructions']['formbuilder_data'] as $ele_id=>$frm_data){
		$final_value = "";
		$is_change_instruction = '';
		if(isset($processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]) && $processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]!=$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]){
			$is_change_instruction = ' text-warning';
                        $is_change_instruction = ' bg-warning';
		}
		if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
			$values = (new FormBuilder)->getSelectedOptionText($processTrackData['task_instructions']['instruct']->id,$frm_data['form_builder_id'],2);
			$final_value= implode(",",$values);
		}else if($frm_data['type']=='textarea'){
			$final_value.= nl2br(html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']])));
		}else if($frm_data['type']=='text') {
			if($frm_data['remove']==0){
				$final_value.= html_entity_decode($frm_data['text_val']);
			}
		}else if($frm_data['type']=='number'){	
			$final_value.= $processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']].' '.$processTrackData['task_instructions']['unitValues']['active'][$frm_data['form_builder_id']];
		}else{
			$final_value.= html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]));
		}
		if(isset($final_value) && $final_value!=""){
			$showsection="Y";break;
		}
	}
}

if(!empty($processTrackData['task_instructions']['instructServicetask']->instructionAttachments)){
	$showsection="Y";
	foreach ($processTrackData['task_instructions']['instructServicetask']->instructionAttachments as $attachment){
		if($attachments == ""){
			$attachments = '<a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname; 
		}else{
			$attachments = $attachments . '<a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname;//array('id'=>$attachment->id,'name'=>$attachment->fname);
		}
	}
}
if(isset($processTrackData['task_instructions']['notes'])) {
	$showsection="Y";
}
if($showsection=="Y") { ?> 
<?php 

			$has_value=false;
			{ ?>
			
						<tr>
							<th class="text-left track-exp-th-task-instructions" scope="col" style="width:30%;"><a href="#" title="Instructions / Notes">Task Instructions</a></th>
							<th class="text-left track-exp-th-task-details" scope="col" style="width:70%;" colspan="9"><a href="#" title="Details">Details</a></th>
						</tr>
					   <?php 
                                           //echo "<pre>";print_r($processTrackData['task_instructions']['formValues']['lastversion']);die;
								if(!empty($processTrackData['task_instructions']['formbuilder_data'])) {
								foreach ($processTrackData['task_instructions']['formbuilder_data'] as $ele_id=>$frm_data) {
									//following condition removed for IRT-999 on 19-07-2018 by shadman
                                                                        //if($frm_data['remove'] == 1){ continue; }
									//echo "<pre>",print_r($frm_data),"</pre>";
									//echo html_entity_decode($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]);
									//die;
									$final_value = "";
									$is_change_instruction = '';
									if(isset($processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]) && $processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]!=$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]){
										$is_change_instruction = ' text-warning';
                                                                                $is_change_instruction = ' bg-warning';
									}
                                                                        if(isset($processTrackData['task_instructions']['formValues']['lastversion']) && !isset($processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]) ){
                                                                                $is_change_instruction = ' bg-warning';
									}
									if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
										$values = (new FormBuilder)->getSelectedOptionText($processTrackData['task_instructions']['instruct']->id,$frm_data['form_builder_id'],2);
										$final_value= implode(",",$values);
									} else if($frm_data['type']=='textarea') {
										$final_value.= nl2br(html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']])));
									}else if($frm_data['type']=='text') {
										if($frm_data['remove']==0){
										$is_change_instruction = '';
										$final_value.= html_entity_decode($frm_data['text_val']);//$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']];
										}
									} else if($frm_data['type']=='number'){	
										$final_value.= $processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']].' '.$processTrackData['task_instructions']['unitValues']['active'][$frm_data['form_builder_id']];
									} else {
										$final_value.= html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]));
									}
									if(isset($final_value) && $final_value!="") {
										$has_value=true;
							?>
                            <tr class="<?php echo $is_change_instruction;?>">
								<td headers="instruction_notes" class="v-align-top track-exp-td-task-instructions text-left word-break"><?=$frm_data['label']?></td>
								<td headers="instruction_details" colspan="9" class="v-align-top track-exp-th-task-details word-break text-left "><?php echo $final_value;?></td>
							</tr>
							<?php }}}
							if($attachments!=""){
								$has_value=true;
								?>
                                                        <tr class="<?=$is_change_instruction?>">
								<td headers="instruction_notes" class="v-align-top instruction_notes text-left word-break track-exp-td-task-instructions">Attachments</td>
								<td headers="instruction_details" class="v-align-top track-exp-td-task-details word-break text-left" colspan="9"><?php echo  $attachments;?></td>
							</tr>	
							<?php } if($has_value==false){ ?> <tr>
								<td colspan="2">No Records Found.</td>
								</tr><?php }?>
				
            	
                <?php } if(isset($processTrackData['task_instructions']['notes'])){?>

								<tr>
									<th class="track-exp-th-instruction text-left word-break" colspan="4"><a href="#" title="Additional Task Instructions">Additional Task Instructions</a></th>
									<th class="text-left  track-exp-th-5 word-break"><a href="#" title="Created By">Created By</a></th>
									<th id="data_third" class="text-right track-exp-th-6" scope="col" >&nbsp;</th>
<!--								<th id="data_fourth" class="text-right track-exp-th-7" scope="col">&nbsp;</th>
									<th id="data_fifth" class="text-right track-exp-th-8" scope="col">&nbsp;</th>-->									
									<th id="data_seventh" class="text-right track-exp-th-10" scope="col" colspan="4">Action</th>
								</tr>
								<tr>
									<td class="v-align-top track-exp-td-instruction text-left word-break" headers="instruction_details" colspan="4"><?=$processTrackData['task_instructions']['notes']?>
										<?php 
										$attachment="";
										if (!empty($processTrackData['task_instructions']['attachments'])) {
											foreach ($processTrackData['task_instructions']['attachments'] as  $at) {
												if ($attachment == "")
													$attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
												else
													$attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
											}
									   } ?>
										<?php echo $attachment?>
									</td>
									<td class="v-align-top track-exp-td-5 text-left word-break" headers="instruction_fourth">
										<?php if(isset($processTrackData['task_instructions']['user'])){ 
												if(!$checkAccess){ 
													echo "User";
												} else { 
													echo "<span title='{$processTrackData['task_instructions']['user_date']}'>".$processTrackData['task_instructions']['user']."</span>";
												} 
										}?>
									</td>
									<td headers="data_third" class="v-align-top text-right track-exp-td-6" colspan="3">&nbsp;</td>
									<td  class="v-align-top text-right  td-no-pad track-exp-td-9" headers="instruction_fifth">
									<?php 
										echo Html::a('<em title="Edit Instruction Notes" class="fa fa-pencil text-primary"></em>',null,['href'=>'javascript:AddInstrcutionNotes('.$processTrackData['task_instructions']['instructServicetask']['servicetask_id'].','.$processTrackData['task_instructions']['instructServicetask']['task_id'].');','class'=>'track-icon','title'=>'Edit Instruction Notes','data-name'=>$processTrackData['task_instructions']['notes'],'aria-label'=>'Add Instrcution Notes']);
									?>	
									</td>
									<td  class="v-align-top text-right td-no-pad track-exp-td-10 word-break" headers="instruction_fifth" >
									<?php  
										$onclick="alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
										if (((new User)->checkAccess(4.0612) && $case_id != 0) || ((new User)->checkAccess(5.052) && $team_id != 0)) { /* 83 */
											$onclick='DeleteInstrcutionNotes('.$processTrackData['task_instructions']['instructServicetask']['servicetask_id'].','.$processTrackData['task_instructions']['instructServicetask']['task_id'].',this);';	
										}
										echo Html::a('<em title="Delete Instruction Notes" class="text-primary fa fa-close"></em>',null,['href'=>'javascript:void(0);','onclick'=>$onclick,'class'=>'track-icon','title'=>'Delete Instruction Notes','data-name'=>$processTrackData['task_instructions']['notes']]);
									?>	
									</td>
								</tr>
							
             
                <?php } else {?>
								<tr class="hide-tr">
									<th class="track-exp-th-instruction text-left word-break hide-td" colspan="4"><a href="#" title="Additional Task Instructions">Additional Task Instructions</a></th>
									<th class="text-left  track-exp-th-5 word-break hide-td"><a href="#" title="Created By">Created By</a></th>
									<th id="data_third" class="text-right track-exp-th-6 hide-td" scope="col" >&nbsp;</th>
									<th id="data_seventh" class="text-right track-exp-th-10 hide-td" scope="col" colspan="4">Action</th>
								</tr>
								<tr class="hide-tr">
									<td class="v-align-top track-exp-td-instruction text-left word-break hide-td" headers="instruction_details" colspan="4">&nbsp;</td>
									<td class="v-align-top track-exp-td-5 text-left word-break hide-td" headers="instruction_fourth">&nbsp;</td>
									<td headers="data_third" class="v-align-top text-right track-exp-td-6 hide-td" colspan="3">&nbsp;</td>
									<td  class="v-align-top text-right  td-no-pad track-exp-td-9 hide-td" headers="instruction_fifth">&nbsp;</td>
									<td  class="v-align-top text-right td-no-pad track-exp-td-10 word-break hide-td" headers="instruction_fifth" >&nbsp;</td>
								</tr>
				<?php }?>
<?php } else { ?>
							<tr class="hide-tr">
								<th class="text-left track-exp-th-task-instructions hide-td" scope="col" style="width:30%;"><a href="#" title="Instructions / Notes">Task Instructions</a></th>
								<th class="text-left track-exp-th-task-details  hide-td" scope="col" style="width:70%;" colspan="9"><a href="#" title="Details">Details</a></th>
							</tr>
					   		<tr class="hide-tr">
								<td headers="instruction_notes" class="v-align-top track-exp-td-task-instructions text-left word-break  hide-td">&nbsp;</td>
								<td headers="instruction_details" colspan="9" class="v-align-top track-exp-th-task-details word-break text-left hide-td">&nbsp;</td>
							</tr>
							<tr class="hide-tr">
								<td headers="instruction_notes" class="v-align-top instruction_notes text-left word-break track-exp-td-task-instructions hide-td">Attachments</td>
								<td headers="instruction_details" class="v-align-top track-exp-td-task-details word-break text-left hide-td" colspan="9">&nbsp;</td>
							</tr>	
							<tr class="hide-tr">
								<th class="track-exp-th-instruction text-left word-break hide-td" colspan="4"><a href="#" title="Additional Task Instructions">Additional Task Instructions</a></th>
								<th class="text-left  track-exp-th-5 word-break hide-td"><a href="#" title="Created By">Created By</a></th>
								<th id="data_third" class="text-right track-exp-th-6 hide-td" scope="col" >&nbsp;</th>
								<th id="data_seventh" class="text-right track-exp-th-10 hide-td" scope="col" colspan="4">Action</th>
							</tr>
							<tr class="hide-tr">
								<td class="v-align-top track-exp-td-instruction text-left word-break hide-td" headers="instruction_details" colspan="4">&nbsp;</td>
								<td class="v-align-top track-exp-td-5 text-left word-break hide-td" headers="instruction_fourth">&nbsp;</td>
								<td headers="data_third" class="v-align-top text-right track-exp-td-6 hide-td" colspan="3">&nbsp;</td>
								<td  class="v-align-top text-right  td-no-pad track-exp-td-9 hide-td" headers="instruction_fifth">&nbsp;</td>
								<td  class="v-align-top text-right td-no-pad track-exp-td-10 word-break hide-td" headers="instruction_fifth" >&nbsp;</td>
							</tr>
<?php } ?>
