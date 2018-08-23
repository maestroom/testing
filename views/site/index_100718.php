<?php
use yii\helpers\Url;
use kartik\grid\GridView;
use app\models\User;
use app\models\Role;
use yii\web\Session;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\Tasks;
\app\assets\HighchartAsset::register($this);

$this->title = 'My Assignments';
$this->params['breadcrumbs'][] = $this->title;
$roleId = Yii::$app->user->identity->role_id;
$role_data = $_SESSION['role'];
//Role::findOne($roleId);
$role_type = explode(',',$role_data->role_type);
$columns=[
    ['attribute' => 'unit_status','filterInputOptions' =>['title' => 'Filter By Task Status','prompt' => ' '],'label'=>'Task Status','headerOptions'=>['title'=>'Task Status','class'=>'global-status-width','id'=>'team_task_global_status'], 'contentOptions' => ['class' => 'first-td text-center word-break global-status-width','headers'=>'team_task_global_status'],'filterOptions'=>['headers'=>'team_task_global_status'],'format' => 'raw', 'value' => function ($model) { return (new Tasks)->imageHelperTeamsql($model,0,0,0);},'filterType'=>$filter_type['unit_status'],'filterWidgetOptions'=>$filterWidgetOption['unit_status']],
    //['attribute' => 'task_id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'], 'label'=>'Project #','headerOptions'=>['title'=>'Project #','id'=>'team_task_projects'], 'contentOptions' => ['class' => 'word-break first-td text-center projectno-width','headers'=>'team_task_projects'],'filterOptions'=>['headers'=>'team_task_projects'],'filter'=>'<input type="text" class="form-control filter_number_only" name="TasksUnitsSearch[task_id]" value="'.$params['TasksUnitsSearch']['task_id'].'">'],
    ['attribute' => 'id','label'=>'Task / Project #','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Task #'],'headerOptions'=>['title'=>'Task #','id'=>'team_task_task'], 'contentOptions' => ['class' => 'first-td word-break text-center projectno-width','headers'=>'team_task_task'],'filterOptions'=>['headers'=>'team_task_task'],'value' =>  function ($model)  {
        return
        Html::a($model['id'],null,['href'=>Url::toRoute(['track/index','taskid'=>$model['task_id'],'tasks_unit_id'=>$model['id'],'option'=>'Team'])
        ,'title'=>'Task #'.$model['id']])." / ".$model['task_id']; },'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[id]" value="'.$params['TasksUnitsSearch']['id'].'">'],
    ['attribute' => 'project_name','label'=>'Project Name','format'=>'raw','filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name']],
    ['attribute' => 'client_id','label'=>'Client','value' =>  function ($model)  {
        return $model->tasks->clientCase->client->client_name;
    },'filterType'=>$filter_type['client_id'],'filterWidgetOptions'=>$filterWidgetOption['client_id']],
    ['attribute' => 'client_case_id','label'=>'Case','value'=>function($model){
        return $model->tasks->clientCase->case_name;
    },'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions'=>$filterWidgetOption['client_case_id']],
    ['attribute'=>'workflow_task','label'=>'Workflow','value'=>function($model){
        return html_entity_decode($model->servicetask->service_task);
    },'filterType'=>$filter_type['servicetask_id'],'filterWidgetOptions'=>$filterWidgetOption['servicetask_id']],
    ['attribute'=>'task_priority','label'=>'Project Priority','value'=>function($model){
        return $model->taskInstruct->taskPriority->priority;
    },'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority']],
    ['attribute'=>'task_duedate','label'=>'Project Due Date','format' => 'raw','value' => function($model){ return (new Tasks)->getTaskDuedatesql($model); }
    ,'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions'=>$filterWidgetOption['task_duedate']],


];
 ?>
<div class="row">
	<div class="col-md-12">
		<h1 id="page-title" role="heading" class="page-header">
                    <em class="fa fa-mouse-pointer" title="My Task Assignments"></em> <a href="javascript:void(0);" title="My Task Assignments" class="tag-header-red">My Task Assignments</a>
		</h1>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 single-cols-container">
		<fieldset class="single-cols-fieldset workflow-management my-assignments">
            <div class="myassign-chart-box col-md-3">
				<div id="statuswisetaskschart" class="clsperiodchart chart-container"></div>
			</div>
            <div class="myassign-chart-box col-md-3">
                <div id="clientwisetaskschart" class="clsperiodchart chart-container"></div>
            </div>
            <div class="myassign-chart-box col-md-3">
                <div id="prioritywisetaskschart" class="clsperiodchart chart-container"></div>
            </div>
            <div class="myassign-chart-box col-md-3">
                <div id="workflowwisetaskschart" class="clsperiodchart chart-container"></div>
            </div>
            <div class="clearfix"></div>
            <div class="case-project-fieldset table-responsive" style="position:relative">
            <?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'myactivetasks-grid',
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
            </div>
            <?php /*<div id="tabs">
				<ul>
				<?php if ((new User)->checkAccess(1.001)) { ?>
				<?php if(in_array(1, $role_type) || $roleId=='0'){ ?>
						<li><a href="#tabs-1" title="My Cases Assignments">My Cases Assignments </a></li>
				<?php } ?>
				<?php if(in_array(2, $role_type) || $roleId=='0'){ ?>
					<li><a href="#tabs-2"  title="My Teams Assignments">My Teams Assignments </a></li>
				<?php } } ?>
				<?php if ((new User)->checkAccess(1.01)) { ?>
					<li><a href="#tabs-3" onclick="changeTodaysActivity();" title="Today's Activity">Today's Activity</a></li>
				<?php } ?>
				</ul>
				<?php if ((new User)->checkAccess(1.001)) { ?>
					<?php if(in_array(1, $role_type) || $roleId=='0'){ ?>
				<div id="tabs-1">
					<?= GridView::widget([
						'dataProvider' => $casearrayDataProvider,
						'layout' => '{items}',
                    'columns' => [
                         ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['site/getmycaseassignmentnew']),'headerOptions'=>['title'=>'Expand/Collapse All','id'=>'my_case_task_assignments_expand','aria-label'=>"My Case Task Assignments Expand", 'class' => 'first-th'],'contentOptions'=>['title'=>'Expand/Collapse Row','class'=>'my_case_layout','headers'=>'my_case_task_assignments_expand'], 'expandIcon' => '<a href="javascript:void(0)" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="ectext">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0)" aria-label="Collapse Row"><span class="glyphicon glyphicon-minus"></span><span class="ectext">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                         ['attribute' => 'name', 'label' => 'My Case Task Assignments', 'format' => 'raw','headerOptions'=>['title'=>'My Case Task Assignments','id'=>'my_case_task_assignments_title'],'contentOptions'=>['headers'=>'my_case_task_assignments_title'], 'value' => function($model){return '<a href="#" class="tag-header-black" title="'.$model['name'].'">'.$model['name'].'</a>';}],
                    ],
                    'rowOptions' =>  ['class'=>'mycaseassignment_tr'],
					]); ?>
				</div>
				<?php } ?>
				<?php if(in_array(2, $role_type) || $roleId=='0'){ ?>
				<div id="tabs-2">
					<?= GridView::widget([
						'dataProvider' => $TeamarrayDataProvider,
						'layout' => '{items}',
                    'columns' => [
                         ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['site/getmyteamassignmentnew']),'headerOptions'=>['title'=>'Expand/Collapse All','id'=>'my_team_task_assignments_expand','aria-label'=>'My Team Task Assignments'],'contentOptions'=>['title'=>'Expand/Collapse Row','headers'=>'my_team_task_assignments_expand'], 'expandIcon' => '<a href="javascript:void(0)" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="ectext">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0)" aria-label="Collapse Row"><span class="glyphicon glyphicon-minus"></span><span class="ectext">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                         ['attribute' => 'name', 'label' => 'My Team Task Assignments', 'format' => 'raw','headerOptions'=>['title'=>'My Team Task Assignments','id'=>'my_team_task_assignments_title'],'contentOptions'=>['headers'=>'my_team_task_assignments_title'],'value' => function($model){return '<a href="#" class="tag-header-black" title="'.$model['name'].'">'.$model['name'].'</a>';}],
                    ],
                    'rowOptions' =>  ['class'=>'myteamassignment_tr'],
					]); ?>
				</div>
				<?php } } ?>
				<?php if ((new User)->checkAccess(1.01)) { ?>
				<div id="tabs-3">
					<input type="hidden" id="activity_offset" value="0">
					<div class="tab-inner-fix">

						<div id="kv-grid-demo" class="grid-view">
							<div id="kv-grid-demo-container" class="kv-grid-container"
								style="overflow: auto;">
								<input type="hidden" value="0" id="noactivities" />
								<div  id="activity-log-dynamic"></div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div> */?>
		</fieldset>
	</div>
</div>
<script type="text/javascript">
 $(function() {
    /*$( "#tabs" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });*/
    // Highchart functionality
Highcharts.getSVG = function (charts) {
    var svgArr = [],
            top = 0,
            width = 0;
    $.each(charts, function (i, chart) {
        var svg = chart.getSVG();
        svg = svg.replace('<svg', '<g transform="translate(0,' + top + ')" ');
        svg = svg.replace('</svg>', '</g>');
        top += chart.chartHeight;
        width = Math.max(width, chart.chartWidth);
        svgArr.push(svg);
    });

    return '<svg height="' + top + '" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + svgArr.join('') + '</svg>';
};
/**
 * Create chart for Tasks by Status
 */

function createTasksbyStatusPieChart(title) {
    var pending = '<?php echo $taskstatus['pending'] ?>';
    var workingtasks = "<?php echo $taskstatus['workingtasks']; ?>";
    var workingtodos = "<?php echo $taskstatus['workingtodos']; ?>";
    var notstarted = "<?php echo $taskstatus['notstarted']; ?>";
    pending = parseFloat(pending);
    workingtasks = parseFloat(workingtasks);
    workingtodos = parseFloat(workingtodos);
    notstarted = parseFloat(notstarted);

    var options = {
        chart: {
            renderTo: 'statuswisetaskschart',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text:title,
            useHTML: true,
            style: {"fontSize": "15px",'display':'block',
            'width':'50% !important',
'white-space':'normal !important',
'left':'0 !important',
'right':'0 !important',
'margin':'0 auto !important',
'word-break':'break-all'
            },
        },
        tooltip: {
            pointFormat: '<strong>{point.y}</strong>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<strong>{point.name}</strong>: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                type: 'pie',
                data: [
                    ["Pending", pending],
                    ['Working Tasks', workingtasks],
                    ['Working Todos', workingtodos],
                    ['Not Started', notstarted]
                ]
            }]
    };
    chart1 = new Highcharts.Chart(options);

    $( window ).resize(function() {
		chart1.redraw();
		chart1.reflow();
    });

}

/**
 * Create chart for Tasks by Client
 */

function createTasksbyClientPieChart(title) {
    var data = [];
    <?php foreach($assignedtasksbyclient as $task) {?>
            data.push(['<?php echo $task['client_name'];?>', <?php echo $task['cnttasksbyclient'];?>]);
    <?php } ?>
    var options = {
        chart: {
            renderTo: 'clientwisetaskschart',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text:title,
            useHTML: true,
            style: {"fontSize": "15px",'display':'block',
            'width':'50% !important',
'white-space':'normal !important',
'left':'0 !important',
'right':'0 !important',
'margin':'0 auto !important',
'word-break':'break-all'
            },
        },
        tooltip: {
            pointFormat: '<strong>{point.y}</strong>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<strong>{point.name}</strong>: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
                point: {
					events: {
					    click: function (e) {
						console.log(this);
						//if (this.series.name == 'val' && case_project_permission != 0) {
						//var res = ccats[this.x];
						//location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[priority][]="+res+"&active=active";
						//}
					    }
					}
				    }
            },

        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                type: 'pie',
                data: data
            }]
    };
    chart2 = new Highcharts.Chart(options);

    $( window ).resize(function() {
		chart2.redraw();
		chart2.reflow();
    });

}

/**
 * Create chart for Tasks by Priority
 */

function createTasksbyPriorityPieChart(title) {
    var data = [];
    <?php foreach($assignedtasksbypriority as $task) {?>
            data.push(['<?php echo $task['task_priority'];?>', <?php echo $task['cnttasksbypriority'];?>]);
    <?php } ?>
    var options = {
        chart: {
            renderTo: 'prioritywisetaskschart',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text:title,
            useHTML: true,
            style: {"fontSize": "15px",'display':'block',
            'width':'50% !important',
'white-space':'normal !important',
'left':'0 !important',
'right':'0 !important',
'margin':'0 auto !important',
'word-break':'break-all'
            },
        },
        tooltip: {
            pointFormat: '<strong>{point.y}</strong>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<strong>{point.name}</strong>: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                type: 'pie',
                data: data
            }]
    };
    chart3 = new Highcharts.Chart(options);

    $( window ).resize(function() {
		chart3.redraw();
		chart3.reflow();
    });

}

/**
 * Create chart for Tasks by Workflow tasks
 */

function createTasksbyWorkflowPieChart(title) {
    var data = [];
    <?php foreach($assignedtasksbyworkflow as $task) {?>
            data.push(['<?php echo $task['service_task'];?>', <?php echo $task['cnttasksbyworkflow'];?>]);
    <?php } ?>
    var options = {
        chart: {
            renderTo: 'workflowwisetaskschart',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text:title,
            useHTML: true,
            style: {"fontSize": "15px",'display':'block',
            'width':'50% !important',
'white-space':'normal !important',
'left':'0 !important',
'right':'0 !important',
'margin':'0 auto !important',
'word-break':'break-all'
            },
        },
        tooltip: {
            pointFormat: '<strong>{point.y}</strong>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<strong>{point.name}</strong>: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                type: 'pie',
                data: data
            }]
    };
    chart4 = new Highcharts.Chart(options);

    $( window ).resize(function() {
		chart4.redraw();
		chart4.reflow();
    });

}

    createTasksbyStatusPieChart('Tasks Assigned by Status');
    createTasksbyClientPieChart('Tasks Assigned by Client');
    createTasksbyPriorityPieChart('Tasks Assigned by Project Priority');
    createTasksbyWorkflowPieChart('Tasks Assigned by Workflow Task');

 });

 function activityalltask2(taskid, caseId, service_task_id, todo_filteredtodayact, opt, type, teamId, team_loc){
                        if(type == "team"){
                        	var path = baseUrl + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&todo_filteredtodayact=todo_filteredtodayact";
                        } else {
                        	var path = baseUrl + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&todo_filteredtodayact=todo_filteredtodayact";
                        }
                    	method = "post";
						var form = document.createElement("form");
                        form.setAttribute("method", method);
                        form.setAttribute("action", path);
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", 'taskid');
                        hiddenField.setAttribute("value", taskid);
                        form.appendChild(hiddenField);
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", 'caseId');
                        hiddenField.setAttribute("value", caseId);
                        form.appendChild(hiddenField);
						if(type == 'team'){
							var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'teamId');
                            hiddenField.setAttribute("value", teamId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'team_loc');
                            hiddenField.setAttribute("value", team_loc);
                            form.appendChild(hiddenField);
						}
						if (opt == 'passtodo'){
                    		var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'service_task_id');
                            hiddenField.setAttribute("value", service_task_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'todo_filteredtodayact');
                            hiddenField.setAttribute("value", todo_filteredtodayact);
                            form.appendChild(hiddenField);
                    	}
                    	var hiddenField = document.createElement("input");
						hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name",  yii.getCsrfParam());
                        hiddenField.setAttribute("value", yii.getCsrfToken());
                        form.appendChild(hiddenField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                   function activityalltasknotes(taskid, caseId, service_task_id, instructionnotes, unit_id){
                    method = "post";
                            var path = baseUrl + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&instructionnotes=instructionnotes&unit_id="+unit_id;
                            var form = document.createElement("form");
                            form.setAttribute("method", method);
                            form.setAttribute("action", path);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskid');
                            hiddenField.setAttribute("value", taskid);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'caseId');
                            hiddenField.setAttribute("value", caseId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'service_task_id');
                            hiddenField.setAttribute("value", service_task_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'instructionnotes');
                            hiddenField.setAttribute("value", instructionnotes);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'unit_id');
                            hiddenField.setAttribute("value", unit_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name",  yii.getCsrfParam());
							hiddenField.setAttribute("value", yii.getCsrfToken());
                            form.appendChild(hiddenField);
                            document.body.appendChild(form);
                            form.submit();
                    }
                    function activityalltask1(taskid, caseId, service_task_id, instructionnotes, unit_id){
                    method = "post";
                            var path = httpPath + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&instructionnotes=instructionnotes&unit_id="+unit_id;
                            var form = document.createElement("form");
                            form.setAttribute("method", method);
                            form.setAttribute("action", path);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskid');
                            hiddenField.setAttribute("value", taskid);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'caseId');
                            hiddenField.setAttribute("value", caseId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'service_task_id');
                            hiddenField.setAttribute("value", service_task_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'instructionnotes');
                            hiddenField.setAttribute("value", instructionnotes);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'unit_id');
                            hiddenField.setAttribute("value", unit_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name",  yii.getCsrfParam());
							hiddenField.setAttribute("value", yii.getCsrfToken());
                            form.appendChild(hiddenField);
                            document.body.appendChild(form);
                            form.submit();
                    }
                    function activityalltask(taskid, caseId, taskunit, servicetask_id){
                   			method = "post";
                            var path = httpPath + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+servicetask_id;
                            var form = document.createElement("form");
                            form.setAttribute("method", method);
                            form.setAttribute("action", path);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskid');
                            hiddenField.setAttribute("value", taskid);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'caseId');
                            hiddenField.setAttribute("value", caseId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskunit');
                            hiddenField.setAttribute("value", taskunit);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'servicetask_id');
                            hiddenField.setAttribute("value", servicetask_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name",  yii.getCsrfParam());
							hiddenField.setAttribute("value", yii.getCsrfToken());
                            form.appendChild(hiddenField);
                            document.body.appendChild(form);
                            form.submit();
                    }

</script>
<noscript></noscript>
