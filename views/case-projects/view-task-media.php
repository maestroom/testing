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
 <div class="instruction_box sml dialog-box">
	<div class="col-sm-12">
<?php	
foreach($task_instructions_data as $key => $val) 
{
	if($val['sort_order'] == 0){ ?>
<?php
if(!empty($processTrackData[$key])) {
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
	}else{?>
		<div>No Media Found</div>
	<?php } 
/*END : Media Section in Track Project */
} else {?>
	<div>No Media Found</div>
<?php } ?>
<?php }?>
<?php }?>
</div>
</div>