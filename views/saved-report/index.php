<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\web\JsExpression;
use app\models\Options;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ReportsUserSavedSSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports User Saved';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
	['class' => '\kartik\grid\ExpandRowColumn','format'=>'raw', 'detailUrl' => Url::to(['saved-report/get-report-details']),'headerOptions'=>['scope'=>'col','title'=>'Expand/Collapse All','class'=>'first-th', 'id'=>'global_project_expand','aria-label'=>'Reports Expand/Collapse All'], 'filterOptions'=>['headers'=>'global_project_expand'], 'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'global_project_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"><span class="not-set">Expand</span></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Row"><span class="glyphicon glyphicon-minus"><span class="not-set">Collapse</span></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => '\kartik\grid\CheckboxColumn','rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['scope'=>'col','title'=>'Select All/None','class'=>'first-th', 'id'=>'global_project_check', 'class' => 'global_project_check'], 'filterOptions'=>['headers'=>'global_project_check'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'global_project_check'],'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_saved_report_'.$key, 'value' => HTML::decode($model->custom_report_name)];},'order'=>DynaGrid::ORDER_FIX_LEFT],
	['attribute' => 'id', 'filterInputOptions' => ['title' => 'Filter Report #'], 'label' => 'Report #','headerOptions'=>['scope'=>'col','title'=>'Report #','class'=>'GPclient-case-width-th','id' => 'report_id'], 'contentOptions' => ['style' => 'width:10%','class' => 'text-left GPclient-case-width','headers'=>'report_id'], 'filterOptions'=>['headers'=>'report_id'], 'format' => 'raw','value' => function ($model) { return $model->id; }, 'filterType'=>GridView::FILTER_SELECT2,'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['minimumInputLength' => 1,'ajax' =>['url' => Url::toRoute(['saved-report/ajax-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"id"}; }')]]],],
	['attribute' => 'report_type_id',  'filterInputOptions' => ['title' => 'Filter By Report Type'], 'headerOptions'=>['scope'=>'col','title'=>'Type','class'=>'GPclient-case-width-th','id' => 'report_type_id'], 'contentOptions' => ['style' => 'width:20%','class' => 'text-left GPclient-case-width','headers'=>'report_type_id'], 'filterOptions'=>['headers'=>'report_type_id'], 'format' => 'raw','value' => function ($model) {
		return $model->reportType->report_type;
	}, 'filterType'=>GridView::FILTER_SELECT2,'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['ajax' =>['url' => Url::toRoute(['saved-report/ajax-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"report_type_id"}; }')]]],],
	['attribute' => 'report_format_id', 'filterInputOptions' => ['title' => 'Filter By Report Format'], 'headerOptions'=>['scope'=>'col','title'=>'Format','class'=>'GPclient-case-width-th','id' => 'report_format_id'], 'contentOptions' => ['style' => 'width:12%','class' => 'text-left GPclient-case-width','headers'=>'report_format_id'], 'filterOptions'=>['headers'=>'report_format_id'], 'format' => 'raw','value' => function ($model)use($reportsReportFormat) { 
		return $model->reportFormat->report_format;
	}, 'filterType'=>GridView::FILTER_SELECT2,'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['ajax' =>['url' => Url::toRoute(['saved-report/ajax-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"report_format_id"}; }')]]],],
	//['attribute' => 'custom_report_name', 'filterInputOptions' => ['title' => 'Filter By Report Name'], 'headerOptions'=>['scope'=>'col','title'=>'Name','class'=>'GPclient-case-width-th','id' => 'report_name'], 'contentOptions' => ['style' => 'width:15%','class' => 'text-left GPclient-case-width','headers'=>'report_name'], 'filterOptions'=>['headers'=>'report_name'], 'format' => 'raw','value' => function ($model) { return Html::decode($model->custom_report_name); }, 'filterType'=>GridView::FILTER_SELECT2,'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['ajax' =>['url' => Url::toRoute(['saved-report/ajax-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"custom_report_name"}; }')]]],],
	['attribute' => 'custom_report_name', 'filterInputOptions' => ['title' => 'Filter By Report Name','class'=>'form-control'], 'headerOptions'=>['scope'=>'col','title'=>'Name','class'=>'GPclient-case-width-th','id' => 'report_name'], 'contentOptions' => ['style' => 'width:15%','class' => 'text-left GPclient-case-width','headers'=>'report_name'], 'filterOptions'=>['headers'=>'report_name'], 'format' => 'raw','value' => function ($model) { return Html::decode($model->custom_report_name); },'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['ajax' =>['url' => Url::toRoute(['saved-report/ajax-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"custom_report_name"}; }')]]],],
	['attribute' =>'custom_report_description','headerOptions'=>['title'=>'Description'],'value'=>function($model){ return Html::decode($model->custom_report_description);}],
	['attribute' => 'created_by', 'filterInputOptions' => ['title' => 'Filter By Created By'], 'label' => 'Created By','headerOptions'=>['scope'=>'col','title'=>'Created By','class'=>'GPclient-case-width-th','id' => 'report_created_by'], 'contentOptions' => ['style' => 'width:15%','class' => 'text-left GPclient-case-width','headers'=>'report_created_by'], 'filterOptions'=>['headers'=>'report_created_by'], 'format' => 'raw','value' => function ($model) { 
		return $model->createdUser->usr_first_name." ".$model->createdUser->usr_lastname;
		//$res = $model->get_user_fullname($model->created_by); return $res[$model->created_by]; 
	}, 'filterType'=>GridView::FILTER_SELECT2,'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['ajax' =>['url' => Url::toRoute(['saved-report/ajax-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"created_by"}; }')]]],],
];
?>
<div class="right-main-container" id="saved_report_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
		<div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'report-user-saved-grid',
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
					'options'=>['id'=>'report-user-saved-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-report-user-saved',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
			<?php /*GridView::widget([
					'id' => 'report-user-saved-grid',
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
					'columns' => [
						 
					],
					'export'=>false,
					'floatHeader'=>true, 
					'floatHeaderOptions' => ['top' => 'auto'],
					'persistResize'=>false,
					'resizableColumns'=>false,
					'pjax'=>true,
					'pjaxSettings'=>[
						'options'=>['id'=>'report-user-saved-grid-pajax','enablePushState' => false],
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
						'maxButtonCount' => 5,    // Set maximum number of page buttons that can be displayed
					],
					'responsive'=>false,    
					'floatOverflowContainer'=>true,
				]); */?>
		</div>
	</fieldset>
	<div class="button-set text-right">
		<?php $allReports_url = Url::toRoute(['saved-report/index']); ?>
		<?= Html::button('All Reports', ['title'=>"All Reports",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'location.href="'.$allReports_url.'"'])?>
	</div>
</div>
<script>
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
