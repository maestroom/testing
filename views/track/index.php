<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\TaskInstructServicetask;
use app\models\Tasks;
use app\models\User;
use app\models\Options;
use app\models\TaskInstructNotes;
use kartik\widgets\Select2;
kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);
kartik\widgets\WidgetAsset::register($this);
$modelTaksInstruction = new TaskInstructServicetask();
$resuest = Yii::$app->request->get();
$controller=Yii::$app->controller->id;

if(isset($resuest['case_id']) && $controller == 'track')
{
	$header_label = "<a href='javascript:void(0);' class='tag-header-black' title='Project Workflow'>Project Workflow </a>".Html::a('# '.Yii::$app->request->get('taskid'), "javascript:viewInstruction(".Yii::$app->request->get('taskid').");", array("class" => "dialog","style"=>"color:#167fac","title"=>"Project #".Yii::$app->request->get('taskid')))." ".Html::a('(Project Media)', "javascript:viewMedia(".Yii::$app->request->get('taskid').");", array("class" => "dialog","style"=>"color:#167fac","title"=>"Project Media"));
}
if(isset($resuest['team_id']) && $controller=='track')
{
	$task_id=Yii::$app->request->get('taskid',0);
	//$project_name=Yii::$app->db->createCommand("SELECT tbl_task_instruct.project_name FROM tbl_task_instruct where tbl_task_instruct.task_id=$task_id and tbl_task_instruct.isactive=1")->queryScalar();
	$header_label = "<a href='javascript:void(0);' class='tag-header-black' title='Project Workflow'>Project Workflow </a> ".Html::a('# '.Yii::$app->request->get('taskid'), "javascript:viewInstruction(".Yii::$app->request->get('taskid').");", array("class" => "dialog","style"=>"color:#167fac","title"=>"Project #".Yii::$app->request->get('taskid')))." ".Html::a('(Project Media)', "javascript:viewMedia(".Yii::$app->request->get('taskid').");", array("class" => "dialog","style"=>"color:#167fac","title"=>"Project Media"));
}
// $assignall = Html::a('<em class="fa fa-thumb-tack text-primary"></em>', "javascript:AssignTaskAll(".$task_id.",".$case_id.",".$team_id.",".$team_loc.");", array("class" => "dialog pull-right","style"=>"margin-left:15px;font-size:17px","title"=>"Bulk Assign Tasks"));
// $changestatus = Html::a('<em class="fa fa-clock-o text-primary"></em>', "javascript:ChangeStatusAll(".$task_id.",".$case_id.",".$team_id.",".$team_loc.");", array("class" => "dialog pull-right","style"=>"margin-left:15px;font-size:17px","title"=>"Bulk Change Status"));
$assignall = '<a href="javascript:void(0);" class="dialog pull-right" style="margin-left:15px;font-size:17px" title = "Bulk Assign Tasks" onClick="javascript:AssignTaskAll('.$task_id.','.$case_id.','.$team_id.','.$team_loc.');"><em title = "Bulk Assign Tasks" class="fa fa-thumb-tack text-primary"></em><span class="sr-only">Bulk Assign Tasks</span></a>';
$changestatus = '<a href="javascript:void(0);" class="dialog pull-right" style="margin-left:15px;font-size:17px" title = "Bulk Change Status" onClick="javascript:ChangeStatusAll('.$task_id.','.$case_id.','.$team_id.','.$team_loc.');"><em title = "Bulk Change Status" class="fa fa-clock-o text-primary"></em><span class="sr-only">Bulk Change Status</span></a>';

$header_top = "";
if(((new User)->checkAccess(4.05) && $resuest['case_id'] != 0) || ((new User)->checkAccess(5.04) && $resuest['team_id'] != 0 && $resuest['team_loc'] != 0)){
	$header_top.= $changestatus;
}
if(((new User)->checkAccess(4.04) && $resuest['case_id'] != 0) || ((new User)->checkAccess(5.03) && $resuest['team_id'] != 0 && $resuest['team_loc'] != 0)){
	$header_top.= $assignall;
}
?>
<div class="right-main-container">
	<fieldset class="one-cols-fieldset track-projects-fieldset" id="track-projects-fieldset">
		 <?= GridView::widget([
                    'id'=>'caseprojects-grid',
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                    		['class' => '\kartik\grid\ExpandRowColumn','extraData'=>Yii::$app->request->queryParams,'detailUrl' => Url::toRoute(['track/get-task-track-details','task_id'=>$task_id,'case_id'=>$case_id,'team_id'=>$team_id,'type'=>$type]),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>' first-td','id'=>'track_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class'=>' first-td','headers'=>'track_expand'], 'expandIcon' => '<a href="javascript:void(0);" title="Expand Row" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"><span class="not-set">Expand</span></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" title="Collapse Row" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"><span class="not-set">Collapse</span></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { 
								return 1;
								}],
                    		['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>function($model, $key, $index, $column) { return ['customInput'=>true,'value'=>$model['servicetask_id'],'data-team_id'=>$model['teamId']];},'headerOptions'=>['title'=>'Select All/None','class'=>' first-td','id'=>'track_check'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class'=>' first-td','style' => 'padding-left:0px!important;','headers'=>'track_check']],
                    		['attribute' => 'teamservice_id', 'header' => $header_label, 'format' => 'raw', 'value' => function ($model) use ($task_id,$case_id,$team_id,$team_loc) {  
                                        if($model['sort_order'] == 0) {
                                               return '<a onclick="TaskInstructPopup('.$task_id.','.$model['teamId'].','.$model['team_loc'].','.$model['servicetask_id'].','.$model['sort_order'].','.$model['taskunit_id'].','.$case_id.','.$team_id.')" href="javascript:void(0)">Project Media - '.$model['teamservice_id'].' Media & '.$model['servicetask_id'].' Contents</a>';}
                                        else {
                                               return '<a onclick="TaskInstructPopup('.$task_id.','.$model['teamId'].','.$model['team_loc'].','.$model['servicetask_id'].','.$model['sort_order'].','.$model['taskunit_id'].','.$case_id.','.$team_id.')" href="javascript:void(0)">'.$model['sort_order']." - ".$model['service_name']." - ".$model['team_location_name']." - ". $model['service_task'].'</a>';
                                        }
                                    } ,    'contentOptions' => ['style' => 'width:52.5%','headers'=>'track_teamservice_id','class'=>'word-break'],'headerOptions' => ['style' => '','title'=>'Project Tasks','id'=>'track_teamservice_id']],
                    		['attribute' => 'assignuser', 'header'=>"<div id='bulk_assign_change'>".$header_top."</div>",'label' => '', 'format' => 'raw', 'value' => function ($model) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction) {  
                                    if($model['assignuser']){ if($model['sort_order'] == 0){ return '';} 
                                        return $modelTaksInstruction->getColumn($model,$task_id,$case_id,$team_id,$team_loc,'assign');
                                    }else{ return ''; } } 
                                    ,    'contentOptions' => ['style' => 'width:25.5%;','headers'=>'track_assignuser'],'headerOptions'=>['id'=>'track_assignuser','title'=>'Assign User']],
                    		['attribute' => 'assign', 'label' => '', 'header'=>'<span class="not-set">Change Status</span>', 'format' => 'raw', 'value' => function($model, $key, $index, $column) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction) {
                    			if($model['sort_order'] == 0){ return ''; }
                    			return $modelTaksInstruction->getColumn($model,$task_id,$case_id,$team_id,$team_loc,'unassign'); 
                    		}  ,    'contentOptions' => ['style' => 'width:15px;','class'=>' td-no-pad','headers'=>'track_assign'],'headerOptions'=>['id'=>'track_assign','title'=>'track assign']],
                    		['attribute' => 'task_assign', 'label' => '', 'header'=>'<span class="not-set">Todo</span>', 'format' => 'raw', 'value' => function($model, $key, $index, $column) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction) {
                    			if($model['sort_order'] == 0){ return '';}
                    			return  $modelTaksInstruction->getColumn($model,$task_id,$case_id,$team_id,$team_loc,'task_status');
                    		}  , 'contentOptions' => ['style' => 'width:20px;','class'=>' td-no-pad','headers'=>'track_task_assign_second'],'headerOptions'=>['id'=>'track_task_assign_second','title'=>'track task assign']],
                    		['attribute' => 'todo', 'header'=>'<span class="not-set">Todo</span>', 'label' => '', 'format' => 'raw', 'value' => function($model, $key, $index, $column) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction) {
                    			if($model['sort_order'] == 0){ return '';}
                    			return $modelTaksInstruction->getColumn($model,$task_id,$case_id,$team_id,$team_loc,'todo');
                    			// Html::a('<em class="fa fa-bell"></em>',null,['href'=>'javascript:AddTodo('.$model["servicetask_id"].','.$task_id.','.$model["team_loc"].','.$model["taskunit_id"].');']);
                    			 
                    		},'contentOptions' => ['style' => 'width:20px;','class'=>' td-no-pad','headers'=>'track_todo'],'headerOptions'=>['id'=>'track_todo']],
                    		['attribute' => 'task_assign','header'=>'<span class="not-set">Task Details</span>', 'label' => '', 'format' => 'raw', 'value' => function($model, $key, $index, $column) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction) {
                    			if($model['sort_order'] == 0){ return ''; }
                    			if((((new User)->checkAccess(4.061) && $case_id != 0)) || (((new User)->checkAccess(5.051) && $team_id != 0 && $team_loc != 0))){
									return $modelTaksInstruction->getColumn($model,$task_id,$case_id,$team_id,$team_loc,'billing');
								}
                    			// '<a class="edit-billable" href="#"><em class="fa fa-star text-dark"></em></a>';
                    			 
                    		}  ,    'contentOptions' => ['style' => 'width:20px;','class'=>' td-no-pad','headers'=>'track_task_assign'],'headerOptions'=>['id'=>'track_task_assign']],
                    		['attribute' => 'instruction_notes', 'header' => '<div class="select-filter-task" style="position:absolute; right:7px; top:5px; width:150px;">
                    		<label for="filtertrackproject" style="display:none;"><span class="sr-only">Filter track project</span></label>
                                    <select class="form-control" name="Select Box" id="filtertrackproject" onchange="filterTrackProject(this.value);">
                                      <option value="All" >Filter Tasks</option>
                                      <option value="Team" >Team Tasks</option>
                                      <option value="My" >My Tasks</option>
                                    </select>
                                  </div>','headerOptions'=>['class'=>'text-center','id'=>'track_instruction_notes'], 'format' => 'raw', 'value' => function($model, $key, $index, $column) use ($task_id) {
                          	if($model['sort_order'] == 0){ return ''; }	
                          	//$data = TaskInstructNotes::find()->where('servicetask_i ='.$model['servicetask_id'].' AND task_id ='.$task_id)->count();
                          	if($model['instruction_notes'] > 0){
								/* replace text-danger to text-dark */
								return '<em title = "Edit Note" class="fa fa-pencil text-danger"></em>';
                          		//return Html::a('<em class="fa fa-pencil text-danger"></em>',null,['href'=>'javascript:AddInstrcutionNotes('.$model["servicetask_id"].','.$task_id.');','class'=>'track-icon','title'=>'Edit Instruction Notes']);
                                    } else {
                                            return Html::a('<em title="Add Instruction Notes" class="fa fa-pencil text-primary"></em>',null,['href'=>'javascript:AddInstrcutionNotes('.$model["servicetask_id"].','.$task_id.');','class'=>'track-icon','title'=>'Add Instruction Notes','aria-label'=>'Add Instrcution Notes']);
                                    } 
                    		
                    		
                    			 
                    		}  ,    'contentOptions' => ['style' => 'width:20px;','class'=>' td-no-pad','headers'=>'track_instruction_notes']],
                    		['attribute' => 'transfer_task','header'=>'<span class="not-set">Task Locations</span>','label' => '' ,  'format' => 'raw', 'value' => function($model, $key, $index, $column) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction) {
                    			if($model['sort_order'] == 0){ return '';}
                    			return $modelTaksInstruction->getColumn($model,$task_id,$case_id,$team_id,$team_loc,'transferTask');
                    			//'<a class="transfer-task" href="#"><em class="fa fa-map-marker text-primary"></em></a>';
                    			 
                    		}  ,    'contentOptions' => ['style' => 'width:20px;','class'=>' td-no-pad','headers'=>'track_transfer_task'],'headerOptions'=>['id'=>'track_transfer_task']],
                    ],
                    'rowOptions' => function ($model, $index, $widget, $grid) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction,$belongtocurr_team){
                    		if($model['sort_order'] == 0){ return ['class'=>'media-tr'];}else{
                    		return $modelTaksInstruction->checkDeniedService($task_id,$case_id,$team_id,$team_loc,$model,$belongtocurr_team);
                    		}
                    	//return ['style'=>'background-color:#fff000;'];
                    },
                    'export'=>false,
                    'floatHeader'=>true,
                    'pjax'=>true,
                    'responsive'=>true,
                    'floatHeaderOptions' => ['top' => 'auto'],
                    'pjaxSettings'=>[
                    		'options'=>['id'=>'trackproject-pajax','enablePushState' => false],
                    				'neverTimeout'=>true,
                    				'beforeGrid'=>'',
                    				'afterGrid'=>'',
                    		],
                    'floatOverflowContainer'=>true,		
                   ]); 
	?>
    </fieldset>
    
    <div class="button-set text-right">
	  <div class="fourth-outer-div">
		<div class="complate-label">Project Name: <?= $project_name?></div>
		<?php /*if($team_id != 0 && $team_loc !=0){ 
		$activeTaskInstruct=$task_model->activeTaskInstruct;
		?>
		<div class="complate-label_due">Due Date: <?php echo $activeTaskInstruct->task_date_time;//(new Options)->ConvertOneTzToAnotherTz($task_model->activeTaskInstruct->task_duedate.' '.$task_model->activeTaskInstruct->task_timedue, 'UTC', $_SESSION['usrTZ']); ?></div>  
		<?php } ?> 
		<div class="complate-label">% Complete: <?= (new Tasks)->getTaskPercentageCompleted($task_id,$type,$case_id,$team_id,$team_loc,null,array(),$perc_complete)?></div>
		<?php if($team_id != 0 && $team_loc !=0){ ?>	  
		<div class="complate-label_submitted">Submitted By: <?php echo $task_model->createdUser->usr_first_name.' '.$task_model->createdUser->usr_lastname; ?> - <?php echo (new Options)->ConvertOneTzToAnotherTz($task_model->created, 'UTC', $_SESSION['usrTZ']); ?></div>
		<div class="complate-label_priority">Priority: <?php echo $activeTaskInstruct->taskPriority->priority; ?></div>  
		<?php } */?>
		  
		 
		  
      </div>
      <div id="task_instruction_popup" style="display:none;"></div>
      <?php if(isset($options) && !empty($options)){
      	$show=false;
      	if($option=='Team' || $option=='My'){
      		$show=true;
      	}
      	if($show==true || in_array('tasks_unit_id',array_keys($options)) || in_array('taskunit',array_keys($options)) || in_array('servicetask_id',array_keys($options)) || in_array('todofilter',array_keys($options)) || in_array('status',array_keys($options)) || in_array('assign',array_keys($options)) || in_array('services',array_keys($options))){
      	?>
      <button class="btn btn-danger" title="All Tasks" onclick="filterTrackProject('All');">All Tasks</button>
      <?php }}?>
      <?php if(isset($resuest['case_id']) && $controller == 'track') { ?>
      	<button class="btn btn-primary" title="Back to Case Projects" onclick="GoBack(<?php echo $resuest['case_id']?>,0,0,'case');">Back</button>
      <?php } else{?>
      	<button class="btn btn-primary" title="Back to Team Projects" onclick="GoBack(0,<?php echo $resuest['team_id']?>,<?php echo $resuest['team_loc']?>,'team');">Back</button>
      <?php }?>
      <?php if(((new User)->checkAccess(4.04) && $resuest['case_id'] != 0) || ((new User)->checkAccess(5.03) && $resuest['team_id'] != 0 && $resuest['team_loc'] != 0)){ ?>
      <!--<button class="btn btn-primary asign-transition-task" title="Assign Tasks" onclick="AssignTaskAll(<?=$task_id?>,<?=$case_id?>,<?=$team_id?>,<?=$team_loc?>);">Assign Tasks</button>-->
      <?php } ?>
       <?php if(((new User)->checkAccess(4.05) && $resuest['case_id'] != 0) || ((new User)->checkAccess(5.04) && $resuest['team_id'] != 0 && $resuest['team_loc'] != 0)){ ?>
      <!--<button class="btn btn-primary change-statuses" title="Change Status" onclick="ChangeStatusAll(<?=$task_id?>,<?=$case_id?>,<?=$team_id?>,<?=$team_loc?>);">Change Status</button>-->
      <?php } ?>
      <?php if($team_id != 0 && $team_loc !=0){
					$allprojects_url = Url::toRoute(['team-projects/post-comment','task_id'=>$task_id ,'team_id' => $team_id,'team_loc'=>$team_loc]); 
			   } else{
					$allprojects_url = Url::toRoute(['case-projects/post-comment','task_id'=>$task_id ,'case_id' => $case_id]); 
			   } ?>	 
	   <?php echo Html::button('Comments',['title'=>"Comments",'class' => 'btn btn-primary ', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>		   
    </div>
<!--complete here-->
</div>
<script>
$('#trackproject-pajax input').customInput();

$('#trackproject-pajax').on('pjax:complete',   function(xhr, textStatus, options) {
	$('#trackproject-pajax input').customInput();
	$( "#filtertrackproject" ).select2({
		theme: "krajee"
	});
});
function GoBack(case_id,team_id,team_loc,type){
	if(type == 'case'){
		location.href=baseUrl+"case-projects/index&case_id="+case_id;
	}else if(type == 'team'){
		location.href=baseUrl+"team-projects/index&team_id="+team_id+"&team_loc="+team_loc;
	} 
}
/*Filter Track Project Section*/
function filterTrackProject(option,task_id,case_id,team_id,team_loc){
	<?php if($team_id!=0) {?>
		location.href=baseUrl + "track/index&taskid=<?=$task_id ?>&team_id=<?=$team_id?>&team_loc=<?=$team_loc?>&option="+option;
	<?php }else{?>
		location.href=baseUrl + "track/index&taskid=<?=$task_id ?>&case_id=<?=$case_id?>&option="+option;
	<?php }?>
}
<?php
if($option=="Team"){?>
$("#filtertrackproject").val('Team');	
<?php } if($option=="My"){ ?>
$("#filtertrackproject").val('My');	
<?php }?>
$('a[data-module="track_project"]').addClass('active');

$(document).ready(function(){
	$('#trackproject-pajax input').customInput();
	$( "#filtertrackproject" ).select2({
		theme: "krajee"
	});
	$('.denied input[type="checkbox"]').each(function(){
		$(this).attr('disabled',true);
	});
	$('.media-tr input[type="checkbox"]').each(function(){
			$(this).parent().remove();
	});	
});	    	  		    	  
</script>
<noscript></noscript>
