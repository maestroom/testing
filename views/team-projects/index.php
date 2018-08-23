<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//print_r($searchModel['project_name']);
$this->title = 'Team Projects';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
		['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['team-projects/get-task-details','team_id'=>$team_id,'team_loc'=>$team_loc]),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th word-break','id'=>'team_projects_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center word-break','headers'=>'team_projects_expand'],'filterOptions'=>['headers'=>'team_projects_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
        ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th word-break','id'=>'team_projects_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center word-break','headers'=>'team_projects_checkbox'],'filterOptions'=>['headers'=>'team_projects_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
        ['attribute' => 'task_id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'], 'label' => 'Project #', 'headerOptions'=>['title'=>'Project #','id'=>'team_projects_project','scope'=>'col','class'=>'project_no'], 'contentOptions' => ['class' => 'first-td word-break text-center projectno-width project_no','headers'=>'team_projects_project'],'filterOptions'=>['headers'=>'team_projects_project','class'=>'project_no'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },
		'filter'=>'<input type="text" class="form-control filter_number_only" name="TeamSearch[task_id]" value="'.$params['TeamSearch']['task_id'].'">'
		//'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']
		],
        ['attribute' => 'task_status', 'label' => 'Project Status', 'format' => 'html','filterInputOptions' => ['title' => 'Filter By Project Status'],'headerOptions'=>['title'=>'Project Status','class'=>'word-break global-status-width project_status','id'=>'team_projects_status','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td text-center global-status-width project_status','headers'=>'team_projects_status'],'filterOptions'=>['headers'=>'team_projects_status','class'=>'project_status'], 'format' => 'raw',  'filterType'=>$filter_type['task_status'],'filterWidgetOptions'=>$filterWidgetOption['task_status'],  'value' => function ($model) use($team_id,$team_loc,$is_accessible_submodule_tracktask) { return $model->imageHelperTeam($model,$is_accessible_submodule_tracktask,$team_id,$team_loc);} ],
        /* IRT 168  Team Status, Team % */
        ['attribute' => 'team_status', 'header' => 'Team&nbsp;Status', 'format' => 'html','filter' => '', 'label' => 'Team Status','headerOptions'=>['title'=>'Team Status','class'=>'word-break global-status-width team_status','id'=>'team_status','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td text-center team-status-width team_status','headers'=>'team_status'], 'format' => 'raw', 'value' => function ($model) use($team_id,$team_loc,$is_accessible_submodule_tracktask) { return $model->imageHelperTeamStatus($model, $team_id, $team_loc); } ],
        /* End IRT 168 */
		[
			'attribute' => 'client_id',
			'filterInputOptions' => ['title' => 'Filter By Client'],
			'label' => 'Client',
			'headerOptions'=>['title'=>'Client','id'=>'team_projects_clients','scope'=>'col','class'=>'client_name'],
			'contentOptions' => ['class' => 'word-break client-name-width client_name','headers'=>'team_projects_case'],
			'filterOptions'=>['headers'=>'team_projects_case','class'=>'client_name'],
			'filterType'=>$filter_type['client_id'],
			'filterWidgetOptions'=>$filterWidgetOption['client_id'],
			'format' => 'raw',
			'filter'=>'<input type="text" class="form-control" name="TeamSearch[client_id]" value="">',
			'value' => function($model) {return $model->client_name;}
		],
        ['attribute' => 'client_case_id', 'filterInputOptions' => ['title' => 'Filter By Case'], 'label' => 'Case','headerOptions'=>['title'=>'Case','id'=>'team_projects_clients_case','scope'=>'col','class'=>'case_name'],'contentOptions' => ['class' => 'word-break case-name-width case_name','headers'=>'team_projects_client_case'],'filterOptions'=>['headers'=>'team_projects_client_case','class'=>'case_name'], 'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions'=>$filterWidgetOption['client_case_id'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TeamSearch[client_case_id]" value="">','value' => function($model) { return $model->clientcase_name; }],

		['attribute' => 'task_duedate', 'label' => 'Project Due Date', 'filterInputOptions' => ['title' => 'Filter By Project Due Date','class'=>'form-control'], 'format' => 'raw','headerOptions'=>['title'=>'Project Due Date','class'=>'global-datetime-width word-break project_duedate','id'=>'team_projects_duedate','scope'=>'col'], 'contentOptions' => ['class' => 'global-datetime-width word-break project_duedate','headers'=>'team_projects_duedate'],'filterOptions'=>['headers'=>'team_projects_duedate','class'=>'project_duedate'],'filter'=>'<input type="text" class="form-control" name="TeamSearch[task_duedate]" value="">', 'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions'=>$filterWidgetOption['task_duedate'], 'value' => function($model){ return $model->getTaskDuedateobj($model); }],

		['attribute' => 'priority', 'label' => 'Project Priority', 'filterInputOptions' => ['title' => 'Filter By Project Priority'],'headerOptions'=>['title'=>'Project Priority','id'=>'team_projects_priority','scope'=>'col','class'=>'project_priority'],'contentOptions' => ['class' => 'priority-width word-break project_priority','headers'=>'team_projects_priority'],'filterOptions'=>['headers'=>'team_projects_priority','class'=>'project_priority'], 'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TeamSearch[priority]" value="">','value' => function($model) use($pporder){if(isset($model->porder) && $pporder == $model->porder){ return "<span class='text-danger'><strong>".$model->pname."</strong></span>"; } else {return $model->pname; }}],

		['attribute' => 'team_priority', 'label' => 'Team Priority', 'filterInputOptions' => ['title' => 'Filter By Project Status'],'headerOptions'=>['title'=>'Team Priority','id'=>'team_projects_tpriority','scope'=>'col','class'=>'team_priority'], 'contentOptions' => ['class' => 'tpriority-width word-break team_priority','headers'=>'team_projects_tpriority'],'filterOptions'=>['headers'=>'team_projects_tpriority','class'=>'team_priority'], 'format' => 'html', 'filterType'=>$filter_type['team_priority'],'filterWidgetOptions'=>$filterWidgetOption['team_priority'], 'value' => function($model)use($team_id,$team_loc){
            //$priority=$model->getTeamPriorities($model->id,$team_id,$team_loc);
            return $model->tasks_priority_name;
			//$priority;
            // return $model->tasksTeams->teamPriority->tasks_priority_name;
        }],
        ['attribute' => 'project_name', 'filterInputOptions' => ['title' => 'Filter By Project Name'], 'label' => 'Project Name','headerOptions'=>['title'=>'Project Name','id'=>'team_projects_name','scope'=>'col','class'=>'project_name'],'contentOptions' => ['class' => 'project_name word-break','headers'=>'team_projects_name'],'filterOptions'=>['headers'=>'team_projects_name','class'=>'project_name'], 'filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TeamSearch[project_name]" value="'.$params['TeamSearch']['project_name'].'">', 'value' => function($model){ return $model->project_name;}],

		['attribute' => 'per_complete', 'label' => 'Project %','headerOptions'=>['title'=>'Project Complete %','id'=>'team_projects_complete','scope'=>'col','class'=>'word-break per_complete'], 'contentOptions' => ['class' => 'percomplete-width word-break text-center per_complete','headers'=>'team_projects_complete'],'filterOptions'=>['headers'=>'team_projects_complete','class'=>'per_complete'], 'filterInputOptions' => ['onkeypress' => 'return isNumber(event);'],'filterType'=>$filter_type['per_complete'],'filterWidgetOptions'=>$filterWidgetOption['per_complete'], 'format' => 'raw', 'value' => function($model) use($team_id,$team_loc){ return $model->getTaskPercentageCompleted($model->id,"team",'',$team_id,$team_loc,null,array(), $model->per_complete,$model->task_status);}],
        /* IRT 168  Team Status, Team % */
        ['attribute' => 'team_per_complete', 'label' => 'Team %', 'headerOptions' =>    ['title'=>'Team Complete %','id'=>'team_per_complete','scope'=>'col','class'=>'word-break team_per_complete'], 'contentOptions' => ['class' => 'team-percomplete-width word-break text-center team_per_complete','headers'=>'team_per_complete'],'filterOptions'=>['headers'=>'team_per_complete', 'class'=>'team_per_complete'], 'filterInputOptions' => ['onkeypress' => 'return isNumber(event);'],'filterType'=>$filter_type['team_per_complete'],'filterWidgetOptions'=>$filterWidgetOption['team_per_complete'], 'format' => 'raw', 'value' => function($model) use($team_id,$team_loc){ return $model->getTeamPercentageCompleted($model->id,"team",'',$team_id,$team_loc,null,array(), $model->team_per_complete,$model->task_status);}],
	/* End IRT 168 */
];
//echo "<pre>",print_r($filterWidgetOption),"</pre>";die;
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset caseproject-unique-fildset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
		    'columns'=>$columns,
		    'storage'=>'db',
		    'theme'=>'panel-info',
		    'gridOptions'=>[
		        'id'=>'teamprojects-grid',
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
							'options'=>['id'=>'teamprojects-pajax','enablePushState' => false],
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
		    'id'=>'dynagrid-teamprojects',
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
                    'columns' => [

                    ],'export'=>false,
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
						'options' => ['class'=>'pagination'],   // set clas name used in ui list of pagination
						'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
						'nextPageLabel' => 'Next',   // Set the label for the "next" page button
						'firstPageLabel' => 'First',   // Set the label for the "first" page button
						'lastPageLabel' => 'Last',    // Set the label for the "last" page button
						'nextPageCssClass' => 'next',    // Set CSS class for the "next" page button
						'prevPageCssClass' => 'prev',    // Set CSS class for the "previous" page button
						'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
						'lastPageCssClass' => 'last',    // Set CSS class for the "last" page button
						'maxButtonCount' => 5,    // Set maximum number of page buttons that can be displayed
					],
					'responsive'=>false,
					'floatOverflowContainer'=>true,
                ]); */?>
</div>
</fieldset>
<div class="button-set text-right" style="min-height:56px;">
        <?php $current_url = Url::current(); ?>
        <?php $allprojects_url = Url::toRoute(['team-projects/index', 'team_id' => $team_id,'team_loc'=>$team_loc]); ?>
        <?php if($current_url!=$allprojects_url) { ?>
           <?=  Html::button('All Projects',['title'=>"All Projects",'class' => 'btn btn-primary all_filter', 'onclick' => 'location.href="'.$allprojects_url.'"']) ?>
        <?php } else { ?>
           <?= Html::button('All Projects',['title'=>"All Projects",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'location.href="'.$allprojects_url.'"']) ?>
        <?php } ?>
     </div>
</div>
<script>
var $grid = $('#teamassigneduser-pajax');
$grid.css('visibility','hidden');
$('#team_id').val('<?php echo $team_id;?>');
$('#team_loc').val('<?php echo $team_loc;?>');
$(document).ready(function(){
	$grid.css('visibility','visible');
});
function readComments(task_id,team_id,team_loc,comment_type,comment_id)
{
 var comment_url=httpPath+"team-projects/post-comment&task_id="+task_id+"&team_id="+team_id+"&team_loc="+team_loc;
 var Url=httpPath+"case-projects/readcomment/";
  $.ajax({
  		 url: Url,
  		 type:"post",
  		 data:{'task_id':task_id},
  		 cache: false,
  		 dataType:'html',
  		 success:function(data){
  					//if (data != "") {
  					   //if(comment_type==1)//1 is use for instruction comment type.
  					   window.location.href= comment_url;
  					//}
  				}
  });
 }
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
	$form.find('[data-krajee-sortable]').each(function (){
		var $el = $(this);
		if ($el.data('sortable')) {
			$el.sortable('destroy');
		}
		$el.sortable(window[$el.attr('data-krajee-sortable')]);
	});
});
/*dyangird setting*/
</script>
<noscript></noscript>
