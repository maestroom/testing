<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression; 
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Case Projects';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
		['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-projects/get-task-details']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'load_closed_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center','headers'=>'load_closed_expand'],'filterOptions'=>['headers'=>'load_closed_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span><span class="screenreader">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
		['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'load_closed_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'load_closed_checkbox'],'filterOptions'=>['headers'=>'load_closed_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
		['attribute' => 'id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'],'filterType'=>$filter_type['id'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },'filterWidgetOptions'=>$filterWidgetOption['id'],'width' => '11%', 'label'=>'Project #','headerOptions'=>['title'=>'Project #','id'=>'load_closed_projects','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td text-center projectno-width','headers'=>'load_closed_projects'],'filterOptions'=>['headers'=>'load_closed_projects'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); }],
		['attribute' => 'task_status', 'label' => 'Status', 'format' => 'html','width'=>'10%','headerOptions'=>['title'=>'Task Status','class'=>'global-status-width','id'=>'chain_custody_task_status','scope'=>'col'],'contentOptions'=>['class'=>'word-break global-status-width text-center','headers'=>'chain_custody_task_status'],'filterOptions'=>['headers'=>'chain_custody_task_status'],'filterWidgetOptions'=>$filterWidgetOption['task_status'], 'filterType'=>$filter_type['task_status'],'format'=>'raw','value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);}],
		['attribute' => 'task_duedate', 'format' => 'html', 'label' => 'Due Date', 'contentOptions' => ['class' => 'global-datetime-width word-break','headers'=>'load_closed_projects_due_date'],'filterOptions'=>['headers'=>'load_closed_projects_due_date'], 'filterInputOptions' => ['title' => 'Filter By Project Due Date','class'=>'form-control'],'filter'=>'', 'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions' => $filterWidgetOption['task_duedate'],'headerOptions'=>['title'=>'Project Due Date','class'=>'global-datetime-width','id'=>'load_closed_projects_due_date','scope'=>'col'], 'value' => function($model){ return $model->getTaskDuedate($model); }],
		['attribute' => 'priority', 'label' => 'Priority', 'filterInputOptions' => ['title' => 'Filter By Project Priority'],'format' => 'raw', 'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority'], 'width'=>'16%','headerOptions'=>['title'=>'Project Priority','id'=>'load_closed_project_priority','scope'=>'col'],'contentOptions' => ['headers'=>'load_closed_project_priority'],'filterOptions'=>['headers'=>'load_closed_project_priority'], 'value' => function($model) use($pporder){ if($pporder == $model->activeTaskInstruct->taskPriority->priority_order){return "<span class='text-danger'><strong>".$model->activeTaskInstruct->taskPriority->priority."</strong></span>"; } else { return $model->activeTaskInstruct->taskPriority->priority;} } ],
		['attribute' => 'project_name', 'label' => 'Project Name', 'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Project Name','class'=>'form-control'], 'filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name'], 'width'=>'22%','headerOptions'=>['title'=>'Project Name','id'=>'load_closed_project_name','scope'=>'col'],'contentOptions' => ['headers'=>'load_closed_project_name'],'filterOptions'=>['headers'=>'load_closed_project_name'], 'value' => function($model){ return $model->activeTaskInstruct->project_name;}],
		['attribute' => 'per_complete', 'label' => '% Complete', 'format' => 'raw', 'width'=>'20%','contentOptions'=>['class'=>'text-center word-break','headers'=>'load_closed_percentage_complete'],'filterOptions'=>['headers'=>'load_closed_percentage_complete'],'filterType'=>$filter_type['per_complete'],'filterWidgetOptions'=>$filterWidgetOption['per_complete'],'headerOptions'=>['title'=>'Percentage Complete','class'=>'text-center','id'=>'load_closed_percentage_complete','scope'=>'col'],'value' => function($model) use($case_id){ return $model->getTaskPercentageCompleted($model->id,"case",$case_id,0,0,null,array(),$model->per_complete);}],
];
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset"> 
        <form method="post" id="load-closed-projects-form" style="height:100%" autocomplete="off">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'closed-projects-grid',
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
					'options'=>['id'=>'closed-projects-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-closed-projects',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
			<?php /* GridView::widget([
            	'id'=>'closed-projects-grid',
            	'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
				'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCount'>{totalCount}</strong> items</div>",
				'columns' => [
					
				],
				'export'=>false,
				'floatHeader'=>($dataProvider->getTotalCount()>0)?true:false,
				'floatHeaderOptions' => ['top' => 'auto'],
	            'responsive'=>false,
				'responsiveWrap' => false,
				'pjax'=>true,
	            'pjaxSettings'=>[
	                'options'=>['id'=>'closed-projects-pajax','enablePushState' => false],
	                'neverTimeout'=>true,
	                'beforeGrid'=>'',
	                'afterGrid'=>'',
	            ],
			]);*/ ?>
		</div> 
        </form>
	</fieldset>
	<div id="bulkreopen-closed-dialog" class="bulkreopentasks hide">
		<fieldset>
			<legend class="sr-only">Bulk ReOpen</legend>
			<div class="custom-inline-block-width">
				<input aria-setsize="2" aria-posinset="1" type="radio" name="bulkreopen" class="bulkreopen" value="selectedtask" id="rdo_selectedreopen"><label for="rdo_selectedreopen">Selected <span id="selectedtask">0</span> Projects in Grid</label>
				<input aria-setsize="2" aria-posinset="2" type="radio" name="bulkreopen" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkreopen"/><label for="rdo_bulkreopen">All Filtered <span id="alltask">0</span> Projects in Grid</label>
			</div>
		</fieldset>
	</div>
    <div class="button-set text-right">
        <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary','onclick'=>'loadProjects();'])?>
     </div>
</div>
<script>
    var $grid = $('#closed-projects-pajax');
    $grid.css('visibility','hidden');
    $(document).ready(function(){
		$grid.css('visibility','visible');
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
</script>    
<noscript></noscript>
