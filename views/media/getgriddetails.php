<?php 
use app\models\ClientCaseEvidence;
use app\models\Options;
use yii\helpers\Html;
//echo "<pre>",print_r($media_form),"</pre>";die;
?>
<div class="table-responsive">
   <table class="table table-striped table-hover">
	<tbody>
        <?php if(!empty($media_form))
        { ?>
        <!-- Media Content -->
        <tr>
            <th scope="row" align="left" width="15%" id="contents"><a href="javascript:void(0);" title="Contents?" class="tag-header-black tag-header-cursor-default">Contents?</a></th>
            <td align="left" headers="contents"><?php if($data->has_contents==1){ echo '<span tabindex="0" class="fa fa-check text-danger" title="Has Contents"></span>'; }else {echo ""; }?></td>
        </tr>
        <!-- Projects -->
        <tr>
            <th scope="row" align="left" width="15%" id="projects"><a href="javascript:void(0);" title="Projects?" class="tag-header-black tag-header-cursor-default">Projects?</a></th>
            <td align="left" headers="projects"><?php echo $tasks_ids?></td>
        </tr>
        <!-- Productions -->
        <tr>
            <th scope="row" align="left" width="15%" id="productions"><a href="javascript:void(0);" title="Productions?" class="tag-header-black tag-header-cursor-default">Productions?</a></th>
            <td align="left" headers="productions"><?php echo $prods_ids ?></td>
        </tr>
		<?php foreach($media_form as $column){
				//if($column=='has_contents'){  ?>
					<?php /*} else*/ if($column=='upload_files') { ?>
						<tr>
							<th scope="row" align="left" width="15%" id="attach"><a href="javascript:void(0);" title="Attach?" class="tag-header-black tag-header-cursor-default">Attach?</a></th>
							<td align="left" headers="attach">
								<?php 
									if (!empty($data['evidenceattachments'])) {
										foreach ($data['evidenceattachments'] as $at) {
    										if ($attachment == "")
    											$attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
    										else
    											$attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
    									}
									}
							   ?>
							   <?php echo $attachment?>
							</td>
						</tr>
					<?php } else if($column=='status'){ ?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php echo  $data->getStatusImage($data->status);?></td>
							</tr> 
						<?php } else if($column=='client_id'){?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php echo  (new ClientCaseEvidence)->getEvidenceClients($data->id);?></td>
							</tr> 
						<?php } else if($column=='client_case_id'){?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php echo (new ClientCaseEvidence)->getEvidenceCases($data->id);;?></td>
							</tr> 
						<?php } else if($column=='received_date'){
                        
                        ?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php 
                                $org_time=$data->received_time;
		                        $org_date=$data->received_date;
                                $received_date_time = $org_date.' '.$org_time;
                                echo (new Options)->ConvertOneTzToAnotherTz($received_date_time, 'UTC', $_SESSION['usrTZ'],"date");?></td>
							</tr> 
						<?php }else if($column=='received_time'){
                        
                        ?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php 
                                $org_time=$data->received_time;
		                        $org_date=$data->received_date;
                                $received_date_time = $org_date.' '.$org_time;
                                echo (new Options)->ConvertOneTzToAnotherTz($received_date_time, 'UTC', $_SESSION['usrTZ'],"time");?></td>
							</tr> 
						<?php }else if($column=='evid_type'){?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php echo $data->evidencetype->evidence_name;;?></td>
							</tr> 
						<?php }else if($column=='cat_id'){ ?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?= $column ?>"><?php echo $data->evidencecategory->category; ?></td>
							</tr> 
						<?php } else if($column=='unit'){ ?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php echo $data->evidenceunit->unit_name; ?></td>
							</tr> 
						<?php } else if($column=='comp_unit'){ ?>
							<tr>
								<th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
								<td align="left" headers="<?=$column?>"><?php echo $data->evidencecompunit->unit_name; ?></td>
							</tr> 
						<?php } else if($column=='evid_stored_location'){?>
                            <tr>
                                <th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
                                <td align="left" headers="<?=$column?>"><?php echo $data->evidencestoredloc->stored_loc?></td>
                            </tr>
                        <?php }else if($column=='enctype'){?>
                            <tr>
                                <th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
                                <td align="left" headers="<?=$column?>"><?php echo $data->evidenceencrypttype->encrypt?></td>
                            </tr>
                        <?php } else if($column=='dup_evid'){?>
                            <tr>
                                <th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
                                <td align="left" headers="<?=$column?>"><?=($data->dup_evid==1)?'Yes':'No';?></td>
                            </tr>
                        <?php } else if($column=='org_link'){?>
                            <tr>
                                <th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
                                <td align="left" headers="<?=$column?>"><?php if(isset($data->org_link)) { 
                                echo Html::a($data->org_link, array('media/index', 'EvidenceSearch[id]' => $data->org_link),['title'=>$data->org_link, 'data-pjax' => '0']);
                                }?></td>
                            </tr>
                        <?php } else if($column=='created_by'){?>
                            <tr>
                                <th scope="row" align="left" width="15%" id="<?=$column?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
                                <td align="left" headers="<?=$column?>"><?php 
                                echo (new app\models\Evidence)->getCreatedByUser($data->created_by);
                                ?></td>
                            </tr>
                        <?php } else { // ?>
    						<tr>
    							<th scope="row" align="left" width="15%" id="<?=$column;?>"><a href="javascript:void(0);" title="<?=$data->getAttributeLabel($column);?>" class="tag-header-black tag-header-cursor-default"><?=$data->getAttributeLabel($column);?></a></th>
    							<td align="left" headers="<?=$column?>"><?php echo htmlentities($data->$column)?></td>
    						</tr>
					<?php } ?>
			 <?php }
		}else{ ?>
        <!-- <tr>
            <th scope="row" align="left" width="15%" id="contents"><a href="javascript:void(0);" title="Contents?" class="tag-header-black tag-header-cursor-default">Contents?</a></th>
            <td align="left" headers="contents"><?php /*if($data->has_contents==1){ echo '<em class="fa fa-check text-danger" title="Contents"></em>'; }else {echo "";}?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="projects"><a href="javascript:void(0);" title="Projects?" class="tag-header-black tag-header-cursor-default">Projects?</a></th>
            <td align="left" headers="projects"><?php echo $tasks_ids?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="productions"><a href="javascript:void(0);" title="Productions?" class="tag-header-black tag-header-cursor-default">Productions?</a></th>
            <td align="left" headers="productions"><?php echo $prods_ids */ ?>
        <!-- </td>
        </tr> -->
        <tr>
            <th scope="row" align="left" width="15%" id="attach"><a href="javascript:void(0);" title="Attach?" class="tag-header-black tag-header-cursor-default">Attach?</a></th>
            <td align="left" headers="attach">
                <?php 
	                if (!empty($data['evidenceattachments'])) {
		                foreach ($data['evidenceattachments'] as $at) {
		                    if ($attachment == "")
		                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                    else
		                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                }
               		}
               ?>
               <?php echo $attachment ?>
            </td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="is_duplicate"><a href="javascript:void(0);" title="Is Duplicate" class="tag-header-black tag-header-cursor-default">Is Duplicate</a></th>
            <td align="left" headers="is_duplicate"><?php if($data->dup_evid==1){ echo '<a href="javascript:void(0); class="icon-fa" title="Duplicate Media #"><em title="Duplicate Media #" class="fa fa-check text-danger"></em></a> &nbsp;(<a title="Duplicate Media #'.$data->org_link.'" href="javascript:void(0);" onclick="go_toMedia('.$data->org_link.');">'.$data->org_link.'</a>)'; }else {echo "";}?></td>       
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="received_from"><a href="javascript:void(0);" title="Received From" class="tag-header-black tag-header-cursor-default">Received From</a></th>
            <td align="left" headers="received_from"><?php echo $data->received_from ?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="uniqueid_serial"><a href="javascript:void(0);" title="Unique ID/Serial" class="tag-header-black tag-header-cursor-default">Unique ID/Serial</a></th>
            <td align="left" headers="uniqueid_serial"><?php echo $data->serial?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="model"><a href="javascript:void(0);" title="Model" class="tag-header-black tag-header-cursor-default">Model</a></th>
            <td align="left" headers="model"><?php echo $data->model?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="media_label_description"><a href="javascript:void(0);" title="Media Label Description" class="tag-header-black tag-header-cursor-default">Media Label Description</a></th>
            <td align="left" headers="media_label_description"><?php echo $data->evid_label_desc?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="internal_media"><a href="javascript:void(0);" title="Internal Media #" class="tag-header-black tag-header-cursor-default">Internal Media #</a></th>
            <td align="left" headers="internal_media"><?php echo $data->evd_Internal_no?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="other_media"><a href="javascript:void(0);" title="Other Media #" class="tag-header-black tag-header-cursor-default">Other Media #</a></th>
            <td align="left" headers="other_media"><?php echo $data->other_evid_num ?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%" id="begbates"><a href="javascript:void(0);" title="Begbates" class="tag-header-black tag-header-cursor-default">Begbates</a></th>
            <td align="left" headers="begbates"><?php echo $data->bbates?></td>
        </tr>
    	<tr>
            <th scope="row" align="left" width="15%" id="endbates"><a href="javascript:void(0);" title="Endbates" class="tag-header-black tag-header-cursor-default">Endbates</a></th>
            <td align="left" headers="endbates"><?php echo $data->ebates?></td>
    	</tr>
    	<tr>
            <th scope="row" align="left" width="15%" id="media_notes"><a href="javascript:void(0);" title="Media Notes" class="tag-header-black tag-header-cursor-default">Media Notes</a></th>
            <td align="left" headers="media_notes"><?php echo $data->evid_notes?></td>
    	</tr>
         <tr>
            <th scope="row" align="left" width="15%" id="stored_location"><a href="javascript:void(0);" title="Stored Location" class="tag-header-black tag-header-cursor-default">Stored Location</a></th>
            <td align="left" headers="stored_location"><?php echo $data->evidencestoredloc->stored_loc?></td>
        </tr>
         <tr>
            <th scope="row" align="left" width="15%" id="container"><a href="javascript:void(0);" title="Container #" class="tag-header-black tag-header-cursor-default">Container #</a></th>
            <td align="left" headers="container"><?php echo $data->cont?></td>
        </tr>
         <tr>
            <th scope="row" align="left" width="15%" id="checkedin_by"><a href="javascript:void(0);" title="Checked In By" class="tag-header-black tag-header-cursor-default">Checked In By</a></th>
            <td align="left" headers="checkedin_by"><?php echo $data->evidencecheckedin->usr_first_name." ".$data->evidencecheckedin->usr_lastname;?></td>
        </tr>
        <?php }?>
	</tbody>
   </table>
</div>
<?php /*<table cellpadding="5" cellspacing="0" border="0" class="subtable" style="font-family: arial; font-size: 13px !important;  padding: 8px; width: 100%;">
    <tr>
        <td width="15%">Comment:</td>
        <td><label tabindex="0"><?php 
        $has_access_408=User::model()->checkAccess(4.08);
        echo Task::model()->findReadUnreadComment($viewClosedTask->id,$viewClosedTask->client_case_id,$has_access_408); ?></label></td>
    </tr>
    <tr>
        <td width="15%">Service:</td>
        <td><label tabindex="0"><?php echo $services; ?></label></td>
    </tr>
    <tr>
        <td width="15%">Submitted By:</td>
        <td><label tabindex="0"><?php if(!empty($viewClosedTask->createdUser)) { 
        		if((isset($viewClosedTask->createdUser->usr_first_name) && $viewClosedTask->createdUser->usr_first_name!="") && (isset($viewClosedTask->createdUser->usr_lastname) && $viewClosedTask->createdUser->usr_lastname!="")){
        			echo $viewClosedTask->createdUser->usr_first_name." ".$viewClosedTask->createdUser->usr_lastname;
        		}else{
        			echo $viewClosedTask->createdUser->usr_username;
        		}
        }; ?></label></td>
    </tr>
    <tr>
        <td width="15%">Submitted Date:</td>
        <td><label tabindex="0"><?php echo Options::model()->ConvertOneTzToAnotherTz($viewClosedTask->created,"UTC",$_SESSION["usrTZ"]); ?></label></td>
    </tr>
</table>*/?>
