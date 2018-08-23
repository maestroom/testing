<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
?>

<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset workflow-management case-client-management">
		<div class="administration-main-cols">
			<div class="administration-lt-cols pull-left">
				<button id="controlbtn" aria-label="Expand or Collapse" title="Expand/Collapse" class="slide-control-btn" onclick="WorkflowToggle();" aria-label="Expand or Collapse"><span>&nbsp;</span></button>
				<input type="hidden" value="" id="client_id">				
				<ul>
					<li><a href="javascript:addNewClient();" title="Clients" class="admin-main-title"><em title="Clients" class="fa fa-folder-open text-danger"></em>Clients</a>
						<div class="select-items-dropdown">
							<?php 
								if(!empty($clientList)){ foreach ($clientList as $client){
									$clientlist_dropdown[$client->id] = html_entity_decode($client->client_name);
								} }
								
								echo Select2::widget([
									'name' => 'select_box',
									'attribute' => 'select_box',
									'data' => $clientlist_dropdown,
									'options' => ['prompt' => 'Select Client', 'title' => 'clients_index' ,'class' => 'form-control','onchange'=>'javascript:updateClient(this.value);','id'=>'client_list_dropdown'],
									/*'pluginOptions' => [
										'allowClear' => true
									]*/
								]);
							?>
							
						</div>
						<div class="left-dropdown-list">
							<div class="admin-left-module-list">
								<ul class="sub-links">
									<?php if(!empty($clientList)){ foreach ($clientList as $client){ ?>
										<li id="client_<?php echo $client->id; ?>"><a href="javascript:updateClient(<?php echo $client->id; ?>);" title="<?= html_entity_decode($client->client_name); ?>"><em class="fa fa-building text-danger" title="<?= html_entity_decode($client->client_name); ?>"></em> <?= html_entity_decode($client->client_name); ?></a></li>
									<?php }
										} ?>
								</ul>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right"></div>
		</div>
	</fieldset>
</div>
<script>
addClient();
$('label.extra_label').remove();
/**
 * Selected li 
 */
var selector = '.sub-links li';
$(selector).on('click', function(){
    $(selector).removeClass('active');
    $(this).addClass('active');
});
$('document').ready(function(){
	$('#is_change_form').val('0'); $('#is_change_form_main').val('0'); 
});

</script>
<noscript></noscript>

