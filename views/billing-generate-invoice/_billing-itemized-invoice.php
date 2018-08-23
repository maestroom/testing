<?php
use yii\helpers\Html;
use app\models\options;
// biling-itemized-invoice

?>
<table border="0" cellspacing="0" cellpadding="0" class="table table-striped header-generated-billing-table">
	<tr>
		<th class="first-th word-break">&nbsp;</th>
		<th class="first-th word-break">
		<input id="itemizedall_<?=$client_case_id?>" class="form-control" name="itemizedall_<?=$client_case_id?>" type="checkbox"  class="itemizedall" data-client_case_id=<?=$client_case_id?> aria-label="Select All/None">
        <label class="form_label" for="itemizedall_<?=$client_case_id?>">&nbsp;</label>
		</th>
		<th width="7%" class="text-left word-break"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
		<th class="word-break" width="20%"><a href="javascript:void(0);" title="Price Point" class="tag-header-black">Price Point</a></th>
		<th width="10%" class="word-break"><a href="javascript:void(0);" title="Date" class="tag-header-black">Date</a></th>
		<th width="10%" class="word-break"><a href="javascript:void(0);" title="Project # / Internal Reference #" class="tag-header-black">Project / IR #</a></th>
		<th width="20%" class="word-break"><a href="javascript:void(0);" title="Custom Description" class="tag-header-black">Custom Description</a></th>
		<th width="10%" class="word-break"><a href="javascript:void(0);" title="# Units" class="tag-header-black"># Units</a></th>
		<th width="8%" class="word-break"><a href="javascript:void(0);" title="Rate" class="tag-header-black">Rate</a></th>
		<th width="10%" class="word-break"><a href="javascript:void(0);" title="SubTotal" class="tag-header-black">SubTotal</a></th>
	</tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" class="table table-striped header-generated-billing-table">

	<?php $i=1; foreach($data['data'] as $k => $val){
		$val['quantity']=round($val['quantity'],2);
		//$val['description']=Html::encode($val['description']);
		$description=htmlentities(htmlspecialchars(addslashes($val['description']), ENT_QUOTES, 'UTF-8', true));
		$description=Html::encode($description);
		$temp_discount_reason=htmlentities(htmlspecialchars(addslashes($val['temp_discount_reason']), ENT_QUOTES, 'UTF-8', true));
		$temp_discount_reason=Html::encode($temp_discount_reason);
		//$val['temp_discount_reason']=Html::encode($val['temp_discount_reason']);
		$val['unit_name']=Html::encode($val['unit_name']);
	?>

		<!-- Hidden values -->
			<input type="hidden" class="inner_invoice2" name="final_units_data[]" id="final_units_data_<?php echo $val['client_case_id']; ?>_<?php echo $k; ?>" value='<?php echo json_encode(['client_id'=>$val['client_id'],'client_case_id'=>$val['client_case_id'],'display_by'=>$view=='Itemized'?1:2,'has_accum_cost'=>$val['created']=='Accumulated'?1:0,'billing_unit_id'=>$val['unitbilling_id'],'invoice_id'=>$val['invoicefinal_id'],'team_loc'=>$val['team_loc'],'final_rate'=>$val['rate'],'discount'=>$val['temp_discount'],'discount_reason'=>$temp_discount_reason,'internal_ref_no_id'=>$val['internal_ref_no_id'],'isnonbillable'=>$val['invoiced']==2?1:0,'subtotal'=>$val['subtotal'],'quantity'=>$val['quantity'],'unit_price_id'=>$val['unit_price_id'],'unit_name'=>$val['unit_name'],'description'=>$description,'istieredrate'=>$val['istieredrate'],'pricing_id'=>$val['pricing_id']]); ?>' />
			<input type="hidden" name="accumulated" id="accumulated_<?php echo $val['client_case_id']; ?>_<?php echo $k; ?>" value="<?= $val['created'] != 'Accumulated'?'servicetask_items':'fail'; ?>" />
			<input type="hidden" name="invoiced" id="invoiced_<?php echo $val['client_case_id']; ?>_<?php echo $k; ?>" value="<?= $val['invoiced'] == 2?'Nonbillable':'fail'; ?>" />
			<input type="hidden" name="istieredrate" id="istieredrate_<?php echo $val['client_case_id']; ?>_<?php echo $k; ?>" value="<?= $val['istieredrate'] == 1?'tieredrate':'fail'; ?>" />
		<!-- End -->

		<?php $style=""; if($val['invoiced']==2){$style='style="color:#c52d2e;"';$class='class="Nonbillable word-break"';}else{ $class='class="word-break"'; } ?>
		<tr>
			<td class="first-td word-break" headers="invoice_itemized_expand">&nbsp;</td>
			<td class="first-td word-break" headers="invoice_itemized_checkbox">
				<div class="pull-right">
					<div class="custom-checkbox">
						<input type="checkbox" name="final_units[]" client-id="<?= $val['client_id'] ?>" case-id="<?= $val['client_case_id'] ?>" team-loc="<?= $val['team_loc'] ?>" pricing-id="<?= $val['pricing_id'] ?>" id="final_units_<?php echo $val['client_id']; ?>_<?php echo $val['client_case_id']; ?>_<?php echo $i; ?>" class="innercheckbox_<?php echo $val['client_case_id']; ?> inner_invoice2 <?= $val['created']=='Accumulated'?"accu_{$val['client_id']}_{$val['pricing_id']}":"";?> <?= $val['created']!='Accumulated'?'servicetask_items':'';?> <?= $val['invoiced'] == 2?'Nonbillable':''; ?> <?= $val['istieredrate']==1?'tieredrate':''; ?>" value='<?php echo json_encode(['client_id'=>$val['client_id'],'client_case_id'=>$val['client_case_id'],'display_by'=>$view=='Itemized'?1:2,'has_accum_cost'=>$val['created']=='Accumulated'?1:0,'billing_unit_id'=>$val['unitbilling_id'],'invoice_id'=>$val['invoicefinal_id'],'team_loc'=>$val['team_loc'],'final_rate'=>$val['rate'],'temp_rate'=>$val['temp_rate'],'discount'=>$val['temp_discount'],'discount_reason'=>$temp_discount_reason,'internal_ref_no_id'=>$val['internal_ref_no_id'],'isnonbillable'=>$val['invoiced']==2?1:0,'subtotal'=>$val['subtotal'],'quantity'=>$val['quantity'],'unit_price_id'=>$val['unit_price_id'],'unit_name'=>$val['unit_name'],'description'=>$description,'istieredrate'=>$val['istieredrate'],'pricing_id'=>$val['pricing_id']]); ?>' aria-label="Final Units"/>
                                                <label for="final_units_<?php echo $val['client_id']; ?>_<?php echo $val['client_case_id']; ?>_<?php echo $i; ?>" class="innercheckbox_<?php echo $val['client_case_id']; ?> final_units_<?php echo $i; ?>"><span class="sr-only">Final Units</span></label>
					</div>
				</div>
			</td>
			<td headers="invoice_itemized_actions" width="7%" <?= $style ?> <?= $class ?>>
				<div class="pull-left">
					<?php if($val['created']!='Accumulated'){ ?>
						<?php
							// Assing variables
							$mytitle='Edit'; $rat_class='text-primary'; if($val['temp_rate'] != '' && $val['temp_rate']!=0){
								$rat_class='text-danger';
								$mytitle="Edited: Rate";
							}
						?>
						<div class="icon-set">
							<a title="<?= $mytitle; ?>" href="javascript:void(0);" onclick="generateInvoiceeditNew(<?php echo $k; ?>,<?php echo $val['client_case_id']; ?>,'I',<?php echo $client_case_id?>);">
								<em class="fa fa-pencil <?= $rat_class ?>" title="<?= $mytitle; ?>"></em>
							</a>
						</div>
					<?php } ?>
					<?php if($val['istieredrate']!=1 && $val['created']!='Accumulated'){ ?>
						<?php
							// Assign variables
							$mytitle='Apply Discount'; $per_class='text-primary';
							if($val['temp_discount']!= '' && $val['temp_discount']!=0){
								$per_class='text-danger'; $mytitle="Discounted: ".$val['temp_discount']."%";
							}
							if($val['temp_discount_reason']!=''){
								$per_class='text-danger'; $mytitle.=" , Reason: ".$temp_discount_reason;
							}
						?>
						<div class="icon-set"><a title="<?php echo $mytitle; ?>" href="javascript:void(0);" onclick="generateInvoicepercentNew(<?php echo $k; ?>,<?php echo $val['client_case_id']; ?>,'I',<?php echo $client_case_id?>);"><em class="fa fa-percentage <?php echo $per_class; ?>" style="font-size: 12px;" title="<?php echo $mytitle; ?>">%</em></a></div>
					<?php } ?>
				</div>
			</td>

			<td headers="invoice_itemized_price_point" width="20%" <?= $style ?> <?= $class ?>>
				<?php echo $val['price_point'].' - '.$val['team_location_name']; ?>
			</td>
			<td headers="invoice_itemized_date" width="10%" <?= $style ?> <?= $class ?>><?php echo ($val['created'] != 'Accumulated')?(new Options)->ConvertOneTzToAnotherTz($val['created'], 'UTC', $_SESSION['usrTZ'],'MDY'):$val['created']; ?></td>
			<td headers="invoice_itemized_project" width="10%" <?= $style ?> <?= $class ?>>
				<?php
					if ($val['created'] != 'Accumulated') {
						echo '<a href="?r=track/index&amp;taskid='.$val['task_id'].'&amp;case_id='.$val['client_case_id'].'&amp;option=All" '.$style.' title="Project #'.$val['task_id'].'">'.$val['task_id'].'</a>';
							if($val['internal_ref_no_id']!=''){echo " / ".$val['internal_ref_no_id'];}
					}
				?>
			</td>
			<td headers="invoice_itemized_description" width="20%" <?= $style ?> <?= $class ?>><?php echo $val['description']; ?></td>
			<td headers="invoice_itemized_units" width="10%" <?= $style ?> <?= $class ?>><?php echo number_format($val['quantity'],2).' '.$val['unit_name']; ?></td>
			<td headers="invoice_itemized_rate" width="8%" <?= $style ?> <?= $class ?>>$<?php echo (isset($val['rate']) && $val['rate']!='')?number_format($val['rate'],2):'0.00'; ?></td>
			<td headers="invoice_itemized_subtotal" width="10%" <?= $style ?> <?= $class ?>>$<?php echo number_format($val['subtotal'],2); ?></td>
		</tr>
	<?php $i++; }
	if(isset($data['total'])){
	?>
	<tr>
			<td class="first-td word-break" headers="invoice_itemized_expand">&nbsp;</td>
			<td class="first-td word-break" headers="invoice_itemized_checkbox">&nbsp;</td>
			<td headers="invoice_itemized_actions" width="7%">&nbsp;</td>
			<td headers="invoice_itemized_price_point" width="20%">&nbsp;</td>
			<td headers="invoice_itemized_date" width="10%">&nbsp;</td>
			<td headers="invoice_itemized_project" width="10%" >&nbsp;</td>
			<td headers="invoice_itemized_description" width="20%">&nbsp;</td>
			<td headers="invoice_itemized_units" width="10%">&nbsp;</td>
			<td headers="invoice_itemized_rate" width="8%"><b>Total</b></td>
			<td headers="invoice_itemized_subtotal" width="10%"><b>$<?php echo number_format($data['total'],2); ?></b></td>
	</tr>
	<?php }?>
</table>
<script>
	$('input').customInput(); // custom input
	$('#itemizedall_<?=$client_case_id?>').on('click',function(){
		if($(this).is(":checked")){
			$(".innercheckbox_<?=$client_case_id?>").prop('checked',true);
			$(".innercheckbox_<?=$client_case_id?>").next('label').addClass('checked');
		}else{
			$(".innercheckbox_<?=$client_case_id?>").prop('checked',false);
			$(".innercheckbox_<?=$client_case_id?>").next('label').removeClass('checked');
		}
	});
</script>
<noscript></noscript>
