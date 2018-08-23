<?php 
// yii 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\options;

$this->title = 'Itemized Invoices';
?>
<div class="right-main-container" id="media_container">
	<div class="sub-heading"><a href="#" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
		<fieldset class="one-cols-fieldset">
			<?php ActiveForm::begin(['id' => 'add-finalized-invoice','action' => '@web/index.php?r=billing-generate-invoice/finalize-invoice']); ?>
			<input type="hidden" name="display_invoiced" id="display_invoiced" value="<?= $view ?>" />
			<div class="row">
				<div class="col-sm-12 mycontainer">
				<?php $k=1; foreach($billingdata as $key=>$outer){ ?>
						<?php 
							$client_case = explode("||", $key);
							$client = explode("=", $client_case[0]);
							$clientcase = explode("=", $client_case[1]);
						?>
						<!-- start -->
						<div class="myheader">
							<span id="myheader_client_case">
								<?php echo $client[1] .'-' .$clientcase[1]; ?>
								<div style="float:right;">
									<?php $total=0; foreach($outer as $val){ if($val['invoiced'] != 2){$total = $total + $val['subtotal'];} } ?>
									<?php echo "$".number_format($total,2);?>
								</div>
							</span>
							<div class="pull-right"> 
								<input type="checkbox" name="consolidate_invoice" id="consolidate_outer_<?php echo $k;?>" class="consolidate_outer_<?php echo $k;?>" onClick="checkallinvoice(<?php echo $k;?>)"/>
								<label for="consolidate_outer_<?php echo $k;?>" class="consolidate_outer_<?php echo $k;?>"></label>	
							</div>
						</div>
						<div class="content">
							<!-- Table Striped -->
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
								<tr>
									<th scope="col" id="dispaly_generate_invioce_price_point" width="20%"><a href="javascript:void(0);" title="Price Point" class="tag-header-black">Price Point</a></th>
									<th scope="col" id="dispaly_generate_invioce_date" width="10%"><a href="javascript:void(0);" title="Date" class="tag-header-black">Date</a></th>
									<th scope="col" id="dispaly_generate_invioce_project_ir" width="10%"><a href="javascript:void(0);" title="Project #/IR #" class="tag-header-black">Project #/IR #</a></th>
									<th scope="col" id="dispaly_generate_invioce_custom_description" width="25%"><a href="javascript:void(0);" title="Custom Description" class="tag-header-black">Custom Description</a></th>
									<th scope="col" id="dispaly_generate_invioce_units" width="10%"><a href="javascript:void(0);" title="# Units" class="tag-header-black"># Units</a></th>
									<th scope="col" id="dispaly_generate_invioce_rate" width="10%"><a href="javascript:void(0);" title="Rate" class="tag-header-black">Rate</a></th>
									<th scope="col" id="dispaly_generate_invioce_subtotal" width="10%"><a href="javascript:void(0);" title="SubTotal" class="tag-header-black">SubTotal</a></th>
									<th scope="col" id="dispaly_generate_invioce_action" width="10%">
										<a href="javascript:void(0);" class="icon-set" onClick="generateInvoiceedit();" aria-label="Generate Invoice"><em title="Generate Invoice" class="fa fa-pencil text-primary"></em></a> 
										<a href="javascript:void(0);" class="icon-set" onClick="generateInvoicepercent();"><em title="Generate Invoice %" class="fa fa-percentage text-primary">%</em></a>
									</th>
								</tr>
							 </table>
							 <!-- Table for inner value -->
							 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
								<?php $i=1; foreach($outer as $val){ ?>
									<?php $style=""; if($val['invoiced']==2){$style='style="color:red;"';$class='class="Nonbillable"';} ?>
									<tr>
										<td headers="dispaly_generate_invioce_price_point" width="20%" <?= $style ?> <?= $class ?>>
											<?php echo $val['price_point'].' - '.$val['team_location_name'];?>
										</td>
										<td headers="dispaly_generate_invioce_date" width="10%" <?= $style ?> <?= $class ?>><?php echo ($val['created'] != 'Accumulated')?(new Options)->ConvertOneTzToAnotherTz($val['created'], 'UTC', $_SESSION['usrTZ'],'YMD'):$val['created']; ?></td>
										<td headers="dispaly_generate_invioce_project_ir" width="10%" <?= $style ?> <?= $class ?>>
										<?php 
											if ($val['created'] != 'Accumulated') {
												echo '<a href="?r=track/index&amp;taskid='.$val['task_id'].'&amp;case_id='.$clientcase[0].'&amp;option=All" '.$style.'>'.$val['task_id'].'</a>';
												if($val['internal_ref_no_id']!=''){echo " / ".$val['internal_ref_no_id'];} 
											} 
										?>
										</td>
										<td headers="dispaly_generate_invioce_custom_description" width="25%" <?= $style ?> <?= $class ?>><?php echo $val['description']; ?></td>
										<td headers="dispaly_generate_invioce_units" width="10%" <?= $style ?> <?= $class ?>><?php echo number_format($val['quantity'],2).' '.$val['unit_name']; ?></td>
										<td headers="dispaly_generate_invioce_rate" width="10%" <?= $style ?> <?= $class ?>>$ <?php echo number_format($val['rate'],2); ?></td>
										<td headers="dispaly_generate_invioce_subtotal" width="10%" <?= $style ?> <?= $class ?>>$ <?php echo number_format($val['subtotal'],2); ?></td>
										<td headers="dispaly_generate_invioce_action" width="5%" <?= $style ?> <?= $class ?>>
											<div class="pull-right"> 
												<input type="checkbox" name="final_units[]" client-id="<?= $val['client_id'] ?>" case-id="<?= $val['client_case_id'] ?>" team-loc="<?= $val['team_loc'] ?>" pricing-id="<?= $val['pricing_id'] ?>" id="final_units_<?php echo $k; ?>_<?php echo $i; ?>" class="consolidate_inner_invoice2 <?= $val['created']=='Accumulated'?"accu_{$val['client_id']}_{$val['pricing_id']}":"";?> <?= $val['created']!='Accumulated'?'servicetask_items':'';?> <?= $val['invoiced'] == 2?'Nonbillable':''; ?> <?= $val['istieredrate'] == 1?'tieredrate':''; ?>" value='<?php echo json_encode(['client_id'=>$val['client_id'], 'client_case_id'=>$val['client_case_id'], 'display_by' => $view=='Itemized'?1:2, 'has_accum_cost'=> $val['created']=='Accumulated'?1:0 , 'billing_unit_id' => $val['unitbilling_id'],'invoice_id'=>$val['invoicefinal_id'], 'team_loc' => $val['team_loc'], 'final_rate' => $val['rate'], 'discount' => $val['temp_discount'], 'discount_reason' => $val['temp_discount_reason'], 'internal_ref_no_id' => $val['internal_ref_no_id'],'isbillable'=>$val['invoiced']==2?1:0]); ?>'/>
												<label for="final_units_<?php echo $k; ?>_<?php echo $i; ?>" class="final_label_units_<?php echo $k; ?>"></label>
												<?php if($val['temp_discount']!='' && $val['temp_discount']!=0){ ?>
													<a href="javascript:void(0);" class="icon-set"><em title="Temp Discount %" class="fa fa-percentage text-primary"></em> %</a>
												<?php } ?>
											</div>
										</td>
									</tr>
								<?php $i++; } ?>
							</table>
							<!-- End Table -->
						</div>
						<!-- End -->
					<?php $k++; } ?>
				</div>
			</div>
			<?php ActiveForm::end(); ?>
		</fieldset>
        <?php $form = ActiveForm::begin(['id' => 'display-generate-invoice-form','action' => '@web/index.php?r=billing-generate-invoice/billing-invoice-management']); ?>
			<div class="generate-invoice"><input type="hidden" name="filter_data" id="filter_data" value='<?php echo json_encode($data); ?>' /></div>
			<div class="button-set text-right">
				<button onclick="previousinvoicebtn();" title="Previous" class="btn btn-primary" id="previousrequestinvoice" type="button" name="yt1">Previous</button>
				<button onclick="savedinvoicebackbtn();" title="Saved" class="btn btn-primary" id="backrequestinvoiced" type="button" name="yt2">Saved</button>
			</div>
		<?php ActiveForm::end(); ?>
        <div class="administration-rt-cols pull-right" id="admin_right"></div>
</div>
<style>.right-main-container .one-cols-fieldset {overflow: auto;}</style>
<script type="text/javascript">
/**
 * customInput
 */
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');

/**
 * Select All Outer Checkbox
 */
function checkallinvoice(loop){
	if($('.consolidate_outer_'+loop).is(':checked')){
		$('.final_units_'+loop).prop('checked',true);
		$('.final_label_units_'+loop).prop('checked',true);
		$('.final_label_units_'+loop).addClass('checked');
	}
	else {
		$('.final_units_'+loop).prop('checked',false);
		$('.final_label_units_'+loop).prop('checked',false);
		$('.final_label_units_'+loop).removeClass('checked');
	}
}

/**
 * myheader span link
 */
$(".myheader span").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
			//change text based on condition
			//return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });
});

/**
 * Header span
 */
$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}	
});
</script>
<noscript></noscript>
