<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceProductionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Productions';
$this->params['breadcrumbs'][] = $this->title;
?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
            <thead>
              </thead>
              <tbody>
                   <tr>
                        <th class="text-left project_no" title="Project #">Project #</th>
                        <th title="Document Name" class="doc_name">Document Name</th>
                        <th class="text-left upload_date" title="Uploaded date">Uploaded Date</th>
                        <th class="text-left upload_by" title="Uploaded By">Uploaded By</th>
                   </tr>
              <?php  if(!empty($dataProvider)){
                  foreach($dataProvider  as $data){ ?>    
                <tr>
                    <td align="left"><?php 
                        if($type == 'IN'){$task_id=$data->taskInstructNotes->task_id;}elseif($type == 'T'){$task_id=$data->tasksUnitsTodos->taskUnit->task_id;}elseif($type == 'C'){ $task_id=$data->comments->task_id;}else {/*$task_id=$data->taskInstruct->task_id;*/$task_id=$data->taskInstructServicetask->task_id;} 
                        echo Html::a($task_id,null,['href'=>Url::toRoute(['case-projects/index','case_id'=>$case_id,'task_id'=>$task_id]),'style'=>'color:#167fac;','title'=>'Project #'.$task_id]);
                    ?></td>
                    <td align="left"><?php 
                    $fname=str_ireplace($term,'<span style="color: #8b0000;font-weight:700;"><em>'.$term.'</em></span>',$data->fname);
                    $fname .= '<span class="screenreader">Download Attachment</span>';
                    echo Html::a($fname,null,['href'=>'javascript:void(0);','onclick'=>'downloadattachment('.$data->id.')']); ?></td>
                    <td align="left"><?php echo $data->created;?></td>
                    <td align="left"><?php echo $data->user->usr_first_name.' '.$data->user->usr_lastname;?></td>
                </tr>
              <?php } }else{ ?>
                <tr><td colspan="4">No Document found.</td></tr>
                <?php } ?>
              </tbody>
        </table> 
    </div>
