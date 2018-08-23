<?php use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Options;
use app\models\User;
use app\models\ProjectSecurity;
use app\models\FormBuilder;

$checkAccess = (new ProjectSecurity)->checkTeamAccess($teamId,$team_loc); 

 if(!empty($processTrackData['attachments']) || !empty($processTrackData['tasksUnitDatas'])) { 
?>
<!-- <div class="table-responsive">
	<table class="table table-striped table-hover">
	<thead>--> 
			<tr>
				<th scope="col" class="text-left track-exp-th-1" id="data_statistics" style="width:10%;"><a href="#" title="Task Outcome Items">Task Outcome Items<!--Data Statistics--></a></th>
				<th scope="col" class="text-left track-exp-th-2" id="data_media" style="width:10%;"><a href="#" title="Media #">Media #</a></th>
				<th scope="col" class="text-left track-exp-th-data-details" id="data_details" colspan="2" style="width:30%;"><a href="#" title="Details">Details</a></th>
				<th scope="col" class="text-left track-exp-th-5" id="data_first"><a href="#" title="Created By">Created By</a></th>
				<th scope="col" class="text-right track-exp-th-9" id="data_sixth">&nbsp;</th>
				<th scope="col" class="text-right track-exp-th-9" id="data_sixth">&nbsp;</th>
				<th scope="col" class="text-right track-exp-th-10" id="data_seventh" colspan="3">Action</th>
			</tr>
		<!-- </thead>
		<tbody> -->
			<?php 
			$formbuilder_id=array();
			foreach ($processTrackData['tasksUnitDatas'] as $tasksUnitDatas) {
				//if(!in_array($tasksUnitDatas->form_builder_id."||".$tasksUnitDatas->created,$formbuilder_id))
		//{
					if (in_array($tasksUnitDatas->formBuilder->element_type,array('checkbox'))) {
						$time_fbid=$tasksUnitDatas->created.'_'.$tasksUnitDatas->form_builder_id;
						if(in_array($time_fbid,$formbuilder_id)){
							continue;
						}
						$formbuilder_id[$time_fbid]=$time_fbid;
					}	
			?>
			 <tr>
				<td class="v-align-top text-left track-exp-td-1 word-break" headers="data_statistics" ><?php echo $tasksUnitDatas->formBuilder->element_label; ?></td>
				<td class="v-align-top text-left track-exp-td-2 word-break" headers="data_media">
					<?php if($tasksUnitDatas->evid_num_id!=0) {
								if ((new User)->checkAccess(3)) {?>
									<a href="javascript:go_toMedia('<?= $tasksUnitDatas->evid_num_id; ?>')" title="Media #<?php echo $tasksUnitDatas->evid_num_id; ?>"><?= $tasksUnitDatas->evid_num_id;?></a>
							<?php } else { 
								echo $tasksUnitDatas->evid_num_id;
								}
					}?></td>
				<td class="v-align-top text-left  track-exp-th-data-details word-break" headers="data_details" colspan="2" >
					<?php if (in_array($tasksUnitDatas->formBuilder->element_type,array('checkbox'))) {
							$i=0;
							$value_array=array();
							$value_array = (new FormBuilder)->getSelectedOption($tasksUnitDatas->modified,$tasksUnitDatas->form_builder_id,2);
								echo implode(", ",$value_array);
							} else if (in_array($tasksUnitDatas->formBuilder->element_type,array('dropdown','radio'))) {
								echo (new FormBuilder)->getSelectedElementOption($tasksUnitDatas->element_value);
							} else {
								echo $tasksUnitDatas->element_value;
							}
							if($tasksUnitDatas->element_unit > 0){
								echo " ".$tasksUnitDatas->unit->unit_name;
							}
					?>
				
				</td>
				<td class="v-align-top text-left word-break" headers="data_second">
					<?php 
						$data_unit_date=(new Options)->ConvertOneTzToAnotherTz($tasksUnitDatas->modified, 'UTC', $_SESSION['usrTZ']);
						if(!$checkAccess){
							echo "<span title='Created by'>User</span>";
						} else {
							echo "<span title='{$data_unit_date}'>".$tasksUnitDatas->createdUser->usr_first_name.' '.$tasksUnitDatas->createdUser->usr_lastname."</span>";
						}
					?>
				</td>
				<td class="v-align-top text-right track-exp-td-7" headers="data_fourth">&nbsp;</td>
				<td class="v-align-top text-right  track-exp-td-8" headers="data_fifth">&nbsp;</td>
				<td class="v-align-top text-right  track-exp-td-8" headers="data_fifth">&nbsp;</td>
				<td class="v-align-top text-right td-no-pad track-exp-td-9" title="Edit Item" headers="data_sixth">
				<?php 
				                $onclick = "javascript:EditDataItem(".$tasksUnitDatas->id.");";
                                if(!$checkAccess){
                                            $onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
                                }?>
                <a href="<?=$onclick?>" class="text-primary track-icon"><em title="Edit Item" class="fa fa-pencil"></em></a>
				</td>
				<td class="v-align-top text-right td-no-pad track-exp-td-10 word-break" title="Delete Item" headers="data_seventh">
				<?php 
                                    $onclick="javascript:alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
                                    if (((new User)->checkAccess(4.0711) && $case_id != 0) || ((new User)->checkAccess(5.06101) && $team_id != 0)) { 
                                        $onclick = "javascript:DeleteDataItem(".$tasksUnitDatas->id.",'".$tasksUnitDatas->formBuilder->element_label."');";
                                    }
                                    if(!$checkAccess){
                                        $onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
                                    }
				?>
				<a href="<?=$onclick?>" class="text-primary track-icon"><em title="Delete Item" class="fa fa-close"></em></a>
				</td>
			</tr>
			<?php //}
			$formbuilder_id[$tasksUnitDatas->form_builder_id]=$tasksUnitDatas->form_builder_id."||".$tasksUnitDatas->created;
			}?>
			<?php if(!empty($processTrackData['attachments'])) { ?>
			<tr>
				<td><strong>Attachments</strong></td>
				<td colspan="3">
					<?php $attachment="";
					    if (!empty($processTrackData['attachments'])) {
					        foreach ($processTrackData['attachments'] as  $at) {
					        	$AssingTo = $at->user->usr_first_name.' '.$at->user->usr_lastname;
					            if ($attachment == "")
			                        $attachment ='<a href="javascript:void(0);" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title='.$at->fname.'><em title='.$at->fname.' class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
			                    else
			                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" title='.$at->fname.' class="icon-fa" title="Attachment"><em title='.$at->fname.' class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
			                }
		               } echo "&nbsp;".$attachment 
		            ?>
	            </td>
	            <td colspan="4"><?= $AssingTo ?></td>
	            <td>
	            	<a href="javascript:void(0);" class="text-primary track-icon" onClick="EditDataItemAttachment('<?php echo $taskunit_id; ?>');"><em title="Edit" class="fa fa-pencil"></em></a>
	            </td>
				<td>&nbsp;</td>
	       </tr>
	       <?php } else { ?>
	       <tr class="hide-tr">
				<td class="hide-td"><strong>Attachments</strong></td>
				<td colspan="3" class="hide-td">&nbsp;</td>
	            <td colspan="4" class="hide-td">&nbsp;</td>
	            <td class="hide-td" colspan="2">&nbsp;</td>
	       </tr>
	       <?php }?>
		<!-- </tbody>	
	</table>
</div>-->

<?php } else {?>
			<tr class="hide-tr">
				<th scope="col" class="text-left track-exp-th-1 hide-td" id="data_statistics" style="width:10%;"><a href="#" title="Task Outcome Items">Task Outcome Items<!--Data Statistics--></a></th>
				<th scope="col" class="text-left track-exp-th-2 hide-td" id="data_media" style="width:10%;"><a href="#" title="Media #">Media #</a></th>
				<th scope="col" class="text-left track-exp-th-data-details hide-td" id="data_details" colspan="2" style="width:30%;"><a href="#" title="Details">Details</a></th>
				<th scope="col" class="text-left track-exp-th-5 hide-td" id="data_first"><a href="#" title="Created By">Created By</a></th>
				<th scope="col" class="text-right track-exp-th-9 hide-td" id="data_sixth">&nbsp;</th>
				<th scope="col" class="text-right track-exp-th-9 hide-td" id="data_sixth">&nbsp;</th>
				<th scope="col" class="text-right track-exp-th-10 hide-td" id="data_seventh" colspan="3">Action</th>
			</tr>
			<tr class="hide-tr">
				<td class="v-align-top text-left track-exp-td-1 word-break hide-td" headers="data_statistics" >&nbsp;</td>
				<td class="v-align-top text-left track-exp-td-2 word-break hide-td" headers="data_media">&nbsp;</td>
				<td class="v-align-top text-left  track-exp-th-data-details word-break hide-td" headers="data_details" colspan="2" >&nbsp;</td>
				<td class="v-align-top text-left word-break hide-td" headers="data_second">&nbsp;</td>
				<td class="v-align-top text-right track-exp-td-7 hide-td" headers="data_fourth">&nbsp;</td>
				<td class="v-align-top text-right  track-exp-td-8 hide-td" headers="data_fifth">&nbsp;</td>
				<td class="v-align-top text-right  track-exp-td-8 hide-td" headers="data_fifth">&nbsp;</td>
				<td class="v-align-top text-right td-no-pad track-exp-td-9 hide-td" title="Edit Item" headers="data_sixth">&nbsp;</td>
				<td class="v-align-top text-right td-no-pad track-exp-td-10 word-break hide-td" title="Delete Item" headers="data_seventh">&nbsp;</td>
			</tr>
			<tr class="hide-tr">
				<td class="hide-td"><strong>Attachments</strong></td>
				<td colspan="3" class="hide-td">&nbsp;</td>
	            <td colspan="4" class="hide-td">&nbsp;</td>
	            <td class="hide-td" colspan="2">&nbsp;</td>
	       </tr>
<?php }?>
