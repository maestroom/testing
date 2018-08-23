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
$attachments="";
$is_change_instruction_attachments = '';
if(in_array($processTrackData['task_instructions']['instructServicetask']->servicetask_id,$changeServiceAttchmentIds)){
$is_change_instruction_attachments = ' bg-warning'; 
}
if(!empty($processTrackData['task_instructions']['instructServicetask']->instructionAttachments)){
	foreach ($processTrackData['task_instructions']['instructServicetask']->instructionAttachments as $attachment){
		if($attachments == ""){
			$attachments = '<a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname;
		}else{
			$attachments = $attachments . '<br><a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname;//array('id'=>$attachment->id,'name'=>$attachment->fname);
		}
	}
}
?>
<div class="table-responsive">
		<table class="table table-striped table-hover">
        	
           <?php 
				$is_row =0;
				if(!empty($processTrackData['task_instructions']['formbuilder_data'])){
				foreach ($processTrackData['task_instructions']['formbuilder_data'] as $ele_id=>$frm_data){
					if($model->isactive==1 && $frm_data['remove'] == 1){ continue; }
					$final_value = "";
					$is_change_instruction = '';
					if(in_array($frm_data['form_builder_id'],$changeFBIds)){
						$is_change_instruction = ' bg-warning';
					}
                		
                	if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
						$values = (new FormBuilder)->getSelectedOptionText($instruct_id,$frm_data['form_builder_id'],2);
						$final_value=implode(", ",$values);
					}else if($frm_data['type']=='textarea'){
						$final_value.= nl2br(html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']])));
					}else if($frm_data['type']=='number'){	
						$final_value.= $processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']].' '.$processTrackData['task_instructions']['unitValues']['active'][$frm_data['form_builder_id']];
					}else if($frm_data['type']=='text' && $frm_data['text_val']!=""){
						$final_value.=html_entity_decode(($frm_data['text_val']));
					}else{
						$final_value.= html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]));
					}
					?>
			<?php if ($final_value!="" || in_array($frm_data['form_builder_id'],$changeFBIds)) { 
				$is_row = 1;
			    ?>				
				<tr class="<?=$is_change_instruction?>">
                	<td class="text-left word-break" style="width:390px;"><a href="javascript:void(0);" class="tag-header-black" title="<?=$frm_data['label']?>">
					<?php if($frm_data['type']=='text' && $frm_data['text_val']!=""){?>
					<?php 
						echo $final_value;
					?>
					<?php } else{?>
					<strong><?=$frm_data['label']?></strong>
					<?php }?>
					</a></td>			
                	
                    <td class="text-left word-break"><?php if($frm_data['type']!='text'){ echo $final_value;}?></td>
                </tr>
		<?php } ?>
		<?php }} ?>
		<?php if($attachments!=""){ $is_row = 1;?>
			<tr class="<?=$is_change_instruction_attachments?>">
				<td class="text-left" style="width:390px;"><a href="javascript:void(0);" class="tag-header-black" title="Attachments">Attachments</a></td>
				<td class="text-left "><?php echo  $attachments;?></td>
			</tr>	
			<?php }?>
            <?php  if(isset($processTrackData['task_instructions']['notes'])){ 
				$is_row = 1;
		    ?>
                <tr class="warning"> 
                	<td class="text-left"><a href="javascript:void(0);" class="tag-header-black"  title="Instruction Note">Instruction Note</a></td>
                	<td class="text-left"><?=$processTrackData['task_instructions']['notes']?>
                	<?php 
                	$attachment="";
	                if (!empty($processTrackData['task_instructions']['attachments'])) {
		                foreach ($processTrackData['task_instructions']['attachments'] as  $at) {
		                    if ($attachment == "")
		                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                    else
		                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                }
	               }?>
                	<?php echo $attachment?>
                	</td>
                	
				</tr>
                <?php 
                }
                
		if($is_row == 0) { ?>
		    <tr><td colspan="2" class="text-left"><a href="javascript:void(0);" class="tag-header-black"   title="No Instructions Provided">No Instructions Provided</a></td></tr>
		<?php }?>				
		
		</table>
	</div>
