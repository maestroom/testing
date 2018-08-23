<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
?>
<style>
.tb-scroll{
/*    overflow-x: hidden;
    overflow-y: scroll;*/
}
</style>
<div class="table-responsive"> 
        <?=  GridView::widget([
                    'id'=>'loadpreviouscaseprojects-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'responsiveWrap' => false,
                    'layout' => '{items}<div class="kv-panel-pager"><div class="col-sm-6">{summary}</div><div class="col-sm-6 text-right">{pager}</div></div>',
                    'columns' => [
                    	// ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-projects/get-task-details','flag'=>'load-prev']),'headerOptions'=>['title'=>'Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="javascript:void(0);"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                         ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center']],
                         ['attribute' => 'id','format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Project #'], 'label'=>'Project #','headerOptions'=>['title'=>'Project #'], 'contentOptions' => ['class' => 'first-td text-center projectno-width'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },'filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
										'dropdownParent' => new JsExpression('$("#loadpreviouscaseprojects-grid")'),
		  								'ajax' =>[
		  									'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id]),
		  									'dataType' => 'json',
		  									'data' => new JsExpression('function(params) { return {q:params.term,field:"id"}; }')
		  								]]]],
                         //['attribute' => 'task_status', 'label' => 'Status', 'format' => 'html', 'filter' => Html::activeDropDownList($searchModel, 'task_status', [ 0=>'Not Started',1=>'Started',3=>'On Hold',4=>'Complete'],['class'=>'form-control','prompt' => 'Select Status', 'multiple' => true]), 'value' => function ($model) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);} ],
                          ['attribute' => 'project_name', 'filterInputOptions' => ['title' => 'Filter By Project Name'], 'label' => 'Project Name','headerOptions'=>['title'=>'Project Name'],'contentOptions' => ['class' => 'projectname-width'], 'filterType'=>GridView::FILTER_SELECT2,
		  				'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
							'dropdownParent' => new JsExpression('$("#loadpreviouscaseprojects-grid")'),
								'ajax' =>[
									'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id]),
									'dataType' => 'json',
									'data' => new JsExpression('function(params) { return {q:params.term,field:"project_name"}; }')
								]]], 'format' => 'raw', 'filter' => '<input type="text" class="form-control" name="TaskSearch[project_name]" value="">', 'value' => function($model){ return $model->taskInstruct->project_name;}],
                    ],'export' => false,
			'floatHeader' => true,  
            'pjax' => true,
            'responsive' => false,
            'floatHeaderOptions' => ['top' => 'auto', 'enableAria' => true],
            'floatOverflowContainer' => true,
            'pjaxSettings' => [
                'options' => ['id'=>'loadpreviouscaseprojectsgrid-pajax','enablePushState' => false],
                'neverTimeout' => true,
                'beforeGrid' => '',
                'afterGrid' => ''
            ],
]); ?>
</div>
<script>
$(function(){
    $('#loadpreviouscaseprojects-grid-container').addClass('tb-scroll');
});


</script>