<?php  use app\models\Options;  ?>

	<div class="title">
				Project # <?php echo $activity_report[0]['id'];?> - Submitted On - <?php echo (new Options)->ConvertOneTzToAnotherTz($activity_report[0]['created'],"UTC",$_SESSION["usrTZ"],"MDYHIS").' By '.$activity_report[0]['project_by'];?>
		</div>
<div class="">&nbsp;</div><div class="">&nbsp;</div>
<?php
	
	$service_name = array();
	$service_task = array();
	
	$total_time_spent=0; 
	if($activity_report[0]['task_complete_date']!='0000-00-00 00:00:00'){
		$total_time_spent =dateDiff($activity_report[0]['created'],$activity_report[0]['task_complete_date']); 
	}
	else if($activity_report[0]['task_status']==1 || $activity_report[0]['task_status']==3){
		$total_time_spent = dateDiff($activity_report[0]['created'],date('Y-m-d H:i:s'));
	}
	else if($activity_report[0]['task_cancel'] == 1){
		$total_time_spent =dateDiff($activity_report[0]['created'],$activity_report[0]['modified']);
	}
	
	foreach($activity_report as $report){ //echo "<pre>";print_r($activity_report); exit; 
		
		
		?><table class="table">
	
		<?php if(in_array($report['servicetask_id'],$service_name)){ 
					if(!empty($report['transaction_type'])){ ?>
					<tr>
						<td><?php $type = $report['transaction_type']; echo $parameters[$type];  ?></td>
						<td><?php $datet =0; $datet = (new Options)->ConvertOneTzToAnotherTz($report['transaction_date'],"UTC",$_SESSION["usrTZ"]); echo $datet;  ?></td>
						<td><?php echo $report['transaction_by'];  ?></td>
						<td><?php if($report['transaction_type'] == 8 || $report['transaction_type'] == 7){ echo $report['transaction_to']; }  ?></td>
						<td><?php $dur_exp=(explode(" ",$report['duration']));
													  	$time_spent_activity = 0;
									if(isset($dur_exp[0])){
										$time_spent_activity+=($dur_exp[0]*24*60);
									}
									if(isset($dur_exp[2])){
										$time_spent_activity+=($dur_exp[2]*60);
									}
									if(isset($dur_exp[4])){
										$time_spent_activity+=$dur_exp[4];
									}
								$tahours=($time_spent_activity/60);
								$tahours=intval($tahours);
							  	$tamins=($time_spent_activity%60);

							  $datetimevar = $tahours."h ".$tamins."m"; 
							  
			echo $datetimevar;  ?></td>
					</tr>
					<?php } ?>
				
		<?php } else{ $service_name[$report['servicetask_id']] = $report['servicetask_id']; ?>
		<thead>
					<tr><td colspan="5"><h5><strong><?php echo $report['service_name'].'-'.$report['service_task']; ?></strong></h5></td></tr>
					<tr>
						<td>Transaction Type</td>
						<td>Transaction Date/Time</td>
						<td>Transaction By</td>
						<td>Transaction To</td>
						<td>Activity Time Spent</td>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($report['transaction_type'])){  ?>
					<tr>
						<td><?php $type = $report['transaction_type']; echo $parameters[$type];  ?></td>
						<td><?php $datet =0; $datet = (new Options)->ConvertOneTzToAnotherTz($report['transaction_date'],"UTC",$_SESSION["usrTZ"]); echo $datet;  ?></td>
						<td><?php echo $report['transaction_by'];  ?></td>
						<td><?php if($report['transaction_type'] == 8 || $report['transaction_type'] == 7){ echo $report['transaction_to']; }  ?></td>
						<td><?php $dur_exp=(explode(" ",$report['duration']));
						$time_spent_activity = 0;
									if(isset($dur_exp[0])){
										$time_spent_activity+=($dur_exp[0]*24*60);
									}
									if(isset($dur_exp[2])){
										$time_spent_activity+=($dur_exp[2]*60);
									}
									if(isset($dur_exp[4])){
										$time_spent_activity+=$dur_exp[4];
									}
								$tahours=($time_spent_activity/60);
								$tahours=intval($tahours);
							  	$tamins=($time_spent_activity%60);
							  $datetimevar = $tahours."h ".$tamins."m"; 
							  
			echo $datetimevar;  ?></td>
					</tr>
					
		<?php } }
		
		?>
		</tbody>
	</table>
	<?php } ?>
	<div class="">&nbsp;</div><div class="">&nbsp;</div>
	<div class="title">
				<div class="MultiFile-remove">Project # <?php echo $report['id']; ?> Total Time Spent is - <?php echo $total_time_spent;?></div>
				<div class="float_right"><?php if($activity_report[0]['task_complete_date']!='0000-00-00 00:00:00' && $activity_report[0]['task_status'] == 4) echo "Completed On : ".(new Options)->ConvertOneTzToAnotherTz($activity_report[0]['task_complete_date'],"UTC",$_SESSION["usrTZ"],"MDYHIS");?></div>
				<div class="clear_both"></div>
	</div>
	<div class="">&nbsp;</div><div class="">&nbsp;</div><div class="">&nbsp;</div>

<?php
function dateDiff($start,$end=false)
{
	$return = array();
	 
	try {
		$start = new DateTime($start);
		$end = new DateTime($end);
		$form = $start->diff($end);
	} catch (Exception $e){
		return $e->getMessage();
	}
	 
	$display = array('y'=>'year',
			'm'=>'month',
			'd'=>'day',
			'h'=>'hour',
			'i'=>'minute',
			's'=>'second');
	foreach($display as $key => $value){
		if($form->$key > 0){
			$return[] = $form->$key.' '.($form->$key > 1 ? $value.'s' : $value);
		}
	}
	 
	return implode($return, ', ');
}
?>

