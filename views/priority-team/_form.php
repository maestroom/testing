<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\PriorityTeam */
/* @var $form yii\widgets\ActiveForm */
?>
<fieldset class="one-cols-fieldset">
    <?php $form = ActiveForm::begin(['id' => 'PriorityTeamLoc', 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
    <?= IsataskFormFlag::widget(); // change flag ?>
    <div class="create-form">
		<input type="hidden" name="priority_order_change" id="priority_order_change" value="0" />
		<?php if(!isset($priority_details) && empty($priority_details)){ ?>
			<?= $form->field($model, 'team_loc_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $team_location,
					'options' => [ 'id' => 'team-teamloc', 'class' => 'form-control ', 'title' => 'Select Team Location', 'multiple' => true,'nolabel'=>true,'aria-required'=>'true'],
				])->label('Select Team Location(s)'); ?>
		<?php } ?>
		<!-- Add Priority Button -->
		<div class="form-group add-priority" <?php if(!isset($priority_details) && empty($priority_details)){ echo "style='display:none;'"; } else { echo "style='display:block;'"; } ?>>
			<div class="row input-field custom-full-width">
				<div class="col-md-12">
					<?= Html::button('Add Priority', ['title'=> 'Add Priority', 'class' => 'btn btn-primary', 'onclick'=>'AddPriority();']) ?>
				</div>
			</div>
		</div>
		<div class="form-group table-pricingrates-tiered">
			<div class="row input-field custom-full-width">
				<div class="col-md-12">
					<table id="associated_team_loc" class="table table-striped">
						<thead> 
							<th width="28%"><a href="javascript:void(0);" title="Team Location Priority" class="tag-header-black">Team Location Priority</a></th>
							<th width="52%"><a href="javascript:void(0);" title="Description" class="tag-header-black">Description</a></th>
							<th width="20%"><a href="javascript:void(0);" title="Action" class="tag-header-black">Action</a></th>
						</thead>
						<!-- Priority Details -->
						<?php if(!isset($priority_details) && empty($priority_details)){  ?>
							<tbody></tbody>
						<?php } else { ?>
							<tbody>
								<?php foreach($priority_details as $key => $value){ ?>
									<tr id="tb_<?= $value['id']; ?>">
										<input type="hidden" id="priority_id" name="priority_id[]" value="<?= $value['priority_team_id'] ?>" />
										<input type="hidden" id="priority_name" name="priority_name[]" value="<?= $value['tasks_priority_name'] ?>" />
										<input type="hidden" id="priority_desc" name="priority_desc[]" value="<?= $value['priority_desc'] ?>" />
										<td id="<?= $value['id']; ?>"><?= $value['tasks_priority_name']; ?></td>
										<td><?= $value['priority_desc']; ?></td>
										<td><a class='icon-set handel_sort' aria-label='Move' href='javascript:void(0);'><em class='fa fa-arrows text-primary' title='Move'></em></a><a class='icon-set' href='javascript:EditPriorityTeam("<?= $value['priority_team_id'] ?>");' title='Edit' aria-label="edit Priority team"><em class='fa fa-pencil text-primary'></em></a><a class='icon-set' href='javascript:DeletePriorityTeam("<?= $value['priority_team_id'] ?>","<?= $value['tasks_priority_name'] ?>");' title='Remove' aria-label='Remove'><em class='fa fa-close text-primary'></em></a></td>
									</tr>
								<?php } ?>
							</tbody>
						<?php } ?>
						<!-- End Priority Details -->
					</table>
				</div>
			</div>
		</div>
		
	</div>
	<?php ActiveForm::end(); ?>
</fieldset>
<div class="button-set text-right">
	<?= Html::button('Cancel', ['title'=>'Cancel', 'class' => 'btn btn-primary', 'onclick'=>'SelectManageDropdown("ProjectPriorityTeam");']) ?>
	<?= Html::button((!isset($priority_details) && empty($priority_details)) ? 'Add' : 'Update', ['title' => (!isset($priority_details) && empty($priority_details)) ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>(!isset($priority_details) && empty($priority_details)) ? 'submitData(this);' : 'submitEditData(this);']) ?>
</div>
<script>
	
function DeleteTeamLoc(id){
	$('#'+id).remove();
}

function submitData() 
{
	var record = $('#associated_team_loc tr').length; // table
	var team_loc = $('#team-teamloc').val(); // get the team location
	if(record > 1 && team_loc != null){
		ManageDropdownSubmitAjaxForm("PriorityTeamLoc", this, "ProjectPriorityTeam");
	} else {
		alert("Please select 1+ Team - Location to perform this action.");
	}
}

function submitEditData(){
	var record = $('#associated_team_loc tr').length; // table
	if(record > 1){
		ManageDropdownSubmitAjaxForm("PriorityTeamLoc", this, "ProjectPriorityTeam");
	} else {
		alert("Please select 1+ Team - Location to perform this action.");
	}
}

$('document').ready(function(){ $("#active_form_name").val('PriorityTeamLoc'); });
$('#team-teamloc').change(function(){
	$('.add-priority').css('display','block');
});
/* tbody class */

$(function(){
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};
	$("table tbody").sortable({
		handle:'.handel_sort',
		helper: fixHelper,
		change: function(){
			$('#PriorityTeamLoc #priority_order_change').val('1');
		},
		stop: function(e,ui) { }
	}).disableSelection();
});

/* AddPriority */
function AddPriority()
{
	
	//var location_id = $('#team_loc').val();
	if($('#addpriorityproject')){
		$('#addpriorityproject').remove();
	}
	$.ajax({
		type: 'post',
		url:baseUrl+'priority-team/associated-team-loc',
		//data: {location_id:location_id},
		beforeSend:function (data) {showLoader();},
		success:function(response){
		hideLoader();
		if($('#availabl-service-tasks')) {
			$('#availabl-service-tasks').remove();
		}
		if($('#availabl-service-tasks').length == 0) {
			$('#admin_right').append('<div class="dialog" id="availabl-service-tasks" title="Select Team Priority"></div>');
		}
		$('#availabl-service-tasks').html('').html(response);		
				$('#availabl-service-tasks').dialog({ 
				modal: true,
				width:'60em',
				height:456,
				create: function(event, ui) { 
					$('#availabl-service-tasks').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                    $('#availabl-service-tasks').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
                    $('#availabl-service-tasks').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close: function(event){
						$('#availabl-service-tasks').dialog('destroy').remove();
				},
				buttons: [
					  { 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('close');
						  } 
					  },
					  { 
						  text: "Add", 
						  "class": 'btn btn-primary',
						  "title": 'Add',
						  click: function () { 
							if($('.teamlocation').is(':checked')) { 
								$('.teamlocation:checked').each(function()
								{
									var priority_name = $(this).attr('data-priority-type');
									var priority_desc = $(this).attr('data-prioritydesc');
									var priority_id = $(this).attr('data-id');
									if(priority_desc==undefined || priority_desc=="") priority_desc = "";
									
									// associated team loc
									if($('#associated_team_loc').find('#'+priority_id).length == 0) 
									{
										var fixHelper = function(e, ui) {
											ui.children().each(function() {
												$(this).width($(this).width());
											});
											return ui;
										};
										$("table tbody").sortable({
											handle:'.handel_sort',
											helper: fixHelper,
											stop: function(e,ui) { 
												// alert("TEST"); return false;
											}
										}).disableSelection();
										
										$('#associated_team_loc').append("<tr id='tb_"+priority_id+"'><td id="+priority_id+"><input type='hidden' name='priority_id[]' id='priority_id' value='"+priority_id+"' /><input type='hidden' name='priority_name[]' id='priority_name' value='"+priority_name+"' />"+priority_name+"</td><td><input type='hidden' name='priority_desc[]' id='priority_desc' value='"+priority_desc+"' />"+priority_desc+"</td><td><a class='icon-set handel_sort' aria-label='Move' href='javascript:void(0);'><em class='fa fa-arrows text-primary' title='Move'></em></a><a class='icon-set' href='javascript:EditPriorityTeam(\""+priority_id+"\");' title='Edit' aria-label='edit Priority team'><em class='fa fa-pencil text-primary'></em></a><a class='icon-set' href='javascript:DeletePriorityTeamTeamp(\""+priority_id+"\");' title='Remove' aria-label='Remove'><em class='fa fa-close text-primary'></em></a></td></tr>");
										
										/** Flag changed **/
										$('#PriorityTeamLoc #is_change_form').val('1'); 
										$('#PriorityTeamLoc #is_change_form_main').val('1');
									}
								});
								
								$(this).dialog('close');
							} else {
								alert("Please Select Team Priority"); return false;
							}
						  }
					 }
				],
			});	
		}
	});
}

function DeletePriorityTeamTeamp(priority_id) 
{
	$('#tb_'+priority_id).remove();
}

/* Delete Priority Team */
function DeletePriorityTeam(priority_id, priority_name)
{
	if(confirm("Are you sure you want to delete: "+priority_name+"?")){
		$.ajax({
			type: 'post',
			url:baseUrl+'priority-team/delete-team-priority',
			data: {priority_id:priority_id},
			beforeSend:function (data) { showLoader(); },
			success:function(response)
			{
				if(response=='Used') {
					alert("Priority Team already used in task"); 
					hideLoader();
					return false;
				}
				if(response=='Ok') {
					hideLoader();
					$('#tb_'+priority_id).remove();
				}
			}
		}); 
	}
}

/** Edit Team Priority **/
function EditPriorityTeam(priority_id)
{
	 $.ajax({
		type: 'post',
		url:baseUrl+'priority-team/edit-team-priority',
		data: { priority_id : priority_id },
		beforeSend:function (data) { showLoader(); },
		success:function(response){
			hideLoader();
			if($('#edit-team-priorityteam')) {
				$('#edit-team-priorityteam').remove();
			}
			if($('#edit-team-priorityteam').length == 0) {
				$('#admin_right').append('<div class="dialog" id="edit-team-priorityteam" title="Edit Project Priority Team"></div>');
			}
			
			$('#edit-team-priorityteam').html('').html(response);		
				$('#edit-team-priorityteam').dialog({ 
					modal: true,
					width:'60em',
					height:456,
					create: function(event, ui) { 
						$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
					},
					close: function(event){
						$('#edit-team-priorityteam').dialog('destroy').remove();
					},
					buttons: [
					  { 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('close');
						  } 
					  },
					  { 
						  text: "Update", 
						  "class": 'btn btn-primary',
						  "title": 'Update',
						  click: function () 
						  { 
							  	var priority_name = $(this).find('input[id="priorityteam-tasks_priority_name"]').val();
								var priority_desc = $(this).find('textarea[id="priorityteam-priority_desc"]').val();
								//$(this+"#priorityteam-priority_desc").val();
								var prior = $(this).find('input[id="priority_id"]').val();
								//$(this+"#priority_id").val();
								//alert(priority_name+" desc ="+priority_desc+" id ="+prior);	
								//return false;
								/* Project Priority Team */
								var url = baseUrl + "priority-team/project-priority-team";
								$.ajax({
									type: "post",
									url: url,
									async: true,
									data: $(this).find('form[id="PriorityTeam"]').serialize(),
									success: function(response) {
										if(response == 'Ok') {
											new_row = "<tr id='tb_"+priority_id+"'><td id="+priority_id+"><input type='hidden' name='priority_id[]' id='priority_id' value='"+priority_id+"' /><input type='hidden' name='priority_name[]' id='priority_name' value='"+priority_name+"' />"+priority_name+"</td><td><input type='hidden' name='priority_desc[]' id='priority_desc' value='"+priority_desc+"' />"+priority_desc+"</td><td><a class='icon-set handel_sort' aria-label='Move' href='javascript:void(0);'><em class='fa fa-arrows text-primary' title='Move'></em></a><a class='icon-set' href='javascript:EditPriorityTeam(\""+priority_id+"\");' title='Edit' aria-label='edit Priority team'><em class='fa fa-pencil text-primary'></em></a><a class='icon-set' href='javascript:DeletePriorityTeam(\""+priority_id+"\");' title='Remove' aria-label='Remove'><em class='fa fa-close text-primary'></em></a></td></tr>";
											$('#tb_'+priority_id).replaceWith(new_row); // new row
											$('#edit-team-priorityteam').dialog('close');
										}else{
											return false;
										}
									}
								});
								/* End */
								
								
						  }
					 }
				],
			});	
		}
	});
}
</script>
<noscript></noscript>
