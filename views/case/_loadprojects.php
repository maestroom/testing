<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
                <?= GridView::widget([
                    'id'=>'caseprojects-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
                    'columns' => [
                         ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case/get-task-details']),'headerOptions'=>['title'=>'Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row'], 'expandIcon' => '<a href="#"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                         ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row']],
                         ['attribute' => 'id','format' => 'raw', 'label'=>'Project#','value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },'filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  								'ajax' =>[
		  									'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
		  									'dataType' => 'json',
		  									'data' => new JsExpression('function(params) { return {q:params.term,field:"id"}; }')
		  								]]]],
                         //['attribute' => 'task_status', 'label' => 'Status', 'format' => 'html', 'filter' => Html::activeDropDownList($searchModel, 'task_status', [ 0=>'Not Started',1=>'Started',3=>'On Hold',4=>'Complete'],['class'=>'form-control','prompt' => 'Select Status', 'multiple' => true]), 'value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);} ],
                         ['attribute' => 'task_status', 'label' => 'Status', 'format' => 'html', 'filter' => [ 0=>'Not Started',1=>'Started',3=>'On Hold',4=>'Complete'], 'value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);} ],
                         ['attribute' => 'task_duedate', 'format' => 'html', 'label' => 'Date Due','filter'=>'<input type="text" class="form-control" name="TaskSearch[task_duedate]" value="">', 'filterType'=>GridView::FILTER_DATE, 'value' => function($model){ return $model->getTaskDuedate($model); }],                         
                         ['attribute' => 'priority', 'label' => 'Priority','filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  								'ajax' =>[
		  									'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
		  									'dataType' => 'json',
		  									'data' => new JsExpression('function(params) { return {q:params.term,field:"priority"}; }')
		  								]]], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[priority]" value="">','value' => function($model){ return $model->taskInstruct->taskPriority->priority;}],
                         ['attribute' => 'project_name', 'label' => 'Project Name','filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  								'ajax' =>[
		  									'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
		  									'dataType' => 'json',
		  									'data' => new JsExpression('function(params) { return {q:params.term,field:"project_name"}; }')
		  								]]], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[project_name]" value="">', 'value' => function($model){ return $model->taskInstruct->project_name;}],
                         ['attribute' => 'percentage_complete', 'label' => '%', 'format' => 'raw', 'value' => function($model){ return $model->getTaskPercentageCompleted($model->id,"case");}],
                         
                        
			
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
                ]); ?>
</div>
</fieldset>
    <div class="button-set text-right">
           <?= Html::button('Bulk Assign',['title'=>"Bulk Assign",'class' => 'btn btn-primary'])?>
           <?= Html::button('Cancelled Projects',['title'=>"Cancelled Projects",'class' => 'btn btn-primary', 'onclick'=>'loadCancelledProjects();'])?>
           <?= Html::button('Closed Projects',['title'=>"Closed Projects",'class' => 'btn btn-primary', 'onclick'=>'loadClosedProjects();'])?>
           <?= Html::button('Saved Projects',['title'=>"Saved Projects",'class' => 'btn btn-primary'])?>
           <?= Html::button('Add New Project', ['title'=>"Add New Project",'class' => 'btn btn-primary','onclick'=>'addMedia();'])?>
     </div>
</div>
<script>
$('#case_id').val('<?php echo $case_id;?>');
</script>
<noscript></noscript>

