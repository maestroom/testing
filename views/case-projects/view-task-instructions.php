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
 <!--<div class="head-title"><?php //echo $settings_data[0]['fieldtext'];
 //
 ?></div>-->
 <a href="javascript:void(0);"></a>
 <div class="instruction_box sml dialog-box">
     <?php //echo "<pre>";print_r($prev_instruction);print_r($task_data->taskInstruct->task_priority);die('erer');?>
    <div class="instruction_title" title="Project Details:<?php echo ' V'.$taskdata['instruct_version'];?>">Project Details:<?php echo ' V'.$taskdata['instruct_version'];?></div>
	<div class="col-sm-12">
	<div class="col-sm-6">
			<?php if($taskdata['client_name']!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Client">Client</a></label></div>
			<div class="col-sm-6"><?php echo $taskdata['client_name'];?></div>
			</div>
			<?php } ?>
			<?php if($taskdata['case_name']!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Case">Case</a></label></div>
			<div class="col-sm-6 word-wrap"><?php echo $taskdata['case_name'];?></div>
			</div>
			<?php } ?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Case Manager">Case Manager</a></label></div>
			<div class="col-sm-6"><?php if($taskdata['case_manager']!=''){ echo $taskdata['case_manager']; }else{ echo " - "; } ?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Sales Representative">Sales Representative</a></label></div>
			<div class="col-sm-6"><?php if($taskdata['salserepofn']!='' || $taskdata['salserepoln']!=''){ echo $taskdata['salserepofn']." ".$taskdata['salserepoln']; } else { echo " - "; }?> </div>
			</div>
			<?php if($taskdata['internal_ref_no']!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Internal Reference No">Internal Reference No</a></label></div>
			<div class="col-sm-6"><?php echo $taskdata['internal_ref_no'];?></div>
			</div>
			<?php } ?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Submitted">Project Submitted</a></label></div>
					<?php 
					$time  = (new Options)->ConvertOneTzToAnotherTz($taskdata['submitted_date'],'UTC',$_SESSION['usrTZ']);?>
			<div class="col-sm-6"><?php echo $time;?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Submitted By">Project Submitted By</a></label></div>
			<div class="col-sm-6"><?php echo  $taskdata["taskcreate_fn"]." ".$taskdata["taskcreate_ln"];?></div>
			</div>
			<?php if($taskdata['id']!=''){ ?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project #">Project #</a></label></div>
			<div class="col-sm-6"><?php echo $taskdata['id'];?></div>
			</div>
			<?php } ?>

</div>
	<div class="col-sm-6">

			<?php //if($task_data->taskInstruct->project_name!=''){ ?>
                        <?php $cls_project_name=""; if(!empty($prev_instruction) && $prev_instruction['project_name'] != $taskdata["project_name"]){$cls_project_name="bg-warning";}?>
			<div class="row <?php echo $cls_project_name;?>">
					<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Name">Project Name</a></label></div>
					<div class="col-sm-6"><?php if($taskdata["project_name"]!=''){echo $taskdata["project_name"];}else { echo " - ";}?></div>
			</div>
			<?php //} ?>
			<?php //if($task_data->taskInstruct->requestor!=''){ ?>
                        
                        <?php $cls_requestor=""; if(!empty($prev_instruction) && $prev_instruction['requestor'] != $taskdata["requestor"]){$cls_requestor="bg-warning";}?>
			<div class="row <?php echo $cls_requestor;?>">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Requester">Project Requester</a></label></div>
			<div class="col-sm-6"><?php if($taskdata["requestor"]!=''){echo $taskdata["requestor"];}else { echo " - ";}?></div>
			</div>
			<?php //} ?>
                        
            <?php $cls_reqtype=""; if(!empty($prev_instruction) && $prev_instruction['task_projectreqtype'] != $taskdata["task_projectreqtype"]){$cls_reqtype="bg-warning";}?>
			<div class="row <?php echo $cls_reqtype;?>">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Request Type">Project Request Type</a></label></div>
			<div class="col-sm-6"><?php if($taskdata["task_projectreqtype"] != '') { echo $project_request_type[$taskdata["task_projectreqtype"]]; } else { echo " - "; }?></div>
			</div>
                        
            <?php $cls_duedate=""; if(!empty($prev_instruction) && $prev_instruction['task_duedate'] != $taskdata["task_duedate"]){$cls_duedate="bg-warning";}?>
			<div class="row <?php echo $cls_duedate;?>">
				<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Due Date">Project Due Date</a></label></div>
				<div class="col-sm-6">
				<?php 
					//echo (new Options)->ConvertOneTzToAnotherTz($task_data->taskInstruct->task_duedate." ".$task_data->taskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"MDY");
					echo $task_date;
				?>
				</div>
			</div>

            <?php $cls_timedue=""; if(!empty($prev_instruction) && $prev_instruction['task_timedue'] != $taskdata["task_timedue"]){$cls_timedue="bg-warning";}?>
			<div class="row <?php echo $cls_timedue;?>">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Time Due">Project Time Due</a></label></div>
			<div class="col-sm-6">
				<?php 
					//echo (new Options)->ConvertOneTzToAnotherTz($task_data->taskInstruct->task_duedate." ".$task_data->taskInstruct->task_timedue,'UTC',$_SESSION['usrTZ'],"time");
					echo $task_time;
				?>
			</div>
			</div>
                        <?php $cls_priority=""; if(!empty($prev_instruction) && $prev_instruction['task_priority'] != $taskdata["task_priority"]){$cls_priority="bg-warning";}?>
			<div class="row <?php echo $cls_priority;?>">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Project Priority">Project Priority</a></label></div>
			<div class="col-sm-6"><?php echo $taskdata["priority"];?></div>
			</div>
			<?php 
				$taskInstruct = TaskInstruct::find()->where('task_id = '.$taskdata['id'].' AND isactive = 1')->one();
				$prod_id = $taskInstruct->taskInstructEvidences[0]->prod_id;
				if(!empty($prod_id)) {
				foreach($taskInstruct->taskInstructEvidences as $data => $value){
					if(isset($value->evidenceProduction->prod_date) && $value->evidenceProduction->prod_date!="0000-00-00")
					 	$product_date[$value->evidenceProduction->prod_date] = date("m/d/Y",strtotime($value->evidenceProduction->prod_date));
					else	 
						$product_date[$value->evidenceProduction->prod_date] = "00/00/0000";

					 $product_party[$value->evidenceProduction->prod_party] = $value->evidenceProduction->prod_party;
				}	
			?>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Production Date">Production Date</a></label></div>
			<div class="col-sm-6"><?php echo implode(',',$product_date);?></div>
			</div>
			<div class="row">
			<div class="col-sm-6"><label><a href="javascript:void(0);" title="Producing Party">Producing Party</a></label></div>
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
	<div class="instruction_title"><a href="javascript:void(0);" title="Project Media">Project Media</a></div>    
        <div class="myheader"><a href="javascript:void(0);"><?= count($processTrackData[$key]['media']) ?> Media & <?= count($processTrackData[$key]['media_content']) ?> Contents</a></div>

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
    			'processTrackData'=>$processTrackData[$key],
                        'cnt_instruction_evidence'=>$cnt_instruction_evidence
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
			'processTrackData'=>$processTrackData[$key],
			'old_instruction_id'=>$old_instruction_id,
			'changeFBIds'=>$changeFBIds
	]);
}
/*END: Task Instruction in Track Project */
?>
</div>
<?php
} 
?>

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
