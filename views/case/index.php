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
				<button id="controlbtn" title="Expand/Collapse" class="slide-control-btn" aria-label="Expand or Collapse" onclick="WorkflowToggle();" aria-label="Expand or Collapse"><span>&nbsp;</span></button>
				<input type="hidden" value="" id="client_id">
				<input type="hidden" value="" id="case_id">
				<ul>
					<li>
						<a href="javascript:addCaseNew();" title="Cases"><em title="Cases" class="fa fa-folder-open text-danger"></em>Cases</a>
						<div class="select-items-dropdown">
							<?php 
								if(!empty($clientList)){ 
									foreach ($clientList as $client){
										$clientlist_dropdown[$client->id] = html_entity_decode($client->client_name);
									}
								}
								echo Select2::widget([
									'name' => 'select_box',
									'attribute' => 'select_box',
									'data' => $clientlist_dropdown,
									'options' => ['prompt' => 'Select Client to View Cases','class' => 'form-control','onchange'=>'javascript:loadCasesByClientSelect(this.value);','id'=>'nolabel-2'],
									'pluginOptions' => [
									  'allowClear' => true
									]
								]);
							
							?>
							
						</div>
						<div id="clientbasecaseslist" class='left-dropdown-list'></div>
					</li>
				</ul>	
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right"></div>
		</div>
	</fieldset>
</div>
