	<?php if(!empty($projectWorkflow)){ ?>
<li id="first_row"><div class="pull-left"><input type="checkbox" aria-label="Select All"  id="chkall" name="checkall_workflow" class="left checkall_workflow"><label title="Select All" class="pull-left servicetask-info-label" for="chkall"><span class="sr-only">Select All</span></label></div><div class="pull-left"><span class="pull-left servicetask-info-label">Select All</span></div> <span class="est_time_header">SLA/Est Time</span> 
	<div class="icon-set pull-right">
		<a  href="javascript:void(0);" onclick="AddManEstimatedTime();" title="Bulk Add Estimated Time">
                    <span class="fa fa-clock-o text-primary"></span>
                    <span class="sr-only">Bulk Add Estimated Time</span>
		</a>
		<a href="javascript:void(0);" onclick="removealltasks();">
                    <span class="fa fa-close text-primary" title="Bulk Delete"></span>
                    <span class="sr-only">Remove All Tasks</span>
                </a>
	</div>
	</li>
	<?php foreach ($projectWorkflow as $service_task){ ?>
	<li id="<?=$service_task['servicetask_id']?>" class="li_<?=$service_task['servicetask_id']?> clear" rel="loadprev" data-project="<?=$project_id?>">
	<div class="pull-left">
	<input type="checkbox" class="left aaa" name="Service_tasks[]" id="workflow_servicetasks_<?=$service_task['servicetask_id']?>" value="<?=$service_task['servicetask_id']?>" aria-label="Select All">
        <label for="workflow_servicetasks_<?=$service_task['servicetask_id']?>">&nbsp;<span class="sr-only">Work    Flow Service Task</span></label>
	</div>
	<input type="hidden" value="<?=$service_task['team_loc']?>" id="stl_<?=$service_task['servicetask_id']?>" class="sloc_<?=$service_task['servicetask_id']?>" name="ServiceteamLoc1[<?=$service_task['servicetask_id']?>][]">
	<label for="Service_tasks_<?=$service_task['servicetask_id']?>" class="pull-left servicetask-info-label" title="ServiceTask">
		<span class="sername_div"><?=$service_task['service_name']?><?php if($service_task['team_loc'] > 0){ echo " - ".$service_task['team_location_name']; }?><?php echo " - ".$service_task['service_task']?></span>
	</label>
	<input type="hidden" value="0.00" id="hdn_service_logic_<?=$service_task['servicetask_id']?>" name="hdn_service_logic[<?=$service_task['servicetask_id']?>]">
        <input type="text" readonly="readonly" value="" id="est_time_<?=$service_task['servicetask_id']?>" name="Est_times[<?=$service_task['servicetask_id']?>]" class="right est_time grey est-time-read-only" aria-label="Estimated Time">
	<div class="icon-set pull-right">
		<a href="javascript:void(0);" class="icon-set handel_sort" aria-label="Move" title="Move">
			<span class="fa fa-arrows text-primary"></span>
		</a>
		<a href="javascript:void(0);" onclick="AddServiceManEstimatedTime(<?=$service_task['servicetask_id']?>);" aria-label="Add Service Estimated Time">
			<span title="Add Estimated Time" class="fa fa-clock-o text-primary"></span></a>
		<a href="javascript:void(0)" onclick="removestask(<?=$service_task['servicetask_id']?>);" aria-label="Remove Tasks">
			<span title="Delete" class="fa fa-close text-primary"></span>
		</a>
	</div>
</li>
<?php }
}?>
