<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<?php	
if(!empty($case_productions)){?>
<?php foreach($case_productions as $key => $val) 
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
<a title="Delete Production" aria-label="Delete Production #<?= $val->id ?>" onclick="remove_Prod(<?=$val->id?>);" href="javascript:void(0);"><em class="fa fa-close"></em></a>
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
		foreach ($val->productionmedia as $mediaModel){ if(!in_array($val->id.'_'.$mediaModel->evid_id,explode(",",$attach_media))){ continue;}?>	
       		<tr id="selected_media_<?= $val->id ?>_<?php echo $mediaModel->evid_id;?>">
				<td align="left"><?= $mediaModel->evid_id;?></td>
				<td align="left"><?php if($mediaModel->on_hold==1) { ?><em class="fa fa-check text-danger"></em><?php } else{?>&nbsp;<?php }?></td>
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
					<a title="Delete Media" aria-label="Delete Media #<?= $mediaModel->evid_id ?> of Production #<?=$val->id?>" onclick="remove_ProdMedia(<?=$mediaModel->evid_id;?>,<?=$val->id;?>);" href="javascript:void(0);"><em class="fa fa-close"></em></a>
				</td>
			</tr>
			
			<?php foreach ($mediaModel->proevidence->evidencecontent as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->proevidence->id){continue;} if(!in_array($val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id,explode(",",$attach_media_content))){ continue;}?>
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
					<a title="Delete Media Content" aria-label="Delete Media Content #<?=$contentMediaModel->id ?> of Media <?=$mediaModel->evid_id?> for Production #<?=$val->id?>" onclick="remove_ProdMediaContent(<?= $val->id?>,<?=$mediaModel->evid_id?>,<?=$contentMediaModel->id?>);" href="javascript:void(0);"><em class="fa fa-close text-primary"></em></a>
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
<?php  } } }?>