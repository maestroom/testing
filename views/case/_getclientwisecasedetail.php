<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
       <tr>
	      <th>&nbsp;</th>
	      <th class="text-left">Case</th>
	      <th>Active Projects</th>
	      <th>Active Todos</th>
	      <th>Unread Comments</th>
	      <th>UnAssigned Projects</th>
	    </tr>
      </thead>
      <tbody>
	  <?php
	 // echo "<pre>";print_r($client_case_data);die;
          if (!empty($client_case_data)) {
	    $i=0;
            foreach ($client_case_data as $key => $value) {
            ?>
	    <tr>
	      <td width="5%" align="center">
		  <?php
                        $chart_icon = '<em title="View Chart Information" class="fa fa-pie-chart"></em>';
                        if($firstload==$client_id && $i==0)
                        	$chart_icon = '<em title="View Chart Information" class="fa fa-pie-chart" style="color:red"></em>';
                        
                        echo Html::a($chart_icon, "javascript:void(0);", ["onclick" => "updateChart($key,this,'case');", 'class' => 'changechart', 'id' => $key]);
                        ?>
	      </td>
	      <td width="27%"><?php echo Html::a($value['case_name'], "javascript:void(0);");?></td>
	      <td width="17%" align="center"><?php echo $value['active_projects'];?></td>
	      <td width="17%" align="center"><?php echo $value['active_todos'];?></td>
	      <td width="17%" align="center"><?php echo $value['unread_comments'];?></td>
	      <td width="17%" align="center"><?php echo $value['unassigned_projects'];?></td>
	    </tr>
	 <?php   
	    }
	   } else {
            echo "<tr><td colspan='6'>No Records found...</td></tr>";
        } ?>
      </tbody>
    </table>
</div>
