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

if(!empty($processTrackData)){
	/*START: Media Section in Track Project */
	if(!empty($processTrackData['media'])){
		echo $this->render('media_section', [
			'task_id'=>$task_id,
    			'case_id'=>$case_id,
    			'team_id'=>$team_id,
    			'type'=>$type,
    			'models'=>$models,
    			'taskunit_id'=>$taskunit_id,
    			'servietask_id'=>$servietask_id,
    			'stlocaccess'=>$stlocaccess,
    			'processTrackData'=>$processTrackData,
    			'teamId'=>$teamId,
    			'team_loc'=>$team_loc,
    			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
                        'cnt_instruction_evidence'=>$cnt_instruction_evidence
			]);
	?>
	
<?php }else{
	$isteamlocaccess = "yes";
	if(!empty($stlocaccess) && $roleId != 0){
		if(isset($stlocaccess[$servietask_id]))
		{
			if(($teamId!=1 && !in_array($team_loc,$stlocaccess[$servietask_id]))){
				$isteamlocaccess = "no";
			}
		}
		else
		{
			$isteamlocaccess = "no";
		}
	}
}
/*END : Media Section in Track Project */
?>
<div class="table-responsive"><table class="table table-striped table-hover" cellspacing="0"><tbody>   	
<?php
/*START: Task Instruction Section in Track Project */
//echo "<pre>",print_r($processTrackData),"</pre>";
//if(!empty($processTrackData['task_instructions'])){
    
	echo $this->render('instruction_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'models'=>$models,
			'taskunit_id'=>$taskunit_id,
			'servietask_id'=>$servietask_id,
			'stlocaccess'=>$stlocaccess,
			'processTrackData'=>$processTrackData,
			'teamId'=>$teamId,
			'team_loc'=>$team_loc,
			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
	]);
//}
/*END: Task Instruction in Track Project */
/*START: Todo Section in Track Project*/
//if(isset($processTrackData['tododata']) && !empty($processTrackData['tododata'])){
    
	echo $this->render('todo_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'models'=>$models,
			'taskunit_id'=>$taskunit_id,
			'servietask_id'=>$servietask_id,
			'stlocaccess'=>$stlocaccess,
			'processTrackData'=>$processTrackData,
			'teamId'=>$teamId,
			'team_loc'=>$team_loc,
			'isteamlocaccess'=>$isteamlocaccess,
			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
	]); 
//}
/*END  : Todo Section in Track Project*/
/*START: Data Statistics Section in Track Project*/
//if(isset($processTrackData['tasksUnitDatas']) && (!empty($processTrackData['tasksUnitDatas']) || !empty($processTrackData['attachments']))){
    
	echo $this->render('unit_data_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'models'=>$models,
			'taskunit_id'=>$taskunit_id,
			'servietask_id'=>$servietask_id,
			'stlocaccess'=>$stlocaccess,
			'processTrackData'=>$processTrackData,
			'teamId'=>$teamId,
			'team_loc'=>$team_loc,
			'isteamlocaccess'=>$isteamlocaccess,
			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
			'team_name'=>$team_name,
	]);
//}
/*END  : Data Statistics in Track Project*/
/*START: Data Statistics Section in Track Project*/
//if(isset($processTrackData['billing']) && !empty($processTrackData['billing'])){
    
	echo $this->render('billing_section', [
			'task_id'=>$task_id,
			'case_id'=>$case_id,
			'team_id'=>$team_id,
			'type'=>$type,
			'models'=>$models,
			'taskunit_id'=>$taskunit_id,
			'servietask_id'=>$servietask_id,
			'stlocaccess'=>$stlocaccess,
			'processTrackData'=>$processTrackData,
			'teamId'=>$teamId,
			'team_loc'=>$team_loc,
			'isteamlocaccess'=>$isteamlocaccess,
			'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
			'team_name'=>$team_name,
	]);
//}
/*END  : Data Statistics in Track Project*/

?>
</tbody>
</table>
</div>
<?php }
?>
