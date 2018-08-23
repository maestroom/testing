<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\Options;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Saved Filter';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
	['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['global-projects/get-details']),'headerOptions'=>['scope'=>'col','title'=>'Expand/Collapse All','class'=>'first-th', 'id'=>'global_project_expand'], 'filterOptions'=>['headers'=>'global_project_expand'], 'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'global_project_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['scope'=>'col','title'=>'Select All/None','class'=>'first-th', 'id'=>'global_project_check'], 'filterOptions'=>['headers'=>'global_project_check'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'global_project_check'],'order'=>DynaGrid::ORDER_FIX_LEFT],
	['attribute' => 'id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'], 'label'=>'Project #','headerOptions'=>['scope'=>'col','title'=>'Project #','class'=>'projectno-width-th','id'=>'global_project_id'],'filterOptions'=>['headers'=>'global_project_id'],'contentOptions' => ['class' => 'word-break text-center projectno-width', 'headers'=>'global_project_id'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },
	'filter'=>'<input type="text" class="form-control filter_number_only" name="TaskSearch[id]" value="'.$params['TaskSearch']['id'].'">'
	//'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']
	],
	['attribute' => 'task_status', 'filterInputOptions' => ['title' => 'Filter By Project Status'], 'label' => 'Status','headerOptions'=>['scope'=>'col','title'=>'Project Status','class'=>'global-status-width', 'id' => 'global_task_status'], 'contentOptions' => ['class' => 'text-center word-break global-status-width first-td','headers'=>'global_task_status'], 'filterOptions'=>['headers'=>'global_task_status','prompt'=>''], 'format' => 'html', 'filterType'=>$filter_type['task_status'],'filterWidgetOptions'=>$filterWidgetOption['task_status'], 'value' => function ($model) { return $model->imageGlobalProjectHelperCase($model,$is_accessible_submodule_tracktask);} ],
	//['attribute' => 'client_case_id', 'filterInputOptions' => ['title' => 'Filter By Client Case'], 'label' => 'Client - Case','headerOptions'=>['scope'=>'col','title'=>'Client - Case','class'=>'GPclient-case-width-th','id' => 'global_client_case_id'], 'contentOptions' => ['class' => 'word-break text-left GPclient-case-width','headers'=>'global_client_case_id'], 'filterOptions'=>['headers'=>'global_client_case_id'], 'format' => 'raw','value' => function ($model) { return $model->client_name." - ".$model->clientcase_name/*$model->clientCase->client->client_name." - ".$model->clientCase->case_name*/;}, 'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions'=>$filterWidgetOption['client_case_id']],
	['attribute' => 'client_id', 'filterInputOptions' => ['title' => 'Filter By Client'], 'label' => 'Client','headerOptions'=>['title'=>'Client','id'=>'team_projects_clients','scope'=>'col'],'contentOptions' => ['class' => 'word-break projectname-width','headers'=>'team_projects_case'],'filterOptions'=>['headers'=>'team_projects_case'], 'filterType'=>$filter_type['client_id'],'filterWidgetOptions'=>$filterWidgetOption['client_id'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TeamSearch[client_id]" value="">','value' => function($model) { return $model->client_name;/*$model->clientCase->client->client_name*/;}],
	['attribute' => 'client_case_id', 'filterInputOptions' => ['title' => 'Filter By Client Case'], 'label' => 'Case','headerOptions'=>['scope'=>'col','title'=>'Case','class'=>'GPclient-case-width-th','id' => 'global_client_case_id'], 'contentOptions' => ['class' => 'word-break text-left GPclient-case-width','headers'=>'global_client_case_id'], 'filterOptions'=>['headers'=>'global_client_case_id'], 'format' => 'raw','value' => function ($model) { return $model->clientcase_name/*$model->clientCase->case_name*/;}, 'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions'=>$filterWidgetOption['client_case_id']],
	['attribute' => 'project_name', 'filterInputOptions' => ['title' => 'Filter By Project Name'], 'label' => 'Project Name','headerOptions'=>['scope'=>'col','title'=>'Project Name','class'=>'projectname-width-th', 'id' => 'global_project_name'], 'filterOptions'=>['headers'=>'global_project_name'], 'contentOptions' => ['class' => 'projectname-width word-break','headers'=>'global_project_name'], 'filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[project_name]" value="'.$params['TaskSearch']['project_name'].'">', 'value' => function($model){ return $model->project_name;}],
	['attribute' => 'priority','format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Project Priority'], 'label' => 'Priority','headerOptions'=>['scope'=>'col','title'=>'Project Priority','class'=>'priority-width-th', 'id' => 'global_priority'], 'filterOptions'=>['headers'=>'global_priority'], 'contentOptions' => ['class' => 'priority-width word-break','headers'=>'global_priority'], 'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[priority]" value="">','value' => function($model) use ($pporder) { if($pporder == $model->porder) { return  "<span class='text-danger'><strong>".$model->pname."</strong></span>";} else { return $model->pname; }}],
	//['attribute' => 'task_duedate', 'filterInputOptions' => ['title' => 'Filter By Project Due Date','class'=>'form-control'], 'format' => 'html', 'label' => 'Due Date','headerOptions'=>['scope'=>'col','title'=>'Project Due Date','class'=>'global-datetime-width', 'id' => 'global_task_duedate'], 'filterOptions'=>['headers'=>'global_task_duedate'], 'contentOptions' => ['class' => 'word-break global-datetime-width', 'headers'=>'global_task_duedate'],'filter'=>'<input type="text" class="form-control" name="TaskSearch[task_duedate]" value="">', 'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions'=>$filterWidgetOption['task_duedate'], 'value' => function($model){ return $model->getTaskDuedateobj($model); }],
];
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <form method="post" id="global-filtered-form" style="height:100%" autocomplete="off">
        <div class="table-responsive" >
                <?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'globalproject-saved-grid',
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
				'options'=>['id'=>'globalproject-saved-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-globalproject-saved-filter',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
                <?php /*GridView::widget([
                    'id'=>'globalproject-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
                    'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCount'>{totalCount}</strong> items</div>",
                    'responsiveWrap' => false,
                    'columns' => [
                        
		  			],
                    'export'=>false,
					'floatHeader'=>true, 
					'floatHeaderOptions' => ['top' => 'auto'],
					'persistResize'=>false,
					'resizableColumns'=>false,
					'pjax'=>true,
                        'pjaxSettings'=>[
								'options'=>['id'=>'globalprojectgrid-pajax','enablePushState' => false],
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
        </form>
</fieldset>
    <div class="button-set text-right">
    	 <?= Html::a('All Projects', null,['href'=>Url::toRoute(['global-projects/filter-option','filter_id'=>$filter_id]),'class'=>'btn btn-primary all_filter','title'=>"All Projects",'style'=>'display:none;']) ?>
    	 <?= Html::a('Back', null, ['href'=>'javascript:void(0)','class'=>'btn btn-primary','title'=>"Back",'onclick'=>'BackToSaveFilter('.$filter_id.');']) ?>
    </div>
</div>
<script>
var $grid = $('#globalproject-saved-pajax');
$grid.css('visibility','hidden');
$(document).ready(function($){
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
