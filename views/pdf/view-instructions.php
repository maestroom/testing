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
?>
<div style="color:#333; float:left; margin:0px; padding:10px 15px; width:100%; word-break:break-all;"><?php echo $settings_data_header->fieldtext; ?></div>
<div style="float:left; width:100%;">
<div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px; font-family:Arial;">Project Details :<?php echo ' V'.$model->instruct_version;?></div>
<div style="font-family:Arial; float:left; width:100%; padding:5px 10px;">
    <div style="width:50%; float:left;">
      <?php if($task_data->clientCase->client->client_name!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Client</strong></div>
        <div style="width:50%;float:left;"><?php echo $task_data->clientCase->client->client_name;?></div>
      </div>
      <?php } ?>
      <?php if($task_data->clientCase->case_name!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Case</strong></div>
        <div style="width:50%;float:left;"><?php echo $task_data->clientCase->case_name;?></div>
      </div>
      <?php } ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Case Manager</strong></div>
        <div style="width:50%;float:left;"><?php if($task_data->clientCase->case_manager!=''){ echo $task_data->clientCase->case_manager; } else { echo "-"; }?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Sales Representative</strong></div>
        <div style="width:50%;float:left;"><?php if($task_data->clientCase->salesRepo->usr_first_name!='' || $task_data->clientCase->salesRepo->usr_lastname!=''){ echo $task_data->clientCase->salesRepo->usr_first_name." ".$task_data->clientCase->salesRepo->usr_lastname;} else { echo "-"; }?></div>
      </div>
      <?php if($task_data->clientCase->internal_ref_no!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Internal Reference No</strong></div>
        <div style="width:50%;float:left;"><?php echo $task_data->clientCase->internal_ref_no;?></div>
      </div>
      <?php } ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Submitted</strong></div>
        <?php 
					$time  = (new Options)->ConvertOneTzToAnotherTz($task_data->created,'UTC',$_SESSION['usrTZ']);?>
        <div style="width:50%;float:left;"><?php echo $time;?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Submitted By</strong></div>
        <div style="width:50%;float:left;"><?php echo  $task_data->createdUser->usr_first_name." ".$task_data->createdUser->usr_lastname;?></div>
      </div>
      <?php if($task_data->id!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project #</strong></div>
        <div style="width:50%;float:left;"><?php echo $task_data->id;?></div>
      </div>
      <?php } ?>
    </div>
    <div style="width:50%;float:left;">
      <?php  ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Name</strong></div>
        <div style="width:50%;float:left;"><?php if($model->project_name!=''){ echo $model->project_name; } else { echo "-";}?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Requester</strong></div>
        <div style="width:50%;float:left;"><?php if($model->requestor!=''){ echo $model->requestor; } else { echo "-"; }?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%; float:left;"><strong style="padding-left:10px;">Project Request Type</strong></div>
        <div style="width:50%; float:left;">
          <?php if($model->task_projectreqtype != '') {echo $project_request_type[$model->task_projectreqtype];}else { echo " - ";};?>
        </div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Due Date</strong></div>
        <div style="width:50%;float:left;"><?php echo (new Options)->ConvertOneTzToAnotherTz($model->task_duedate." ".$model->task_timedue,'UTC',$_SESSION['usrTZ'],"MDY");?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Time Due</strong></div>
        <div style="width:50%;float:left;"><?php echo (new Options)->ConvertOneTzToAnotherTz($model->task_duedate." ".$model->task_timedue,'UTC',$_SESSION['usrTZ'],"time");?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Project Priority</strong></div>
        <div style="width:50%;float:left;"><?php echo $model->taskPriority->priority;?></div>
      </div>
      <?php //$prod_id = $task_data->taskInstruct->taskInstructEvidences[0]->prod_id;
			$product_date = array();
			$product_party = array();
			if(!empty($model->taskInstructEvidences)) {
			foreach($model->taskInstructEvidences as $data => $value){
				if($value->prod_id != 0){
				 $product_date[$value->evidenceProduction->prod_date] = date("m/d/Y",strtotime($value->evidenceProduction->prod_date));
				 $product_party[$value->evidenceProduction->prod_party] = $value->evidenceProduction->prod_party;
				}
			} ?>
	<?php if(!empty($product_date) || !empty($product_party)){ ?>		
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Production Date</strong></div>
        <div style="width:50%;float:left;"><?php echo implode(",",$product_date);?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:50%;float:left;"><strong style="padding-left:10px;">Producing Party</strong>
        </div>
        <div style="width:50%;float:left;"><?php echo implode(",",$product_party);?></div>
      </div>
      <?php } } ?>
    </div>
  </div>
  <div style="float:left; width:100%;">
    <?php	
foreach($task_instructions_data as $key => $val) 
{
	if($val['sort_order'] == 0){ ?>
    <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;"><a href="javascript:void(0);" style="color:#FFF;">Project Media -
      <?= $val['teamservice_id'] ?>
      Media &
      <?= $val['servicetask_id'] ?>
      Contents</a></div>
    <?php	}else{ ?>
    <div style="background:#e9e7e8; padding:7px 10px; font-size:11px; color:#333; margin:0px 0px 5px; font-family:Arial;"><a href="javascript:void(0);" style="color:#333;">
      <?= $val['service_name'] ?>
      -
      <?= $val['service_task'] ?>
      </a></div>
    <?php	}?>
    <div style="float:left; width:100%; border:solid 5px #167fac; border-top:none 0px; border-radius:0px 0px 2px 2px; padding:10px; margin:-7px 0px 5px 0px; display:block;">
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
	if(!empty($stlocaccess[$servicetask_id])){
		if(($teamId!=1 && !in_array($team_loc,$stlocaccess[$servicetask_id]))){
			$isteamlocaccess = "no";
		}
	}
      }
/*END : Media Section in Track Project */
/*START: Task Instruction Section in Track Project */
if(!empty($processTrackData[$key]['task_instructions'])){
	echo $this->render('instruction_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
      'instruct_id'=>$instruct_id,
			'type'=>$type,
			'old_instruction_id'=>$old_instruction_id,
			'changeFBIds'=>$changeFBIds,
			'processTrackData'=>$processTrackData[$key]['task_instructions'],
      'model'=>$model
	]);
}
/*END: Task Instruction in Track Project */

}?>
    </div>
    <?php }?>
  </div>
</div>
<div style="color:#333; float:left; margin:0px; padding:10px 15px; width:100%; word-break:break-all;"><?php echo $settings_data_footer->fieldtext; ?></div>