<?php
use yii\helpers\Html;
use app\models\User;
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$type = Yii::$app->request->get('type', '');
$accordianIndex = 'price_list_acc';
if($controller == 'billing-pricelist')
	$accordianIndex = 'price_list_acc';
else if($controller == 'billing-taxes')
	$accordianIndex = 'tax_acc';
else if($controller == 'billing-generate-invoice')
	$accordianIndex = 'generate_acc';
else if($controller == 'billing-finalized-invoice')
	$accordianIndex = 'finalized_acc';
	else if($controller == 'billing-closed-invoice')
		$accordianIndex = 'closed_acc';

?>
 <div class="acordian-main">
  <div id="accordion-container">
	<?php if((new User)->checkAccess(7.01) && ((new User)->checkAccess(7.02) || (new User)->checkAccess(7.04) || (new User)->checkAccess(7.06) || (new User)->checkAccess(7.08))){ ?>
	<h3 id="price_list_acc" title="Price List">Price List</h3>
		<div>
		<div class="acordian-div">
		  <ul class="sidebar-acordian">
			<?php if((new User)->checkAccess(7.02)){ ?>
		    <li><a href="javascript:void(0);" data-module="internal_team_pricing" class="billingModules <?= ($controller == 'billing-pricelist' && $action=='internal-team-pricing')?'active':''; ?>" title="Internal Team Pricing">Internal Team Pricing</a></li>
		    <?php }  if((new User)->checkAccess(7.04)){ ?>
		  	<li><a href="javascript:void(0);" data-module="internal_shared_pricing" class="billingModules <?= ($controller == 'billing-pricelist' && $action=='internal-shared-pricing')?'active':''; ?>" title="Internal Shared Pricing">Internal Shared Pricing</a></li>
		  	<?php } if((new User)->checkAccess(7.08)){ ?>
		    <li><a href="javascript:void(0);" data-module="client_preferred_pricing" class="billingModules <?= ($controller == 'billing-pricelist' && $action=='get-preferred-pricing' && $type == 'client')?'active':''; ?>" title="Client Preferred Pricing">Client Preferred Pricing</a></li>
		    <?php } if((new User)->checkAccess(7.06)){ ?>
		  	<li><a href="javascript:void(0);" data-module="case_preferred_pricing" class="billingModules <?= ($controller == 'billing-pricelist' && $action=='get-preferred-pricing' && $type == 'case')?'active':''; ?>" title="Case Preferred Pricing">Case Preferred Pricing</a></li>
		  	<?php } ?>
		  </ul>
		  </div>
		</div>
	<?php } if((new User)->checkAccess(7.10)){  ?>
	<h3 id="tax_acc" title="Taxes">Taxes</h3>
	<div>
		<div class="acordian-div">
			 <ul class="sidebar-acordian">
			 	<li><a href="javascript:void(0);" data-module="tax_classes" class="billingModulesTax <?= ($controller == 'billing-taxes' && $action=='tax-classes')?'active':''; ?>" title="Tax Classes">Tax Classes</a></li>
	  			<li><a href="javascript:void(0);" data-module="tax_codes" class="billingModulesTax <?= ($controller == 'billing-taxes' && $action=='tax-codes')?'active':''; ?>" title="Tax Codes">Tax Codes</a></li>
			 </ul>
		</div>
	</div>
	<?php } if((new User)->checkAccess(7.12)){ ?>
	<h3 id="generate_acc" title="Generate Invoices">Generate Invoices</h3>
	<div>
		<div class="acordian-div">
			<ul class="sidebar-acordian">
				<?php //if($controller != 'billing-generate-invoice'){ ?>
					<li><a href="javascript:void(0);" data-module="invoice_criteria" class="billingModulesInvoice <?= ($controller == 'billing-generate-invoice' && $action=='billing-invoice-management')?'active':''; ?>" title="Display New Invoice Criteria">Display New Invoice Criteria</a></li>
				<?php //} ?>
				<?php //if($controller == 'billing-generate-invoice' && $action == 'saved-invoice'){ ?>
					
					<li><a href="javascript:void(0);" data-module="display_saved_invoice" class="billingModulesInvoice <?= ($controller == 'billing-generate-invoice' && $action=='saved-invoice')?'active':''; ?>" title="Display Saved Invoices">Display Saved Invoices</a></li>
					
			 	<?php //} ?>

					<li style="display:<?php if($controller == 'billing-generate-invoice' && $action == 'display-generate-invoice'){ ?>block<?php } else { ?>none<?php }?>" id="finalizedinvoice_li"><a href="javascript:void(0);" data-module="final_invoice" class="billingModulesInvoice <?= ($controller == 'billing-generate-invoice' && $action=='delete-saved-invoice')?'active':''; ?>" title="Finalize Invoice">Finalize Invoice</a></li>

					<?php if((new User)->checkAccess(7.14)){ ?>
					<li style="display:<?php if($controller == 'billing-generate-invoice' && $action == 'saved-invoice'){ ?>block<?php } else { ?>none<?php }?>" id="deletesavedinvoice_li"><a href="javascript:void(0);" data-module="delete_saved_invoice" class="billingModulesInvoice <?= ($controller == 'billing-generate-invoice' && $action=='delete-saved-invoice')?'active':''; ?>" title="Delete Saved Invoices">Delete Saved Invoices</a></li>
					<?php } ?>
	  		</ul>
		</div>
	</div>
	<?php } if((new User)->checkAccess(7.15)){ ?>
		<h3 id="finalized_acc" title="Finalized Invoices">Finalized Invoices</h3>
		<div>
			<div class="acordian-div">
				<ul class="sidebar-acordian">
					<?php // if($controller=='billing-finalized-invoice'){ ?>
						<li><a href="javascript:void(0);" data-module="finalized_invoices" class="billingModulesFinalize <?= ($controller == 'billing-finalized-invoice' && $action=='finalized-invoices')?'active':''; ?>" title="Display Finalized Invoices">Display Finalized Invoices</a></li>
					<?php // } ?>

						<li id="exp_finalize_inv_li" style="display:<?php if($controller == 'billing-finalized-invoice' && $action == 'finalized-invoices'){ ?>block<?php } else {?>none<?php }?>;"><a href="javascript:void(0);" data-module="export_finalized_invoices" class="billingModulesFinalize <?= ($controller == 'billing-final-invoice' && $action=='export-final-invoice')?'active':''; ?>" title="Export Invoices">Export Invoices</a></li>
						<?php if((new User)->checkAccess(7.17)){ ?>
						<li id="delete_finalize_inv_li" style="display:<?php if($controller == 'billing-finalized-invoice' && $action == 'finalized-invoices'){ ?>block<?php } else {?>none<?php }?>;"><a href="javascript:void(0);" data-module="delete_finalized_invoices" class="billingModulesFinalize <?= ($controller == 'billing-final-invoice' && $action=='delete-final-invoice')?'active':''; ?>" title="Delete Invoices">Delete Invoices</a></li>
						<?php }?>
						<?php if((new User)->checkAccess(7.16)){ ?>
						<li id="merge_finalize_inv_li" style="display:<?php if($controller == 'billing-finalized-invoice' && $action == 'finalized-invoices'){ ?>block<?php } else {?>none<?php }?>;"><a href="javascript:void(0);" data-module="merge_finalized_invoices" class="billingModulesFinalize <?= ($controller == 'billing-final-invoice' && $action=='merge-final-invoice')?'active':''; ?>" title="Merge Invoices">Merge Invoices</a></li>
						<?php }?>
						<?php if((new User)->checkAccess(7.18)){ ?>
						<li id="close_finalize_inv_li" style="display:<?php if($controller == 'billing-finalized-invoice' && $action == 'finalized-invoices'){ ?>block<?php } else {?>none<?php }?>;"><a href="javascript:void(0);" data-module="close_finalized_invoices" class="billingModulesFinalize <?= ($controller == 'billing-final-invoice' && $action=='close-final-invoice')?'active':''; ?>" title="Close Invoices">Close Invoices</a></li>
						<?php }?>
				</ul>
			</div>
		</div>
<?php } if((new User)->checkAccess(7.19)){ ?>
	<h3 id="closed_acc" title="Closed Invoices">Closed Invoices</h3>
	<div>
		<div class="acordian-div">
			<ul class="sidebar-acordian">
				<?php if((new User)->checkAccess(7.2)){ ?>
					<li><a href="javascript:void(0);" data-module="closed_invoices" class="billingModulesClose <?= ($controller == 'billing-closed-invoice' && $action=='closed-invoices')?'active':''; ?>" title="Display Closed Invoices">Display Closed Invoices</a></li>
				<?php  } ?>
				<?php if((new User)->checkAccess(7.21)){ ?>
					<li id="reopen_finalize_inv_li" style="display:<?php if($controller == 'billing-closed-invoice' && $action == 'closed-invoices'){ ?>block<?php } else {?>none<?php }?>;"><a href="javascript:void(0);" data-module="reopen_finalized_invoices" class="billingModulesClose <?= ($controller == 'billing-closed-invoice' && $action=='reopen-final-invoice')?'active':''; ?>" title="ReOpen Invoices">ReOpen Invoices</a></li>
				<?php  } ?>
				<?php if((new User)->checkAccess(7.22)){ ?>
					<li id="exp_closed_inv_li" style="display:<?php if($controller == 'billing-closed-invoice' && $action == 'closed-invoices'){ ?>block<?php } else {?>none<?php }?>;"><a href="javascript:void(0);" data-module="export_closed_invoices" class="billingModulesClose <?= ($controller == 'billing-closed-invoice' && $action=='export-closed-invoice')?'active':''; ?>" title="Export Invoices">Export Invoices</a></li>
					<?php  } ?>
			</ul>
		</div>
	</div>
<?php } ?>
</div>
</div>
<script type="text/javascript">
var accordionOptions = {
	 heightStyle: 'fill',clearStyle: true,autoHeight: false, icons: { "header": "fa fa-caret-right pull-right", "activeHeader": "fa fa-caret-down pull-right" }
,create: function( event, ui ) {

//$("#accordion-container h3 span").removeClass('ui-accordion-header-icon');
$("#accordion-container h3 span").removeClass('ui-icon');

}, active:$('#accordion-container h3').index($('#<?=$accordianIndex?>'))};
$("#accordion-container").accordion(accordionOptions);
jQuery(document).ready(function($) {

 	$(window).resize(function(){
	 	// update accordion height
 		$('#accordion-container').accordion("refresh");
		$("#accordion-container" ).accordion( "destroy" );
		$("#accordion-container" ).accordion(accordionOptions);
 	});
 });
</script>
<noscript></noscript>
