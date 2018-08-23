<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
$prod_ids=array();
$evid_ids=array();
$evid_content_ids=array();
if(!empty($modelInstruct->taskInstructEvidences)){
	foreach ($modelInstruct->taskInstructEvidences as $task_instruct_evid){
		$prod_ids[$task_instruct_evid->prod_id]=$task_instruct_evid->prod_id;
		$evid_ids[$task_instruct_evid->evidence_id]=$task_instruct_evid->evidence_id;
		$evid_content_ids[$task_instruct_evid->evidence_contents_id]=$task_instruct_evid->evidence_contents_id;
	}
}?>
<?php if(!empty($case_media['media'])){?>
	<?php foreach ($case_media['media'] as $media_model){?>
		<div class="myheader" id="selected_media_<?= $media_model->id;?>">
			<a href="javascript:void(0);">Media #<?= $media_model->id;?>  - <?=$media_model->evidencetype->evidence_name; ?>
			<div class="my-head-th-title">Est Size: <?php if (isset($media_model->contents_total_size) && ($media_model->contents_total_size != 0 || $media_model->contents_total_size != "")) {	echo $media_model->contents_total_size . ' ' . $media_model->evidenceunit->unit_name;} else {  	echo $media_model->contents_total_size_comp . ' ' . $media_model->evidencecompunit->unit_name;}?></div></a>
				<div class="pull-right header-checkbox">
					<span class="hide">
						<input type="checkbox" id="chk123_<?= '_media_'.$media_model->id;?>" value="<?= $media_model->id ?>" checked="checked" onclick="toggleCheckboxes('<?= '_media_'.$media_model->id ?>', this);" class="parent_<?=$media_model->id;?> media" name="Evidence[]" aria-label="select evidence" /><label for="chk123_<?= '_media_'.$media_model->id ?>">&nbsp;</label>
					</span>
					<a title="Delete Media" aria-label="Delete Media #<?= $media_model->id;?>  - <?=$media_model->evidencetype->evidence_name;?>" onclick="remove_Media(<?=$media_model->id?>);" href="javascript:void(0);"><em class="fa fa-close"></em></a>
				</div>
			</div>
			<?php if(!empty($case_media['media_content'])){?>
				<div class="content" style="padding:0px;" id="selected_media_contents_<?= $media_model->id;?>"">
					<table class="table table-striped table-hover">
						<thead>
					      <tr>
					           <th class="text-left contents-td"><a href="#" title="Media #">Contents #</a></th>
						       <th class="text-left custodian-td"><a href="#" title="Custodian">Custodian</a></th>
						       <th class="text-left data-type-td"><a href="#" title="Data Type">Data Type</a></th>
					           <th class="text-left data-est-size-td"><a href="#" title="Est Size">Data Type Est Size</a></th>
						       <th>&nbsp;</th>
					     </tr>
					   </thead>
					   <tbody>
				<?php 	  
				$row = 0;     
				foreach ($case_media['media_content'] as $media_content){
						if($media_content->evid_num_id == $media_model->id){
							$row = 1;
					?>
							<tr id="selected_media_content_<?=$media_content->id;?>">
							   <td class="text-left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$media_content->id; ?></td>
						       <td class="text-left"><?php if (isset($media_content->cust_id)){echo $media_content->evidenceCustodians->cust_lname .' '.$media_content->evidenceCustodians->cust_fname,' ,'.$media_content->evidenceCustodians->cust_mi;} ?></td>
						       <td class="text-left"><?=$media_content->datatype->data_type; ?></td>
					           <td class="text-left"><?php if (isset($media_content->data_size)) { echo $media_content->data_size . ' ' .$media_content->evidenceContentUnit->unit_name; } ?></td>
						       <td class="pull-right last-td">
						         	<span class="hide">
						         	<input class="child_media_<?=$media_model->id;?> Evidence_contents " style="left: 7px;" type="checkbox" name="Evidence_contents[]" id="Evidence_content123_<?=$media_content->id;?>" value="<?=$media_model->id.'_'.$media_content->id; ?>" data-value="<?=$media_content->id; ?>" checked="checked" onclick="selectedParentForMedia(<?=$media_model->id;?>);" aria-label="Evidence content <?=$media_content->id; ?>">
						       		<label for="Evidence_content123_<?=$media_content->id;?>">&nbsp;&nbsp;</label>
						       		</span>
						       		<a title="Delete Media Content" aria-label="Delete Media Content #<?=$media_content->id; ?>" onclick="remove_MediaContent(<?=$media_content->id?>);" href="javascript:void(0);"><em class="fa fa-close text-primary"></em></a>
						       </td>
						    </tr>
						
				<?php } }
				if($row==0){ 
				?>
				<tr>
					<td colspan="5" class="text-left">No records found.</td>
				</tr>
				<?php }?>
					</tbody>
				</table>
			</div>
		<?php }?>
<?php }
}?>
