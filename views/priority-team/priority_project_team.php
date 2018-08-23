<div id="addpriorityproject" title="Add New Project Priority Team" style="display:none;overflow:hidden;">
	<?= $this->render('_form_project_priority_team', [
			'model' => $model
		]); 
    ?>
</div>
<div id="add-todo-item" title="Select Team Priority">
<input type="hidden" id="priority_add" name="priority_add" value="0" />
<div class="create-form">
<?=  $this->renderAjax('team_locs', [
		'myteams'=> $myteams,
		'priority_id' => $priority_id,
		'last_id' => $last_id
	]); 
?>
</div>		
</div>
<script>
	
/* Add Priority Team */
function AddPriorityTeam() 
{
	var $custodianDialogContainer = $('#addpriorityproject');
	if($('#addpriorityproject')){
		$('#addpriorityproject').remove();
	}
	if ($custodianDialogContainer.hasClass('ui-dialog-content')) {
		$custodianDialogContainer.dialog('destroy').remove();
	}
    $custodianDialogContainer.dialog({
		autoOpen: false,
		resizable: false,
		height: 456,
		width: '50em',
		modal: true,
		create: function(event, ui) { 
		// if($('.ui-dialog-titlebar-close').html() != '<span class="ui-button-icon-primary ui-icon"></span>') {	
			$('#addpriorityproject').last().prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
			$('#addpriorityproject').last().prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
			$('#addpriorityproject').last().prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
		// }
		},
		buttons: {
			'Cancel': {
				text: 'Cancel',
				"title": 'Cancel',
				"id" : "cancel-btn",
				"class": 'btn btn-primary',
				'aria-label': "Cancel New Project Priority Team",
					click:  function (event) {
						$custodianDialogContainer.dialog("close");
						/*$.each($('.ui-dialog'), function (i, e) {
							$custodianDialogContainer.dialog("close");
						});*/
					}
				},
			"Add":  {
				text: 'Add',
				"title": 'Add',
				"class": 'btn btn-primary',
				"id" : "add-btn",
				'aria-label': "Add New Project Priority Team",
				click: function () {
					
					/* Project Priority Team */
					var url = baseUrl + "priority-team/project-priority-team";
					$.ajax({
						type: "post",
						url: url,
						async: true,
						data: $('#PriorityTeam').serialize(),
						success: function(response){
							if(response=='Ok') {
								clearall(); 
								$custodianDialogContainer.dialog("close"); 
								AddPriority();
							}
						}
					});
				}
			},
		},
	    close: function(event){
		//	$custodianDialogContainer.dialog('destroy').remove();
		} 
	});
	$custodianDialogContainer.dialog("open");
}	

/* clearall */
function clearall()
{
	$('#priorityteam-tasks_priority_name').val('');
	$('#priorityteam-priority_desc').val('');
}
</script>
<noscript></noscript>
