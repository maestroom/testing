<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\Options;
use app\models\EvidenceCustodians;
use app\models\Servicetask;

$taskdatetime = explode(" ",$model->task_date_time);
$task_date = $taskdatetime[0];
$task_time = $taskdatetime[1].' '.$taskdatetime[2];
?>
 <div class="instruction_box sml dialog-box">
    <div class="instruction_title" title="Project Details:<?php echo ' V'.$model->instruct_version;?>">Project Details:<?php echo ' V'.$model->instruct_version;?></div>
	<div class="col-sm-12">
	<div class="col-sm-6">
			<?php if($task_data->clientCase->client->client_name!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label title="Client">Client</label></div>
			<div class="col-sm-6"><?php echo $task_data->clientCase->client->client_name;?></div>
			</div>
			<?php } ?>
			<?php if($task_data->clientCase->case_name!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label title="Case">Case</label></div>
			<div class="col-sm-6  word-wrap"><?php echo $task_data->clientCase->case_name;?></div>
			</div>
			<?php } ?>
			<?php  ?>
			<div class="row">
			<div class="col-sm-6"><label title="Case Manager">Case Manager</label></div>
			<div class="col-sm-6"><?php if($task_data->clientCase->case_manager!=''){ echo $task_data->clientCase->case_manager; }else { echo " - "; }?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label title="Sales Representative">Sales Representative</label></div>
			<div class="col-sm-6"><?php if($task_data->clientCase->salesRepo->usr_first_name!='' || $task_data->clientCase->salesRepo->usr_lastname!=''){ echo $task_data->clientCase->salesRepo->usr_first_name." ".$task_data->clientCase->salesRepo->usr_lastname; } else { echo " - "; }?></div>
			</div>
			<?php if($task_data->clientCase->internal_ref_no!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label title="Internal Reference No">Internal Reference No</label></div>
			<div class="col-sm-6"><?php echo $task_data->clientCase->internal_ref_no;?></div>
			</div>
			<?php } ?>
			<div class="row">
			<div class="col-sm-6"><label title="Project Submitted">Project Submitted</label></div>
					<?php 
					$time  = (new Options)->ConvertOneTzToAnotherTz($task_data->created,'UTC',$_SESSION['usrTZ']);?>
			<div class="col-sm-6"><?php echo $time;?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label title="Project Submitted By">Project Submitted By</label></div>
			<div class="col-sm-6"><?php echo  $task_data->createdUser->usr_first_name." ".$task_data->createdUser->usr_lastname;?></div>
			</div>
			<?php if($task_data->id!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label title="Project #">Project #</label></div>
			<div class="col-sm-6"><?php echo $task_data->id;?></div>
			</div>
			<?php } ?>

</div>
	<div class="col-sm-6">

			<?php //if($task_data->taskInstruct->project_name!=''){ ?>
			<div class="row">
				<div class="col-sm-6"><label title="Project Name">Project Name</label></div>
				<div class="col-sm-6"><?php if($task_data->taskInstruct->project_name!=''){echo $task_data->taskInstruct->project_name;}else { echo " - ";}?></div>
			</div>
			<?php //} ?>
			<?php //if($task_data->taskInstruct->requestor!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label title="Project Requester">Project Requester</label></div>
			<div class="col-sm-6"><?php if($task_data->taskInstruct->requestor!=''){echo $task_data->taskInstruct->requestor;}else { echo " - ";}?></div>
			</div>
			<?php //} ?>
			<div class="row">
			<div class="col-sm-6"><label title="Project Request Type">Project Request Type</label></div>
			<div class="col-sm-6"><?php if($task_data->taskInstruct->task_projectreqtype != '') { echo $project_request_type[$task_data->taskInstruct->task_projectreqtype]; } else { echo " - "; }?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label title="Project Due Date">Project Due Date</label></div>
			<div class="col-sm-6">
				<?php 
					//echo (new Options)->ConvertOneTzToAnotherTz($task_data->taskInstruct->task_duedate." ".$task_data->taskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"MDY");
					echo $task_date;
				?>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label title="Project Time Due">Project Time Due</label></div>
			<div class="col-sm-6">
				<?php 
					//echo (new Options)->ConvertOneTzToAnotherTz($task_data->taskInstruct->task_duedate." ".$task_data->taskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"time");
					echo $task_time;
				?>
			</div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label title="Project Priority">Project Priority</label></div>
			<div class="col-sm-6"><?php echo $task_data->taskInstruct->taskPriority->priority;?></div>
			</div>
			<?php 
			$prod_id = $task_data->taskInstruct->taskInstructEvidences[0]->prod_id;
			
			if(!empty($prod_id)) {
			foreach($task_data->taskInstruct->taskInstructEvidences as $data => $value){
				if(isset($value->evidenceProduction->prod_date) && $value->evidenceProduction->prod_date!="0000-00-00")
					$product_date[$value->evidenceProduction->prod_date] = date("m/d/Y",strtotime($value->evidenceProduction->prod_date));
				else	 
					$product_date[$value->evidenceProduction->prod_date] = "00/00/0000";

				$product_party[$value->evidenceProduction->prod_party] = $value->evidenceProduction->prod_party;
			}	
			?>
			<div class="row">
			<div class="col-sm-6"><label title="Production Date">Production Date</label></div>
			<div class="col-sm-6"><?php echo implode(',',$product_date);?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label title="Producing Party">Producing Party</label></div>
			<div class="col-sm-6"><?php echo implode(',',$product_party);?></div>
			</div>
			<?php } ?>
		  </div>
	</div>
	
	<div class="mycontainer">
	    
<?php	
foreach($task_instructions_data as $key => $val) 
{
	if($val['sort_order'] == 0){ ?>
	<div class="instruction_title" title="Project Media">Project Media</div>
	<div class="myheader"><a href="javascript:void(0);"><?= $val['teamservice_id'] ?> Media & <?= $val['servicetask_id'] ?> Contents</a></div>

<div class="content">
<?php
if(!empty($processTrackData[$key])){
	/*START: Media Section in Track Project */
	if(!empty($processTrackData[$key]['media'])){
		echo $this->render('media_section', [
				'task_id'=>$task_id,
    			'case_id'=>$case_id,
    			'team_id'=>$team_id,
    			'type'=>$type,
    			'processTrackData'=>$processTrackData[$key]['media'],
			]);
	?>
	
<?php }else{
	$isteamlocaccess = "yes";
	if(!empty($stlocaccess)){
		if(($teamId!=1 && !in_array($team_loc,$stlocaccess[$servicetask_id]))){
			$isteamlocaccess = "no";
		}
	}
      }
/*END : Media Section in Track Project */
} else {?>
    <div>No Media Found</div>
    <?php } ?>
</div>
<?php }?>
<?php }?>
<div class="instruction_title" title="Project Workflow">Project Workflow</div>
<?php	
foreach($task_instructions_data as $key => $val) 
{
    
    if($val['sort_order'] == 0){ ?>
	
<?php	}else{ ?>
	<div class="myheader"><a href="javascript:void(0);" title="<?= $val['service_name'] ?> - <?= $val['service_task'] ?>"><?= $val['service_name'] ?> - <?= $val['service_task'] ?></a></div>
<?php	}?>
<div class="content">
<?php
/*START: Task Instruction Section in Track Project */
if(!empty($processTrackData[$key]['task_instructions'])){
	echo $this->render('instruction_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'instruct_id'=>$instruct_id,
			'processTrackData'=>$processTrackData[$key]['task_instructions'],
			'old_instruction_id'=>$old_instruction_id,
			'changeFBIds'=>$changeFBIds,
			'changeServiceAttchmentIds'=>$changeServiceAttchmentIds,
			'model'=>$model
	]);
}
/*END: Task Instruction in Track Project */
?>
</div>
<?php }?>
</div>

 </div>
 
 <!--<div class="foot-title"><?php //echo $settings_data[1]['fieldtext']; ?></div>-->
<script>
$(function() {
    $(".myheader a").click(function () {
		    $header = $(this).parent();
		    $content = $header.next();
		    $content.slideToggle(500, function () {
			$header.text(function () {
			  //  change text based on condition
			  //return $content.is(":visible") ? "Collapse" : "Expand";
			});
		    });	
		});

		/**
		 * Header span
		 */
		$('.myheader').on('click',function(){
			if($(this).hasClass('myheader-selected-tab')){
				$(this).removeClass('myheader-selected-tab');
			}else{
				$(this).addClass('myheader-selected-tab');
			}	
		});
});
</script>
<noscript></noscript>
