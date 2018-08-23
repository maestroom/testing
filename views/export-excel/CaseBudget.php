<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\grid\GridView;
?>
<div style="float:left; width:100%;">
			 <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;">Case Budget</div>
			 <div style="background:#e9e7e8; color:#333; font-size:11px; margin:0px; padding:7px 10px; position:relative;">Budget Summary</div>
			 <div style="float:left; width:100%;">
			 	<div style="float:left; width:50%; padding:5px 0px;">
					<table><tr><th colspan="2">Budget Summary</th></tr><td colspan="2">&nbsp;</td></tr></table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><strong>Client Name</strong></td>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><?=$case_info->client->client_name?></td>
					  </tr>
					  <tr>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><strong>Case Name</strong></td>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><?=$case_info->case_name;?></td>
					  </tr>
					  <tr>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><strong>Budget Value</strong></td>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><?="$ ".number_format($case_info->budget_value, 2, '.', ',');?></td>
					  </tr>
					  <tr>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><strong>Case Spend Alert Value</strong></td>
						<td style="font-family:Arial; font-size:10px; padding:3px 10px;"><?="$ ".number_format($case_info->budget_alert, 2, '.', ',');?></td>
					  </tr>
					</table>
				</div>				
				<div style="float:right; width:40%; padding:10px; text-align:right; font-size:11px;">
					<img alt="" src="data:image/svg+xml;base64,<?php echo $pdfimage?>" style="width:150px;">
				</div>
				</div>
				
				 <div style="float:left; width:100%;">
				   <div style="background:#e9e7e8; color:#333; font-size:11px; margin:0px 0px 5px; padding:7px 10px; position:relative;">Billable Items</div>
				   <table><tr><th colspan="5">Billable Items</th></tr><tr><td colspan="5">&nbsp;</td></tr></table>
				   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="casespend_table">
				   	<thead>
				   		<tr>
				   			<th align="left" style="font-size:10px; font-family:Arial; padding:8px 10px; border:none 0px; background:#e9e7e8;" title="Project #"><strong>Project #</strong></th>
				   			<th align="left" style="font-size:10px; font-family:Arial; padding:8px 10px; border:none 0px; background:#e9e7e8;" title="Project Name"><strong>Project Name</strong></th>
				   			<th align="left" style="font-size:10px; font-family:Arial; padding:8px 10px; border:none 0px; background:#e9e7e8;" title="Invoiced"><strong>Invoiced</strong></th>
				   			<th align="left" style="font-size:10px; font-family:Arial; padding:8px 10px; border:none 0px; background:#e9e7e8;" title="Pending"><strong>Pending</strong></th>
				   			<th align="left" style="font-size:10px; font-family:Arial; padding:8px 10px; border:none 0px; background:#e9e7e8;" title="Total Spend"><strong>Total Spend</strong></th>
				   		</tr>
					</thead>
				   	<tbody>
				   	<?php if(!empty($table_data)){
						$last_key="";
				   		foreach ($table_data as $key=>$model){ if($model['Project #']==='Spend Totals:'){$last_key=$key; continue; }?>
				   			<tr>
				   				<td class="text-center" style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?= $model['Project #']; ?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?=$model['Project Name'] ?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?=$model['Invoiced']; ?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?=$model['Pending'];?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?=$model['Total Spend'];?></td>
				   			</tr>
				   		<?php }?>
				   			<tr id="total_tbl">
				   				<td align="right" colspan="2" title="Spend Totals" style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong>Spend Totals</strong></td>
								<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong><?=$table_data[$last_key]['Invoiced']; ?></strong></td>
   							   	<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong><?=$table_data[$last_key]['Pending'];?></strong></td>
   						   		<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong><?=$table_data[$last_key]['Total Spend'];?></strong></td>
				   			</tr>
				   		<?php }?>
				   	</tbody>
				   </table>
				 </div>
</div>