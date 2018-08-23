<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
                <?= GridView::widget([
                    'id'=>'cancelled-projects-grid',
                    'dataProvider' => $dataProvider,
                	'filterModel' => $searchModel,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
                    'columns' => [
                        ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-projects/get-task-details']),'width' => '5%','headerOptions'=>['title'=>'Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="#" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                        ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'width' => '5%','headerOptions'=>['title'=>'Select All'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center']],
                        ['attribute' => 'id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'],'filterType'=>GridView::FILTER_SELECT2,'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },
			  			'filterWidgetOptions'=>[
			  				'pluginOptions'=>[
		  						'ajax' =>[
		  							'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id, 'task_cancel'=>1]),
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,field:"id"}; }')
		  						]
			  				]
			  			],'width' => '10%', 'label'=>'Project #','headerOptions'=>['title'=>'Project #'], 'contentOptions' => ['class' => 'first-td text-center projectno-width'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); }],
						['attribute' => 'task_cancel_reason', 'label' => 'Cancel Reason', 'filterInputOptions' => ['title' => 'Filter By Cancel Reason'], 'format' => 'raw', 'filterType'=>GridView::FILTER_SELECT2,
	  					'filterWidgetOptions'=>[
	  						'pluginOptions'=>[
	  							'ajax' =>[
	  								'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id, 'task_cancel'=>1]),
	  								'dataType' => 'json',
	  								'data' => new JsExpression('function(params) { return {q:params.term,field:"task_cancel_reason"}; }')
	  							]
	  						]
	  					], 'filter'=>'<input type="text" class="form-control" name="TaskSearch[task_cancel_reason]" value="">', 'width'=>'22%','headerOptions'=>['style'=>'text-align:left;']],
						['attribute' => 'task_status', 'label' => 'Status', 'format' => 'html', 'filter' => false, 'width'=>'10%','headerOptions'=>['style'=>'text-align:left;'], 'value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);} ],
						['attribute' => 'task_duedate', 'format' => 'html', 'label' => 'Date Due', 'contentOptions' => ['class' => 'datedue-width'], 'filterInputOptions' => ['title' => 'Filter By Project Due Date'],'filter'=>'', 'filterType'=>GridView::FILTER_DATE, 'width'=>'17%','headerOptions'=>['style'=>'text-align:left;'], 'value' => function($model){ return $model->getTaskDuedate($model); }],
						['attribute' => 'priority', 'label' => 'Priority', 'filterInputOptions' => ['title' => 'Filter By Project Priority'],'format' => 'raw', 'filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  					'pluginOptions'=>[
		  						'ajax' =>[
		  							'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id, 'task_cancel'=>1]),
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,field:"priority"}; }')
		  						]
		  					]
		  				], 'filter'=>'<input type="text" class="form-control" name="TaskSearch[priority]" value="">', 'width'=>'5%','headerOptions'=>['style'=>'text-align:left;'], 'value' => function($model){ return $model->taskInstruct->taskPriority->priority; }],
						['attribute' => 'project_name', 'label' => 'Project Name', 'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Project Name'], 'filterType'=>GridView::FILTER_SELECT2,
	  					'filterWidgetOptions'=>[
	  						'pluginOptions'=>[
	  							'ajax' =>[
	  								'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id, 'task_cancel'=>1]),
	  								'dataType' => 'json',
	  								'data' => new JsExpression('function(params) { return {q:params.term,field:"project_name"}; }')
	  							]
	  						]
	  					], 'filter'=>'<input type="text" class="form-control" name="TaskSearch[project_name]" value="">', 'width'=>'15%','headerOptions'=>['style'=>'text-align:left;'], 'value' => function($model){ return $model->activeTaskInstruct->project_name;}],
						['attribute' => 'percentage_complete', 'label' => '% Complete', 'format' => 'raw', 'filter' => false, 'width'=>'17%','headerOptions'=>['style'=>'text-align:left;'],'value' => function($model){ return $model->getTaskPercentageCompleted($model->id,"case");}],
                         //['attribute' => 'comment', 'label' => 'Comment', 'format' => 'raw', 'filter' => false, 'value' => function($model){ return $model->findReadUnreadComment($model->id,$model->client_case_id);}],
                    ],
                    'export'=>false,
					'floatHeader'=>true,
                    'floatHeaderOptions' => ['top' => 'auto'],
		            'responsive'=>false,
                    'responsiveWrap' => false,
                    'pjax'=>true,
		            'pjaxSettings'=>[
		                'options'=>['id'=>'cancelled-projects-pajax','enablePushState' => false],
		                'neverTimeout'=>true,
		                'beforeGrid'=>'',
		                'afterGrid'=>'',
		            ],
                ]); ?>
</div>
</fieldset>
    <div class="button-set text-right">
           <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary','onclick'=>'loadProjects();'])?>
     </div>
</div>
