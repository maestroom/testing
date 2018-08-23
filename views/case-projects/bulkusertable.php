<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */

$this->title = 'Bulk User Table';
$this->params['breadcrumbs'][] = ['label' => 'Case Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if($client_name != ''){ ?>
	<div class="row">
		<div class="col-md-12">		
		<h6>
			<strong>			
				<?php echo $client_name; 
				if($case_name != ''){ echo ' - '.$case_name;} ?>
			</strong>			
		</h6>
		</div>
	</div>
<?php } ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
	<input type="hidden" name="caseId" value="<?php echo $caseId; ?>"/>
	<input type="hidden" name="checkboxVal" value="<?php echo $taskId; ?>" />
	<input type="hidden" name="dropdownVal" value="<?php echo $dropdownVal; ?>" />
	<?php if($dropdownVal==1){ ?>
		<thead>
		<tr>
			<th width="40%" align="left" title="Task Name">Task Name</th>
			<th width="25%" align="left" title="# Tasks"># Tasks</th>
			<th width="35%" align="left" title="Assign To">Assign To</th>
		</tr>
		</thead>
		<?php if(!empty($TaskArr)){ ?>
		<tbody>
			<tr>
				<?php foreach($TaskArr as $key => $tasks){ ?>
					<tr>
			   			<td><?php echo $key; ?></td>
			   			<td><?php echo $tasks['count']; ?></td> 
			   			<input type="hidden" name="servicetasks[<?php echo $tasks['servicetask_id'];  ?>][tasklist]" value="<?php echo $tasks['task_list']; ?>">
                		<input type="hidden" name="servicetasks[<?php echo $tasks['servicetask_id'];  ?>][sampletask]" value="0">
                		<td align="left">
                		 	<?php 
			                   echo Select2::widget([
				                    'model' => $model,
				               	    'attribute' => "servicetasks[{$tasks['servicetask_id']}][assigntouser]",
				               	    'data' => ArrayHelper::map($assignUsersArr,'id','fullname'),
								    'options' => ['prompt' => 'Select User', 'title' => 'Select User', 'class' => 'form-control taskdropdown'],
				                    /*'pluginOptions' => [
				                      'allowClear' => true
				                    ]*/
				                ]);
			                ?>
		                 </td>
		             </tr>
				<?php } ?>
			</tr>
		</tbody>
		<?php } else { ?>
			<?php echo "<tr><td colspan='3' style='text-align:center'>No Records found.</td></tr>"; ?>
		<?php } ?>	  
	<?php } else if($dropdownVal==2){ ?>  
  		<thead>
		<tr>
			<th width="40%" align="left" title="Task Name">Task Name</th>
			<th width="25%" align="left" title="# Tasks"># Tasks</th>
			<th width="35%" align="left" title="Assign To">Assign To</th>
		</tr>
		</thead>
  	   <?php if(!empty($TaskArr)){ ?>
	   <tbody>
	   <tr>
	   		<?php foreach($TaskArr as $key => $tasks){ ?>
	   			<?php foreach($tasks as $key1 => $tasks1){ ?>
	   			<tr>
		   			<td><?php echo $key1;?></td>
		   			<td><?php echo $tasks1['count'];?></td>
		   			<input type="hidden" name="servicetasks[<?php echo $tasks1['servicetask_id'];  ?>][tasklist]" value="<?php echo $tasks1['task_list']; ?>">
                	<input type="hidden" name="servicetasks[<?php echo $tasks1['servicetask_id'];  ?>][sampletask]" value="0">
		   			<td width="25%" align="left">
                		<?php 
		                  echo Select2::widget([
			                    'model' => $model,
			               	    'attribute' => "servicetasks[{$tasks1['servicetask_id']}][assigntouser]",
			                    'data' => ArrayHelper::map($assignUsersArr,'id','fullname'),
			                    'options' => ['prompt' => 'Select User', 'title' => 'Select User', 'class' => 'form-control taskdropdown'],
			                    /*'pluginOptions' => [
			                      'allowClear' => true
			                    ]*/
			              ]);
			            ?>
	                 </td>
	             </tr>
	   			<?php } 
				} ?>
	   		</tr>
			</tbody>	   		
	   <?php } else {
	  	 	echo "<tr><td colspan='3' style='text-align:center'>No Records found.</td></tr>";
	 	} 
	} else if($dropdownVal==3){ ?>
    	<thead>
		<tr>
			<th width="40%" align="left" title="Task Name">Task Name</th>
			<th width="25%" align="left" title="# Tasks"># Tasks</th>
			<th width="35%" align="left" title="Assign To">Assign To</th>
		</tr>
		</thead>
    	<?php if(!empty($TaskArr)){ ?>
    		<tbody>
			<?php foreach($TaskArr as $key => $tasks){ ?>
    	  		<?php foreach($tasks as $key1 => $tasks1){ ?>
	   				
					<tr>
	   					<td><?php echo $key1;?></td>
		   				<td><?php echo $tasks1['count'];?></td>
		   				<input type="hidden" name="servicetasks[<?php echo $tasks1['servicetask_id'];  ?>][tasklist]" value="<?php echo $tasks1['task_list']; ?>">
                		<input type="hidden" name="servicetasks[<?php echo $tasks1['servicetask_id'];  ?>][sampletask]" value="0">
		   				<td width="25%" align="left">
		                	<span style="width:200px !important;" class="taskspandrop">
		                    	<?php 
				                  echo Select2::widget([
					                    'model' => $model,
					               	    'attribute' => "servicetasks[{$tasks1['servicetask_id']}][assigntouser]",
					                    'data' => ArrayHelper::map($assignUsersArr,'id','fullname'),
					                    'options' => ['prompt' => 'Select User', 'title' => 'Select User', 'class' => 'form-control taskdropdown'],
					                    /*'pluginOptions' => [
					                      'allowClear' => true
					                    ]*/
					              ]);
					            ?>
		                    </span>
	                 	</td>
	   				</tr>	
	   			<?php } ?>
				</tbody>
    	  <?php } 
		} else {
			echo "<tr><td colspan='3' style='text-align:center'>No Records found.</td></tr>";
		}
	}  else { ?>
		<thead>
		<tr>
	        <th width="30%" align="left" title="Task Name">Task Name</th>
		    <th width="10%" align="left" title="# Tasks"># Tasks</th>
		    <th width="10%" align="left" title="% Sampled">% Sampled</th>
		    <th width="15%" align="left" title="# Sampled Tasks"># Sampled Tasks</th>
		    <th width="35%" align="left" title="Assign To">Assign To</th>
	    </tr>
		</thead>
	    <tbody>
		<?php if(!empty($TaskArr)){ ?>
			<tr>
				<?php foreach($TaskArr as $key => $tasks){ 

				
					?>
					<tr>
			   			<td><?php echo $key;?></td>
			   			<td><?php echo $tasks['count'];?></td>
			   			<input type="hidden" name="servicetasks[<?php echo $tasks['servicetask_id'];  ?>][tasklist]" value="<?php echo $tasks['task_list']; ?>">
                		<input type="hidden" name="servicetasks[<?php echo $tasks['servicetask_id'];  ?>][sampletask]" value="0">
			   			<td><?php echo ($tasks['sampling']==0)?"":$tasks['sampling'].'%'; ?></td>
			   			<td><?php echo ($tasks['sampling_task']==0)?"":$tasks['sampling_task']; ?></td>
			   			<td>
	                		<?php 
			                   echo Select2::widget([
				                    'model' => $model,
				               	    'attribute' => "servicetasks[{$tasks['servicetask_id']}][assigntouser]",
				                    'data' => ArrayHelper::map($assignUsersArr,'id','fullname'),
								    'options' => ['prompt' => 'Select User', 'title' => 'Select User', 'class' => 'form-control taskdropdown'],
				                    /*'pluginOptions' => [
				                      'allowClear' => true
				                    ]*/
				                ]);
					         ?>
		                 </td>
		             </tr>
				<?php } ?>
			</tr>
		<?php } else { ?>
			<?php echo "<tr><td colspan='5' style='text-align:center'>No Records found.</td></tr>"; ?>
		<?php } ?>
		</tbody>
	<?php } ?>
</table>
<script>
	/* change event */
	$('select').on('change', function(){
		$('#add-bulkassignproject-form #is_change_form').val('1');
		$('#add-bulkassignproject-form #is_change_form_main').val('1');
	});
	$('document').ready(function(){
		$('#active_form_name').val('add-bulkassignproject-form'); // check
	});
	/* End */
</script>
    

