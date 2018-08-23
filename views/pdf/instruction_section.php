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
if(!empty($processTrackData['task_instructions']['instructServicetask']->instructionAttachments)){
	foreach ($processTrackData['task_instructions']['instructServicetask']->instructionAttachments as $attachment){
		if($attachments == ""){
			$attachments = $attachment->fname."<br>";
		}else{
			$attachments = $attachments . $attachment->fname."<br>";//array('id'=>$attachment->id,'name'=>$attachment->fname);
		}
	}
}?>
<div style="padding:0px 3px 8px;">
<table width="100%">
  <!--<thead>
		<tr>
			<th class="text-left" width="30%"><a href="#" title="Instructions / Notes">Instructions / Notes</a></th>
			<th class="text-left" ><a href="#" title="Details">Details</a></th>
		</tr>
   </thead>-->
  <tbody>
    <?php 
   $is_row =0;
		if(!empty($processTrackData['task_instructions']['formbuilder_data'])){
		foreach ($processTrackData['task_instructions']['formbuilder_data'] as $ele_id=>$frm_data){
					if($model->isactive==1 && $frm_data['remove'] == 1){ continue; }
					$final_value = "";
					$is_change_instruction = '';
					if(in_array($frm_data['form_builder_id'],$changeFBIds)){
						$is_change_instruction = ' background-color: #efe18c;border-bottom: 2px solid #fff;';
					}
					/*if(isset($processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]) && $processTrackData['task_instructions']['formValues']['lastversion'][$frm_data['form_builder_id']]!=$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]){
						$is_change_instruction = ' btn-warning';
					}*/
					if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
					    if(isset($instruct_id) && $instruct_id!="")
						$instruct_id = $instruct_id;
					    else
						$instruct_id = $processTrackData['task_instructions']['instruct']->id;
					    
						$values = (new FormBuilder)->getSelectedOptionText($instruct_id,$frm_data['form_builder_id'],2);
						$final_value= implode(",",$values);
						/*if(isset($old_instruction_id) && $old_instruction_id!="" && $old_instruction_id!=0){
							$oldvalue = (new FormBuilder)->getSelectedOptionText($old_instruction_id,$frm_data['form_builder_id'],2);
							if(count($oldvalue)!= count($values)){
								$is_change_instruction = ' btn-warning';
							}
						}*/
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
    <tr style="<?=$is_change_instruction?>">
      <td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px; width:30%;">
			<?php if($frm_data['type']=='text' && $frm_data['text_val']!=""){?>
					<?php 
						echo $final_value;
					?>
					<?php } else{?>
					<strong><?=$frm_data['label']?></strong>
					<?php }?>
			</td>
      <td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;" class=""><?php if($frm_data['type']!='text'){ echo $final_value;}?></td>
    </tr>
    <?php } ?>
    <?php }}?>
     <?php if($attachments!=""){ $is_row = 1;?>
                <tr>
                	<td class="text-left" width="25%">Attachments</td>
                	<td class="text-left"><?php echo  $attachments;?></td>
                </tr>	
                <?php }?>
		<?php if(isset($processTrackData['task_instructions']['notes'])){
			$is_row = 1;
			?>
    <tr>
      <td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px; width:30%;"><strong>Instruction Note</strong></td>
      <td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?=$processTrackData['task_instructions']['notes']?>
        <?php 
			$attachment="";
			if (!empty($processTrackData['task_instructions']['attachments'])) {
				foreach ($processTrackData['task_instructions']['attachments'] as  $at) {
					if ($attachment == "")
						$attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader"></span></a>';
					else
						$attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader"></span></a>';
				}
		   }?>
        <?php //echo strip_tags($attachment)?> </td>
    </tr>
    <?php }?>
    <?php if($is_row == 0) { ?>
    <tr>
      <td colspan="2" align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;">No Instructions Provided</td>
    </tr>
    <?php }?>
  </tbody>
</table>
</div>