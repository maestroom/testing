<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceFinal */
/* @var $form yii\widgets\ActiveForm */

// select invoice criteria
$this->title = 'Invoice Preview';
?>
<style>
#edit_finalized_invoice .form-group{
	overflow-y:scroll;
	overflow-x:hidden;
	bottom: 0;
    left: 0;
    padding: 20px 15px;
    position: absolute;
    right: 0;
    top: 0;
    margin-bottom:0px;
}
</style>
<div class="right-main-container">
	<div class="sub-heading"><?= Html::encode($this->title) ?></div>
	<fieldset class="one-cols-fieldset">
		<form id="edit_finalized_invoice" name="edit_finalized_invoice" method="post" autocomplete="off">
		<div class="form-group">
		
			<div class="rows">
				
				<div class="col-sm-12">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td colspan="5"><strong title="Invoice No">Invoice No </strong></td>
							<td>
								<div class="pull-right">
									<a href="javascript:void(0);" id="invoice_display">Click</a>
								</div>
							</td>
						</tr>
					</table>
				</div>
				
				<!--<div class="col-sm-2">
					<strong>Invoice No </strong>
				</div>
				
				<div class="col-sm-10"><?php // $invoice['id']; ?>
					<div class="pull-right">
						<a href="javascript:void(0);" id="invoice_display">Click</a>
					</div>
					<div id="invoice_date" style="display:none;">
						<hr /><?php // $invoice['created_date']; ?><hr />
					</div> 
				</div>-->
				
			</div>
			
			<div class="rows">
				<div class="col-sm-2">
					<strong title="Biller">Biller </strong>
				</div>
				<div class="col-sm-10"><?= $contactData['fname'].' '.$contactData['lname']; ?>
					<div class="pull-right">
						<a href="javascript:void(0);" id="biller_invoice">Click</a>
					</div>
					<div id="biller_detail" style="display:none;">
						<hr />
							<!-- Display Biller Details -->
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>Street Address</td>
									<td><?= $contactData['add_1']; ?></td>
								</tr>
								<tr>
									<td>Street Address 2</td>
									<td><?= $contactData['add_2']; ?></td>
								</tr>
								<tr>
									<td>City</td>
									<td><?= $contactData['city']; ?></td>
								</tr>
								<tr>
									<td>State, Zip code</td>
									<td>
										<?= $contactData['state'] .','. $contactData['zip']; ?>
									</td>
								</tr>
								<tr>
									<td>Email</td>
									<td><?= $contactData['email']; ?></td>
								</tr>
								<tr>
									<td>Ph</td>
									<td><?= $contactData['phone_o']; ?></td>
								</tr>
								<tr>
									<td>Mob</td>
									<td><?= $contactData['phone_m']; ?></td>
								</tr>
							</table>
						<hr />
					</div> <!-- Biller details -->
				</div>
			</div>	
			
			<div class="rows">
				<div class="col-sm-2">
					<strong title="Client">Client </strong>
				</div>
				<div class="col-sm-10"><?= $clientData['client_name']; ?>
					<div class="pull-right">
						<a href="javascript:void(0);" id="client_details">Click</a>
					</div>
					<div id="client_addetail" style="display:none;">
						<!-- Display Client Details -->
						<hr />
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td>Street Address</td>
									<td><?= $clientData['address1']; ?></td>
								</tr>
								<tr>
									<td>Street Address</td>
									<td><?= $clientData['address2']; ?></td>
								</tr>
								<tr>
									<td>City</td>
									<td><?= $clientData['city']; ?></td>
								</tr>
								<tr>
									<td>State, Zip Code</td>
									<td><?= $clientData['state'].' , '.$clientData['zip']; ?></td>
								</tr>
								<tr>
									<td>Ph</td>
									<td><?= $clientData['zip']; ?></td>
								</tr>
							</table>
						<hr />
					</div> <!-- Client Details -->
				</div>
			</div>
			
			<table class="table" width="100%" class="table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10%">UTBMS</td>
					<td width="20%">Price Point</td>
					<td width="20%">Default Description</td>
					<td width="10%">Int Ref#</td>
					<td width="10%">Qty</td>
					<td width="10%">Rate</td>
					<td width="10%">Price</td>
				</tr>
			</table>	
			
			<div class="rows">
				<div class="col-sm-12"><?= $clientcaseData['case_name']; ?>
					<div class="pull-right">
						<a href="javascript:void(0);" id="case_detail">Click</a>
					</div>
					<div id="billing_case_details" style="display:none;">
						<hr />
						<!-- case Details -->
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>Case Matter No </td>
								<td><?= $clientcaseData['case_matter_no']; ?></td>
							</tr>
							<tr>
								<td>Counsel Name </td>
								<td><?= $clientcaseData['counsel_name']; ?></td>
							</tr>	
							<tr>
								<td>Sales Representative </td>
								<td><?= $clientcaseData['salesRepo']; ?></td>
							</tr>
						</table>
						<!-- End Case Details -->
						<hr />
					</div>
				</div>
			</div>
			
			<!-- Invoice preview -->
			<table class="table" width="100%" class="table" border="0" cellspacing="0" cellpadding="0">
				<?php $subtotal = 0; foreach($taskunitbillingdata1 as $key => $outval){ ?>
					<?php foreach($outval as $innerkey => $val){ ?>
						<?php if(is_array($val)) { ?>
							<tr>
								<td width="10%"><?= $val['utbms_code']; ?></td>
								<td width="20%"><?= $val['price_point'].' - '.$teamLocation[$val['team_loc']]; ?></td>
								<td width="20%"><?= $val['pricing_description']; ?></td>
								<td width="10%"><?= $val['internal_ref_no_id']; ?></td>
								<td width="10%"><?= $val['quantity'].' '.$val['unit_name']; ?></td>
								<td width="10%">$ <?= $val['final_rate']; ?></td>
								<td width="10%">$ <?= $val['subtotal']; ?></td>
							</tr>
						<?php } else {$subtotal = $val;} ?>
					<?php }	?>
				<?php } ?>
				<tr>
					<td colspan="4"></td>
					<td>SubTotal</td>
					<td></td>
					<td>$ <?= $subtotal; ?></td>
				</tr>
				<?php $taxperadd=0; foreach($taxcodes as $key=>$taxpercent){ ?>
					<tr>
						<td colspan="4"></td>
						<td><?= $key ?></td>
						<td><?= number_format($taxpercent);?> %</td>
						<td>$ <?= isset($taxcodewiseAr[$key])?$taxcodewiseAr[$key]:''; ?></td>
						<?php $taxperadd = $taxperadd + $taxcodewiseAr[$key]; ?>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="4"></td>
					<td>Invoice Total </td>
					<td> </td>
					<td>$ <?= $taxperadd + $subtotal; ?></td>
				</tr>
			</table>
			<!-- End Invoice preview-->
				<div class="row">
					<div class="col-sm-12">
						<table class="table" width="100%">
						<tr>
							<td colspan="5"><strong>Invoice Supporting Notes</strong></td>
						</tr>
						<tr>
							<td colspan="5">
								<strong><?= $clientcaseData['case_name']; ?></strong>
							</td>
						</tr>
						<tr>
							<th>Price Point</th>
							<th>Project #</th>
							<th>Custom Description</th>
							<th>Quantity</th>
							<th>Item Created</th>
						</tr>
						<?php foreach($summarydata as $key => $val){ ?>
								<tr>
									<td><?= $val['price_point']." - ".$teamLocation[$val['team_loc']]; ?></td>
									<td><a href="<?= Url::base('http'); ?>/index.php?r=case-projects/index&case_id=<?= $clientcaseData['id']; ?>&task_id=<?= $val['task_id']; ?>&active=active"><?= $val['task_id']; ?></a></td>
									<td><?= $val['pricing_description']!=''?$val['pricing_description']:$val['pricing_cust_desc_template']; ?></td>
									<td><?= $val['quantity']." ".$val['unit_name']; ?></td>
									<td><?= $val['unit_created']; ?></td>
								</tr>
						<?php } ?>
						</table>
					</div>
				</div> 
			</div>
		</form>
	</fieldset>
	
	<!-- Button set text right-->
	<div class="button-set text-right">
		<button onclick="" title="Back" class="btn btn-primary" id="backrequest" type="button" name="yt1">Back</button>
		<button onclick="edit_invoice(<?= $invoice['id']; ?>);" title="Edit" class="btn btn-primary" id="editrequest" type="button" name="yt2">Edit</button>
		<button onclick="" title="Export" class="btn btn-primary" id="exportrequest" type="button" name="yt3">Export</button>
	</div>
	<!-- End text right -->
</div>
<script>
	
	$(function () {
		var start_date = datePickerController.createDatePicker({             
		formElements: { "start_date": "%Y-%m-%d" },         
		callbackFunctions:{
			"dateset":[ function (){
					var start_value = $('#start_date').val();
					if(start_value.length > 0){
						$('#ddduration').val('0');
					}
					$('#start_date_error').empty();
					$('#start_date_error').parent().removeClass('has-error');
				}],
			}
		});   
	});
	
	$('#backrequest').click(function(){
		showLoader();
		location.href = baseUrl +'billing-finalized-invoice/finalized-invoices';
	});
	
	/** invoice date **/
	$('#invoice_display').click(function(){
		if ($('#invoice_date').css('display') == 'none') {
			$('#invoice_date').css('display','block');
		} else {
			$('#invoice_date').css('display','none');
		}
	});
	
	/** Biller details **/
	$('#biller_invoice').click(function(){
		if ($('#biller_detail').css('display') == 'none') {
			$('#biller_detail').css('display','block');
		} else {
			$('#biller_detail').css('display','none');
		}
	});
	
	/** Client Details **/
	$('#client_details').click(function(){
		if ($('#client_addetail').css('display') == 'none') {
			$('#client_addetail').css('display','block');
		} else {
			$('#client_addetail').css('display','none');
		}
	});
	
	/** Case Details **/
	$('#case_detail').click(function(){
		if ($('#billing_case_details').css('display') == 'none') {
			$('#billing_case_details').css('display','block');
		} else {
			$('#billing_case_details').css('display','none');
		}
	});
	
</script>
<noscript></noscript>
