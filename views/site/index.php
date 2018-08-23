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
use app\models\Options;
\app\assets\HighchartAsset::register($this);

$this->title = 'My Assignments';
$this->params['breadcrumbs'][] = $this->title;
$roleId = Yii::$app->user->identity->role_id;
$role_data = $_SESSION['role'];
$role_type = explode(',',$role_data->role_type);
$params = Yii::$app->request->queryParams;
$columns=[
    ['attribute' => 'unit_status','filterInputOptions' =>['title' => 'Filter By Task Status','prompt' => ' '],'label'=>'Status','headerOptions'=>['title'=>'Task Status','class'=>'taskstatus-width','id'=>'team_task_global_status'], 'contentOptions' => ['class' => 'first-td text-center word-break taskstatus-width','headers'=>'team_task_global_status'],'filterOptions'=>['headers'=>'team_task_global_status','class' => 'global-status-width'],'format' => 'raw', 'value' => function ($model) { return (new Tasks)->imageHelperTeamsql($model,0,0,0);},'filterType'=>$filter_type['unit_status'],'filterWidgetOptions'=>$filterWidgetOption['unit_status']],
    ['attribute' => 'id','label'=>'Task / Project #','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Task #'],'headerOptions'=>['title'=>'Task / Project #','id'=>'team_task_task','class' => 'projectno-width'], 'contentOptions' => ['class' => 'first-td word-break text-center projectno-width','headers'=>'team_task_task'],'filterOptions'=>['headers'=>'team_task_task'],'value' =>  function ($model)  {
        
                $url="index.php?r=track/index&taskid=".$model['task_id']."&case_id=".$model['client_case_id']."&tasks_unit_id=".$model['id'];
				if((new User)->checkAccess(4.03)){
				    return Html::a($model['id'], $url, ["style" => "color:#167FAC","title"=>"Task #".$model['id']])." / ".$model['task_id'];
                }else if((new User)->checkAccess(5.02)){
                    $url="index.php?r=track/index&taskid=".$model['task_id']."&team_id=".$model['team_id']."&team_loc=".$model['team_loc']; 
                    return Html::a($model['id'], $url, ["style" => "color:#167FAC","title"=>"Task #".$model['id']])." / ".$model['task_id'];
                }else{
					return $model['id'].' / '.$model['task_id'];
				}
    },'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[id]" value="'.$params['TasksUnitsSearch']['id'].'">'],
    ['attribute' => 'project_name','label'=>'Project Name','format'=>'raw','headerOptions' => ['class'=> 'projectname-width','id'=>'projectname_width','title'=>'Project Name'],'contentOptions' => ['class' => 'projectname-width'],'filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name']],
    ['attribute' => 'client_id','label'=>'Client','headerOptions' => ['class'=> 'client-width','id'=>'client_id','title'=>'Client'],'contentOptions' => ['class'=> 'client-width'],'value' =>  function ($model)  {
        return $model->tasks->clientCase->client->client_name;
    },'filterType'=>$filter_type['client_id'],'filterWidgetOptions'=>$filterWidgetOption['client_id']],
    ['attribute' => 'client_case_id','label'=>'Case','headerOptions' => ['class'=> 'case-width','id'=>'client_case_id','title'=>'Case'],'contentOptions' => ['class'=> 'case-width'],'value'=>function($model){
        return $model->tasks->clientCase->case_name;
    },'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions'=>$filterWidgetOption['client_case_id']],
    ['attribute'=>'workflow_task','label'=>'Workflow Task','value'=>function($model){
        return html_entity_decode($model->servicetask->service_task);
    },'filterType'=>$filter_type['servicetask_id'],'headerOptions' => ['class'=> 'workflow-width','id'=>'workflow_task','title'=>'Workflow Task'],'contentOptions' => ['class' => 'workflow-width'],'filterWidgetOptions'=>$filterWidgetOption['servicetask_id']],
    ['attribute'=>'task_priority','label'=>'Project Priority','value'=>function($model){
        return $model->taskInstruct->taskPriority->priority;
    },'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority'],'headerOptions' => ['class'=> 'priority-width','id'=>'task_priority','title'=>'Project Priority'],'contentOptions' => ['class' => 'priority-width']],
    ['attribute'=>'task_duedate','label'=>'Project Due Date','format' => 'raw','headerOptions'=> ['class'=>'taskduedate-width','id'=>'task_duedate','title'=>'Project Due Date'],'contentOptions'=> ['class'=>'taskduedate-width'],'value' => function($model){ 
        if(strpos($model['task_date_time'],"-")!==false)
			return date('m/d/Y h:i A',strtotime($model['task_date_time']));
		else
			return $model['task_date_time'];
        //return (new Tasks)->getTaskDuedatesql($model);
    
    }
    ,'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions'=>$filterWidgetOption['task_duedate']],


];

$todocolumns=[
    ['attribute' => 'tasks_unit_id','label'=>'Task / Project #','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Task #'],'headerOptions'=>['title'=>'Task / Project #','id'=>'team_task_task', 'class' => 'projectno-width'], 'contentOptions' => ['class' => 'first-td word-break text-center projectno-width','headers'=>'team_task_task'],'filterOptions'=>['headers'=>'team_task_task'],'value' =>  function ($model)  {
        
                $url="index.php?r=track/index&taskid=".$model['task_id']."&case_id=".$model['client_case_id']."&tasks_unit_id=".$model['tasks_unit_id'];
				if((new User)->checkAccess(4.03)){
				    return Html::a($model['tasks_unit_id'], $url, ["style" => "color:#167FAC","title"=>"Task #".$model['tasks_unit_id']])." / ".$model['task_id'];
				}else if((new User)->checkAccess(5.02)){
                    $url="index.php?r=track/index&taskid=".$model['task_id']."&team_id=".$model['team_id']."&team_loc=".$model['team_loc'];
                    return Html::a($model['tasks_unit_id'], $url, ["style" => "color:#167FAC","title"=>"Task #".$model['tasks_unit_id']])." / ".$model['task_id'];
                }else{
					return $model['tasks_unit_id'].' / '.$model['task_id'];
				}
    },'filter'=>'<input type="text" class="form-control" name="TasksUnitsSearch[tasks_unit_id]" value="'.$params['TasksUnitsSearch']['tasks_unit_id'].'">'],
    ['attribute' => 'todo_project_name','label'=>'Project Name','headerOptions' => ['class'=> 'projectname-width','title' => 'Project Name'],'contentOptions' => ['class'=>'projectname-width'],'format'=>'raw','filterType'=>$todofilter_type['project_name'],'filterWidgetOptions'=>$todofilterWidgetOption['project_name']],
    ['attribute' => 'todo_client_id','label'=>'Client','headerOptions' => ['class'=> 'client-width','title' => 'Client'],'contentOptions' => ['class'=> 'client-width'],'value' =>  function ($model)  {
        return $model->tasks->clientCase->client->client_name;
    },'filterType'=>$todofilter_type['client_id'],'filterWidgetOptions'=>$todofilterWidgetOption['client_id']],
    ['attribute' => 'todo_client_case_id','label'=>'Case','headerOptions' => ['class'=> 'case-width','title' => 'Case'],'contentOptions' => ['class'=> 'case-width'],'value'=>function($model){
        return $model->tasks->clientCase->case_name;
    },'filterType'=>$todofilter_type['client_case_id'],'filterWidgetOptions'=>$todofilterWidgetOption['client_case_id']],
    ['attribute' => 'todo','label'=>'ToDo Details','format'=>'raw','filterType'=>$todofilter_type['todo'],'headerOptions'=> ['title' => 'ToDo Details','class' => 'todo-width'],'contentOptions' => ['class' => 'todo-width'], 'filterWidgetOptions'=>$todofilterWidgetOption['todo']],
    ['attribute' => 'todo_cat','label'=>'Follow-Up Category','format'=>'raw','headerOptions'=> ['class'=>'followupcat-width','title' => 'Follow-Up Category'],'contentOptions' => ['class' => 'followupcat-width'],'filterType'=>$todofilter_type['todo_cat'],'filterWidgetOptions'=>$todofilterWidgetOption['todo_cat']],
    ['attribute'=>'todo_assigned','label'=>'ToDo Assigned','format' => 'raw','headerOptions'=> ['class'=>'todoassigned-width','title' => 'ToDo Assigned'],'contentOptions'=> ['class'=>'todoassigned-width'],'value' => function($model){ 
            //return  date('m/d/Y h:i A',strtotime($model['todo_assigned']));
            return (new Options)->ConvertOneTzToAnotherTz($model['todo_assigned'], 'UTC', $_SESSION['usrTZ']);
    },'filterType'=>$todofilter_type['modified'],'filterWidgetOptions'=>$todofilterWidgetOption['modified']],


];
 ?>
<div class="row">
        <div class="col-md-12">
            <div id="page-title" role="heading" class="page-header default-header"><em class="fa fa-mouse-pointer" title="My Task Assignments"></em> <a href="javascript:void(0);" title="My Assignments" class="tag-header-red">My Assignments</a></div>
        </div>
      </div>
      <div class="row two-cols-right">
        <div class="col-xs-12 col-sm-8 col-md-9 left-side">
          <div class="right-main-container workflow-management my-assignment-tabs">
          <div id="tabs" style="display:none;">
            <ul>
                <?php if ((new User)->checkAccess(1.001)) { ?>
                   <?php// if(in_array(1, $role_type) || $roleId=='0'){ ?>
                    <li><a href="#tabs-1" title="My Task Assignments">My Task Assignments</a></li>
                    <?php //} ?>
                    <?php //if(in_array(2, $role_type) || $roleId=='0'){ ?>
                        <li><a href="#tabs-2" title="My ToDo Assignments">My ToDo Assignments</a></li>
                    <?php //} 
                } ?>
                
                
            </ul>
            <?php if ((new User)->checkAccess(1.001)) { 
					//if(in_array(1, $role_type) || $roleId=='0'){ ?>
            <div id="tabs-1">
                <fieldset class="case-project-fieldset table-responsive my-assignment-grid" style="top:1px;bottom:1px;height:100%;position:absolute">
                <style>
                .kv-expand-icon a span.ectext,
                .kv-expand-header-icon a span.ectext{
                    display: none;
                }
                #myactivetasks-grid-container,#myworkingtodos-grid-container{min-height:320px!important;}
                .my-assignment-tabs{border:0 none}
                .my-assignment-tabs .ui-tabs{padding:1px} 
                .my-assignment-tabs .case-project-fieldset .grid-view .kv-panel-pager{background:#fff;min-height:55px;padding-top:3px;padding-right:10px;}
                </style>
                <?php 
                if(isset($params['TasksUnitsSearch'])){
                    $buttonset=Html::button('All Assignments', ['class'=>'btn btn-primary all_filter ','onclick'=>'displayAllAssignments();','title'=>"All Assignments"]);
                }else{
                    $buttonset=Html::button('All Assignments', ['class'=>'btn btn-primary all_filter ','onclick'=>'displayAllAssignments();','title'=>"All Assignments",'style'=>'display:none;']);
                }
                $dynagrid = DynaGrid::begin([
                        'columns'=>$columns,
                        'storage'=>'db',
                        'theme'=>'panel-info',
                        'gridOptions'=>[
                            'id'=>'myactivetasks-grid',
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'panel'=>false,
                            'layout' => '{items}<div class="kv-panel-pager text-right">{summary}'.$buttonset.'&nbsp;{dynagridSort}{dynagrid}{pager}</div>',
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
                </fieldset>
                
             </div>  
            <?php //} if(in_array(2, $role_type) || $roleId=='0'){ ?>
             <div id="tabs-2">
                    <!--My ToDo Assignments-->
                <fieldset class="case-project-fieldset table-responsive my-assignment-grid" style="top:1px;bottom:1px;height:100%;position:absolute">
                <?php 
                if(isset($params['TasksUnitsSearch'])){
                    $todobuttonset=Html::button('All ToDo Assignments', ['class'=>'btn btn-primary all_filter ','onclick'=>'displayAllToDosAssignments();','title'=>"All ToDo Assignments"]);
                }else{
                    $todobuttonset=Html::button('All ToDo Assignments', ['class'=>'btn btn-primary all_filter ','onclick'=>'displayAllToDosAssignments();','title'=>"All ToDo Assignments",'style'=>'display:none;']);
                }
                $dynagrid = DynaGrid::begin([
                        'columns'=>$todocolumns,
                        'storage'=>'db',
                        'theme'=>'panel-info',
                        'gridOptions'=>[
                            'id'=>'myworkingtodos-grid',
                            'dataProvider' => $todoDataProvider,
                            'filterModel' => $searchModel,
                            'panel'=>false,
                            'layout' => '{items}<div class="kv-panel-pager text-right">{summary}'.$todobuttonset.'&nbsp;{dynagridSort}{dynagrid}{pager}</div>',
                            'responsiveWrap' => false,
                            'export'=>false,
                            'floatHeader'=>true,
                            'floatHeaderOptions' => ['top' => 'auto'],
                            'persistResize'=>false,
                            'resizableColumns'=>false,
                            'pjax'=>true,
                                'pjaxSettings'=>[
                                        'options'=>['id'=>'workingtodos-pajax','enablePushState' => false],
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
                        'id'=>'dynagrid-myworkingtodos',
                        ] // a unique identifier is important
                    ]);
                    if (substr($dynagrid->theme, 0, 6) == 'simple') {
                        $dynagrid->gridOptions['panel'] = false;
                    }
                    DynaGrid::end();
                    ?>
                </fieldset>
             </div>
                <?php //} 
            }?>
            </div>

          
          
          </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3 right-side">
          <div class="projects-main">
            <div class="project-block">
              <h3 class="block-title"><a href="javascript:void(0);" title="Task Assignments by Status" class="tag-header-black">Task Assignments by Status</a></h3>
              <div class="block-content">
              <div id="statuswisetaskschart" class="clsperiodchart chart-container"></div>
			  </div>
            </div>
            <div class="project-block">
              <h3 class="block-title"><a href="javascript:void(0);" title="Task Assignments by Project Priority" class="tag-header-black">Task Assignments by Project Priority</a></h3>
              <div class="block-content">
                <div id="prioritywisetaskschart" class="clsperiodchart chart-container"></div>
			  </div>
            </div>
          </div>
        </div>
      </div>
<script type="text/javascript">
 $(function() {
    $( "#tabs" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      },
      create: function(event, ui) { 
          setTimeout(function(){
            $('#tabs').show();    
          }, 200);
          
      }

    });
    <?php if(isset($params['WorkingToDos']) && $params['WorkingToDos']=='WorkingToDos'){?>
        $( "#tabs" ).tabs({ active:1});
    <?php }?>

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
        },
        tooltip: {
            pointFormat: '{point.y}'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                size: 130,
                dataLabels: {
                    enabled: true,
                    distance: 15,
                    crop: false,
                    padding: 0,
                    shadow: false,
                    connectorPadding: 0,
                    connectorWidth: 1,
                    overflow: "none",
                    format: '{point.name}: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                        width: '60px',
                        fontSize: '11px',
                        fontWeight:'normal'
                    }
                },
                events: {
                    click: function () {
                        let selected=null;
                        var options=this.options;
                        setTimeout(function() {
                            options.data.forEach(function(element) {
                                if(element && typeof element === 'object' && element.constructor === Object){
                                    if(element.selected){
                                        selected=element;
                                    }
                                }
                            }); 
                            if(selected!=null){
                                if(selected.name=="Pending"){
                                   window.location.href="index.php?r=site/index&TasksUnitsSearch[unit_status]=&TasksUnitsSearch[unit_status][]=7&TasksUnitsSearch[id]=&TasksUnitsSearch[project_name]=&TasksUnitsSearch[client_id]=&TasksUnitsSearch[client_case_id]=&TasksUnitsSearch[workflow_task]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_duedate]=";
                                }else if(selected.name=="Working Tasks"){
                                   window.location.href="index.php?r=site/index&TasksUnitsSearch[unit_status]=&TasksUnitsSearch[unit_status][]=1&TasksUnitsSearch[unit_status][]=2&TasksUnitsSearch[id]=&TasksUnitsSearch[project_name]=&TasksUnitsSearch[client_id]=&TasksUnitsSearch[client_case_id]=&TasksUnitsSearch[workflow_task]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_duedate]=";
                                }else if(selected.name=="Working ToDos"){
                                   window.location.href="index.php?r=site/index&WorkingToDos=WorkingToDos";
                                }else if(selected.name=="Not Started"){
                                   window.location.href="index.php?r=site/index&TasksUnitsSearch[unit_status]=&TasksUnitsSearch[unit_status][]=0&TasksUnitsSearch[id]=&TasksUnitsSearch[project_name]=&TasksUnitsSearch[client_id]=&TasksUnitsSearch[client_case_id]=&TasksUnitsSearch[workflow_task]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_duedate]=";
                                }
                            }
                        }, 100);
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
                    ['Working ToDos', workingtodos],
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
            data.push(["<?php echo $task['client_name'];?>", <?php echo $task['cnttasksbyclient'];?>]);
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
            pointFormat: '{point.y}'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
                point: {
					events: {
					    click: function (e) {

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

    $(window).resize(function() {
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
            data.push(["<?php echo $task->priority;?>", <?php echo $task->cnttasksbypriority;?>]);
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

        },
        tooltip: {
            pointFormat: '{point.y}'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                size: 130,
                dataLabels: {
                    enabled: true,
                    distance: 15,
                    crop: false,
                    padding: 0,
                    shadow: false,
                    connectorPadding: 0,
                    connectorWidth: 1,
                    overflow: "none",
                    format: '{point.name}: {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                        width: '60px',
                        fontSize: '11px',
                        fontWeight:'normal'
                    }
                },
                events: {
                    click: function () {
                        let selected=null;
                        var options=this.options;
                        setTimeout(function(){
                            options.data.forEach(function(element){
                                if(element && typeof element === 'object' && element.constructor === Object){
                                    if(element.selected){
                                        selected=element;
                                    }
                                }
                            }); 
                            if(selected!=null){
                                if(selected.name){
                                    window.location.href="index.php?r=site/index&TasksUnitsSearch[unit_status]=&TasksUnitsSearch[id]=&TasksUnitsSearch[project_name]=&TasksUnitsSearch[client_id]=&TasksUnitsSearch[client_case_id]=&TasksUnitsSearch[workflow_task]=&TasksUnitsSearch[task_priority]=&TasksUnitsSearch[task_priority][]="+selected.name+"&TasksUnitsSearch[task_duedate]=";
                                }
                            }
                        }, 100);
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

    $(window).resize(function() {
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
            data.push(["<?php echo $task['service_task'];?>", <?php echo $task['cnttasksbyworkflow'];?>]);
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
            pointFormat: '{point.y}'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y}',
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

    $(window).resize(function() {
		chart4.redraw();
		chart4.reflow();
    });
}
<?php if($taskstatus['pending']==0 && $taskstatus['workingtasks']==0 && $taskstatus['workingtodos']==0 && $taskstatus['notstarted'] == 0) { } else {?>    
createTasksbyStatusPieChart('');
<?php } ?>
    //createTasksbyClientPieChart('Tasks Assigned by Client');
<?php if(!empty($assignedtasksbypriority)) {?>
    createTasksbyPriorityPieChart('');
<?php }?>
    //createTasksbyWorkflowPieChart('Tasks Assigned by Workflow Task');

 });

 function displayAllAssignments(){
     window.location.href="index.php?r=site/index";
 }
 function displayAllToDosAssignments(){
    window.location.href="index.php?r=site/index&WorkingToDos=WorkingToDos";
 }

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