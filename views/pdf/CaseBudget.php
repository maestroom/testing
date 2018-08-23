<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\grid\GridView;
?>
<div style="float:left; width:100%;">
			<?php $form = ActiveForm::begin(['id' => $case_info->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
			 <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;">Case Budget</div>
			 <div style="background:#e9e7e8; color:#333; font-size:11px; margin:0px; padding:7px 10px; position:relative;">Budget Summary</div>
			 <div style="float:left; width:100%;">
			 	<div style="float:left; width:50%; padding:5px 0px;">
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
				   	<?php if(!empty($caseSpendPerProject)){
				   		foreach ($caseSpendPerProject as $key=>$model){ if($key==='total'){ continue; }?>
				   			<tr>
				   				<td class="text-center" style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?= $model['project_id']; ?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?=$model['project_name'] ?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?="$ ".number_format($model['invoiced'], 2, '.', ','); ?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?="$ ".number_format($model['pending'], 2, '.', ',');?></td>
				   				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?="$ ".number_format($model['total_spent'], 2, '.', ',');?></td>
				   			</tr>
				   		<?php }?>
				   			<tr id="total_tbl">
				   				<td align="right" colspan="2" title="Spend Totals" style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong>Spend Totals</strong></td>
								<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong><?="$ ".number_format($caseSpendPerProject['total']['invoiced'], 2, '.', ','); ?></strong></td>
   							   	<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong><?="$ ".number_format($caseSpendPerProject['total']['pending'], 2, '.', ',');?></strong></td>
   						   		<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><strong><?="$ ".number_format($caseSpendPerProject['total']['total_spent'], 2, '.', ',');?></strong></td>
				   			</tr>
				   		<?php }?>
				   	</tbody>
				   </table>
				 </div>
				
				<input type="hidden" id="pdfimage" name="pdfimage">
			 	
			 	<div style="float:left; width:100%;">
			 		<div id="container-speed" class="chart-container"></div>
			 	</div>
			 <?php ActiveForm::end(); ?>
</div>
<?php /*?>
<div class="right-main-container">
			<fieldset class="one-half-cols-fieldset project-comments case-budgets">
			
			 <div class="sub-heading">Case Budget</div>
			 <div class="comments-area">
			 	<div class="col-sm-4">
			 	Client Name  <?=$case_info->client->client_name?>
			 	<br/>
			 	Case Name <?=$case_info->case_name;?>
			 	<br/>
			 	Enter Budget Value : <?="$ ".number_format($case_info->budget_value, 2, '.', ',');?>
			 	<br/>
			 	Alert Me When Case Spend Reaches Value :<?="$ ".number_format($case_info->budget_alert, 2, '.', ',');?>
				</div>
			 	<div class="col-sm-8">
			 		<img alt="" src="data:image/svg+xml;base64,<?php echo $pdfimage?>">
			 	</div>	
			 </div>
			</fieldset>
			
	<fieldset class="two-half-cols-fieldset project-comments case-budgets">
		<div id="comments" class="comments left case_88">
			  <?= GridView::widget([
                    'id'=>'case-budget-grid',
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                    		['attribute'=>'project_id','hAlign'=>'center','header'=>'Project #','format'=>'raw','value'=>function($model) use ($case_info){ return Html::a($model['project_id'],['track/index&case_id='.$case_info->id.'&task_id='.$model['project_id']],['title'=>'Project #']); }],
							['attribute'=>'project_name','hAlign'=>'left','headerOptions'=>['style'=>'text-align:left;']],
							['attribute'=>'invoiced','hAlign'=>'right','headerOptions'=>['style'=>'text-align:right;'],'value'=>function($model) use ($case_info){ return "$ ".number_format($model['invoiced'], 2, '.', ',');}],
							['attribute'=>'pending','hAlign'=>'right','headerOptions'=>['style'=>'text-align:right;'],'value'=>function($model) use ($case_info){ return "$ ".number_format($model['pending'], 2, '.', ',');}],
							['attribute'=>'total_spent','hAlign'=>'right','headerOptions'=>['style'=>'text-align:right;'],'value'=>function($model) use ($case_info){ return "$ ".number_format($model['total_spent'], 2, '.', ',');}],
					],
                    'export'=>false,
                    'floatHeader'=>true,
                    'pjax'=>true,
                    'responsive'=>true,
                    'floatHeaderOptions' => ['top' => 'auto'],
                    'pjaxSettings'=>[
                    		'options'=>['id'=>'case-budget-pajax','enablePushState' => false],
                    				'neverTimeout'=>true,
                    				'beforeGrid'=>'',
                    				'afterGrid'=>'',
                    		],
                   ]); 
	?>
		</div>
	</fieldset>
</div>
<?php */?>