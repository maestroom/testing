<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$team_project_permission = 0;
if((new User)->checkAccess(5.01)){
	$team_project_permission = 1;
}
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
       <tr>
	      <th id="first_empty_<?=$team_id; ?>" width="4%">&nbsp;</th>
	      <th id="team_location_<?=$team_id; ?>" width="22%" class="text-left"><a href="javascript:void(0);" title="Team Location" class="tag-header-black">Team Location</a></th>
	      <th id="active_projects_<?=$team_id; ?>" width="12%"><a href="javascript:void(0);" title="Active Projects" class="tag-header-black">Active Projects</a></th>
	      <th id="incomp_tasks_<?=$team_id; ?>" width="12%"><a href="javascript:void(0);" title="Incomp Tasks" class="tag-header-black">Incomp Tasks</a></th>
	      <th id="incomp_todos_<?=$team_id; ?>" width="12%"><a href="javascript:void(0);" title="Incomp ToDos" class="tag-header-black">Incomp ToDos</a></th>
	      <th id="unread_comments_<?=$team_id; ?>" width="18%"><a href="javascript:void(0);" title="Unread Project Comments" class="tag-header-black">Unread Project Comments</a></th>
		  <th id="unread_summary_comments_<?=$team_id; ?>" width="21%"><a href="javascript:void(0);" title="Unread Summary Comments" class="tag-header-black">Unread Summary Comments</a></th>
	    </tr>
      </thead>
      <tbody>
	  <?php
	 	  $i=0;
          if (!empty($team_data)) {
            foreach ($team_data as $key => $value) {
				$team_loc_id = $value['team_loc'];
				if(strip_tags($value['task_count']) == 0 && strip_tags($value['incmpt_task']) == 0 && strip_tags($value['incmpt_todos']) == 0 && strip_tags($value['comment'] == 0)){
					continue;
				}
				$i++;
				 ?>
	    <tr>	
	      <td headers="first_empty" width="4%" align="center">
		  <?php
                        $chart_icon = '<em class="fa fa-pie-chart" title="View Chart Information of team"></em>';
                        if($firstload==$team_id && $i==0)
                        	$chart_icon = '<em class="fa fa-pie-chart" style="color:red"  title="View Chart Information of team"></em>';
                        
                        echo Html::a($chart_icon, "javascript:void(0);", ["title" => "View Chart Information of team, " . $value['team_loc_name'], "onclick" => "updateteamChart($team_id,$team_loc_id,this,'team_loc',$team_project_permission);", 'class' => 'changechart','id'=>$team_loc_id,'data-id'=>'l_'.$team_loc_id]);
                        ?>
	      </td>
	      <td headers="team_location_<?=$team_id; ?>" width="22%"><?php echo $value['team_loc_name'];?></td>
	      <td headers="active_projects_<?=$team_id; ?>" width="12%" align="center"><?php echo $value['task_count'];?></td>
	      <td headers="incomp_tasks_<?=$team_id; ?>" width="12%" align="center"><?php echo $value['incmpt_task'];?></td>
	      <td headers="incomp_todos_<?=$team_id; ?>" width="12%" align="center"><?php echo $value['incmpt_todos'];?></td>
	      <td headers="unread_comments_<?=$team_id; ?>" width="18%" align="center"><?php echo $value['comment'];?></td>
		  <td headers="unread_summary_comments_<?=$team_id; ?>" width="21%" align="center"><?php echo $value['unread_case_comments'];?></td>
	    </tr>
	 <?php   
	    }
	   } else {
            echo "<tr><td colspan='7' headers=''>No Records found...</td></tr>";
        }if($i == 0 && !empty($team_data)){ 
			 echo "<tr><td colspan='7' headers=''>No Records found...</td></tr>"; } ?>
      </tbody>
    </table>
</div>
