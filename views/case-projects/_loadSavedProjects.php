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
			['class' => '\kartik\grid\ActionColumn','contentOptions' => ['class' => ' text-center third-td','headers'=>'saved_projects_actions'],'filterOptions'=>['headers'=>'saved_projects_actions'],'headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'saved_projects_actions','scope'=>'col'],'mergeHeader'=>false, 'buttons' =>  ['update' => function ($url, $model) use ($case_id) { return '<a href="javascript:void(0);" onclick="edit_saved_project('.$model->id.', '.$case_id.')" title="Edit"><em title="Edit" class="fa fa-pencil text-primary"></em><span class="screenreader">Saved Project</span></a>'; }, 'view' => function ($url, $model) { return ''; }, 'delete' => function ($url, $model) use ($case_id) { return '<a href="javascript:void(0);" onclick="removesavedprojects('.$model->id.','.$case_id.')" title="Delete" aria-label="Delete"><em title="Delete" class="fa fa-close text-primary"></em><span class="screenreader">Project</span></a>'; }],'order'=>DynaGrid::ORDER_FIX_LEFT],
			['attribute' => 'id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Instruction #'], 'label'=>'Instruction #','headerOptions'=>['title'=>'Instruction #','id'=>'saved_projects_instruction','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td word-break text-center instructionno-width','headers'=>'saved_projects_instruction'],'filterOptions'=>['headers'=>'saved_projects_instruction'], 'value' =>  function ($model) { return $model->id; }, 'filterType'=>$filter_type['id'], 'filterWidgetOptions'=>$filterWidgetOption['id']],
			['attribute' => 'service_name', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Service'], 'label'=>'Service', 'headerOptions'=>['title'=>'Service','id'=>'saved_projects_service','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td word-break workflow-width','headers'=>'saved_projects_service'],'filterOptions'=>['headers'=>'saved_projects_service'], 'value' =>  function ($model) { return $model->getServiceNameSavedProject($model->id); }, 'filterType'=>GridView::FILTER_SELECT2,
						'filterWidgetOptions'=>[
						'pluginOptions'=>[
						'ajax' =>[
							'url' => Url::toRoute(['case-projects/ajax-saved-filter', 'case_id' => $case_id]),
							'dataType' => 'json',
							'data' => new JsExpression('function(params) { return {q:params.term,field:"service_name"}; }')
			]],'options'=>['nolabel'=>true,'aria-label'=>'Service']]],
			['attribute' => 'created', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Created Date','class'=>'form-control'], 'label'=>'Created Date', 'format' => 'html', 'filter'=>'<input type="text" class="form-control" name="TaskInstructSearch[created]" value="">', 'headerOptions'=>['title'=>'Created Date','class'=>'global-datetime-width word-break','id'=>'saved_projects_created_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-datetime-width word-break word-break','headers'=>'saved_projects_created_date'],'filterOptions'=>['headers'=>'saved_projects_created_date'], 'filterType'=>$filter_type['created'],'filterWidgetOptions' => $filterWidgetOption['created'], 'value' => function ($model) { return $model->getTaskSubmittedDate($model); }],
			['attribute' => 'created_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Created By'], 'label'=>'Created By', 'headerOptions'=>['title'=>'Created By','id'=>'saved_projects_created_by','scope'=>'col'], 'contentOptions' => ['class' => 'createdby-width word-break word-break','headers'=>'saved_projects_created_by'],'filterOptions'=>['headers'=>'saved_projects_created_by'], 'value' =>  function ($model) { return $model->createdUser->usr_first_name." ".$model->createdUser->usr_lastname;}, 'filterType'=>$filter_type['created_by'],'filterWidgetOptions'=>$filterWidgetOption['created_by']],
			['attribute' => 'modified', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Modified Date','class'=>'form-control'], 'label'=>'Modified Date', 'format' => 'html', 'filter'=>'<input type="text" class="form-control" name="TaskInstructSearch[created]" value="">', 'headerOptions'=>['title'=>'Modified Date','class' => 'global-datetime-width','id'=>'saved_projects_modified_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-datetime-width  word-break','headers'=>'saved_projects_modified_date'],'filterOptions'=>['headers'=>'saved_projects_modified_date'], 'filterType'=>$filter_type['modified'],'filterWidgetOptions' =>$filterWidgetOption['modified'], 'value' =>  function ($model) { return $model->getTaskModifiedDate($model); }],
			['attribute' => 'modified_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Modified By'], 'label'=>'Modified By', 'headerOptions'=>['title'=>'Modified By','id'=>'saved_projects_modified_by','scope'=>'col'], 'contentOptions' => ['class' => 'first-td word-break modifiedby-width','headers'=>'saved_projects_modified_by'],'filterOptions'=>['headers'=>'saved_projects_modified_by'], 'value' =>  function ($model) { return $model->modifiedUser->usr_first_name." ".$model->modifiedUser->usr_lastname; }, 'filterType'=>$filter_type['modified_by'],'filterWidgetOptions'=>$filterWidgetOption['modified_by']],
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
        'id'=>'saved-projects-grid',
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
					'options'=>['id'=>'saved-projects-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-saved-projects',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
			<?php /* GridView::widget([
            	'id'=>'saved-projects-grid',
            	'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
				'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCount'>{totalCount}</strong> items</div>",
				'columns' => [
				//	['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'saved_projects_expand','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'saved_projects_expand'],'filterOptions'=>['headers'=>'saved_projects_expand']],
 					['class' => '\kartik\grid\ActionColumn','contentOptions' => ['class' => ' text-center third-td','headers'=>'saved_projects_actions'],'filterOptions'=>['headers'=>'saved_projects_actions'],'headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'saved_projects_actions','scope'=>'col'],'mergeHeader'=>false, 'buttons' =>  ['update' => function ($url, $model) use ($case_id) { return '<a href="javascript:void(0);" onclick="edit_saved_project('.$model->id.', '.$case_id.')" title="Edit"><em class="fa fa-pencil text-primary"></em></a>'; }, 'view' => function ($url, $model) { return ''; }, 'delete' => function ($url, $model) use ($case_id) { return '<a href="javascript:void(0);" onclick="removesavedprojects('.$model->id.','.$case_id.')" title="Delete"><em class="fa fa-close text-primary"></em></a>'; }]],
					['attribute' => 'id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Instruction #'], 'label'=>'Instruction #','headerOptions'=>['title'=>'Instruction #','id'=>'saved_projects_instruction','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td word-break text-center instructionno-width','headers'=>'saved_projects_instruction'],'filterOptions'=>['headers'=>'saved_projects_instruction'], 'value' =>  function ($model) { return $model->id; }, 'filterType'=>$filter_type['id'], 'filterWidgetOptions'=>$filterWidgetOption['id']],
		  			['attribute' => 'service_name', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Service'], 'label'=>'Service', 'headerOptions'=>['title'=>'Service','id'=>'saved_projects_service','scope'=>'col'], 'contentOptions' => ['class' => 'word-break first-td word-break workflow-width','headers'=>'saved_projects_service'],'filterOptions'=>['headers'=>'saved_projects_service'], 'value' =>  function ($model) { return $model->getServiceNameSavedProject($model->id); }, 'filterType'=>GridView::FILTER_SELECT2,
		  						'filterWidgetOptions'=>[
		  						'pluginOptions'=>[
		  						'ajax' =>[
		  							'url' => Url::toRoute(['case-projects/ajax-saved-filter', 'case_id' => $case_id]),
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,field:"service_name"}; }')
		  			]]]],
		  			['attribute' => 'created', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Created Date','class'=>'form-control'], 'label'=>'Created Date', 'format' => 'html', 'filter'=>'<input type="text" class="form-control" name="TaskInstructSearch[created]" value="">', 'headerOptions'=>['title'=>'Created Date','class'=>'global-datetime-width word-break','id'=>'saved_projects_created_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-datetime-width word-break word-break','headers'=>'saved_projects_created_date'],'filterOptions'=>['headers'=>'saved_projects_created_date'], 'filterType'=>$filter_type['created'],'filterWidgetOptions' => $filterWidgetOption['created'], 'value' => function ($model) { return $model->getTaskSubmittedDate($model); }],
		  			['attribute' => 'created_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Created By'], 'label'=>'Created By', 'headerOptions'=>['title'=>'Created By','id'=>'saved_projects_created_by','scope'=>'col'], 'contentOptions' => ['class' => 'createdby-width word-break word-break','headers'=>'saved_projects_created_by'],'filterOptions'=>['headers'=>'saved_projects_created_by'], 'value' =>  function ($model) { return $model->createdUser->usr_first_name." ".$model->createdUser->usr_lastname;}, 'filterType'=>$filter_type['created_by'],'filterWidgetOptions'=>$filterWidgetOption['created_by']],
		  			['attribute' => 'modified', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Modified Date','class'=>'form-control'], 'label'=>'Modified Date', 'format' => 'html', 'filter'=>'<input type="text" class="form-control" name="TaskInstructSearch[created]" value="">', 'headerOptions'=>['title'=>'Modified Date','class' => 'global-datetime-width','id'=>'saved_projects_modified_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-datetime-width  word-break','headers'=>'saved_projects_modified_date'],'filterOptions'=>['headers'=>'saved_projects_modified_date'], 'filterType'=>$filter_type['modified'],'filterWidgetOptions' =>$filterWidgetOption['modified'], 'value' =>  function ($model) { return $model->getTaskModifiedDate($model); }],
		  			['attribute' => 'modified_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Modified By'], 'label'=>'Modified By', 'headerOptions'=>['title'=>'Modified By','id'=>'saved_projects_modified_by','scope'=>'col'], 'contentOptions' => ['class' => 'first-td word-break modifiedby-width','headers'=>'saved_projects_modified_by'],'filterOptions'=>['headers'=>'saved_projects_modified_by'], 'value' =>  function ($model) { return $model->modifiedUser->usr_first_name." ".$model->modifiedUser->usr_lastname; }, 'filterType'=>$filter_type['modified_by'],'filterWidgetOptions'=>$filterWidgetOption['modified_by']],
				],
				'export'=>false,
				'floatHeader'=>true,
				'floatHeaderOptions' => ['top' => 'auto'],
	            'responsive'=>false,
				'responsiveWrap' => false,
				'pjax'=>true,
				'floatOverflowContainer'=>true,
	            'pjaxSettings'=>[
	                'options'=>['id' => 'saved-projects-pajax','enablePushState' => false],
	                'neverTimeout' => true,
	                'beforeGrid' => '',
	                'afterGrid' => '',
	            ],
			]);*/ ?>
		</div>
	</fieldset>
	
    <div class="button-set text-right">
    	   <?php $allprojects_url = Url::toRoute(['case-projects/load-saved-projects', 'case_id' => $case_id]); ?>	
    	   <?= Html::button('All Projects',['title'=>"All Projects",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>
           <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary','onclick'=>'loadSaveProjects('.$case_id.');']) ?>
     </div>
</div>
<script>
	var $grid = $('#saved-projects-pajax');
    $grid.css('visibility','hidden');
	$('#case_id').val('<?php echo $case_id;?>');
	$(document).ready(function(){
		$grid.css('visibility','visible');
	});
	function loadSaveProjects(case_id){
		location.href = baseUrl +'case-projects/index&case_id='+case_id;
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
