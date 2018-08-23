<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\InvoiceFinal;
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceFinal */
/* @var $form yii\widgets\ActiveForm */
?>

<div style="padding:1px 2px 1px 1px; border:solid 1px #333;">
<h2 style="padding:10px; color:#FFF; background:#333; font-size:18px; margin:0px;">Invoice</h2>

<table style="font-family:Arial; font-size:14px;" border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td width="20%" style="padding:5px; font-weight:bold;">Invoice No:</td>
		<td width="30%" style="padding:5px;"><?= $invoice['id']; ?></td>
		<td width="15%" style="padding:5px; font-weight:bold;">Dated:</td>
		<td width="35%" style="padding:5px;"><?= $invoice['created_date']; ?></td>
	</tr>
</table>

<div style="border-bottom:solid 1px #e9e7e8; margin:0px; padding:0px; height:1px;"></div>

<table style="font-family:Arial; font-size:10px;" border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td width="10%" style="padding:5px; font-weight:bold;">Biller:</td>
		<td width="40%" style="padding:5px;"><?= $contactData['fname'].' '.$contactData['lname']; ?></td>
		<td width="10%" style="padding:5px; font-weight:bold;">Client:</td>
		<td width="40%" style="padding:5px;"><?= $clientData['client_name']; ?></td>
	</tr>
</table>

<div style="border-bottom:solid 1px #e9e7e8; margin:0px; padding:0px; height:1px;"></div>

<table style="font-family:Arial; font-size:10px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding:5px;" valign="top">
			<table style="font-family:Arial; font-size:10px;" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">Street Address 1: </td>
					<td style="padding:5px; width:50%;"><?= $contactData['add_1']; ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">Street Address 2: </td>
					<td style="padding:5px; width:50%;"><?= $contactData['add_2']; ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">City, State, Zip: </td>
					<td style="padding:5px; width:50%;">
						<?php // $contactData['state'].$contactData['zip']!=''?','. $contactData['zip']:''; ?>
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
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">Email: </td>
					<td style="padding:5px; width:50%;"><?= $contactData['email']; ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">Phone: </td>
					<td style="padding:5px; width:50%;"><?= $contactData['phone_o']; ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:30%;" align="right">Mobile Phone:</td>
					<td style="padding:5px;"><?= $contactData['phone_m']; ?></td>
				</tr>
			</table>
		</td>
		<td style="padding:5px;" valign="top">
			<table style="font-family:Arial; font-size:10px;" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">Street Address 1: </td>
					<td style="padding:5px; width:50%;"><?= $clientData['address1'] ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">Street Address 2: </td>
					<td style="padding:5px; width:50%;"><?= $clientData['address2'] ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:50%;" align="right">City, State, Zip: </td>
					<td style="padding:5px; width:50%;"><?= $clientData['city'].' , '.$clientData['state'].' , '.$clientData['zip']; ?></td>
				</tr>
				<tr>
					<td style="padding:5px; font-weight:bold; width:30%;" align="right">Phone:</td>
					<td style="padding:5px;"><?= $clientData['phone']; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<div style="border-bottom:solid 1px #e9e7e8; margin:15px 0px; padding:0px; height:1px;"></div>

<table style="font-family:Arial; font-size:10px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding:5px; font-weight:bold; width:12%;">UTBMS</td>
		<td style="padding:5px; font-weight:bold; width:22%;">Price Point</td>
		<td style="padding:5px; font-weight:bold; width:22%;">Default Description</td>
		<td style="padding:5px; font-weight:bold; width:10%;">Int Ref#</td>
		<td style="padding:5px; font-weight:bold; width:10%;">Quantity</td>
		<td style="padding:5px; font-weight:bold; width:14%;">Rate</td>
		<td style="padding:5px; font-weight:bold; width:10%;">Price</td>
	</tr>
	<?php
		$total = 0; 
		foreach($clientcaseData as $key => $val1){ 
	?>
	<tr>
		<td colspan="7" style="padding:5px; font-weight:bold;"><?= $val1['case_name']; ?></td>
	</tr>
	<?php if(isset($taskunitbillingdata1[$key])){foreach($taskunitbillingdata1[$key] as $key1 => $val){ ?>
		<tr>
			<td style="padding:5px; width:12%;"><?= $val['utbms_code']; ?></td>
			<td style="padding:5px; width:22%;"><?= $val['price_point']." - ".$teamLocation[$val['team_loc']]; ?></td>
			<td style="padding:5px; width:22%;"><?= $val['pricing_description']; ?></td>
			<td style="padding:5px; width:10%;"><?= $val['internal_ref_no_id']; ?></td>
			<td style="padding:5px; width:10%;"><?= number_format(round($val['quantity'],2),2)." ".$val['unit_name']; ?></td>
			<td style="padding:5px; width:14%; text-align:left;">$ <?= number_format($val['final_rate'],2); ?></td>
			<td style="padding:5px; width:10%; text-align:left;">$ <?= number_format($val['subtotal'],2); ?></td>
		</tr>
	<?php $total += $val['subtotal']; } 
		}
	?>
	<tr>
	 <td colspan="7"><div style="border-bottom:solid 1px #e9e7e8; margin:15px 0px; font-size:10px; padding:0px; height:1px;"></div></td>
	</tr>
	<tr>
		<td style="padding:5px; width:12%;"></td>
		<td style="padding:5px; width:22%;"></td>
		<td style="padding:5px; width:22%;"></td>
		<td style="padding:5px; width:10%;"></td>
		<td style="padding:5px; width:10%;"></td>
		<td style="padding:5px; width:14%; font-weight:bold;">Subtotal</td>
		<td style="padding:5px; width:10%; font-weight:bold;text-align:right;">$ <?= number_format($total,2); ?></td>
	</tr>
	<?php 
		}
		if(!empty($taxcodes)){
			foreach($taxcodes as $key => $percent) {
	?>
	<tr>
		<td style="padding:5px; width:12%;"></td>
		<td style="padding:5px; width:22%;"></td>
		<td style="padding:5px; width:22%;"></td>
		<td style="padding:5px; width:10%;"></td>
		<td style="padding:5px; width:10%;"></td>
		<td style="padding:5px; width:14%; font-weight:bold;"><?= $key; ?>&nbsp;(<?= $percent!=0?number_format($percent):'0.00'; ?> %)</td>
		<td style="padding:5px; width:10%; font-weight:bold;text-align:right;">$ <?= number_format($taxcodewiseAr[$key],2); ?></td>
	</tr>
	<?php
				$total += $taxcodewiseAr[$key]; 
			}
		} 
	?>
	<tr>
		<td style="padding:5px; width:12%;"></td>
		<td style="padding:5px; width:22%;"></td>
		<td style="padding:5px; width:22%;"></td>
		<td style="padding:5px; width:10%;"></td>
		<td style="padding:5px; width:10%;"></td>
		<td style="padding:5px; width:14%; font-weight:bold;">Invoice Amount</td>
		<td style="padding:5px; width:10%; font-weight:bold;text-align:right;">$ <?= number_format($total,2); ?></td>
	</tr>
	
	<tr>
		<td colspan="7">
		    <table style="font-family:Arial; font-size:10px;" cellpadding="0" cellspacing="0" width="100%">
				<?php if($summarynote==1){ ?>
					<tr>
						<td colspan="7" style="padding:5px; font-weight:bold;">Invoice Supporting Notes</td>
					</tr>
					<?php foreach($clientcaseData as $key => $val){ ?>
					<tr>
						<td colspan="7" style="padding:5px; font-weight:bold;"><?= $val['case_name']; ?></td>
					</tr>
					<tr>
						<td style="padding:5px; width:12%;"></td>
						<td style="padding:5px; width:22%; font-weight:bold;">Price Point</td>
						<td style="padding:5px; width:22%; font-weight:bold;">Custom Description</td>
						<td style="padding:5px; width:10%; font-weight:bold;">Project #</td>
						<td style="padding:5px; width:10%; font-weight:bold;">Quantity</td>
						<td style="padding:5px; width:14%; font-weight:bold;">Item Created</td>
						<td style="padding:5px; width:10%;"></td>
					</tr>
					<?php foreach($summarydata[$key] as $key1 => $val){ ?>
					<tr>
						<td style="padding:5px; width:12%;"></td>
						<td style="padding:5px; width:22%;"><?= $val['price_point']." - ".$teamLocation[$val['team_loc']]; ?></td>
						<td style="padding:5px; width:22%;">
							<?= 
								(new InvoiceFinal)->smart_wordwrap($val['billing_desc'], 20); // word-wrap function
							?>
						</td>
						<td style="padding:5px; width:10%;"><?= $val['task_id']; ?></td>
						<td style="padding:5px; width:10%;"><?= number_format(round($val['quantity'],2),2)." ".$val['unit_name']; ?></td>
						<td style="padding:5px; width:14%;"><?= $val['unit_created']; ?></td>
						<td style="padding:5px; width:10%;"></td>
					</tr>
					<?php
						} 
					}
				} ?>
				</td>
			</tr>
		</table>
	</table>
</div>

