<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\InvoiceFinal;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceFinal */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Closed Invoice Preview';
?>
<div class="right-main-container">
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
	<div class="one-cols-fieldset edit_finalized_invoice">
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<table class="table preview-invoice-table no-border" width="100%">
						<tr>
							<th id="preview_invoices_no" scope="col"><a href="javascript:void(0);" title="Invoice No" class="tag-header-black">Invoice No</a></th>
							<td headers="preview_invoices_no"><?= $invoice['id']; ?></td>
						</tr>
						<tr>
							<th id="preview_invoices_date" scope="col"><a href="javascript:void(0);" title="Date" class="tag-header-black">Date</a></th>
							<td headers="preview_invoices_date"><?= $invoice['created_date']; ?></td>
						</tr>
						<tr>
							<th id="preview_invoices_biller" scope="col"><a href="javascript:void(0);" title="Biller" class="tag-header-black">Biller</a></th>
							<td headers="preview_invoices_biller"><?= $contactData['fname'].' '.$contactData['lname']; ?> <a href="javascript:void(0);" id="biller_invoice" class="icon-set text-muted" title="Biller Info"><em class="fa fa-search" title="Search" aria-hidden="true"></em></a></td>
						</tr>
						<tr id="biller_detail" class="expand-detail-tr" style="display:none;">
							<td colspan="7" headers="preview_invoices_biller">
								<div class="row" style="display:none;">
									<div class="col-sm-121">
										<table class="table table-striped" width="100%"> <!-- no-border -->
											<tr>
												<th id="preview_invoices_blank_id1" scope="col">&nbsp;</th>
												<th id="preview_invoices_street_address1" scope="col"><a href="javascript:void(0);" title="Street Address 1" class="tag-header-black">Street Address 1</a></th>
												<td headers="preview_invoices_street_address1"><?= $contactData['add_1']; ?></td>
											</tr>
											<tr>
												<th id="preview_invoices_blank_id2" scope="col">&nbsp;</th>
												<th id="preview_invoices_street_address2" scope="col"><a href="javascript:void(0);" title="Street Address 2" class="tag-header-black">Street Address 2</a></th>
												<td headers="preview_invoices_street_address2"><?= $contactData['add_2']; ?></td>
											</tr>
											<tr>
												<th id="preview_invoices_blank_id3" scope="col">&nbsp;</th>
												<th id="preview_invoices_city_state" scope="col"><a href="javascript:void(0);" title="City, State, Zip Code" class="tag-header-black">City, State, Zip Code</a></th>
												<td headers="preview_invoices_city_state">
													<?php
														if($contactData['city'].$contactData['state'].$contactData['zip']!='')
														{
															echo $contactData['city'].' ,'.$contactData['state'].' ,'.$contactData['zip'];
														}
														else if($contactData['state'].$contactData['zip']!='')
														{
															echo $contactData['state'].' ,'.$contactData['zip'];
														}
														else if($contactData['city'].$contactData['zip']!='')
														{
															echo $contactData['city'].' ,'.$contactData['zip'];
														}
														else
														{
															echo $contactData['zip'];
														}
													?>
												</td>
											</tr>
											<tr>
												<th id="preview_invoices_blank_id4" scope="col">&nbsp;</th>
												<th id="preview_invoices_emails" scope="col"><a href="javascript:void(0);" title="Email" class="tag-header-black">Email</a></th>
												<td headers="preview_invoices_emails"><?= $contactData['email']; ?></td>
											</tr>
											<tr>
												<th id="preview_invoices_blank_id5" scope="col">&nbsp;</th>
												<th id="preview_invoices_phone_no" scope="col"><a href="javascript:void(0);" title="Phone" class="tag-header-black">Phone</a></th>
												<td headers="preview_invoices_phone_no"><?= $contactData['phone_o']; ?></td>
											</tr>
											<tr>
												<th id="preview_invoices_blank_id6" scope="col">&nbsp;</th>
												<th id="preview_invoices_mobile_no" scope="col"><a href="javascript:void(0);" title="Mobile" class="tag-header-black">Mobile</a></th>
												<td headers="preview_invoices_mobile_no"><?= $contactData['phone_m']; ?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<th id="preview_invoices_clients" scope="col"><a href="javascript:void(0);" title="Client" class="tag-header-black">Client</a></th>
							<td headers="preview_invoices_clients"><?= $clientData['client_name']; ?> <a href="javascript:void(0);" id="client_details" class="icon-set text-muted" title="Client Info"><em class="fa fa-search" title="Search" aria-hidden="true"></em></a></td>
						</tr>
						<tr id="client_addetail" class="expand-detail-tr" style="display:none;">
							<td colspan="7" headers="preview_invoices_clients">
								<div class="row" style="display:none;">
									<div class="col-sm-121">
										<table class="table table-striped" width="100%"> <!-- no-border -->
											<tr>
												<th id="client_invoices_blank_id1" scope="col">&nbsp;</th>
												<th id="client_invoices_street_address1" scope="col"><a href="javascript:void(0);" title="Street Address 1" class="tag-header-black">Street Address 1</a></th>
												<td headers="client_invoices_street_address1"><?= $clientData['address1']; ?></td>
											</tr>
											<tr>
												<th id="client_invoices_blank_id2" scope="col">&nbsp;</th>
												<th id="client_invoices_street_address2" scope="col"><a href="javascript:void(0);" title="Street Address 2" class="tag-header-black">Street Address 2</a></th>
												<td headers="client_invoices_street_address2"><?= $clientData['address2']; ?></td>
											</tr>
											<tr>
												<th id="client_invoices_blank_id3" scope="col">&nbsp;</th>
												<th id="client_invoices_city_state" scope="col"><a href="javascript:void(0);" title="City, State, Zip Code" class="tag-header-black">City, State, Zip Code</a></th>
												<td headers="client_invoices_city_state"><?php
														if($clientData['city'].$clientData['state'].$clientData['zip']!='')
														{
															echo $clientData['city'].' ,'.$clientData['state'].' ,'.$clientData['zip'];
														}
														else if($clientData['state'].$clientData['zip']!='')
														{
															echo $clientData['state'].' ,'.$clientData['zip'];
														}
														else if($clientData['city'].$clientData['zip']!='')
														{
															echo $clientData['city'].' ,'.$clientData['zip'];
														}
														else
														{
															echo $clientData['zip'];
														}
													?>
												</td>
											</tr>
											<tr>
												<th id="client_invoices_blank_id4" scope="col">&nbsp;</th>
												<th id="client_invoices_phone_no" scope="col"><a href="javascript:void(0);" title="Phone" class="tag-header-black">Phone</a></th>
												<td headers="client_invoices_phone_no"><?= $clientData['phone']; ?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<h5 class="th-title-head"><a href="javascript:void(0);" title="Invoice Items" class="tag-header-black">Invoice Items</a></h5>
					<table class="table table-striped preview-invoice-items-table" width="100%">
						<thead>
							<tr class="grid-row">
								<th id="preview_invoices_utmbs" scope="col" class="utmbs-th-width"><a href="javascript:void(0);" title="UTBMS" class="tag-header-black">UTBMS</a></th>
								<th id="preview_invoices_price_point" scope="col" class="price-point-th-width"><a href="javascript:void(0);" title="Price Point" class="tag-header-black">Price Point</a></th>
								<th id="preview_invoices_description" scope="col" class="discription-th-width"><a href="javascript:void(0);" title="Default Description" class="tag-header-black">Default Description</a></th>
								<th id="preview_invoices_refrence" scope="col" class="refrence-th-width text-left"><a href="javascript:void(0);" title="Int Ref#" class="tag-header-black">Int Ref#</a></th>
								<th id="preview_invoices_quantity" scope="col"class="qty-th-width text-left"><a href="javascript:void(0);" title="Quantity" class="tag-header-black">Quantity</a></th>
								<th id="preview_invoices_rate" scope="col" class="rate-th-width"><a href="javascript:void(0);" title="Rate" class="tag-header-black">Rate</a></th>
								<th id="preview_invoices_price" scope="col" class="price-th-width"><a href="javascript:void(0);" title="Price" class="tag-header-black">Price</a></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$total = 0;
								foreach($clientcaseData as $key => $val1){
							?>
							<tr>
								<td colspan="7" headers="preview_invoices_utmbs"><a href="javascript:void(0);" title="<?= $val1['case_name']; ?>" class="tag-header-black"><strong><?= $val1['case_name']; ?></strong></a> <a href="javascript:void(0);" onclick="show_casedetails(<?= $key; ?>);" class="icon-set text-muted" title="<?= $val1['case_name']; ?> Info"><em class="fa fa-search"  title="Search" aria-hidden="true"></em></a></td>
							</tr>
							<tr id="billing_case_details_<?php echo $key; ?>" class="expand-detail-tr" style="display:none;">
								<td colspan="7" headers="preview_invoices_utmbs">
									<div class="row" style="display:none;">
										<div class="col-sm-12">
											<!-- case Details -->
											<table class="table table-striped" width="100%"> <!-- no-border -->
												<tr>
													<th id="invoices_blank_id1" scope="col">&nbsp;</th>
													<th id="invoices_case_matter_no" scope="col"><a href="javascript:void(0);" title="Case Matter No" class="tag-header-black">Case Matter No</a></th>
													<td headers="invoices_case_matter_no"><?= $val1['case_matter_no']; ?></td>
												</tr>
												<tr>
													<th id="invoices_blank_id2" scope="col">&nbsp;</th>
													<th id="invoices_counsel_name" scope="col"><a href="javascript:void(0);" title="Counsel Name" class="tag-header-black">Counsel Name</a></th>
													<td headers="invoices_counsel_name"><?= $val1['counsel_name']; ?></td>
												</tr>
												<tr>
													<th id="invoices_blank_id3" scope="col">&nbsp;</th>
													<th id="invoices_sales_representative" scope="col"><a href="javascript:void(0);" title="Sales Representative" class="tag-header-black">Sales Representative</a></th>
													<td headers="invoices_sales_representative"><?= $val1['salesRepo']['usr_first_name']." ".$val1['salesRepo']['usr_lastname']; ?></td>
												</tr>
											</table>
											<!-- End Case Details -->
										</div>
									</div>
								</td>
							</tr>
						<?php foreach($taskunitbillingdata1[$key] as $key1 => $val){ ?>
						<tr>
							<td headers="preview_invoices_utmbs" class="utmbs-td-width"><?= $val['utbms_code']; ?></td>
							<td headers="preview_invoices_price_point" class=" word-break price-point-td-width"><?= Html::encode($val['price_point']." - ".$teamLocation[$val['team_loc']]); // word-wrap function; ?></td>
							<td headers="preview_invoices_description" class=" word-break discription-td-width"><?= Html::encode($val['pricing_description']); // word-wrap function ?></td>
							<td headers="preview_invoices_refrence" class="refrence-td-width text-left"><?= $val['internal_ref_no_id']; ?></td>
							<td headers="preview_invoices_quantity " class="word-break qty-td-width text-left"><?= Html::encode(number_format($val['quantity'],2)." ".$val['unit_name']); // word-wrap function ?></td>
							<td headers="preview_invoices_rate" class="rate-td-width">$<?= $val['final_rate']; ?></td>
							<td headers="preview_invoices_price" class="price-td-width">$<?= number_format($val['subtotal'],2); ?></td>
						</tr>
						<?php $total += $val['subtotal']; } ?>
						<tr>
							<td headers="preview_invoices_utmbs"></td>
							<td headers="preview_invoices_price_point"></td>
							<td headers="preview_invoices_description"></td>
							<td headers="preview_invoices_refrence"></td>
							<td headers="preview_invoices_quantity"></td>
							<td headers="preview_invoices_rate"><a href="javascript:void(0);" title="Subtotal" class="tag-header-black"><strong>Subtotal</strong></a></td>
							<td headers="preview_invoices_price"><strong>$<?= number_format($total,2); ?></strong></td>
						</tr>
						<?php
							}
							if(!empty($taxcodes)) {
								foreach($taxcodes as $key => $percent) {
							?>
							<tr>
								<td headers="preview_invoices_utmbs"></td>
								<td headers="preview_invoices_price_point"></td>
								<td headers="preview_invoices_description"></td>
								<td headers="preview_invoices_refrence"></td>
								<td headers="preview_invoices_quantity"></td>
								<td headers="preview_invoices_rate"><strong><?= $key; ?>&nbsp;(<?= $percent!=0?number_format($percent):'0.00'; ?> %)</strong></td>
								<td headers="preview_invoices_price"><strong>$<?= number_format($taxcodewiseAr[$key],2); ?></strong></td>
							</tr>
							<?php
								$total += $taxcodewiseAr[$key];
							}
						}
						?>
						<tr>
							<td headers="preview_invoices_utmbs"></td>
							<td headers="preview_invoices_price_point"></td>
							<td headers="preview_invoices_description"></td>
							<td headers="preview_invoices_refrence"></td>
							<td headers="preview_invoices_quantity"></td>
							<td headers="preview_invoices_rate"><a href="javascript:void(0);" title="Invoice Amount" class="tag-header-black"><strong>Invoice Amount</strong></a></td>
							<td headers="preview_invoices_price"><strong>$<?= number_format($total,2); ?></strong></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- Invoice Supporting Notes -->
			<div class="row">
				<div class="col-sm-12">
					<h5 class="th-title-head"><a href="javascript:void(0);" title="Invoice Supporting Notes" class="tag-header-black">Invoice Supporting Notes</a></h5>
					<table class="table table-striped invoice-supporting-table" width="100%"><!-- invoice-supporting-table -->
						<?php foreach($clientcaseData as $key => $val){ ?>
							<thead>
								<tr>
									<th id="invoices_notes_utmbs" scope="col" class="utmbs-th-width">&nbsp;</th>
									<th id="invoices_notes_price_point" scope="col" class="price-width-th"><a href="javascript:void(0);" title="Price Point" class="tag-header-black">Price Point</a></th>
									<th id="invoices_notes_description" scope="col" class="custom-width-th"><a href="javascript:void(0);" title="Custom Description" class="tag-header-black">Custom Description</a></th>
									<th id="invoices_notes_project_id" scope="col" class="project-width-th"><a href="javascript:void(0);" title="Project #" class="tag-header-black">Project #</a></th>
									<th id="invoices_notes_quantity" scope="col" class="qty-width-th"><a href="javascript:void(0);" title="Quantity" class="tag-header-black">Quantity</a></th>
									<th id="invoices_notes_item_created" scope="col" class="item-width-th"><a href="javascript:void(0);" title="Item Created" class="tag-header-black">Item Created</a></th>
									<th id="invoices_notes_blank" scope="col" class="price-th-width">&nbsp;</th>
								</tr>
							</thead>
						<tbody>
						<tr>
							<td colspan="7" headers="invoices_notes_blank">
								<a href="javascript:void(0);" title="<?= $val['case_name']; ?>" class="tag-header-black"><strong><?= $val['case_name']; ?></strong></a>
							</td>
						</tr>
						<?php foreach($summarydata[$key] as $key1 => $val){ ?>
							<tr>
								<td headers="invoices_notes_utmbs" class="utmbs-td-width">&nbsp;</td>
								<td headers="invoices_notes_price_point" class="price-width-td  word-break"><?= $val['price_point']." - ".$teamLocation[$val['team_loc']]; ?></td>
								<td headers="invoices_notes_description" class="custom-width-td  word-break"><?= $val['billing_desc']; ?></td>
								<td headers="invoices_notes_project_id" class="project-width-td"><a title="Project #<?php echo $val['task_id']; ?>" href="<?= Url::base('http'); ?>/index.php?r=case-projects/index&case_id=<?= $key; ?>&task_id=<?= $val['task_id']; ?>"><?= $val['task_id']; ?></a></td>
								<td headers="invoices_notes_quantity" class="qty-width-td  word-break"><?= number_format($val['quantity'],2)." ".$val['unit_name']; ?></td>
								<td headers="invoices_notes_item_created" class="item-width-td"><?= $val['unit_created']; ?></td>
								<td headers="invoices_notes_blank" class="price-td-width">&nbsp;</td>
							</tr>
						<?php
								}
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- End Invoice -->
		</div>
	</div>
	<div class="button-set text-right">
		<button onclick="" title="Back" class="btn btn-primary" id="backrequest" type="button" name="yt1">Back</button>
		<?php if((new User)->checkAccess(7.22)){ ?>
		<button title="Export" class="btn btn-primary" type="button" name="yt3" onclick="exportrequest_pdf(<?= $invoice['id']; ?>);">Export</button>
		<?php } ?>
	</div>
</div>
<script>
	$('#backrequest').click(function(){
		showLoader();
		location.href = baseUrl +'billing-closed-invoice/closed-invoices';
	});

	/** invoice date **/
	$('#invoice_display').click(function(){
		if ($('#invoice_date').css('display') == 'none') {
			$('tr#invoice_date').fadeToggle('fast',function(){
				$(this).find('div.row').slideToggle('fast');
			});
		} else {
			$('tr#invoice_date').find('div.row').slideToggle('fast',function(){
				$('tr#invoice_date').fadeToggle('fast');
			});
		}
	});

	/** Biller details **/
	$('#biller_invoice').click(function(){
		if ($('#biller_detail').css('display') == 'none') {
			$('tr#biller_detail').fadeToggle('fast',function(){
				$(this).find('div.row').slideToggle('fast');
			});
		} else {
			$('tr#biller_detail').find('div.row').slideToggle('fast',function(){
				$('tr#biller_detail').fadeToggle('fast');
			});
		}
	});

	/** Client Details **/
	$('#client_details').click(function(){
		if ($('#client_addetail').css('display') == 'none') {
			$('tr#client_addetail').fadeToggle('fast',function(){
				$(this).find('div.row').slideToggle('fast');
			});
		} else {
			$('tr#client_addetail').find('div.row').slideToggle('fast',function(){
				$('tr#client_addetail').fadeToggle('fast');
			});
		}
	});

	/** Case Details **/
	function show_casedetails(key){
		if ($('#billing_case_details_'+key).css('display') == 'none') {
			$('tr#billing_case_details_'+key).fadeToggle('fast',function(){
				$(this).find('div.row').slideToggle('fast');
			});
		} else {
			$('tr#billing_case_details_'+key).find('div.row').slideToggle('fast',function(){
				$('tr#billing_case_details_'+key).fadeToggle('fast');
			});
		}
	}
</script>
<noscript></noscript>
