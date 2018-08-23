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
			$attachments = '<a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname;
		}else{
			$attachments = $attachments . '<br><a href="javascript:void(0)" onclick="downloadattachment(' . $attachment->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>'.$attachment->fname;//array('id'=>$attachment->id,'name'=>$attachment->fname);
		}
	}
}
//echo "<pre>",print_r($processTrackData['task_instructions']),"</pre>";
?>
<div class="table-responsive shadman">
		<table class="table table-hover" id="tbl_instructions">
        	<!--<thead>
            	<tr>
                	<th class="text-left" width="25%"><a href="#" title="Instructions / Notes">Instructions / Notes</a></th>
					<th class="text-left" ><a href="#" title="Details">Details</a></th>
                </tr>
           </thead>
           <tbody>-->
           <?php 
				$is_row =0;
				if(!empty($processTrackData['task_instructions']['formbuilder_data'])){
				foreach ($processTrackData['task_instructions']['formbuilder_data'] as $ele_id=>$frm_data){
					
                                        //if($frm_data['remove'] == 1){ continue; }
                                    
                                        //echo "<pre>",print_r($frm_data);
					$final_value = "";
					$is_change_instruction = '';
					if(in_array($frm_data['form_builder_id'],$changeFBIds)){
						$is_change_instruction = ' bg-warning';
					}
					
					if($frm_data['type'] == 'dropdown' || $frm_data['type'] == 'radio' || $frm_data['type'] == 'checkbox' ){
						$values = (new FormBuilder)->getSelectedOptionText($processTrackData['task_instructions']['instruct']->id,$frm_data['form_builder_id'],2);
						$final_value=implode(", ",$values);
					}else if($frm_data['type']=='textarea'){
						$final_value.= nl2br(html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']])));
					}else if($frm_data['type']=='number'){	
						$final_value.= $processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']].' '.$processTrackData['task_instructions']['unitValues']['active'][$frm_data['form_builder_id']];
					}else if($frm_data['type']=='text' && $frm_data['text_val']!=""){
                                            
                                                //following line commented on 20-07-2018 by shadman
                                                //$final_value.= html_entity_decode(($frm_data['text_val']));
                                                
                                                //following line added on 20-07-2018 by shadman
                                                if($frm_data['remove']==0){
                                                    $is_change_instruction = '';
                                                    $final_value.= html_entity_decode($frm_data['text_val']);//$processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']];
                                                }                                                
                                                
					}else{
						$final_value.= html_entity_decode(htmlspecialchars($processTrackData['task_instructions']['formValues']['active'][$frm_data['form_builder_id']]));
					}
					?>
                    
                    
			<?php 
                        //$final_value!="" || in_array($frm_data['form_builder_id'],$changeFBIds)
                        
                        if (isset($final_value) && $final_value!="") { 
				//echo $frm_data['form_builder_id'];
			    $is_row = 1;
			    ?>		
           		<tr class="<?=$is_change_instruction?>">
                	<td class="text-left word-break" style="width:390px;"><a href="javascript:void(0);" class="tag-header-black" title="<?=$frm_data['label']?>">
					
					<?php if($frm_data['type']=='text' && $frm_data['text_val']!=""){?>
					<?php echo $final_value;?>
					<?php } else{?>
					<strong><?=$frm_data['label']?></strong>
					<?php }?>
					
					</a></td>
                	
                    <td class="text-left word-break"><?php if($frm_data['type']!='text'){ echo $final_value;}?></td>
                </tr>
		<?php } ?> 
                <?php }}?>
                <?php if($attachments!=""){ $is_row = 1;?>
                <tr>
                	<td class="text-left" style="width:390px;"><a href="javascript:void(0);" class="tag-header-black" title="Attachments">Attachments</a></td>
                	<td class="text-left <?=$is_change_instruction?>"><?php echo  $attachments;?></td>
                </tr>	
                <?php }?>
                <?php if(isset($processTrackData['task_instructions']['notes'])){
		    $is_row = 1;
		    ?>
                <tr class="warning"> 
                	<td class="text-left" style="width:390px;"><a href="javascript:void(0);" class="tag-header-black"  title="Instruction Note"><strong>Instruction Note</strong></a></td> <!-- width="25%" -->
                	<td class="text-left"><?=$processTrackData['task_instructions']['notes']?>
                	<?php 
                	$attachment="";
	                if (!empty($processTrackData['task_instructions']['attachments'])) {
		                foreach ($processTrackData['task_instructions']['attachments'] as  $at) {
		                    if ($attachment == "")
		                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                    else
		                        $attachment .='<br><a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
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
			<!--</tbody>-->
		</table>
	</div>
