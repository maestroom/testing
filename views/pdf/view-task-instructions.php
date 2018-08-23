<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\TaskInstruct;
use app\models\Options;
use app\models\EvidenceCustodians;
use app\models\Servicetask;

if(isset($duedate) && $duedate!="") {
$taskdata["task_date_time"]=$duedate;
}
$taskdatetime = explode(" ",$taskdata["task_date_time"]);
$task_date = $taskdatetime[0];
$task_time = $taskdatetime[1].' '.$taskdatetime[2];
?>

<div style="color:#333; float:left; margin:0px; padding:0px 0px 10px; width:100%; word-break:break-all;"><?php echo $settings_data_header->fieldtext; ?></div>

<div style="float:left; width:100%;">
  <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px; font-family:Arial;">Project Details :<?php echo ' V'.$taskdata["instruct_version"];?></div>
  <div style="font-family:Arial; float:left; width:100%; padding:5px 0px;">
    <div style="width:50%; float:left;">
      <?php if($taskdata["client_name"]!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Client</strong></div>
        <div style="width:45%; float:left;"><?php echo $taskdata["client_name"]; ?></div>
      </div>
      <?php } ?>
      <?php if($taskdata["case_name"]!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Case</strong></div>
        <div style="width:45%; float:left;word-wrap:break-word !important"><?php echo $taskdata["case_name"];?></div>
      </div>
      <?php } ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Case Manager</strong></div>
        <div style="width:45%; float:left;"><?php if($taskdata["case_manager"]!=''){ echo $taskdata["case_manager"]; } else { echo "-"; }?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Sales Representative</strong></div>
        <div style="width:45%; float:left;"><?php if($taskdata['salserepofn']!='' || $taskdata['salserepoln']!=''){ echo $taskdata['salserepofn']." ".$taskdata['salserepoln']; } else { echo " - "; }?></div>
      </div>
      <?php if($taskdata["internal_ref_no"]!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Internal Reference No</strong></div>
        <div style="width:45%; float:left;"><?php echo $taskdata["internal_ref_no"];?></div>
      </div>
      <?php } ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project Submitted</strong></div>
        <?php 
					$time  = (new Options)->ConvertOneTzToAnotherTz($taskdata['submitted_date'],'UTC',$_SESSION['usrTZ']);?>
        <div style="width:45%; float:left;"><?php echo $time;?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project Submitted By</strong></div>
        <div style="width:45%; float:left;"><?php echo $taskdata["taskcreate_fn"]." ".$taskdata["taskcreate_ln"];?></div>
      </div>
      <?php if($taskdata["id"]!=''){ ?>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project #</strong></div>
        <div style="width:45%; float:left;"><?php echo $taskdata["id"];?></div>
      </div>
      <?php } ?>
    </div>
    <div style="width:50%;float:left;">
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%;float:left; padding-left:10px;"><strong>Project Name</strong></div>
        <div style="width:45%;float:left;"><?php if($taskdata["project_name"]!=''){ echo $taskdata["project_name"]; } else { echo "-"; }?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%;float:left; padding-left:10px;"><strong>Project Requester</strong></div>
        <div style="width:45%;float:left;"><?php if($taskdata["requestor"]!=''){ echo $taskdata["requestor"]; } else { echo "-"; }?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project Request Type</strong></div>
        <div style="width:45%; float:left;">
          <?php if($taskdata["task_projectreqtype"] != '') {echo $project_request_type[$taskdata["task_projectreqtype"]];}else { echo " - ";};?>
        </div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project Due Date</strong></div>
        <div style="width:45%; float:left;"><?php echo $task_date?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project Time Due</strong></div>
        <div style="width:45%; float:left;"><?php echo $task_time?></div>
      </div>
      <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
        <div style="width:45%; float:left; padding-left:10px;"><strong>Project Priority</strong></div>
        <div style="width:45%; float:left;"><?php echo $taskdata["priority"];?></div>
      </div>
      <?php
		  $taskInstruct = TaskInstruct::find()->where('task_id = '.$taskdata['id'].' AND isactive = 1')->one();
		  $prod_id = $taskInstruct->taskInstructEvidences[0]->prod_id;
			
			if(!empty($prod_id)) {
			foreach($taskInstruct->taskInstructEvidences as $data => $value){
				 $product_date[$value->evidenceProduction->prod_date] = date("m/d/Y",strtotime($value->evidenceProduction->prod_date));
				 $product_party[$value->evidenceProduction->prod_party] = $value->evidenceProduction->prod_party;
			} ?>
			  <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
				<div style="width:45%; float:left; padding-left:10px;"><strong>Production Date</strong></div>
				<div style="width:45%; float:left;"><?php echo implode(",",$product_date);?></div>
			  </div>
			  <div style="width:100%; float:left; font-size:10px; padding:3px 0px;">
				<div style="width:45%; float:left; padding-left:10px;"><strong>Producing Party</strong></div>
				<div style="width:45%; float:left;"><?php echo implode(",",$product_party);?></div>
			  </div>
		<?php } ?>	  
    
    </div>
  </div>
  <div style="float:left; width:100%;">
    <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;">Project Media</div>
    <?php	
foreach($task_instructions_data as $key => $val) 
{
	if($val['sort_order'] == 0){ ?>
    <div style="background:#e9e7e8; padding:7px 10px; font-size:11px; color:#333; margin:0px 0px 5px; font-family:Arial;"><a href="javascript:void(0);" style="color:#333; text-decoration:none;">
      <?= count($processTrackData[$key]['media']) ?>
      Media &
      <?= count($processTrackData[$key]['media_content']) ?>
      Contents</a></div>
    <?php	}else{ ?>
    <?php	}?>
    
      <?php
if(!empty($processTrackData[$key])){
	/*START: Media Section in Track Project */
	if(!empty($processTrackData[$key]['media'])){
		echo $this->render('media_section', [
				'task_id'=>$task_id,
    			'case_id'=>$case_id,
    			'team_id'=>$team_id,
    			'type'=>$type,
    			'processTrackData'=>$processTrackData[$key],
			]);
	?>
      <?php }else{
	$isteamlocaccess = "yes";
	if(!empty($stlocaccess)){
		if(!isset($stlocaccess[$servicetask_id]))
				$stlocaccess[$servicetask_id] = array();
			
		if(($teamId!=1 && !in_array($team_loc,$stlocaccess[$servicetask_id]))){
			$isteamlocaccess = "no";
		}
	}
      }
}
}      

?>
  </div>
</div>

<div style="float:left; width:100%;">
  <div style="background:#c52d2e; padding:7px 10px; font-size:12px; font-weight:bold; color:#FFF; margin:0px 0px 5px; font-family:Arial;">Project Workflow</div>
  <?php
foreach($task_instructions_data as $key => $val) 
{
	if($val['sort_order'] == 0){ ?>
  <?php	}else{ ?>
  <div style="background:#e9e7e8; padding:7px 10px; font-size:11px; color:#333; margin:0px 0px 5px; font-family:Arial;"><span style="color:#333; text-decoration:none;">
    <?= $val['service_name'] ?>
    -
    <?= $val['service_task'] ?>
    </span></div>
  <?php	}?>
  <?php
if(!empty($processTrackData[$key])){
if(!empty($processTrackData[$key]['task_instructions'])){
	echo $this->render('instruction_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'old_instruction_id'=>$old_instruction_id,
			'changeFBIds'=>$changeFBIds,
			'processTrackData'=>$processTrackData[$key],
      'model'=>$model
	]);
}
/*END: Task Instruction in Track Project */

}?>
  <?php }?>
</div>

<div style="color:#333; float:left; margin:0px; padding:10px 0px 0px; width:100%; word-break:break-all;"><?php echo $settings_data_footer->fieldtext; ?></div>

