<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User; ?>
<div class="table-responsive">
    <table class="table table-striped table-hover tablesorter" id="myteamTable<?=$id?>">
      <thead>
       <tr>
	      <th width="10%" class="text-center" <?php if($id == 2){ ?> title="ToDo Status" id="todo_status_<?=$id?>" <?php } else { ?>title="Task Status" id="task_status_<?=$id?>" <?php  }?>>
	      <?php if($id == 2){ ?>
				<a href="javascript:void(0);" title="<?php echo "ToDo Status"; ?>" class="tag-header-black"><?php echo "ToDo Status"; ?></a>
		  <?php	} else { ?>
				<a href="javascript:void(0);" title="<?php echo "Task Status"; ?>" class="tag-header-black"><?php echo "Task Status"; ?></a>
		  <?php } ?>
		  </th>
	      <th width="13%" class="text-center" id="task_project_<?=$id?>"><a href="javascript:void(0);" title="Task / Project #" class="tag-header-black">Task / Project #</a></th>
	      <th width="22%" class="text-left" id="team_location_<?=$id?>"><a href="javascript:void(0);" title="Team - Location" class="tag-header-black">Team - Location</a></th>
	      <?php if($id == 2){ ?>
			  <th width="22%" class="text-left" id="todo_item_<?=$id?>"><a href="javascript:void(0);" title="ToDo Item" class="tag-header-black">ToDo Item</a></th>
			  <th width="17%" class="text-left" id="todo_followup_category_<?=$id?>"><a href="javascript:void(0);" title="ToDo Follow-up Category" class="tag-header-black">ToDo Follow-up Category</a></th>
			  <th width="13%" class="text-left" id="todo_assigned_date_<?=$id?>"><a href="javascript:void(0);" title="ToDo Assigned Date" class="tag-header-black">ToDo Assigned Date</a></th>
		  <?php } else { ?>	  
			  <th width="22%" class="text-left" id="workflow_task_<?=$id?>"><a href="javascript:void(0);" title="Workflow Task" class="tag-header-black">Workflow Task</a></th>
			  <th width="17%" class="text-left" id="project_priority_<?=$id?>"><a href="javascript:void(0);" title="Project Priority" class="tag-header-black">Project Priority</a></th>
			  <th width="13%" class="text-left" id="project_due_date_<?=$id?>"><a href="javascript:void(0);" title="Project Due Date" class="tag-header-black">Project Due Date</a></th>
	      <?php } ?>
	    </tr>
      </thead>
      <tbody>
	 <?php
          if (!empty($myteamtaskdata)) {
            foreach ($myteamtaskdata as $key => $value) {
            ?>
	    <tr>
			<?php if($id != 2) {?>
				 <td width="10%" class="text-center" headers="todo_status_<?=$id?>"><?php echo $value['todo_icon']; ?></td>
			<?php } else {  ?>
				<td width="10%" class="text-center" headers="task_status_<?=$id?>"><?php echo $value['todo_icon']; ?></td>
			 <?php }  ?> 
			 <td width="13%" class="text-center" headers="followup_category_<?=$id?>"> 
				<?php $url="index.php?r=track/index&taskid=".$value['project_id']."&team_id=".$value['team_id']."&team_loc=".$value['team_loc']."&tasks_unit_id=".$value['task_id'];
				if((new User)->checkAccess(5.02)){
				 echo Html::a($value['task_id'], $url, ["style" => "color:#167FAC","title"=>"Task #".$value['task_id']]);
					echo " / ".$value['project_id'];
				}else{
					echo $value['task_id'].' / '.$value['project_id'];
				}
				 ?>
			 </td>
			 <td width="22%" headers="team_location_<?=$id?>"><?php echo $value['team_location']?></td>
			  <?php if($id == 2) {?>
				  <td width="22%" class="text-left" headers="todo_item_<?=$id?>"><?php echo $value['todo_item'];?></td>
				  <td width="17%" class="text-left" headers="todo_followup_category_<?=$id?>"><?php echo $value['followup_category'];?></td>
				  <td width="13%" class="text-left" headers="todo_assigned_date_<?=$id?>"><?php echo $value['todo_assigned'];?></td>
			  <?php } else { ?>
				  <td width="22%" class="text-left" headers="workflow_task_<?=$id?>"><?php echo $value['workflow_task'];?></td>
				  <td width="17%" class="text-left" headers="project_priority_<?=$id?>"><?php echo $value['project_priority'];?></td>
				  <td width="13%" class="text-left" headers="project_due-date_<?=$id?>"><?php echo $value['project_due_date'];?></td>
				  
			 <?php 	  } ?>	  
	    </tr>
	 <?php   
	    }
	   } else {
            echo "<tr><td colspan='6' headers=''>No Records found...</td></tr>";
        } ?>
      </tbody>
    </table>
</div>
<script>
$("#myteamTable<?=$id?>").tablesorter();
</script>
<noscript></noscript>
