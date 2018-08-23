<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use app\models\Evidence;
?>
<?php	
if(!empty($case_productions)){
	foreach($case_productions as $key => $val){
		if(!empty($val->productionmedia)){
?>	
<div class="myheader">
	<a href="javascript:void(0);">
		Production #<?= $val->id ?> - Received <?= date("m/d/Y",strtotime($val->prod_rec_date)) ?><div class="my-head-th-title">Production By <?= $val->prod_party ?></div>
	</a>
	<div class="pull-right header-checkbox">
			<input type="checkbox" id="chk_<?= $val->id ?>" value="<?= $val->id ?>" onclick="checkProdChild(<?php echo $val->id;?>);" class="parent_<?php echo $val->id;?> Production prod_<?= $val->id ?>" name="Production[<?= $val->id ?>]" />
			<label for="chk_<?= $val->id ?>">&nbsp;</label>
	</div>
</div>
<div class="content" style="padding:0px;">
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
       <tbody id="child_<?php echo $val->id;?>">
       	<?php 
       	if(!empty($val->productionmedia)) {
       	foreach ($val->productionmedia as $mediaModel){ if(in_array($mediaModel->proevidence->status,array(3,5))) {continue;}?>	
       		<tr>
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
					<input type="checkbox" id="mediachk_<?= $val->id ?>_<?= $mediaModel->evid_id ?>" value="<?= $mediaModel->evid_id ?>" rel="<?= $val->id ?>" class="child_<?php echo $val->id;?> child1_<?php echo $mediaModel->evid_id?> prod_<?=$val->id?>_<?= $mediaModel->evid_id?> media" name="Production[<?=$val->id?>][<?= $mediaModel->evid_id?>]" onclick="checkParent(<?php echo $val->id;?>);checkChildCont(<?php echo $val->id;?>,<?php echo $mediaModel->evid_id;?>);" />
					<label for="mediachk_<?= $val->id ?>_<?= $mediaModel->evid_id ?>">&nbsp;</label>
					
				</td>
			</tr>
			
			<?php foreach ($mediaModel->proevidence->evidencecontent as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->proevidence->id){continue;}?>
			<tr class="child_conts_<?php echo $mediaModel->evid_id;?>" >
				<td align="left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
				<td>&nbsp;</td>
				<td><?=$contentMediaModel->datatype->data_type; ?></td>
				<td align="left"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
				<td>&nbsp;</td>
				<td><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
				<td class="pull-right last-td">
						<input rel="<?= $val->id ?>" class="child_cont_<?php echo $mediaModel->evid_id;?> prod_<?=$val->id?>_<?= $mediaModel->evid_id ?>_<?=$contentMediaModel->id?> Evidence_contents " style="left: 7px;" type="checkbox" name="Production[<?=$val->id?>][<?=$mediaModel->evid_id?>][<?=$contentMediaModel->id?>]" id="prod_evidence_content_<?=$val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id;?>" value="<?=$contentMediaModel->id;?>" onclick="selectedParentForProd(<?php echo $val->id; ?>,<?php echo $mediaModel->evid_id;?>);">
						<label for="prod_evidence_content_<?=$val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id;?>">&nbsp;&nbsp;</label>
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
</div>
<?php  } } }else{?>
<!--<span class="text-left">No records found.</span>-->
<?php }?>
