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
?>
<table class="table table-striped table-hover">
		<thead>
             <tr class="<?php if($cnt_instruction_evidence > 0){echo "bg-warning";} ?>">
               <th id="media_id" class="text-center" scope="col"><a href="#" title="Media #">Media #</a></th>
               <th id="media_type" class="text-left" scope="col"><a href="#" title="Media Type">Media Type</a></th>
               <th id="media_desc" class="text-left" scope="col"><a href="#" title="Media Desc/Data Type">Media Desc/Data Type</a></th>
               <th id="media_quantity" class="text-center" scope="col"><a href="#" title="Quantity">Quantity</a></th>
               <th id="media_size" class="text-left" scope="col"><a href="#" title="Est Size">Est Size</a></th>
               <th colspan="5" id="media_data_copied" class="text-left" scope="col"><a href="#" title="Data Copied To">Data Copied To</a></th>
             </tr>
       </thead>
       <tbody>
       	<?php foreach ($processTrackData['media'] as $mediaModel){?>	
           <tr class="<?php if($cnt_instruction_evidence > 0){echo "bg-warning";} ?>">
				<td align="center" headers="media_id">
				<?php if ((new User)->checkAccess(3)) { /* 39 */ ?>
					<a href="javascript:go_toMedia('<?= $mediaModel->id;?>')" style="color:#167fac;" title="Media #<?php echo $mediaModel->id; ?>"><?= $mediaModel->id;?></a>
				<?php } else { echo  $mediaModel->id; } ?>
				</td>
                <td align="left" headers="media_type"><?=$mediaModel->evidencetype->evidence_name; ?></td>
				<td align="left" headers="media_desc word-break"><?=$mediaModel->evid_desc?></td>
				<td align="center" headers="media_quantity"><?=$mediaModel->quantity?></td>
				<td align="left" headers="media_size">
				<?php 
					 if (isset($mediaModel->contents_total_size) && ($mediaModel->contents_total_size != 0 || $mediaModel->contents_total_size != "")) {
						echo $mediaModel->contents_total_size . ' ' . $mediaModel->evidenceunit->unit_name;
					 } else {
						echo $mediaModel->contents_total_size_comp . ' ' . $mediaModel->evidencecompunit->unit_name;
					 } 
                ?>
				</td>
				<td colspan="5" headers="media_data_copied"><?=$mediaModel->contents_copied_to?></td>
			</tr>
			<?php foreach ($processTrackData['media_content'] as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->id){continue;}?>
			<tr>
				<td align="center" headers="media_id"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
				<td align="left" headers="media_type"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
				<td headers="media_desc word-break"><?=$contentMediaModel->datatype->data_type; ?></td>
				<td headers="media_quantity">&nbsp;</td>
				<td headers="media_size"><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
				<td colspan="5" headers="media_data_copied"><?=$contentMediaModel->data_copied_to?></td>
           </tr>
		   <?php }?>
       	<?php }?>
       </tbody>
    </table>
