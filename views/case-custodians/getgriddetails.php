<?php
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Tasks;

?>
<div class="table-responsive"> 
<?php if(empty($clientMediaNum) && empty($taskintructs)) {
	echo "No associated Media or Projects are available.";
}else{?>
   <table class="table table-striped table-hover">
	<tbody>
    	<tr>
		<th id="associated_media" class="text-left" width="15%" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Associated Media">Associated Media</a></th>
			<td scope="row" headers="associated_media" class="text-left"><?php 
				$link_evids=array();
				foreach ($clientMediaNum as $modelevid){
					if((new User)->checkAccess(3)){
						$link_evids[]=Html::a($modelevid->evid_num_id,"javascript:void(0);",["data-pjax"=>"0",'onclick'=>'window.location.href="index.php?r=media/index&id=' . $modelevid->evid_num_id . '";','title'=>'Media #'.$modelevid->evid_num_id]);
					}else{
						$link_evids[]=$modelevid->evid_num_id;
					}
				} echo implode(", ",$link_evids);?></td>
		</tr>
	    <tr>
            <th scope="row" id="associated_projects" align="left" width="15%" scope="col"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Associated Projects">Associated Projects</a></th>
            <td headers="associated_projects" align="left"><?php
            $project_link=array();
            foreach ($taskintructs as $project){
            	
            	if((new User)->checkAccess(4.01)){
            		$task_info=Tasks::findOne($project['id']);
            		if($task_info->task_cancel){
            			$project_link[]=Html::a($project['id'],null,["data-pjax"=>"0",'title'=>'Project #'.$project['id'],"href"=>Url::toRoute(['case-projects/load-canceled-projects', 'case_id' => $case_id, 'task_id' => $project['id']])]);
            		}
            		else if($task_info->task_closed){
            			$project_link[]=Html::a($project['id'],null,["data-pjax"=>"0",'title'=>'Project #'.$project['id'],"href"=>Url::toRoute(['case-projects/load-closed-projects', 'case_id' => $case_id, 'task_id' => $project['id']])]);
            		}else{
            			$project_link[]=Html::a($project['id'],null,["data-pjax"=>"0",'title'=>'Project #'.$project['id'],"href"=>Url::toRoute(['case-projects/index', 'case_id' => $case_id, 'task_id' => $project['id']])]);
            		}
            	}else{
            		$project_link[]=$project['id'];
            	}
            } echo implode(", ",$project_link);?></td>
         </tr>
         <?php if(!empty($cust_form)){
			 	foreach($cust_form as $column){?>
				<tr>
					<th scope="row" align="left" width="15%" id="<?=$column;?>"><a href="javascript:void(0);" title="<?=$model->getAttributeLabel($column);?>" class="tag-header-black"><?=$model->getAttributeLabel($column);?></a></th>
					<td align="left" headers="<?=$column?>"><?php echo $model->$column?></td>
				</tr>
				<?php }
		 }?>
	</tbody>
   </table>
<?php }?>
</div>
