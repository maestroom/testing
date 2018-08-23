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
			]);
	?>
	
<?php }else{
	$isteamlocaccess = "yes";
	if(!empty($stlocaccess)){
		if(($teamId!=1 && !in_array($team_loc,$stlocaccess[$servietask_id]))){
			$isteamlocaccess = "no";
		}
	}
}
/*END : Media Section in Track Project */
/*START: Task Instruction Section in Track Project */
if(!empty($processTrackData['task_instructions'])){
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
}
/*END: Task Instruction in Track Project */

}
?>
