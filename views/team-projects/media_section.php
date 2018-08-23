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
             <tr>
               <th class="text-left"><a href="#" title="Media #">Media #</a></th>
               <th class="text-left"><a href="#" title="Media Type/Custodians">Media Type/Custodians</a></th>
               <th class="text-left"><a href="#" title="Media Desc/Data Type">Media Desc/Data Type</a></th>
               <th class="text-left"><a href="#" title="Est Data Size">Est Data Size</a></th>
             </tr>
       </thead>
       <tbody>
       	<?php foreach ($processTrackData['media'] as $mediaModel){?>	
       		<tr>
				<td align="left">
				<?php if ((new User)->checkAccess(3)) { /* 39 */ ?>
							<a href="javascript:go_toMedia('<?= $mediaModel->id;?>')" style="color:#167fac;" title="Media #<?php echo $mediaModel->id; ?>"><?= $mediaModel->id;?></a>
				<?php } else { echo  $mediaModel->id; } ?>
				</td>
                <td align="left"><?=$mediaModel->evidencetype->evidence_name; ?></td>
				<td align="left"><?=$mediaModel->evid_desc?></td>
				<td align="left">
				<?php 
				 if (isset($mediaModel->contents_total_size) && ($mediaModel->contents_total_size != 0 || $mediaModel->contents_total_size != "")) {
					echo $mediaModel->contents_total_size . ' ' . $mediaModel->evidenceunit->unit_name;
				} else {
                	echo $mediaModel->contents_total_size_comp . ' ' . $mediaModel->evidencecompunit->unit_name;
                }?>
				</td>
			</tr>
			<?php foreach ($processTrackData['media_content'] as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->id){continue;}?>
			<tr>
				<td align="left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
				<td align="left"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
				<td><?=$contentMediaModel->datatype->data_type; ?></td>
				<td>&nbsp;</td>
				<td><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
				<td><?=$contentMediaModel->data_copied_to?></td>
           </tr>
		   <?php }?>
       	<?php }?>
       </tbody>
    </table>
