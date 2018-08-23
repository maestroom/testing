<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\grid\GridView;
use app\models\Options;
use app\models\Tasks;
use app\models\TasksUnits;
?>

<div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px; font-family:Arial;">Project % Complete</div>

<div style="float:left; width:100%;">
 <div style="float:left; width:50%; padding:10px 0px 0px 0px;"><img alt="" src="data:image/svg+xml;base64,<?= $pdfimage; ?>"></div>
 <div style="float:right; width:47%;">
  <div style="float:left; width:100%; font-family:Arial; font-size:10px; padding:7px 0px;" id="casespend_table">
  	
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">Project #</strong></div>
	 <div style="float:left; width:50%;"><?= $taskmodel->id; ?></div>
	</div>
	
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">Project Name</strong></div>
	 <div style="float:left; width:50%;"><?= $taskinstruct->project_name; ?></div>
	</div>
	
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">Submitted Date</strong></div>
	 <div style="float:left; width:50%;"><?= (new Options)->ConvertOneTzToAnotherTz($taskmodel->created,"UTC",$_SESSION["usrTZ"]); ?></div>
	</div>
	
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">% Complete</strong></div>
	 <div style="float:left; width:50%;"><?= (new Tasks)->getTaskPercentageCompleted($taskmodel->id,"case",0,0,0,'NUM'); ?></div>
	</div>
	
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">Project Due Date</strong></div>
	 <div style="float:left; width:50%;"><?= (new Options)->ConvertOneTzToAnotherTz($taskinstruct->task_duedate." ".$taskinstruct->task_timedue, 'UTC', $_SESSION['usrTZ']); ?></div>
	</div>
	
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">Project Completed Date</strong></div>
	 <div style="float:left; width:50%;"><?= ($taskmodel->task_complete_date !='0000-00-00 00:00:00' && $taskmodel->task_status==4)?(new Options)->ConvertOneTzToAnotherTz($taskmodel->task_complete_date, 'UTC', $_SESSION['usrTZ']):''; ?></div>
	</div>
		
	<?php
		$is_pastdue=(new Tasks)->ispastduetask($taskmodel->id);
		if($is_pastdue) {
			$diff=(new TasksUnits)->dateDiff(time(),strtotime($taskinstruct->task_duedate." ".$taskinstruct->task_timedue));
		$letters = array('days','hours','minutes');
		$fruit   = array('d', 'h','m');
		$output  = str_replace($letters, $fruit, $diff);
	?>
	<div style="float:left; width:100%; padding:3px 0px;">
	 <div style="float:left; width:50%;"><strong style="padding:0px 10px;">Project Past Due</strong></div>
	 <div style="float:left; width:50%;">
	 <?php 
		echo $output;
	 ?>
	 </div>
	</div>
	<?php } ?>
	</div>

 </div>
</div>

<div style="float:left; width:100%;">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" id="casespend_table">
	<thead>
		<tr>
			<th align="left" style="font-size:10px; font-family:Arial; padding:10px; border:none 0px; background:#e9e7e8;" title="Status"><strong>Status</strong></th>
			<th align="left" style="font-size:10px; font-family:Arial; padding:10px; border:none 0px; background:#e9e7e8;" title="Task Name"><strong>Task Name</strong></th>
			<th align="left" style="font-size:10px; font-family:Arial; padding:10px; border:none 0px; background:#e9e7e8;" title="Task Started"><strong>Task Started</strong></th>
			<th align="left" style="font-size:10px; font-family:Arial; padding:10px; border:none 0px; background:#e9e7e8;" title="Task Completed"><strong>Task Completed</strong></th>
			<?php if($est_hours > 0) {?>
			<th align="left" style="font-size:10px; font-family:Arial; padding:10px; border:none 0px; background:#e9e7e8;" title="Est"><strong>Est</strong></th>
			<?php }?>
			<th align="left" style="font-size:10px; font-family:Arial; padding:10px; border:none 0px; background:#e9e7e8;" title="Actual"><strong>Actual</strong></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(!empty($myfinal_arr))
		{
			foreach ($myfinal_arr as $data)
			{ 
			?>
			<tr>
				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?php echo $data['status'];?></td>
				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?php echo $data['task_name'];?></td>
				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?php echo $data['started']?></td>
				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?php echo $data['completed']?></td>
				<?php if($est_hours > 0) {?>
				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;"><?php echo $data['est']?></td>
				<?php }?>
				<td style="font-size:10px; font-family:Arial; padding:3px 10px; border:none 0px;" class="<?php if(($est_hours > 0) && $data['actualHr'] > $data['estHr']) echo " past_due_est";?>"><?php echo $data['actual']?></td>
			</tr>	
			<?php 
			}
		}?>
	</tbody>
 </table>
</div>