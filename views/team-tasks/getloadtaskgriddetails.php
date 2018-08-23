<?php
use app\models\Options;
use app\models\TasksUnitsTodos;
use app\models\search\TasksUnitsSearch;
$todoinfo = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id','tbl_tasks_units_todos.complete','tbl_tasks_units_todos.todo','tbl_tasks_units_todos.todo_cat_id','tbl_tasks_units_todos.assigned',"concat(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as assigned_user"])->join('left join','tbl_user','tbl_user.id=tbl_tasks_units_todos.assigned')->where(['tasks_unit_id'=>$id])->orderBy('tbl_tasks_units_todos.modified desc')->all();
?>
<div class="table-responsive">
	<table class="table table-striped table-hover load-task-grid-details">
		<thead>
			<tr>
				<th class="text-left task-detail-todo-width"><a href="javascript:void(0);" title="ToDo" class="tag-header-black tag-header-cursor-default">ToDo</a></th>
				<th class="text-left task-detail-follow-category-width"><a href="javascript:void(0);" title="ToDo Follow-up Category" class="tag-header-black tag-header-cursor-default">ToDo Follow-up Category</a></th>
				<th class="text-left task-detail-assigned-width"><a href="javascript:void(0);" title="ToDo Assigned To" class="tag-header-black tag-header-cursor-default">ToDo Assigned To</a></th>
				<th class="text-left task-detail-complete-width"><a href="javascript:void(0);" title="ToDo Complete" class="tag-header-black tag-header-cursor-default">ToDo Complete</a></th>
			</tr>
		</thead>
		<tbody>
				<?php
				if(!empty($todoinfo)){ 
				 foreach($todoinfo as $todo){ ?>
					 <tr>
					<td headers="team_task_detail_todo"><?php echo $todo->todo; ?><?php 
                	$attachment="";
	                if (!empty($todo->todoattachments)) {
		                foreach ($todo->todoattachments as  $at) {
		                    if ($attachment == "")
		                        $attachment ='<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                    else
		                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                }
	               }echo "&nbsp;".$attachment?></td>
					<td headers="team_task_detail_todo_followup_category"><?php if(isset($todo->todoCats->todo_cat)) echo $todo->todoCats->todo_cat; 
					if (isset($todo->todoCats->todo_desc)) echo " - " . $todo->todoCats->todo_desc; ?></td>
					<td headers="team_task_detail_todo_assigned_to"><?php if ($todo->assigned != 0) {
						//echo $todo->assignedUser->usr_first_name . " " . $todo->assignedUser->usr_lastname . " " . (new Options)->ConvertOneTzToAnotherTz($todo->modified, 'UTC', $_SESSION['usrTZ']);
						echo $todo->assignedUser->usr_first_name . " " . $todo->assignedUser->usr_lastname;
					}  ?></td>
					<td headers="team_task_detail_todo_complete"><?php if ($todo->complete == 1){ ?> <em title="Completed" class="fa fa-check"></em> <?php } ?></td>
				</tr>
				<?php  }
				}else{ ?>
					<tr><td headers="" colspan="4">No records are available.</td></tr>
				<?php } ?>	 
		</tbody>
	</table>
