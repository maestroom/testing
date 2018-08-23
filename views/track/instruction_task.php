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
				$final_value.= html_entity_decode(($frm_data['text_val']));
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
			$attachments = '<a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em  title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname; 
		}else{
			$attachments = $attachments . '<a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname;//array('id'=>$attachment->id,'name'=>$attachment->fname);
		}
	}
}
if(isset($processTrackData['task_instructions']['notes'])){
	$showsection="Y";
}
if($showsection=="Y"){
?>
					<table class="table table-striped table-hover">
						<tr>
							<th id="instruction_notes" class="text-left track-exp-th-task-instructions" scope="col" colspan="2"><a href="#" title="Instructions / Notes">Task Instructions</a></th>
							<th id="instruction_details" class="text-left track-exp-th-task-details" scope="col" colspan="8"><a href="#" title="Details">Details</a></th>
						 </tr>
					   <?php 
							if(!empty($processTrackData['task_instructions']['formbuilder_data'])){
							foreach ($processTrackData['task_instructions']['formbuilder_data'] as $ele_id=>$frm_data){
								//echo "<pre>",print_r($frm_data);die;
								//following condition removed for IRT-999 on 19-07-2018 by shadman
                                                                
                                                                //if($frm_data['remove'] == 1){ continue; }
                                                                
								$final_value = "";
								$is_change_instruction = '';
								if(isset($processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]) && $processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]!=$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]){
									$is_change_instruction = ' text-warning';
                                                                         $is_change_instruction = ' bg-warning';
								}
                                                                if(isset($processTrackData['task_instructions']['formValues']['lastversion']) && !isset($processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]))
                                                                {
                                                                    $is_change_instruction = ' bg-warning';
                                                                }
								if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
									$values = (new FormBuilder)->getSelectedOptionText($processTrackData['task_instructions']['instruct']->id,$frm_data['form_builder_id'],2);
									$final_value= implode(",",$values);
								}else if($frm_data['type']=='textarea'){
									$final_value.= nl2br(html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']])));
								}else if($frm_data['type']=='text') {
										if($frm_data['remove']==0){
										$is_change_instruction = '';
										$final_value.= html_entity_decode(($frm_data['text_val']));//$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']];
										}
								}else if($frm_data['type']=='number'){	
									$final_value.= $processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']].' '.$processTrackData['task_instructions']['unitValues']['active'][$frm_data['form_builder_id']];
								}else{
									$final_value.= html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]));
								}
								if(isset($final_value) && $final_value!=""){
								?>
                                                        <tr class="<?=$is_change_instruction?>">
								<td headers="instruction_notes" colspan="2" class="v-align-top track-exp-td-task-instructions text-left word-break"><?=$frm_data['label']?></td>
								<td headers="instruction_details" colspan="8" class="v-align-top track-exp-th-task-details word-break text-left "><?= $final_value?></td>
							</tr>
							<?php }}}
							if($attachments!=""){?>
                                                        <tr class="<?=$is_change_instruction?>">
								<td headers="instruction_notes" class="v-align-top instruction_notes text-left word-break track-exp-td-task-instructions" colspan="2">Attachments</td>
								<td headers="instruction_details" class="v-align-top track-exp-td-task-details word-break text-left" colspan="8"><?php echo  $attachments;?></td>
							</tr>	
							<?php } ?>	
					</table>
				
				
<?php } else{ 
			echo "No Records Found.";
	 } ?>
