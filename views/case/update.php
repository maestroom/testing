<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Case */

$this->title = 'Update Case';
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<fieldset class="two-cols-fieldset">
	<div id="client-case-tabs">
		<ul>
			<li><a href="#edit-case" title="Edit Case">Edit Case</a></li>
			<li><a href="#add-case-contacts" title="Contacts">Contacts</a></li>
			<!--<li><a href="#case-summary" title="Case Summary">Case Summary</a></li>-->
			<li><a href="#exclude-services" title="Exclude Services">Exclude Services</a></li>
			<li><a href="#assigned-case-user" title="Assigned Users">Assigned Users</a></li>
		</ul>
		<div id="edit-case">
			<!-- <div class="sub-heading"><?= Html::encode($this->title) ?></div>  -->
			<?= $this->render('_form',['model'=>$model, 'client_id' => $client_id, 'listCaseType' => $listCaseType, 'listCaseCloseType' => $listCaseCloseType, 'listSalesRepo' => $listSalesRepo, 'actLog_case' => $actLog_case,'model_field_length' => $model_field_length]) ?>
		</div>
		<div id="add-case-contacts"></div>
		<div id="case-summary"></div>
		<div id="exclude-services"></div>
		<div id="assigned-case-user"></div>
	</div>
</fieldset>
<script>
$(function() {
    $( "#client-case-tabs" ).tabs({
      beforeActivate: function (event, ui) {
		  var chk_status = checkformstatus(event,''); // check form edit status 
		  if(chk_status==true) {
				/* before active */
				var form_id = $('#active-form-name').val(); // get active form name
				if(form_id!='' && form_id!=undefined){
					$('#'+form_id+' #is_change_form').val('0');
					$('#'+form_id+' #is_change_form_main').val('0');
					$('#'+form_id).val('');
				} else 
					$('#is_change_form').val('0');	$('#is_change_form_main').val('0');
				
				/* tab */
				if(ui.newPanel.attr('id') == 'add-case-contacts'){
					loadCaseContactList();
				}
				if(ui.newPanel.attr('id') == 'case-summary'){
					loadClientCaseSummary();
				}
				if(ui.newPanel.attr('id') == 'exclude-services'){
					loadExcludedServiceList();
				}
				if(ui.newPanel.attr('id') == 'assigned-case-user'){
					loadAssignedCaseUserList();
				}
		 }
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html("Error loading current tab.");
        });
      }
    });
 });
</script>
<noscript></noscript>
