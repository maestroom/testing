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
<div style="padding:0px 10px 10px;">
<table width="100%" style="color:#333;">
	<thead>
		 <tr>
		   <th align="left" style="font-size:10px; font-family:Arial; padding:3px 5px 3px 0px; border:none 0px;"><a style="color:#333;" href="#" title="Media #">Media #</a></th>
		   <th align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><a style="color:#333;" href="#" title="Media Type/Custodians">Media Type/Custodians</a></th>
		   <th align="left" style="font-size:10px; font-family:Arial; padding:3px5px; border:none 0px;"><a style="color:#333;" href="#" title="Media Desc/Data Type">Media Desc/Data Type</a></th>
		   <th align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><a style="color:#333;" href="#" title="Est Data Size">Est Data Size</a></th>
		 </tr>
   </thead>
   <tbody>
	<?php foreach ($processTrackData['media'] as $mediaModel){?>	
		<tr>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px 3px 0px; border:none 0px;"><a style="color:#333;" href="<?php if ((new User)->checkAccess(3)) { /* 39 */ ?>javascript:go_toMedia('<?= $mediaModel->id;?>') <?php } else { ?>javascript:void(0)<?php } ?>" title="See Media"><?= $mediaModel->id;?></a></td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?=$mediaModel->evidencetype->evidence_name; ?></td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?=$mediaModel->evid_desc?></td>
			<?php /*?><td align="center"><?=$mediaModel->quantity?></td><?php */?>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;">
			<?php 
			 if (isset($mediaModel->contents_total_size) && ($mediaModel->contents_total_size != 0 || $mediaModel->contents_total_size != "")) {
				echo $mediaModel->contents_total_size . ' ' . $mediaModel->evidenceunit->unit_name;
			} else {
				echo $mediaModel->contents_total_size_comp . ' ' . $mediaModel->evidencecompunit->unit_name;
			}?>
			</td>
			<?php /*?><td><?=$mediaModel->contents_copied_to?></td><?php */?>
		</tr>
		<?php foreach ($processTrackData['media_content'] as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->id){continue;}?>
		<tr>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px 3px 0px; border:none 0px;"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?=$contentMediaModel->datatype->data_type; ?></td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;">&nbsp;</td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
			<td align="left" style="font-size:10px; font-family:Arial; padding:3px 5px; border:none 0px;"><?=$contentMediaModel->data_copied_to?></td>
	   </tr>
	   <?php }?>
	<?php }?>
   </tbody>
</table>
</div>
