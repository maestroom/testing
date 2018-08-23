<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
$case_project_permission  = 0;
if((new User)->checkAccess(4.01)){
	$case_project_permission = 1;
}
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
       <tr>
	      <th id="first_empty<?php echo $client_id; ?>" width="4%">&nbsp;<span class="screenreader">Client/Case</span></th>
	      <th id="case<?php echo $client_id; ?>" width="22%" class="text-left"><a href="javascript:void(0);" title="Case" class="tag-header-black">Case</a></th>
	      <th id="active_projects<?php echo $client_id; ?>" width="12%"><a href="javascript:void(0);" title="Active Projects" class="tag-header-black">Active Projects</a></th>
	      <th id="active_todos<?php echo $client_id; ?>" width="12%"><a href="javascript:void(0);" title="Active ToDos" class="tag-header-black">Active ToDos</a></th>
	      <th id="unassigned_projects<?php echo $client_id; ?>" width="12%"><a href="javascript:void(0);" title="UnAssigned Projects" class="tag-header-black">UnAssigned Projects</a></th>
	      <th id="unread_comments<?php echo $client_id; ?>" width="18%"><a href="javascript:void(0);" title="Unread Project Comments" class="tag-header-black">Unread Project Comments</a></th>
            <th id="unread_case_comments<?php echo $client_id; ?>" width="18%"><a href="javascript:void(0);" title="Unread Case Comments" class="tag-header-black">Unread Case Comments</a></th>
	    </tr>
      </thead>
      <tbody>
	  <?php
//	  echo "<pre>";print_r($client_case_data);die;
          if (!empty($client_case_data)) {
	    $i=0;
            foreach ($client_case_data as $key => $value) {
            ?>
	    <tr>
	      <td headers="first_empty<?php echo $client_id; ?>" width="4%" align="center">
		  <?php
                        $chart_icon = '<em class="fa fa-pie-chart" title="View Chart Information"></em>';
                        if($firstload==$client_id && $i==0)
                        	$chart_icon = '<em class="fa fa-pie-chart" title="View Chart Information" style="color:red"></em>';
                        $chart_icon .= "<span class='screenreader'>".$value['case_name']."</span>";
                        echo Html::a($chart_icon, "javascript:void(0);", ["title" => "View Chart Information", "onclick" => "updateChart($key,this,'case','','',$case_project_permission);", 'class' => 'changechart casenames caseid_'.$key, 'id' => $key]);
                        ?>
	      </td>
	      <td headers="case<?php echo $client_id; ?>" width="22%"><?php echo $value['case_name'];?></td>
	      <td headers="active_projects<?php echo $client_id; ?>" width="12%" align="center"><?php echo $value['active_projects'];?></td>
	      <td headers="active_todos<?php echo $client_id; ?>" width="12%" align="center"><?php echo $value['active_todos'];?></td>
	      <td headers="unassigned_projects<?php echo $client_id; ?>" width="12%" align="center"><?php echo $value['unassigned_projects'];?></td>
	      <td headers="unread_comments<?php echo $client_id; ?>" width="18%" align="center"><?php echo $value['unread_comments'];?></td>
		  <td headers="unread_comments<?php echo $client_id; ?>" width="18%" align="center"><?php echo $value['unread_case_comments'];?></td>
	    </tr>
	 <?php   
	    }
	   } else {
            echo "<tr><td colspan='7' headers=''>No Records found...</td></tr>";
        } ?>
      </tbody>
    </table>
</div>
