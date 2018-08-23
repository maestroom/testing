<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceFinal */
/* @var $form yii\widgets\ActiveForm */	
?>
<table class="table" width="100%">
	<tr>
		<td><strong>Invoice #</strong></td>
		<td><strong>Invoice Date</strong></td>
		<td><strong>Internal Reference Number</strong></td>
		<td><strong>Client Name</strong></td>
		<td><strong>Case Name</strong></td>
		<td><strong>Case Matter Number</strong></td>
		<td><strong>Counsel Name</strong></td>
		<td><strong>Sales Representative</strong></td>
		<td><strong>Location</strong></td>
		<td><strong>Price Point</strong></td>
		<td><strong>Default Description</strong></td>
		<td><strong>#Units</strong></td>
		<td><strong>Unit Name</strong></td>
		<td><strong>Rate</strong></td>
		<td><strong>Price</strong></td>
		<td><strong>Total Invoice Amount</strong></td>
	</tr>
	<?php $invoice_subtotal = 0 ;foreach($taskunitbillingdata1[$clientData['id']] as $key => $val){ ?>
		<tr>
			<td><?= $val['invoice_final_id']; ?></td>
			<td><?= $val['invoice_created']; ?></td>
			<td><?= $val['internal_ref_no_id']; ?></td>
			<td><?= $val['client_name']; ?></td>
			<td><?= $val['case_name']; ?></td>
			<td><?= $val['case_matter_no']; ?></td>
			<td><?= $val['counsel_name']; ?></td>
			<td><?= $val['sales_user_name']; ?></td>
			<td><?= $val['team_loc']; ?></td>
			<td><?= $val['price_point']; ?></td>
			<td><?= $val['billing_desc']; ?></td>
			<td><?= $val['quantity']; ?></td>
			<td><?= $val['unit_name']; ?></td>
			<td><?= $val['final_rate']; ?></td>
			<td><?= $val['subtotal']; ?></td>
			<td><?= $invoice_subtotal += $val['subtotal']; ?></td>
		</tr>
	<?php } ?>
</table>
				
				
				
	
