<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Projects';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
		['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-projects/get-task-details']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'canceled_projects_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td word-break text-center','headers'=>'canceled_projects_expand'],'filterOptions'=>['headers'=>'canceled_projects_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="screenreader">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse Row', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
        ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'canceled_projects_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'canceled_projects_checkbox'],'filterOptions'=>['headers'=>'canceled_projects_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
        ['attribute' => 'id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'],'filterType'=>$filter_type['id'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },'filterWidgetOptions'=>$filterWidgetOption['id'], 'label'=>'Project #','headerOptions'=>['title'=>'Project #','id'=>'canceled_projects_id','scope'=>'col'], 'contentOptions' => ['class' => 'word-break text-center projectno-width','headers'=>'canceled_projects_id'],'filterOptions'=>['headers'=>'canceled_projects_id'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); }],
		['attribute' => 'task_status','filterWidgetOptions'=>$filterWidgetOption['task_status'], 'label' => 'Status', 'format' => 'html',  'headerOptions'=>['title'=>'Project Status','class'=>'global-status-width','id'=>'canceled_projects_status','scope'=>'col'],'contentOptions'=>['class'=>'global-status-width word-break text-center','headers'=>'canceled_projects_status'],'filterOptions'=>['headers'=>'canceled_projects_status'],'filterType'=>$filter_type['task_status'],'format'=>'raw','value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);} ],
		['attribute' => 'task_cancel_reason', 'label' => 'Cancel Reason', 'filterInputOptions' => ['title' => 'Filter By Cancel Reason', 'class'=>'form-control'], 'format' => 'raw', 'headerOptions'=>['title'=>'Project Cancel Reason','id'=>'canceled_projects_reason','scope'=>'col'],'contentOptions'=>['class'=>'word-break project-cancel-width','headers'=>'canceled_projects_reason'],'filterOptions'=>['headers'=>'canceled_projects_reason']],
		['attribute' => 'task_duedate', 'format' => 'html', 'label' => 'Due Date', 'contentOptions' => ['class' => 'word-break global-datetime-width','headers'=>'canceled_projects_duedate'],'filterOptions'=>['headers'=>'canceled_projects_duedate'],'filterInputOptions' => ['title' => 'Filter By Project Due Date','class'=>'form-control'],'filter'=>'', 'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions' => $filterWidgetOption['task_duedate'],'headerOptions'=>['title'=>'Project Due Date','class'=>'global-datetime-width','id'=>'canceled_projects_duedate','scope'=>'col'], 'value' => function($model){ return $model->getTaskDuedate($model); }],
		['attribute' => 'priority', 'label' => 'Priority', 'filterInputOptions' => ['title' => 'Filter By Project Priority'],'format' => 'raw', 'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority'],'headerOptions'=>['title'=>'Project Priority','id'=>'canceled_projects_priority','scope'=>'col'],'contentOptions'=>['class'=>'word-break priority-width','headers'=>'canceled_projects_priority'],'filterOptions'=>['headers'=>'canceled_projects_priority'],'value' => function($model) use ($pporder){ if($pporder == $model->activeTaskInstruct->taskPriority->priority_order){return "<span class='text-danger'><strong>".$model->activeTaskInstruct->taskPriority->priority."</strong></span>"; } else { return $model->activeTaskInstruct->taskPriority->priority;}}],
		['attribute' => 'project_name', 'label' => 'Project Name', 'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Project Name'], 'filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name'], 'headerOptions'=>['title'=>'Project Name','id'=>'canceled_projects_name','scope'=>'col'],'contentOptions'=>['class'=>'word-break project-name-width','headers'=>'canceled_projects_name'],'filterOptions'=>['headers'=>'canceled_projects_name'],'value' => function($model){ return $model->activeTaskInstruct->project_name;}],
		['attribute' => 'per_complete', 'label' => '% Complete', 'format' => 'raw','contentOptions'=>['style'=>'text-align:center;'],'headerOptions'=>['title'=>'Percentage Complete','id'=>'canceled_projects_complete','scope'=>'col'],'contentOptions'=>['class'=>'word-break percentage-width text-center','headers'=>'canceled_projects_complete'],'filterOptions'=>['headers'=>'canceled_projects_complete'],'filterType'=>$filter_type['per_complete'],'filterWidgetOptions'=>$filterWidgetOption['per_complete'],'value' => function($model) use($case_id){ return $model->getTaskPercentageCompleted($model->id,"case",$case_id,0,0,null,array(),$model->per_complete);}],	
];
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
                <?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'canceled-projects-grid',
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
					'options'=>['id'=>'canceled-projects-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-canceled-projects',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
                <?php /* GridView::widget([
                    'id'=>'canceled-projects-grid',
                    'dataProvider' => $dataProvider,
                	'filterModel' => $searchModel,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
                    'columns' => $column,
                    'export'=>false,
					'floatHeader'=>($dataProvider->getTotalCount()>0)?true:false,
                    'floatHeaderOptions' => ['top' => 'auto'],
		            'responsive'=>false,
                    'responsiveWrap' => false,
                    'floatOverflowContainer'=>true,
                    'pjax'=>true,
		            'pjaxSettings'=>[
		                'options'=>['id'=>'canceled-projects-pajax','enablePushState' => false],
		                'neverTimeout'=>true,
		                'beforeGrid'=>'',
		                'afterGrid'=>'',
		            ],
                ]); */?>
</div>
</fieldset>
    <div class="button-set text-right">
           <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary','onclick'=>'loadProjects();'])?>
     </div>
</div>
<script>
    var $grid = $('#canceled-projects-pajax');
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
