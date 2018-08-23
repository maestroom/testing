<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = 'Update Client';
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<fieldset class="two-cols-fieldset">
	<div id="client-tabs">
		<ul>
			<li><a href="#edit-client" title="Edit Client" data-name="Client">Edit Client</a></li>
			<li><a href="#add-client-contacts" title="Contacts" data-name="ClientContacts">Contacts</a></li>
			<li><a href="#exclude-services" title="Exclude Services">Exclude Services</a></li>
			<li><a href="#assigned-user" title="Assigned Users" data-name="">Assigned Users</a></li>
		</ul>
		<div id="edit-client">
			<div id='clientform_div'>
				<?= $this->render('_form',['model'=>$model, 'industryList'=>$industryList, 'countryList'=>$countryList,'model_field_length' => $model_field_length]) ?>
			</div>
		</div>
		<div id="add-client-contacts"></div>
		<div id="exclude-services"></div>
		<div id="assigned-user"></div>
	</div>
</fieldset>
<script>
$(function() {
    $( "#client-tabs" ).tabs({
      beforeActivate: function (event, ui) {
		var chk_status = checkformstatus(event); // check form status
		if(chk_status == true) {
			var form_id = $('#active-form-name').val(); // get the active form name
			$("#"+form_id+" #is_change_form").val('0'); 
			$("#"+form_id+" #is_change_form_main").val('0'); 
			if(ui.newPanel.attr('id') == 'add-client-contacts') {
				loadClientContactList();
			}
			if(ui.newPanel.attr('id') == 'assigned-user') {
				loadAssignedUserList();
			}
			if(ui.newPanel.attr('id') == 'exclude-services'){
				loadExcludedClientServiceList();
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
