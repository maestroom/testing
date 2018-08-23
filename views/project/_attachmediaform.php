<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\EvidenceProduction;
use app\models\TaskInstructServicetask;
use yii\db\Query;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;

$instruct_id=($modelInstruct->isNewRecord)?0:$modelInstruct->id;
$prod_ids=array();
$evid_ids=array();
$attach_media=array();
$attach_media_content=array();
$evid_content_ids=array();
if(!empty($modelInstruct->taskInstructEvidences)){
	foreach ($modelInstruct->taskInstructEvidences as $task_instruct_evid){
		$prod_ids[$task_instruct_evid->prod_id]=$task_instruct_evid->prod_id;
		$attach_media[$task_instruct_evid->prod_id."_".$task_instruct_evid->evidence_id]=$task_instruct_evid->prod_id."_".$task_instruct_evid->evidence_id;
		$attach_media_content[$task_instruct_evid->prod_id."_".$task_instruct_evid->evidence_id."_".$task_instruct_evid->evidence_contents_id]=$task_instruct_evid->prod_id."_".$task_instruct_evid->evidence_id."_".$task_instruct_evid->evidence_contents_id;
		$evid_ids[$task_instruct_evid->evidence_id]=$task_instruct_evid->evidence_id;
		$evid_content_ids[$task_instruct_evid->evidence_contents_id]=$task_instruct_evid->evidence_contents_id;
	}
}


if(!empty($prod_ids) && ((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==2))){
	$query = EvidenceProduction::find()->select(['tbl_evidence_production.*','tbl_evidence_production_media.prod_id','tbl_evidence_production_media.evid_id'])->where(['tbl_evidence_production.client_case_id' => $case_id])->limit(10)->orderBy('tbl_evidence_production.created desc');
	$query->join('INNER JOIN','tbl_evidence_production_media','tbl_evidence_production_media.prod_id=tbl_evidence_production.id');
	$query->join('INNER JOIN','tbl_evidence','tbl_evidence.id=tbl_evidence_production_media.evid_id');
	$query->join('LEFT JOIN','tbl_evidence_contents','tbl_evidence.id=tbl_evidence_contents.evid_num_id');
	if($prod_ids!=""){
		$query->andWhere('tbl_evidence_production.id IN('.implode(",",$prod_ids).')');
	}
	$selectedcaseproductions = $query->all();
	if(count($selectedcaseproductions) == 0){
		$prod_ids=$evid_ids=array();
			
	}
}
if(!empty($evid_ids) && ((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==1))){
	$case_media=(new TaskInstructServicetask())->getSelectedMedias($case_id,implode(",",$evid_ids),implode(",",$evid_content_ids));
}

//echo "<pre>",print_r($evid_ids),print_r($prod_ids),"</pre>";
?>
<fieldset class="one-cols-fieldset">
    <div class="template_wrkflow-main next">
    	<div class="template_wrkflow-right">
    		<div class="head-title"><a href="javascript:void(0);" class="tag-header-black" title="Attach Media or Production to Project" class="">Attach Media or Production to Project</a>
    		<div class="icon-set pull-right">
    			<?php  if(isset($flag) && $flag=='Edit'){
					if(empty($evid_ids) && empty($prod_ids)){ ?>
						<a title="Add Media to the Project" aria-label="Attach Media to Project"  href="javascript:void(0);" aria-haspopup="true" onClick="AttachMedia(<?=$case_id?>);"><span class="fa fa-plus text-primary"></span> Media</a>
						<a title="Add Production(s) to the Project" aria-label="Attach Production to Project"  href="javascript:void(0);" aria-haspopup="true" onClick="AttachProduction(<?=$case_id?>);"><span class="fa fa-plus text-primary"></span> Production</a>
					<?php }else{?>
    				
    				<a id="att_media" style="display:<?php if((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==2)){?>none;<?php }?>" title="Add Media to the Project"  href="javascript:void(0);" onClick="AttachMedia(<?=$case_id?>);"><span class="fa fa-plus text-primary"></span> Media</a>
    				<a id="att_prod" style="display:<?php if((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==1)){?>none;<?php }?>"  title="Add Production(s) to the Project"  href="javascript:void(0);" onClick="AttachProduction(<?=$case_id?>);"><span class="fa fa-plus text-primary"></span> Production</a>
    				
    			<?php } }else{?>
				<a title="Add Media to the Project"  href="javascript:void(0);" aria-label="Attach Media to Project" aria-haspopup="true" onClick="AttachMedia(<?=$case_id?>);"><span class="fa fa-plus text-primary"></span> Media</a>
				<a title="Add Production(s) to the Project"  href="javascript:void(0);" aria-label="Attach Production to Project" aria-haspopup="true" onClick="AttachProduction(<?=$case_id?>);"><span class="fa fa-plus text-primary"></span> Production</a>
				<?php }?>
				<input type="hidden" name="display_by" id="mediadisplay_by" <?php if((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==1) || $modelInstruct->isNewRecord)  {?>value="M"<?php } elseif(isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==2) {?> value="PM" <?php } else { ?> value="M" <?php }?> />
			</div>
		</div>
		
    	<div class="list_media_production mycontainer">
    		<?php if(isset($flag) && ($flag=='Saved' || $flag=='Edit')) {
    			if(isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==2) { /*PM*/
    			?>
    			<?php	
if(!empty($selectedcaseproductions)){?>
<?php foreach($selectedcaseproductions as $key => $val) 
{
	if(!empty($val->productionmedia)) 
	{
?>	
<div class="myheader" id="selected_production_<?= $val->id ?>">
<a href="javascript:void(0);">Production #<?= $val->id ?> - Received <?= date("m/d/Y",strtotime($val->prod_rec_date)) ?>
<div class="my-head-th-title">Production By <?= $val->prod_party ?></div></a>
<div class="pull-right header-checkbox">
<span class="hide">
		<input type="checkbox" id="chk12345_<?= $val->id ?>" value="<?= $val->id ?>" onclick="checkProdChild(<?php echo $val->id;?>);" class="parent_<?php echo $val->id;?> Production prod_<?= $val->id ?>" name="Production[<?= $val->id ?>]" checked="checked" />
		<label for="chk12345_<?= $val->id ?>">&nbsp;</label>
</span>
<a title="Delete Production" onclick="remove_Prod(<?=$val->id?>);" href="javascript:void(0);"><em title="Delete Production" class="fa fa-close"></em></a>
</div></div>

<div class="content selected_production_<?= $val->id ?>" style="padding:0px;">
    
    <table class="table table-striped table-hover">
		<thead>
             <tr>
               <th class="text-left media-td"><a href="#" title="Media #">Media #</a></th>
	       	   <th class="text-left onhold-td"><a href="#" title="On Hold?">On Hold?</a></th>
               <th class="text-left media-type-td"><a href="#" title="Media Type">Media Type</a></th>
               <th class="text-left media-dir-cus-td"><a href="#" title="Media Description / Custodian">Media Description / Custodian</a></th>
               <th class="text-center quantity-td"><a href="#" title="Quantity">Quantity</a></th>
               <th class="text-left est-size-td"><a href="#" title="Est Size">Est Size</a></th>
	       <th>&nbsp;</th>
             </tr>
       </thead>
       <tbody>
       	<?php 
       	if(!empty($val->productionmedia)) {
       	foreach($val->productionmedia as $mediaModel){ if(!in_array($val->id.'_'.$mediaModel->evid_id,$attach_media)){ continue;}?>	
       		<tr id="selected_media_<?= $val->id ?>_<?php echo $mediaModel->evid_id;?>">
				<td align="left"><?= $mediaModel->evid_id;?></td>
				<td align="left"><?php if($mediaModel->on_hold==1) { ?><em title="On Hold" class="fa fa-check text-danger"></em><?php } else{?>&nbsp;<?php }?></td>
				<td align="left"><?=$mediaModel->proevidence->evidencetype->evidence_name; ?></td>
				<td align="left"><?=$mediaModel->proevidence->evid_desc?></td>
				<td align="center"><?=$mediaModel->proevidence->quantity?></td>
				<td align="left">
				<?php 
				 if (isset($mediaModel->proevidence->contents_total_size) && ($mediaModel->proevidence->contents_total_size != 0 || $mediaModel->proevidence->contents_total_size != "")) {
					echo $mediaModel->proevidence->contents_total_size . ' ' . $mediaModel->proevidence->evidenceunit->unit_name;
				} else {
                	echo $mediaModel->proevidence->contents_total_size_comp . ' ' . $mediaModel->proevidence->evidencecompunit->unit_name;
                }?>
				</td>
				<td class="pull-right last-td">
					<span class="hide">
						<input type="checkbox" id="mediachk123_<?= $val->id ?>_<?= $mediaModel->evid_id ?>" data-prod_id="<?= $val->id ?>" value="<?= $mediaModel->evid_id ?>" checked="checked" class="child_<?php echo $val->id;?> child1_<?php echo $mediaModel->evid_id?> prod_<?=$val->id?>_<?= $mediaModel->evid_id?> media" name="Production[<?=$val->id?>][<?= $mediaModel->evid_id?>]" onclick="checkParent(<?php echo $val->id;?>);checkChildCont(<?php echo $val->id;?>,<?php echo $mediaModel->evid_id;?>);" />
						<label for="mediachk123_<?= $val->id ?>_<?= $mediaModel->evid_id ?>">&nbsp;</label>
					</span>	
					<a title="Delete Media" onclick="remove_ProdMedia(<?=$mediaModel->evid_id;?>,<?=$val->id;?>);" href="javascript:void(0);"><em title="Delete Media" class="fa fa-close"></em></a>
				</td>
			</tr>
			
			<?php foreach ($mediaModel->proevidence->evidencecontent as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->proevidence->id){continue;} if(!in_array($val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id,$attach_media_content)){ continue;}?>
			<tr class="child_conts_<?= $val->id ?>_<?php echo $mediaModel->evid_id;?> " id="selected_production_media_content_<?echo $val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id ?>">				<td align="left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
				<td>&nbsp;</td>
				<td><?=$contentMediaModel->datatype->data_type; ?></td>
				<td align="left"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
				<td>&nbsp;</td>
				<td><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
				<td class="pull-right last-td">
						<span class="hide">
						<input data-prod_id="<?= $val->id ?>" data-media_id="<?= $mediaModel->evid_id ?>" class="child_cont_<?= $val->id ?>_<?php echo $mediaModel->evid_id;?> prod_<?=$val->id?>_<?= $mediaModel->evid_id ?>_<?=$contentMediaModel->id?> Evidence_contents " style="left: 7px;" type="checkbox" name="Production[<?=$val->id?>][<?=$mediaModel->evid_id?>][<?=$contentMediaModel->id?>]" id="prod_evidence_content123_<?=$val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id;?>" value="<?=$contentMediaModel->id;?>"  checked="checked" onclick="selectedParentForProd(<?php echo $val->id; ?>,<?php echo $mediaModel->evid_id;?>);">
						<label for="prod_evidence_content123_<?=$val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id;?>">&nbsp;&nbsp;</label>
						</span>
						<a title="Delete Media Content" aria-label="Delete Media Content" onclick="remove_ProdMediaContent(<?= $val->id?>,<?=$mediaModel->evid_id?>,<?=$contentMediaModel->id?>);" href="javascript:void(0);"><em title="Delete Media Content" class="fa fa-close text-primary"></em></a>
				</td>
           </tr>
		   <?php } ?>
		<?php }
		}	
		else
		{
		?>
		<tr>
					<td colspan="7" class="text-left">No records found.</td>
				</tr>
		<?php
	}?>
       </tbody>
    </table>
</div>
<?php  } } } } if(isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==1) { /*M*/
		if(!empty($case_media['media'])){?>
		<?php foreach ($case_media['media'] as $media_model){?>
			<div class="myheader" id="selected_media_<?= $media_model->id;?>">
			<a href="javascript:void(0);">Media #<?= $media_model->id;?>  - <?=$media_model->evidencetype->evidence_name; ?>
			<div class="my-head-th-title">Est Size: <?php if (isset($media_model->contents_total_size) && ($media_model->contents_total_size != 0 || $media_model->contents_total_size != "")) {	echo $media_model->contents_total_size . ' ' . $media_model->evidenceunit->unit_name;} else {  	echo $media_model->contents_total_size_comp . ' ' . $media_model->evidencecompunit->unit_name;}?></div></a>
			<div class="pull-right header-checkbox">
			
				<span class="hide">
				<input type="checkbox" id="chk123_<?= '_media_'.$media_model->id;?>" value="<?= $media_model->id ?>" checked="checked" onclick="toggleCheckboxes('<?= '_media_'.$media_model->id ?>', this);" class="parent_<?=$media_model->id;?> media" name="Evidence[]" /><label for="chk123_<?= '_media_'.$media_model->id ?>">&nbsp;</label>
				</span>
				<a title="Delete Media" onclick="remove_Media(<?=$media_model->id?>);" href="javascript:void(0);"><em title="Delete Media" class="fa fa-close"></em></a>
			</div>
			</div>
			<?php if(!empty($case_media['media_content'])){?>
					<div class="content" style="padding:0px;" id="selected_media_contents_<?= $media_model->id;?>">
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
							if(!in_array($media_content->id,$evid_content_ids)){ continue; }
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
							         	<input class="child_media_<?=$media_model->id;?> Evidence_contents " style="left: 7px;" type="checkbox" name="Evidence_contents[]" id="Evidence_content123_<?=$media_content->id;?>" value="<?=$media_model->id.'_'.$media_content->id; ?>" data-value="<?=$media_content->id; ?>"  checked="checked" onclick="selectedParentForMedia(<?=$media_model->id;?>);" >
							       		<label for="Evidence_content123_<?=$media_content->id;?>">&nbsp;&nbsp;</label>
							       		</span>
							       		<a title="Delete Media Content" aria-label="Delete Media Content" onclick="remove_MediaContent(<?=$media_content->id?>);" href="javascript:void(0);"><em title="Delete Media Content" class="fa fa-close text-primary"></em></a>
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
	}
	} }?>
    		</div>	
    				
		</div>
    </div>
</fieldset>
<div class=" button-set text-right">
 
 <?php if(isset($flag) && $flag=='Saved') {?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"saved","");']) ?>
	<?php }else if(isset($flag) && $flag=='Edit'){?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"change",'.$task_id.');']) ?>
	<?php }else{?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"","");']) ?>
	<?php }?>
 
 <?= Html::button('Previous', ['title'=>'Previous','class' => 'btn btn-primary','onclick'=>'gotostep(0);']) ?>
 <?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep2','onclick'=>'gotostep(2);']) ?>
 <?php if(isset($flag) && ($flag=='Saved' || $flag=='Edit')) {
 	if((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==1)){?>
 	<input type="hidden" id="attach_media" value="<?=implode(",",$evid_ids);?>">
 	<input type="hidden" id="attach_media_content" value="<?=implode(",",$evid_content_ids);?>">
 	<input type="hidden" id="attach_production" value="">
 	<input type="hidden" id="attach_production_media" value="">
 	<input type="hidden" id="attach_production_media_content" value="">
 	<?php }if((isset($modelInstruct->mediadisplay_by) && $modelInstruct->mediadisplay_by==2)){?>
 	<input type="hidden" id="attach_media" value="">
 	<input type="hidden" id="attach_media_content" value="">
 	<input type="hidden" id="attach_production" value="<?=implode(",",$prod_ids);?>">
 	<input type="hidden" id="attach_production_media" value="<?=implode(",",$attach_media);?>">
 	<input type="hidden" id="attach_production_media_content" value="<?=implode(",",$attach_media_content);?>">
 <?php }
 }else{?>
 	<input type="hidden" id="attach_media" value="">
 	<input type="hidden" id="attach_media_content" value="">
 	<input type="hidden" id="attach_production" value="">
 	<input type="hidden" id="attach_production_media" value="">
 	<input type="hidden" id="attach_production_media_content" value="">
 <?php }?>
</div>
<script>
$(".list_media_production").on('click','.myheader a',function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
	$header.text(function () {
	  //  change text based on condition
	  //return $content.is(":visible") ? "Collapse" : "Expand";
	});
    });	
});

/**
 * Header span
 */
$(".list_media_production").on('click','.myheader',function () {
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}	
});
/*remove media content*/
function remove_MediaContent(mediaContent_id){
	$("#selected_media_content_"+mediaContent_id).remove();
	var new_val=removeValue($('#attach_media_content').val(),mediaContent_id,',');
	$('#attach_media_content').val(new_val);
}
/*remove media and its contents*/
function remove_Media(media_id){
	$("#selected_media_"+media_id).remove();
	var new_val=removeValue($('#attach_media').val(),media_id,',');
	$('#attach_media').val(new_val);

	$(".list_media_production .child_media_"+media_id).each(function(){
		remove_MediaContent($(this).data('value'));
	});
	$('#is_change_form_main').val('1'); // change flag
	$("#selected_media_contents_"+media_id).remove();

	checktotalhours();
	if($('.media').length == 0){
		<?php  if(isset($flag) && $flag=='Edit'){?>
		$('#att_prod').show();
		<?php }?>
	}
}

function remove_Prod(prod_id){
	var new_val=removeValue($('#attach_production').val(),prod_id,',');
	$('#attach_production').val(new_val);
	$(".list_media_production .child_"+prod_id).each(function(){
		remove_ProdMedia($(this).val(),prod_id);
	});
	$('#is_change_form_main').val('1'); // change flag
	$("#selected_production_"+prod_id).remove();
	$(".selected_production_"+prod_id).remove();

	checktotalhours();
	if($('.media').length == 0){
		<?php  if(isset($flag) && $flag=='Edit'){?>
		$('#att_media').show();
		<?php }?>
	}
}
function remove_ProdMedia(media_id,prod_id){
	media_val = prod_id+'_'+media_id
	var new_val=removeValue($('#attach_production_media').val(),media_val,',');
	$('#attach_production_media').val(new_val);
	$(".list_media_production .child_cont_"+prod_id+"_"+media_id+"").each(function(){
		remove_ProdMediaContent($(this).data('prod_id'),$(this).data('media_id'),$(this).val());
	});
	$(".child_conts_"+prod_id+"_"+media_id+"").remove();
	$("#selected_media_"+media_val).remove();
	if($('#attach_production_media').val()==""){
		remove_Prod(prod_id);
	}
}
/*working*/
function remove_ProdMediaContent(prod_id,media_id,mediaContent_id){
	var content_val = prod_id+'_'+media_id+'_'+mediaContent_id;
	$("#selected_production_media_content_"+content_val).remove();
	var new_val=removeValue($('#attach_production_media_content').val(),content_val,',');
	$('#attach_production_media_content').val(new_val);	
}
function removeValue(list, value, separator) {
	  separator = separator || ",";
	  var values = list.split(separator);
	  for(var i = 0 ; i < values.length ; i++) {
	    if(values[i] == value) {
	      values.splice(i, 1);
	      return values.join(separator);
	    }
	  }
	  return list;
	}
function AttachProduction(case_id){
	if($("#attach_media").val()==""){
		if(!$( "#attach-production-popup" ).length){
			$('body').append("<div id='attach-production-popup'></div>");
		}
		$.ajax({
	        url: httpPath + "project/attach-production&case_id="+case_id,
	        data:{'instruct_id':'<?=$instruct_id?>','attach_production':$('#attach_production').val(),'attach_production_media':$('#attach_production_media').val(),'attach_production_media_content':$('#attach_production_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	hideLoader();
	            if (data != "") {
	                $('#attach-production-popup').html(data);
	                if ($.ui && $.ui.dialog && $.ui.dialog.prototype._allowInteraction) {
	                    var ui_dialog_interaction = $.ui.dialog.prototype._allowInteraction;
	                    $.ui.dialog.prototype._allowInteraction = function(e) {
	                        if ($(e.target).closest('.select2-dropdown').length) return true;
	                        return ui_dialog_interaction.apply(this, arguments);
	                    };
	                }
			$( "#attach-production-popup" ).dialog({
				  title:'Add Production(s) to the Project', 
			      autoOpen: false,
			      resizable: false,
			      width: "80em",
			      height: 692,
				  modal: true,
				  show: {
				effect: "fade",
				duration: 500
			      },
			      hide: {
				effect: "fade",
				duration: 500
			      },
				create: function(event, ui) { 
				     $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
				     $('.ui-dialog-titlebar-close').attr("title", "Close");
				     $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				  buttons: [
				{
				    text: "Cancel",
				    "title":"Cancel",
				    "class": 'btn btn-primary',
				    click: function() {
						$( this ).dialog( "close" );
				    }
				},
				{
				    text: "Attach",
				    "title":"Attach",
				    "class": 'btn btn-primary',
				    click: function() {
						
						attachProductionToProject(case_id,'<?php if($modelInstruct->isNewRecord){ echo 0;}else{$modelInstruct->id;}?>}');
				    }
				}
			    ],
			    close: function() {
				$(this).dialog('destroy').remove();
				// Close code here (incidentally, same as Cancel code)
			    }
			});
				$( "#attach-production-popup" ).dialog( "open" );
				$( "#attach-production-popup input" ).customInput();
			  }
	        }
	    });
	}else{
		alert('Please remove associated Media(s) to add a Production to the Project.');
	}
}

function AttachMedia(case_id){
	if($("#attach_production").val()==""){
		if(!$( "#attach-media-popup" ).length){
			$('body').append("<div id='attach-media-popup'></div>");
		}
		
		$.ajax({
			url: httpPath + "project/attach-media&case_id="+case_id,
			data:{'instruct_id':'<?=$instruct_id?>','attach_media':$('#attach_media').val(),'attach_media_content':$('#attach_media_content').val()},
			cache: false,
			dataType: 'html',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				if (data != "") {
					$('#attach-media-popup').html(data);
					if ($.ui && $.ui.dialog && $.ui.dialog.prototype._allowInteraction) {
						var ui_dialog_interaction = $.ui.dialog.prototype._allowInteraction;
						$.ui.dialog.prototype._allowInteraction = function(e) {
							if ($(e.target).closest('.select2-dropdown').length) return true;
							return ui_dialog_interaction.apply(this, arguments);
						};
					}
					$( "#attach-media-popup" ).dialog({
						title:'Add Media to the Project', 
						autoOpen: false,
						resizable: false,
						width: "80em",
						height: 692,
						modal: true,
						show: {
							effect: "fade",
							duration: 500
						},
						hide: {
							effect: "fade",
							duration: 500
						},
						create: function(event, ui) { 
							$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
							$('.ui-dialog-titlebar-close').attr("title", "Close");
							$('.ui-dialog-titlebar-close').attr("aria-label", "Close");
						},
						buttons: [
						{
							text: "Cancel",
							"title":"Cancel",
							"class": 'btn btn-primary',
							click: function() {
								$( this ).dialog( "close" );
							}
						},
						{
							text: "Attach",
							"title":"Attach",
							"class": 'btn btn-primary',
							click: function() {
								attachMedia(case_id,'<?php if($modelInstruct->isNewRecord){ echo 0;}else{$modelInstruct->id;}?>}');
							}
						}
						],
						close: function() {
							$(this).dialog('destroy').remove();
							// Close code here (incidentally, same as Cancel code)
						}
					});
					$( "#attach-media-popup" ).dialog( "open" );
					$( "#attach-media-popup input" ).customInput();
				}
			}
		});
	}else{
		alert('Please remove associated Production(s) to add Media to the Project.');
	}
}
function attachProductionToProject(case_id,instruct_id){
	var prod_id="";
	var media_id="";
	var contents_id="";
	if($('#attach-production-popup  .Production:checked').length){ 
		$('#attach-production-popup .Production:checked').each(function(){
			if(prod_id=="")
				prod_id=$(this).val();
			else
				prod_id=prod_id+","+$(this).val();
		});
		$('#attach-production-popup .media:checked').each(function(){
			var media_val=$(this).attr('rel')+'_'+$(this).val();
			if(media_id=="")
				media_id=media_val;
			else
				media_id=media_id+","+media_val;
		});
		$('#attach-production-popup  .Evidence_contents:checked').each(function(){
			var contents_val=$(this).attr('rel')+'_'+$(this).val();
			if(contents_id=="")
				contents_id=contents_val;
			else
				contents_id=contents_id+","+contents_val;
		});
		attach_production=$("#attach_production").val();
		if(attach_production!=""){
			$("#attach_production").val(attach_production+','+prod_id);
		}else{
			$("#attach_production").val(prod_id);
		}
		attach_media=$("#attach_production_media").val();
		if(attach_media!=""){
			$("#attach_production_media").val(attach_media+','+media_id);
		}else{
			$("#attach_production_media").val(media_id);
		}
		attach_media_content=$("#attach_production_media_content").val();
		if(attach_media_content!=""){
			$("#attach_production_media_content").val(attach_media_content+','+contents_id);
		}else{
			$("#attach_production_media_content").val(contents_id);
		}
		$.ajax({
	        url: httpPath + "project/showattachprod&case_id="+case_id,
	        type:'post',
	        data:{'instruct_id':'<?=$instruct_id?>','attach_production':$('#attach_production').val(),'attach_media':$('#attach_production_media').val(),'attach_media_content':$('#attach_production_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	$('#mediadisplay_by').val('PM');
				$("#attach-production-popup" ).dialog('destroy').remove();
	        	$('#is_change_form_main').val('1');
	        	$(".list_media_production" ).html(data);
				checktotalhours();
				hideLoader();
		    },
		});
	}else{
		alert('Please select a record to perform this action.');
		return false;
	}
}
function attachMedia(case_id,instruct_id){
	var media_id="";
	var contents_id="";
	if($('#attach-media-popup  .media:checked').length){ 
		$('#attach-media-popup .media:checked').each(function(){
			if(media_id=="")
				media_id=$(this).val();
			else
				media_id=media_id+","+$(this).val();
		});
		$('#attach-media-popup  .Evidence_contents:checked').each(function(){
			if(contents_id=="")
				contents_id=$(this).data('value');
			else
				contents_id=contents_id+","+$(this).data('value');
		});
		attach_media=$("#attach_media").val();
		if(attach_media!=""){
			$("#attach_media").val(attach_media+','+media_id);
		}else{
			$("#attach_media").val(media_id);
		}
		attach_media_content=$("#attach_media_content").val();
		if(contents_id!=''){
			
			if(attach_media_content!=""){
				$("#attach_media_content").val(attach_media_content+','+contents_id);
			}else{
				$("#attach_media_content").val(contents_id);
			}
		}
		$.ajax({
	        url: httpPath + "project/showattachmedia&case_id="+case_id,
	        type:'post',
	        data:{'instruct_id':'<?=$instruct_id?>','attach_media':$('#attach_media').val(),'attach_media_content':$('#attach_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	$('#mediadisplay_by').val('M');
	        	$( "#attach-media-popup" ).dialog('destroy').remove();
	        	$('#is_change_form_main').val('1');
	        	$( ".list_media_production" ).html(data);
				checktotalhours();
				hideLoader();
		    },
		});
	}else{
		alert('Please select a record to perform this action.');
		return false;
	}
}

</script>
<noscript></noscript>
