<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
use app\models\Options;
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceFinal */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Edit Finalized Invoice';

//$his = (new Options)->ConvertOneTzToAnotherTz(date('h:i:s'),'UTC',$_SESSION['usrTZ'],'HIS');
//$datetime = (new Options)->ConvertOneTzToAnotherTz($data['created_date']." ".$his,'UTC',$_SESSION['usrTZ'],'YMDHIS');	
//(new Options)->ConvertOneTzToAnotherTz($datetime,'UTC',$_SESSION['usrTZ'],'HIS'); 
?>
<div class="right-main-container">
	<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
		<?php 
			$form = ActiveForm::begin([
				'id' => 'edit_finalized_invoice',
				'action' => '@web/index.php?r=billing-finalized-invoice/update-invoice',
			]); 
		?>
		<?= IsataskFormFlag::widget(); // change flag ?>
		<div class="one-cols-fieldset edit_finalized_invoice">
			<input type="hidden" name="flag" id="flag" value="<?= $flag; ?>" />
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<table class="table preview-invoice-table no-border" width="100%">
							<tr>
								<th id="edit_invoices_no" scope="col"><a href="javascript:void(0);" title="Invoice No" class="tag-header-black">Invoice No</a></th>
								<td headers="edit_invoices_no"><?= $invoice->id; ?></td> 
								<input type="hidden" name="display_type" id="display_type" value="<?= $invoice->display_by == 1 ? 'Itemized' : 'Consolidated'; ?>" />
								<input type="hidden" name="invoice_id" id="InvoiceFinal_id" value="<?= $invoice->id ?>" />
							</tr>
							<tr>
								<th id="edit_invoices_date" scope="col"><a href="javascript:void(0);" title="Date" class="tag-header-black">Date</a></th>
								<td headers="edit_invoices_date">
									<div class="input-group calender-group" style="width:25%;">
										<label for="created_date" style="display:none">&nbsp;</label>
										<input type="text" name="created_date" readonly="readonly" class="form-control" id="created_date" value="<?= $invoice->created_date; ?>" />
									</div>									
								</td>
							</tr>
							<tr>
								<th id="edit_invoices_biller" scope="col"><a href="javascript:void(0);" title="Biller" class="tag-header-black">Biller</a></th>
								<td headers="edit_invoices_biller">
									<table>
										<tr>
											<td style="padding:0px;" headers="edit_invoices_biller">
												<label for="invoice-biller-contact-id" style="display:none">&nbsp;</label>
												<?php 
													echo Select2::widget([
														'model' => $invoice,
														'attribute' => 'contact_id',
														'data' => $contactList,
														'options' => ['placeholder' => 'Select Contact', 'id' => 'invoice-biller-contact-id'],
														'pluginOptions' => [
															//'allowClear' => true,
															'width' => '180px',
														],
													]);
												?>
											</td>
											<td headers="edit_invoices_biller">
												<a href="javascript:void(0);" class="icon-set text-muted" id="biller_invoice">
													<em class="fa fa-search" title="Search" aria-hidden="true"></em>
												</a>
											</td>
										</tr>	
									</table>
								</td>
							</tr>
							<tr id="biller_detail" class="expand-detail-tr" style="display:none;">
								<td colspan="7" headers="edit_invoices_biller">
									<div class="row" style="display:none;">
										<div class="col-sm-121">
											<table class="table table-striped" width="100%"> <!-- no-border -->
												<tr>
													<th id="edit_invoices_blank_id1" scope="col">&nbsp;</th>
													<th id="edit_invoices_street_address1" scope="col"><a href="javascript:void(0);" title="Street Address 1" class="tag-header-black">Street Address 1</a></th>
													<td headers="edit_invoices_street_address1"><?= $contactData['add_1']; ?></td>
												</tr>
												<tr>
													<th id="edit_invoices_blank_id2" scope="col">&nbsp;</th>
													<th id="edit_invoices_street_address2" scope="col"><a href="javascript:void(0);" title="Street Address 2" class="tag-header-black">Street Address 2</a></th>
													<td headers="edit_invoices_street_address2"><?= $contactData['add_2']; ?></td>
												</tr>
												<tr>
													<th id="edit_invoices_blank_id3" scope="col">&nbsp;</th>
													<th id="edit_invoices_city_state" scope="col"><a href="javascript:void(0);" title="City, State, Zip Code" class="tag-header-black">City, State, Zip Code</a></th>
													<td headers="edit_invoices_city_state">
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
													<th id="edit_invoices_blank_id4" scope="col">&nbsp;</th>
													<th id="edit_invoices_emails" scope="col"><a href="javascript:void(0);" title="Email" class="tag-header-black">Email</a></th>
													<td headers="edit_invoices_emails"><?= $contactData['email']; ?></td>
												</tr>
												<tr>
													<th id="edit_invoices_blank_id5" scope="col">&nbsp;</th>
													<th id="edit_invoices_phone_no" scope="col"><a href="javascript:void(0);" title="Phone" class="tag-header-black">Phone</a></th>
													<td headers="edit_invoices_phone_no"><?= $contactData['phone_o']; ?></td>
												</tr>
												<tr>
													<th id="edit_invoices_blank_id6" scope="col">&nbsp;</th>
													<th id="edit_invoices_mobile_no" scope="col"><a href="javascript:void(0);" title="Mobile" class="tag-header-black">Mobile</a></th>
													<td headers="edit_invoices_mobile_no"><?= $contactData['phone_m']; ?></td>
												</tr>
											</table>	
										</div>
									</div>							
								</td>
							</tr>
							<tr>
								<th id="edit_invoices_clients" scope="col"><a href="javascript:void(0);" title="Client" class="tag-header-black">Client</a></th>
								<td headers="edit_invoices_clients"><?= $clientData['client_name']; ?> <a href="javascript:void(0);" id="client_details" class="icon-set text-muted"><em class="fa fa-search" title="Search" aria-hidden="true"></em></a></td>
							</tr>
							<tr id="client_addetail" class="expand-detail-tr" style="display:none;">
								<td colspan="7" headers="edit_invoices_clients">
									<div class="row" style="display:none;">
										<div class="col-sm-121">
											<table class="table table-striped" width="100%"> <!-- no-border -->
												<tr>
													<th id="client_edit_invoices_blank_id1" scope="col">&nbsp;</th>
													<th id="client_edit_invoices_street_address1" scope="col"><a href="javascript:void(0);" title="Street Address 1" class="tag-header-black">Street Address 1</a></th>
													<td headers="client_edit_invoices_street_address1"><?= $clientData['address1']; ?></td>
												</tr>
												<tr>
													<th id="client_edit_invoices_blank_id2" scope="col">&nbsp;</th>
													<th id="client_edit_invoices_street_address2" scope="col"><a href="javascript:void(0);" title="Street Address 2" class="tag-header-black">Street Address 2</a></th>
													<td headers="client_edit_invoices_street_address2"><?= $clientData['address2']; ?></td>
												</tr>
												<tr>
													<th id="client_edit_invoices_blank_id3" scope="col">&nbsp;</th>
													<th id="client_edit_invoices_city_state" scope="col"><a href="javascript:void(0);" title="City, State, Zip Code" class="tag-header-black">City, State, Zip Code</a></th>
													<td headers="client_edit_invoices_city_state"><?php 
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
													<th id="client_edit_invoices_blank_id4" scope="col">&nbsp;</th>
													<th id="client_edit_invoices_phone_no" scope="col"><a href="javascript:void(0);" title="Phone" class="tag-header-black">Phone</a></th>
													<td headers="client_edit_invoices_city_state"><?= $clientData['phone']; ?></td>
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
									<th id="edit_invoices_utmbs" scope="col" class="utmbs-th-width"><a href="javascript:void(0);" title="UTBMS" class="tag-header-black">UTBMS</a></th>
									<th id="edit_invoices_price_point" scope="col" class="price-point-th-width"><a href="javascript:void(0);" title="Price Point" class="tag-header-black">Price Point</a></th>
									<th id="edit_invoices_description" scope="col" class="discription-th-width"><a href="javascript:void(0);" title="Default Description" class="tag-header-black">Default Description</a></th>
									<th id="edit_invoices_refrence" scope="col" class="refrence-th-width text-left"><a href="javascript:void(0);" title="Int Ref#" class="tag-header-black">Int Ref#</a></th>
									<th id="edit_invoices_quantity" scope="col" class="qty-th-width text-left"><a href="javascript:void(0);" title="Quantity" class="tag-header-black">Quantity</a></th>
									<th id="edit_invoices_rate" scope="col" class="rate-th-width"><a href="javascript:void(0);" title="Rate" class="tag-header-black">Rate</a></th>
									<th id="edit_invoices_price" scope="col" class="price-th-width"><a href="javascript:void(0);"  title="Price" class="tag-header-black">Price</a></th>
								</tr>
							</thead>
							<tbody>
							<?php
								$total = 0; 
								foreach($clientcaseData as $key => $val1){ 
							?>
							<tr>
								<td colspan="7" headers="edit_invoices_utmbs"><a href="javascript:void(0);" title="<?= $val1['case_name']; ?>" class="tag-header-black"><strong><?= $val1['case_name']; ?></strong> </a> <a href="javascript:void(0);" onclick="show_casedetails(<?= $key; ?>);" class="icon-set text-muted"><em class="fa fa-search" title="Search"  aria-hidden="true"></em></a></td>
							</tr>
							<tr id="billing_case_details_<?php echo $key; ?>" class="expand-detail-tr" style="display:none;">
								<td colspan="7" headers="edit_invoices_utmbs">
									<div class="row" style="display:none;">
										<div class="col-sm-121">
											<!-- case Details -->
											<table class="table table-striped" width="100%"><!-- no-border -->
												<tr>
													<th id="edit_case_details_blank1" scope="col">&nbsp;</th>
													<th id="edit_invoices_case_matter_no" scope="col" title="Case Matter No">Case Matter No</th>
													<td headers="edit_invoices_case_matter_no"><?= $val1['case_matter_no']; ?></td>
												</tr>
												<tr>
													<th id="edit_case_details_blank2" scope="col">&nbsp;</th>
													<th id="edit_invoices_counsel_name" scope="col" title="Counsel Name">Counsel Name</th>
													<td headers="edit_invoices_counsel_name"><?= $val1['counsel_name']; ?></td>
												</tr>	
												<tr>
													<th id="edit_case_details_blank3" scope="col">&nbsp;</th>
													<th id="edti_invoices_sales_representative" scope="col" title="Sales Representative">Sales Representative</th>
													<td headers="edti_invoices_sales_representative"><?= $val1['salesRepo']['usr_first_name']." ".$val1['salesRepo']['usr_lastname']; ?></td>
												</tr>
											</table>
											<!-- End Case Details -->
										</div>
									</div>
								</td>
							</tr>
							<?php foreach($summarydata[$key] as $key1 => $val){ ?>
								<tr>
									<td headers="edit_invoices_utmbs"><?= $val['utbms_code']; ?></td>
									<td headers="edit_invoices_price_point"><?= $val['price_point']." - ".$teamLocation[$val['team_loc']]; ?></td>
									<td headers="edit_invoices_description"><?= $val['pricing_description']; ?></td>
									<td headers="edit_invoices_refrence"><?= $val['internal_ref_no_id']; ?></td>
									<td headers="edit_invoices_quantity">
										<label for="q" style="display:none">&nbsp;</label>
										<input class="numeric-field-qu negative-key" type="text" value="<?= number_format($val['quantity'],2,'.',''); ?>" style="width:55%;" name="quantity[<?= $key; ?>][<?php echo $val['billing_unit_id']; ?>]" id="q" />&nbsp;&nbsp;<?= $val['unit_name'] ?>
										<input type="hidden" name="final_units[<?= $key; ?>][<?= $val['billing_unit_id']; ?>]" value='<?php echo json_encode(['client_id'=>$val['client_id'],'client_case_id'=>$val['client_case_id'],'display_by'=>$invoice->display_by,'billing_unit_id'=>$val['billing_unit_id'],'invoice_id'=>$invoice->id,'team_loc'=>$val['team_loc'],'final_rate'=>$val['final_rate'], 'unit_price_id'=>$val['unit_price_id'],'discount'=>$val['discount'],'pricing_id'=>$val['pricing_id']]); ?>'/>
									</td>
									<td headers="edit_invoices_rate">$<?= $val['final_rate']; ?></td>
									<td headers="edit_invoices_price">$<?= number_format($val['subtotalamount'],2); ?></td>
								</tr>
							<?php $total += $val['subtotalamount']; } ?>
							<tr>
								<td headers="edit_invoices_utmbs"></td>
								<td headers="edit_invoices_price_point"></td>
								<td headers="edit_invoices_description"></td>
								<td headers="edit_invoices_refrence"></td>
								<td headers="edit_invoices_quantity"></td>
								<td headers="edit_invoices_rate"><a href="javascript:void(0);" title="Subtotal" class="tag-header-black"><strong>Subtotal</strong></a></td>
								<td headers="edit_invoices_price"><strong>$<?= number_format($total,2); ?></strong></td>
							</tr>
							<?php 
						}
							if(!empty($taxcodes)) {
								foreach($taxcodes as $key => $percent) {
							?>
							<tr>
								<td headers="edit_invoices_utmbs"></td>
								<td headers="edit_invoices_price_point"></td>
								<td headers="edit_invoices_description"></td>
								<td headers="edit_invoices_refrence"></td>
								<td headers="edit_invoices_quantity"></td>
								<td headers="edit_invoices_rate"><strong><?= $key; ?>&nbsp;(<?= $percent!=0?number_format($percent):'0.00'; ?> %)</strong></td>
								<td headers="edit_invoices_price"><strong>$<?= number_format($taxcodewiseAr[$key],2); ?></strong></td>
							</tr>
							<?php
									$total += $taxcodewiseAr[$key]; 
								}
							} 
							?>
							<tr>
								<td headers="edit_invoices_utmbs"></td>
								<td headers="edit_invoices_price_point"></td>
								<td headers="edit_invoices_description"></td>
								<td headers="edit_invoices_refrence"></td>
								<td headers="edit_invoices_quantity"></td>
								<td headers="edit_invoices_rate"><a href="javascript:void(0);" title="Invoice Amount" class="tag-header-black"><strong>Invoice Amount</strong></a></td>
								<td headers="edit_invoices_price"><strong>$<?= number_format($total,2); ?></strong></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<h5 class="th-title-head"><a href="javascript:void(0);" title="Invoice Supporting Notes" class="tag-header-black">Invoice Supporting Notes</a></h5>
						<table class="table table-striped invoice-supporting-table" width="100%">
							<?php foreach($clientcaseData as $key => $val){ ?>
							<thead>
								<tr>
									<th id="invoices_supporting_utmbs" scope="col" class="utmbs-th-width">&nbsp;</th>
									<th id="invoices_supporting_price" scope="col" class="price-width-th"><a href="javascript:void(0);" title="Price Point" class="tag-header-black">Price Point</a></th>
									<th id="invoices_supporting_description" scope="col" class="custom-width-th"><a href="javascript:void(0);" title="Custom Description" class="tag-header-black">Custom Description</a></th>
									<th id="invoices_supporting_project_name" scope="col" class="project-width-th"><a href="javascript:void(0);" title="Project #" class="tag-header-black">Project #</a></th>
									<th id="invoices_supporting_quantity" scope="col" class="qty-width-th"><a href="javascript:void(0);" title="Quantity" class="tag-header-black">Quantity</a></th>
									<th id="invoices_supporting_item_created" scope="col" class="item-width-th"><a href="javascript:void(0);" title="Item Created" class="tag-header-black">Item Created</a></th>
									<th id="invoices_supporting_price" scope="col" class="price-th-width">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="7" headers="invoices_supporting_utmbs">
										<a href="javascript:void(0);" title="<?= $val1['case_name']; ?>" class="tag-header-black"><strong><?= $val['case_name']; ?></strong></a>
									</td>
								</tr>
								<?php foreach($summarydata[$key] as $key1 => $val){ ?>
									<tr>
										<td headers="invoices_supporting_utmbs">&nbsp;</td>
										<td headers="invoices_supporting_price"><?= $val['price_point']." - ".$teamLocation[$val['team_loc']]; ?></td>
										<td headers="invoices_supporting_description"><textarea style="width:100%;" rows="3" cols="25" type="text" name="billing_desc[<?= $key; ?>][<?= $val['billing_unit_id']; ?>]" id="billing_desc_<?= $key; ?>" ><?= $val['billing_desc']; ?></textarea></td>
										<td headers="invoices_supporting_project_name"><a title="Project #<?php echo $val['task_id'];?>" href="<?= Url::base('http'); ?>/index.php?r=case-projects/index&case_id=<?= $key; ?>&task_id=<?= $val['task_id']; ?>"><?= $val['task_id']; ?></a></td>
										<td headers="invoices_supporting_quantity"><?= number_format($val['quantity'],2)." ".$val['unit_name']; ?></td>
										<td headers="invoices_supporting_item_created"><?= $val['unit_created']; ?></td>
										<td headers="invoices_supporting_price">&nbsp;</td>
									</tr>
								<?php
									} 
								}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php ActiveForm::end(); ?>
	<div class="button-set text-right">
		<button title="Cancel" class="btn btn-primary" id="cancelinvoice" type="button" name="yt3" onClick="cancel_invoice();">Cancel</button>
		<button title="Update" class="btn btn-primary" id="updateinvoice" type="button" name="yt2" onClick="update_invoice();">Update</button>
	</div>
</div>
<script>
	/* Change Event flag */
	$('input').bind("input", function(){ 
		$('#edit_finalized_invoice #is_change_form').val('1');
		$('#edit_finalized_invoice #is_change_form_main').val('1');
	});
	$('#fd-created_date').on('click',function(){
		$('#edit_finalized_invoice #is_change_form').val('1');
		$('#edit_finalized_invoice #is_change_form_main').val('1');
	});
	$('textarea').bind('input', function(){
		$('#edit_finalized_invoice #is_change_form').val('1');
		$('#edit_finalized_invoice #is_change_form_main').val('1');
	});
	$('select').on('change', function(){
		$('#edit_finalized_invoice #is_change_form').val('1');
		$('#edit_finalized_invoice #is_change_form_main').val('1');
	});
	$('document').ready(function(){
		$('#active_form_name').val('edit_finalized_invoice');
	});
	
	/**
	 * Cancel invoice from edit invoice form
	 */
	function cancel_invoice(){
		var flag = $('#flag').val(); var invoice_id = $('#InvoiceFinal_id').val();
		if(flag=='preview'){ 
			location.href = baseUrl +'billing-finalized-invoice/preview-invoice&invoice_id='+invoice_id+'&flag=preview';
		} else {
			location.href = baseUrl +'billing-finalized-invoice/finalized-invoices';
		}
	}
	
	/**
	 * Date picker for start_date and end_date
	 */
	$(function () {
		var date = datePickerController.createDatePicker({             
		formElements: { "created_date": "%m/%d/%Y" },         
		callbackFunctions:{
			"datereturned": [changeflag],
			"dateset":[ function (){
						var start_value = $('#created_date').val();
						if(start_value.length > 0){
							$('#ddduration').val('0');
						}
					},
				],
			}
		});   
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
	
	
	 /**
	  * Change Biller Contact From Edit invoice & get Details (like address,city etc...)
	  */
	 $('#invoice-biller-contact-id').change(function(){
		var contact_id = $('#invoice-biller-contact-id').val();
		$.ajax({
			type: "POST",
			url: baseUrl+"billing-finalized-invoice/get-biller-contact-details",
			data:'contact_id='+contact_id,
			cache: false,
			success:function(data){
				hideLoader();
				if ($('tr#biller_detail').css('display') != 'none') {
					$('tr#biller_detail').find('div.row').slideToggle('fast',function(){
						$('tr#biller_detail').fadeToggle('fast',function(){
							$('#biller_detail').html(data);
						});	
					});
				} else {
					$('#biller_detail').html(data);
				}
			}
		});
	});
</script>
<noscript></noscript>
