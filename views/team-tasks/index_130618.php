<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\Tasks;
use app\models\User;


$modelTask = new Tasks;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Team Tasks';
$this->params['breadcrumbs'][] = $this->title;
$team_id = Yii::$app->request->get('team_id');
$team_loc = Yii::$app->request->get('team_loc');
//$models=$dataProvider->getModels();
$columns=[
			 //['class' => '\kartik\grid\ExpandRowColumn','detail'=>function($model) {return Yii::$app->controller->renderPartial('getloadtaskgriddetails', ['id'=>$model['id']]);},'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'team_task_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center','headers'=>'team_task_expand'],'filterOptions'=>['headers'=>'team_task_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) {  return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
			 ['class' => '\kartik\grid\ExpandRowColumn','detailUrl' => Url::to(['team-tasks/getloadtaskgriddetails']),'extraData'=>['team_id'=>$team_id,'team_loc'=>$team_loc,'models'=>$dataProvider->getModels()],'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'team_task_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center','headers'=>'team_task_expand'],'filterOptions'=>['headers'=>'team_task_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) {  return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
			 ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions' => function ($model){ return ['customInput'=>true,'value'=>$model['id']]; },'headerOptions'=>['title'=>'Select All/None','class'=>'word-break first-th','id'=>'team_task_checkbox'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'word-break first-td text-center','headerds'=>'team_task_checkbox'],'filterOptions'=>['headers'=>'team_task_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
			 ['attribute' => 'task_id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'], 'label'=>'Project #','headerOptions'=>['title'=>'Project #','id'=>'team_task_projects'], 'contentOptions' => ['class' => 'word-break first-td text-center projectno-width','headers'=>'team_task_projects'],'filterOptions'=>['headers'=>'team_task_projects'],'value' =>  function ($model) { return (new Tasks)->getTaskInstruction($model['task_id'], $model['task_status']); },
			 //'filterType'=>$filter_type['task_id'],'filterWidgetOptions'=>$filterWidgetOption['task_id']
			 'filter'=>'<input type="text" class="form-control filter_number_only" name="TasksUnitsSearch[task_id]" value="'.$params['TasksUnitsSearch']['task_id'].'">'
			 ],
			 ['attribute' => 'id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Task #'], 'label'=>'Task #','headerOptions'=>['title'=>'Task #','id'=>'team_task_task'], 'contentOptions' => ['class' => 'first-td word-break text-center projectno-width','headers'=>'team_task_task'],'filterOptions'=>['headers'=>'team_task_task'],'value' =>  function ($model) use($team_id,$team_loc) { return Html::a($model['id'],null,['href'=>Url::toRoute(['track/index','taskid'=>$model['task_id'],'team_id'=>$team_id,'team_loc'=>$team_loc,'tasks_unit_id'=>$model['id'],'option'=>'Team']),'title'=>'Task #'.$model['id']]); },
			 //'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']
			 'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[id]" value="'.$params['TasksUnitsSearch']['id'].'">'
			 ],
			 ['attribute' => 'unit_status', 'filterInputOptions' => ['title' => 'Filter By Task Status','prompt' => ' '], 'label' => 'Status','headerOptions'=>['title'=>'Task Status','class'=>'global-status-width','id'=>'team_task_global_status'], 'contentOptions' => ['class' => 'first-td text-center word-break global-status-width','headers'=>'team_task_global_status'],'filterOptions'=>['headers'=>'team_task_global_status'], 'format' => 'raw',  'value' => function ($model) use($modelTask,$team_id,$team_loc,$is_accessible_submodule_tracktask) { return $modelTask->imageHelperTeamsql($model,$is_accessible_submodule_tracktask,$team_id,$team_loc);},'filterType'=>$filter_type['unit_status'],'filterWidgetOptions'=>$filterWidgetOption['unit_status']],
			 ['attribute' => 'unit_assigned_to','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter by Assigned'], 'label'=>'Assigned To','headerOptions'=>['title'=>'Task Assigned To','id'=>'team_task_assigned_to','class'=>'white-space'], 'contentOptions' => ['class' => 'word-break first-td asign-task-width','headers'=>'team_task_assigned_to'],'filterOptions'=>['headers'=>'team_task_assigned_to'],'value' =>  function ($model) { return $model['usr_first_name'].' '.$model['usr_lastname']; },'filterType'=>$filter_type['unit_assigned_to'],'filterWidgetOptions'=>$filterWidgetOption['unit_assigned_to']],
			 
			 ['attribute' => 'workflow_task','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Workflow Task'], 'label'=>'Workflow Task','headerOptions'=>['title'=>'Workflow Task','id'=>'team_task_workflow_task'], 'contentOptions' => ['class' => 'asign-task-width projectname-width teamTaskWorkflowTask','headers'=>'team_task_workflow_task'],'filterOptions'=>['headers'=>'team_task_workflow_task'],'value' =>  function ($model) { return $model['service_name']." - ".$model['service_task']; },'filterType'=>$filter_type['servicetask_id'],'filterWidgetOptions'=>$filterWidgetOption['servicetask_id']],

			 ['attribute' => 'client_id', 'filterInputOptions' => ['title' => 'Filter By Client'], 'label' => 'Client','headerOptions'=>['title'=>'Client','id'=>'team_projects_clients','scope'=>'col'],'contentOptions' => ['class' => 'word-break projectname-width','headers'=>'team_projects_case'],'filterOptions'=>['headers'=>'team_projects_case'], 'filterType'=>$filter_type['client_id'],'filterWidgetOptions'=>$filterWidgetOption['client_id'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TeamSearch[client_id]" value="">','value' => function($model) {return $model['client_name'];}],
			 ['attribute' => 'client_case_id', 'filterInputOptions' => ['title' => 'Filter By Case'], 'label' => 'Case','headerOptions'=>['title'=>'Case','id'=>'team_projects_clients_case','scope'=>'col'],'contentOptions' => ['class' => 'word-break projectname-width','headers'=>'team_projects_client_case'],'filterOptions'=>['headers'=>'team_projects_client_case'], 'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions'=>$filterWidgetOption['client_case_id'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TeamSearch[client_case_id]" value="">','value' => function($model) {return $model['case_name'];}],
			 ['attribute' => 'task_duedate', 'filterInputOptions' => ['title' => 'Filter By Project Due Date','class'=>'form-control'], 'format' => 'html', 'label' => 'Due Date','headerOptions'=>['title'=>'Project Due Date','class'=>'global-datetime-width','id'=>'team_task_date_time'], 'contentOptions' => ['class' => 'global-datetime-width word-break','headers'=>'team_task_date_time'],'filterOptions'=>['headers'=>'team_task_date_time'],'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[task_duedate]" value="">', 'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions'=>$filterWidgetOption['task_duedate'], 'value' => function($model){ return (new Tasks)->getTaskDuedatesql($model); }],
			 ['attribute' => 'task_priority', 'filterInputOptions' => ['title' => 'Filter by Project Priority'], 'format' => 'html', 'label' => 'Priority','headerOptions'=>['title'=>'Project Priority','id'=>'team_task_project_priority'], 'contentOptions' => ['class' => 'priority-width word-break','headers'=>'team_task_project_priority'],'filterOptions'=>['headers'=>'team_task_project_priority'],'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[task_priority]" value="">','value' => function($model) use($pporder){ if($pporder == $model['project_order']){ return "<span class='text-danger'><strong>".$model['priority']."</strong></span>"; } else {return $model['priority'];} }, 'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority']],
			 ['attribute' => 'team_priority', 'filterInputOptions' => ['title' => 'Filter By Team Priority'], 'format' => 'html', 'label' => 'T-Priority','headerOptions'=>['title'=>'Team Priority','id'=>'team_task_team_priority'], 'contentOptions' => ['class' => 'tpriority-width word-break','headers'=>'team_task_team_priority'],'filterOptions'=>['headers'=>'team_task_team_priority'],'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[team_priority]" value="">', 'filterType'=>$filter_type['team_priority'],'value' => function($model){ return $model['tasks_priority_name']; },'filterWidgetOptions'=>$filterWidgetOption['team_priority']],
];
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
		
		 <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
		<input type="hidden" name="team_id" id="hidden_team_id" value="<?php echo $team_id; ?>">
		<input type="hidden" name="team_loc" id="hidden_team_loc" value="<?php echo $team_loc; ?>">
        <div class="table-responsive">
                <?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'teamtaskprojects-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel'=>false,
		'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{dynagridSort}{dynagrid}{pager}</div>',
		'responsiveWrap' => false,
		'export'=>false,
		'floatHeader'=>true,
		'floatHeaderOptions' => ['top' => 'auto'],
		'persistResize'=>false,
		'resizableColumns'=>false,
		'pjax'=>true,
			'pjaxSettings'=>[
					'options'=>['id'=>'teamtaskprojects-pajax','enablePushState' => false],
					'neverTimeout'=>true,
					'beforeGrid'=>'',
					'afterGrid'=>'',
			],
			'pager' => [
					'options'=>['class'=>'pagination'], // set clas name used in ui list of pagination
					'prevPageLabel' => 'Previous',  // Set the label for the "previous" page button
					'nextPageLabel' => 'Next',  // Set the label for the "next" page button
					'firstPageLabel'=>'First',  // Set the label for the "first" page button
					'lastPageLabel'=>'Last',  // Set the label for the "last" page button
					'nextPageCssClass'=>'next',  // Set CSS class for the "next" page button
					'prevPageCssClass'=>'prev',  // Set CSS class for the "previous" page button
					'firstPageCssClass'=>'first',  // Set CSS class for the "first" page button
					'lastPageCssClass'=>'last',  // Set CSS class for the "last" page button
					'maxButtonCount'=>5,  // Set maximum number of page buttons that can be displayed
			],
			'responsive'=>true,
			'floatOverflowContainer'=>true,

    ],
    'allowThemeSetting'=>false,
    'allowFilterSetting'=>false,
    'allowPageSetting'=>false,
    'enableMultiSort'=>true,
    'toggleButtonGrid'=>['class'=>'btn btn-info btn-sm'],
    'toggleButtonSort'=>['class'=>'btn btn-sm'],
    'options'=>[
    'id'=>'dynagrid-teamtaskprojects',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
                <?php /* GridView::widget([
                    'id'=>'teamprojects-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'responsiveWrap' => false,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
                    'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCountteam'>$totalCount</strong> items</div>",
                    'toolbar'=> [
						'{export}',
					],
                    'columns' => [


                    ],
			'floatHeader'=>true,
			'pjax'=>true,
            'responsive'=>false,
            'floatHeaderOptions' => ['top' => 'auto'],
            'pjaxSettings'=>[
                'options'=>['id'=>'teamassigneduser-pajax','enablePushState' => false],
                'neverTimeout'=>true,
                'beforeGrid'=>'',
                'afterGrid'=>'',
            ],
            'pager' => [
								'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
								'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
								'nextPageLabel' => 'Next',   // Set the label for the "next" page button
								'firstPageLabel'=>'First',   // Set the label for the "first" page button
								'lastPageLabel'=>'Last',    // Set the label for the "last" page button
								'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
								'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
								'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
								'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
								'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
                        ],
                        'responsive'=>false,
                        'floatOverflowContainer'=>true,
                ]); */?>
</div>
<form method="post" id="team-task-form" style="height:100%;display:none;" action="<?php echo Url::to(['export-excel/team-tasks-export',Yii::$app->request->queryParams]) ?>" autocomplete="off">
</form>
</fieldset>
<div class="button-set text-right" style="min-height:56px;">
	<?php $current_url = Url::current(); ?>
        <?php $allprojects_url = Url::toRoute(['team-tasks/index', 'team_id' => $team_id,'team_loc'=>$team_loc]); ?>
        <?php if($current_url!=$allprojects_url) { ?>
           <?= Html::button('All Active Tasks',['title'=>"All Active Tasks",'class' => 'btn btn-primary all_filter', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>
        <?php } else { ?>
            <?= Html::button('All Active Tasks',['title'=>"All Active Tasks",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>
        <?php } ?>
			<?php if ((new User)->checkAccess(5.0145)){ ?>
		<?= Html::button('Export',['title'=>"Export",'class' => 'btn btn-primary all_filter', 'onclick' => 'export_team_task();'])?>
			<?php } ?>
     </div>
<div id="bulkcompletetask-closed-dialog" class="bulkreopentasks hide">
		<fieldset>
             <legend class="sr-only">Bulk Complete Tasks</legend>
			<div class="custom-inline-block-width">
				<input type="radio" aria-setsize="2" aria-posinset="1" name="bulkcompletetask" class="bulkreopen" value="selectedtask" id="rdo_selectedcompletetask"><label for="rdo_selectedcompletetask">Selected <span id="selectedtask">0</span> Tasks in Grid</label>
				<input type="radio" aria-setsize="2" aria-posinset="2" name="bulkcompletetask" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkcompletetask"/><label for="rdo_bulkcompletetask">All Filtered <span id="allbulkcompletetask">0</span> Tasks in Grid</label>
			</div>
		</fieldset>
	</div>
</div>
	<div id="bulkassign-dialog" class="bulkassigntasks hide">
				<form id="frmbulkassigntasks" name="bulkassigntasks" action="" method="post" autocomplete="off">
                                    <fieldset>
                    <legend class="sr-only">Bulk Assign Tasks</legend>
				<div class="custom-full-width">
				<input type="radio" aria-setsize="2" aria-posinset="1" name="bulkassigntask" class="bulkassigntask" id="bulkassignselectedtask" value="selectedtask"><label for="bulkassignselectedtask">Selected <span id="assignselectedtask">0</span> Tasks in Grid</label>
                </div>
                <div class="clr"></div>
                <div class="custom-full-width">
                    <input type="radio" name="bulkassigntask" aria-setsize="2" aria-posinset="2" class="bulkassigntask" value="alltask" id="bulkassignalltask" checked="checked"/><label for="bulkassignalltask">All Filtered<span id="assignalltask"><?php echo $totalCount; ?></span> Tasks in Grid</label>
                </div>
                </fieldset>
                <div class="clr">&nbsp;</div>
                <div class="clsusersdata">
                <?php if(!empty($data)) { ?>
					<div class="asign-dropdown">
					  <ul class="asign-menu">
						<li class="search-box">
						  <label for="searchUsers" style="display:none">&nbsp;</label>
						  <input id="searchUsers" type="text" name="search1" class="form-control" placeholder="Filter List">
						  <label for="services" style="display:none">&nbsp;</label>
<!--						  <input id="services" value="<?=$services?>" type="hidden">-->
						  <label for="taskunits" style="display:none">&nbsp;</label>
<!--						  <input id="taskunits" value="<?=$taskunit_id?>" type="hidden">-->

						</li>
						<li class="search-result">
							<ul>
								<?php if(isset($data['team_members'])){ ?>
									<li data-id="header"><strong>Team Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['team_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>

									</ul>

								<?php } ?>
								<?php if(isset($data['case_members'])){ ?>
									<li data-id="header"><strong>Case Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['case_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>

									</ul>

								<?php } ?>
								<li data-id="header"><strong>Both Case Managers & Team Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['both_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>

									</ul>
							</ul>
						</li>
                	</ul>
                	</div>
                	<?php } else {
                		echo "No User have permission to this Team and Location";
                	}
                	?>                </div>
                </form>
		</div>
		<div id="bulktransition-dialog" class="bulktransitiontasks hide">
				<form id="frmbulktransittasks" name="frmbulktransittasks" action="" method="post" autocomplete="off">
                                    <fieldset>
                                        <legend class="sr-only">Bulk Transition Tasks</legend>
                                        <div class="custom-full-width">
                                            <input type="radio" aria-setsize="2" aria-posinset="1" name="bulktransittask" class="bulktransittask" id="bulktransitionselectedtask" value="selectedtask"><label for="bulktransitionselectedtask">Selected <span id="transitionselectedtask">0</span> Tasks in Grid</label>
                                        </div>
                                        <div class="clr"></div>
                                        <div class="custom-full-width">
                                            <input type="radio" aria-setsize="2" aria-posinset="2" name="bulktransittask" class="bulktransittask" value="alltask" id="bulktransitionalltask" checked="checked"/><label for="bulktransitionalltask">All Filtered<span id="transitionalltask"><?php echo $totalCount; ?></span> Tasks in Grid</label>
                                        </div>
                                    </fieldset>
                <div class="clr">&nbsp;</div>
                <div class="clsusersdata1">
                	<?php
                	if(!empty($data)){?>
					<div class="asign-dropdown">
					  <ul class="asign-menu">
						<li class="search-box">
						  <input id="searchUsers1" type="text" name="search1" class="form-control" placeholder="Filter List">
						  <label for="searchUsers1" style="display:none">&nbsp;</label>
<!--						  <input id="services" value="<?=$services?>" type="hidden">-->
						  <label for="services" style="display:none">&nbsp;</label>
<!--						  <input id="taskunits" value="<?=$taskunit_id?>" type="hidden">-->
						  <label for="taskunits" style="display:none">&nbsp;</label>

						</li>
						<li class="search-result">
							<ul>
								<?php if(isset($data['team_members'])){ ?>
									<li data-id="header"><strong>Team Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['team_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>

									</ul>

								<?php } ?>
								<?php if(isset($data['case_members'])){ ?>
									<li data-id="header"><strong>Case Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['case_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>

									</ul>

								<?php } ?>
								<li data-id="header"><strong>Both Case Managers & Team Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['both_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>

									</ul>
							</ul>
						</li>
                	</ul>
                	</div>
                	<?php } else {
                		echo "No User have permission to this Team and Location";
                	}
                	?>
                </div>
                </form>
		</div>
		<!-- PopUp For Bulk Unassign Tasks -->
		<div id="bulkunassign-dialog" class="bulkunassign hide">
			<fieldset>
				<legend class="sr-only">Bulk Unassign Tasks</legend>
				<div class="custom-inline-block-width">
					<input type="radio" aria-setsize="2" aria-posinset="1" name="bulkunassigntask" class="bulkreopen" value="selectedtask" id="rdo_selectedunassigntask"><label for="rdo_selectedunassigntask">Selected <span id="unassignselectedtask">0</span> Tasks in Grid</label>
					<input type="radio" aria-setsize="2" aria-posinset="2" name="bulkunassigntask" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkunassigntask"/><label for="rdo_bulkunassigntask">All Filtered<span id="allbulkassigntask">0</span> Tasks in Grid</label>
				</div>
			</fieldset>
		</div>
</div>


<?php $unit_assigned_to  = $_REQUEST['unit_assigned_to'];  ?>
<script>
var $grid = $('#teamassigneduser-pajax');
$grid.css('visibility','hidden');
$(document).ready(function($){
	$grid.css('visibility','visible');
	var unit_assigned_to = '<?php echo $unit_assigned_to; ?>';
	var assigned_name = '<?php echo $assigned_name; ?>';
	if(unit_assigned_to != ''){
		$('.assignedonly_content').show();

	}else{
		$('.assignedonly_content').hide();
	}
	jQuery("#searchUsers").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery(".search-result ul li").each(function () {
		    if(jQuery(this).data('id')!='header'){
		        if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		        	jQuery(this).hide();
		        } else {
		            jQuery(this).show()
		        }
	        }
	    });
	});
	jQuery("#searchUsers1").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery(".search-result ul li").each(function () {
		    if(jQuery(this).data('id')!='header'){
		        if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		        	jQuery(this).hide();
		        } else {
		            jQuery(this).show()
		        }
	        }
	    });
	});
});
/*dyangird setting*/
$('#dynagrid-<?=$dynagrid->gridOptions['id']?>-modal').on('shown.bs.modal', function () {
	//var self = this,
	$element = $('input[name="<?=$dynagrid->options['id']?>-dynagrid');
	$form = self.$element.closest('form');
	$form.find('select[data-krajee-select2]').each(function () {
		var $el = $(this), settings = window[$el.attr('data-krajee-select2')] || {};
		if ($el.data('select2')) {
			$el.select2('destroy');
		}
		$.when($el.select2(settings)).done(function () {
			initS2Loading($el.attr('id'), '.select2-container--krajee'); // jshint ignore:line
		});
	});
	$form.find('[data-krajee-sortable]').each(function () {
		var $el = $(this);
		if ($el.data('sortable')) {
			$el.sortable('destroy');
		}
		$el.sortable(window[$el.attr('data-krajee-sortable')]);
	});
});
/*dyangird setting*/
$(document).ready(function() {

	$(document).on('pjax:end',   function(xhr, textStatus, options) {
			if($('.all_filter').is(':visible')){
				$('#bulk_transfer_task_location').hide();
				if($('#tasksunitssearch-workflow_task').val()!=null){
				var wft=$('#tasksunitssearch-workflow_task').val().toString();
				if(wft.indexOf(",") == '-1'){
					$.ajax({
						url : baseUrl+'/team-tasks/chklocation',
						type : 'post',
						data : {
						'service_location' : wft, 'team_id'	:'<?=$team_id?>'
						},
						success: function(response){
							if(response == "OK"){
								$('#bulk_transfer_task_location').show();
							}
						}
					});
				}
			 }
			}
	});
});
</script>
<noscript></noscript>
