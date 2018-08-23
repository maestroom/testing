<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
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
            	'id'=>'closed-projects-grid',
            	'dataProvider' => $dataProvider,
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
				'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCount'>{totalCount}</strong> items.</div>",
				'columns' => [
					['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case/get-task-details']),'headerOptions'=>['title'=>'Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row'], 'expandIcon' => '<a href="#" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
					['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row']],
					['attribute' => 'id','format' => 'raw', 'label'=>'Project#', 'filter' => false],
					['attribute' => 'task_status', 'label' => 'Status', 'format' => 'html', 'filter' => false, 'value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);} ],
					['attribute' => 'task_duedate', 'format' => 'html', 'label' => 'Date & Time Due','filter'=>false, 'value' => function($model){ return $model->getTaskDuedate($model); }],
					['attribute' => 'priority', 'label' => 'Priority','format' => 'raw', 'filter'=>false,'value' => function($model){ return $model->taskInstruct->taskPriority->priority; }],
					['attribute' => 'percentage_complete', 'label' => '%', 'format' => 'raw', 'filter' => false, 'value' => function($model){ return $model->getTaskPercentageCompleted($model->id,"case");}],
				],'export'=>false,
				'floatHeader'=>true,    
				'pjax'=>true,
	            'responsive'=>false,
	            'floatHeaderOptions' => ['top' => 'auto'],
	            'pjaxSettings'=>[
	                'options'=>['id'=>'closed-projects-pajax','enablePushState' => false],
	                'neverTimeout'=>true,
	                'beforeGrid'=>'',
	                'afterGrid'=>'',
	            ],
			]); ?>
		</div>
	</fieldset>
	<div id="bulkreopen-closed-dialog" class="bulkreopentasks hide">
		<fieldset>
			<div class="custom-inline-block-width">
				<input type="radio" name="bulkreopen" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkreopen"/><label for="rdo_bulkreopen">All Filtered <span id="alltask">0</span> Projects in Grid</label>
				<input type="radio" name="bulkreopen" class="bulkreopen" value="selectedtask" id="rdo_selectedreopen"><label for="rdo_selectedreopen">Selected <span id="selectedtask">0</span> Projects in Grid</label>
			</div>
		</fieldset>
	</div>
    <div class="button-set text-right">
           <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary','onclick'=>'loadProjects();'])?>
     </div>
</div>
