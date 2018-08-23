<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\web\JsExpression;
use app\models\User;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//echo '<pre>';
//print_r($filter_type['per_complete']);
//die;
$this->title = 'Case Projects';
$this->params['breadcrumbs'][] = $this->title;

if((new User)->checkAccess(4.082) || (new User)->checkAccess(4.0821)){

	$columns =
        [
	    ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-projects/get-task-details']),'headerOptions'=>['scope'=>'col','title'=>'Expand/Collapse All','class'=>'first-th word-break','id'=>'case_project_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center word-break','headers'=>'case_project_expand'], 'filterOptions'=>['headers'=>'case_project_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="screenreader">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="collapse Row"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
	    ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['scope'=>'col','title'=>'Select All/None Rows','class'=>'first-th','id'=>'case_project_check'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'case_project_check'], 'filterOptions'=>['headers'=>'case_project_check'],'order'=>DynaGrid::ORDER_FIX_LEFT],
	    ['class' => '\kartik\grid\ActionColumn','headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'case_project_action','scope'=>'col'], 'filterOptions'=>['headers'=>'case_project_action'],'contentOptions' => ['class' => 'third-td text-center action-padding','headers'=>'case_project_action'],'mergeHeader'=>false, 'buttons' =>  ['update' => function ($url, $model) use ($case_id) {
                 if ((new User)->checkAccess(4.0821)) {
					 //echo "here change project";die;
                        return '<a href="javascript:void(0);"  onclick="changeprojects('.$model->id.', '.$case_id.')" aria-label="Change Project" title="Change Project"><em title="Change Project" class="fa fa-pencil text-primary"></em></a>';
                    }
                 }, 'view' => function ($url, $model) { return ''; }, 'delete' => function ($url, $model) use ($case_id) {
                 if ((new User)->checkAccess(4.082)) {
					 //echo "here remove project";die;
                    return '<a href="javascript:void(0);" onclick="remove_project('.$model->id.', '.$case_id.')" title="Delete Project" aria-label="Delete Project"><em title="Delete Project" class="fa fa-close text-primary"></em></a>';
                 }
            }], 'order' => DynaGrid::ORDER_FIX_LEFT],
	    ['attribute' => 'id','format' => 'raw','filterOptions'=>['headers'=>'case_project_id'],  'filterInputOptions' => ['title' => 'Filter By Project #'], 'label'=>'Project #','headerOptions'=>['scope'=>'col','title'=>'Project #','id'=>'case_project_id','class'=>'projectno-width'], 'contentOptions' => ['class' => 'first-td text-center projectno-width','headers'=>'case_project_id'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },
		//'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']
		'filter'=>'<input type="text" class="form-control filter_number_only" name="TaskSearch[id]" value="'.$params['TaskSearch']['id'].'">'
		],
	    ['attribute' => 'task_status','filterOptions'=>['headers'=>'case_project_status'],  'filterInputOptions' => ['title' => 'Filter By Project Status'], 'label' => 'Status','headerOptions'=>['scope'=>'col','title'=>'Project Status','class'=>'global-status-width word-break','id'=>'case_project_status'], 'contentOptions' => ['class' => 'first-td text-center global-status-width word-break','headers'=>'case_project_status'], 'format' => 'raw', 'filterType'=>$filter_type['task_status'], 'value' => function ($model) use ($is_accessible_submodule_tracktask) { return $model->imageHelperCase($model, $is_accessible_submodule_tracktask);},'filterWidgetOptions'=>$filterWidgetOption['task_status']],
	    ['attribute' => 'task_duedate', 'filterOptions'=>['headers'=>'case_project_duedate'], 'filterInputOptions' => ['title' => 'Filter By Project Due Date','class'=>'form-control'], 'format' => 'raw', 'label' => 'Due Date','headerOptions'=>['scope'=>'col','title'=>'Project Due Date','class'=>'global-datetime-width word-break','id'=>'case_project_duedate'], 'contentOptions' => ['class' => 'global-datetime-width word-break','headers'=>'case_project_duedate'],'filter'=>'<input type="text" class="form-control" name="TaskSearch[task_duedate]" value="">', 'filterType'=>$filter_type['task_duedate'],'filterWidgetOptions' => $filterWidgetOption['task_duedate'], 'value' => function($model){ return $model->getTaskDuedateobj($model); }],
	    ['attribute' => 'priority', 'filterOptions'=>['headers'=>'case_project_priority'], 'filterInputOptions' => ['title' => 'Filter By Project Priority'], 'label' => 'Priority','headerOptions'=>['scope'=>'col','title'=>'Project Priority','id'=>'case_project_priority','class'=>'priority-width'],'contentOptions' => ['class' => 'priority-width word-break','headers'=>'case_project_priority'], 'filterType'=>$filter_type['task_priority'],'filterWidgetOptions'=>$filterWidgetOption['task_priority'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[priority]" value="">','value' => function($model) use($pporder){if($pporder == $model->porder){return "<span class='text-danger'><strong>".$model->pname."</strong></span>"; } else { return $model->pname;} }],
	    ['attribute' => 'project_name','filterOptions'=>['headers'=>'case_project_project_name'],  'filterInputOptions' => ['title' => 'Filter By Project Name'], 'label' => 'Project Name','headerOptions'=>['scope'=>'col','title'=>'Project Name','id'=>'case_project_project_name','class'=>'projectname-width'],'contentOptions' => ['class' => 'projectname-width word-break', 'headers'=>'case_project_project_name'], 'filterType'=>$filter_type['project_name'],'filterWidgetOptions'=>$filterWidgetOption['project_name'], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[project_name]" value="'.$params['TaskSearch']['project_name'].'">', 'value' => function($model){ return $model->project_name;
	}],
    ['attribute' => 'per_complete', 'label' => '% Complete', 'filterOptions' => ['headers'=>'case_project_percentage_complete'], 'headerOptions'=> ['class'=>'percomplete-width','title' => 'Percentage Complete'], 'filterOptions'=>['headers'=>'case_project_percentage_complete'], 'contentOptions' => function($model) use($flag) {  if($flag != '') {return ['class' => 'percomplete-width word-break','colspan'=>'2','headers'=>'case_project_action']; } else { return ['class' => 'percomplete-width word-break text-center', 'headers'=>'case_project_percentage_complete'];} }, 'filterInputOptions' => ['onkeypress' => 'return isNumber(event);'],'filterType'=>$filter_type['per_complete'],'filterWidgetOptions'=>$filterWidgetOption['per_complete'], 'format' => 'raw', 'value' => function($model) use($case_id){ return $model->getTaskPercentageCompletedMyCaseGird($model,"case",$case_id,0,0,null,array(),$model->per_complete);}],
];


/*$action_column =
		  $flag = "";
		  $complete_header = ['scope'=>'col','title'=>'Project Complete %','id'=>'case_project_percentage_complete'];*/
	  }else{
		$columns =
[
    ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-projects/get-task-details']),'headerOptions'=>['scope'=>'col','title'=>'Expand/Collapse All','class'=>'first-th','id'=>'case_project_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'word-break first-td text-center','headers'=>'case_project_expand'], 'filterOptions'=>['headers'=>'case_project_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
    ['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['scope'=>'col','title'=>'Select All/None','class'=>'first-th','id'=>'case_project_check'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'case_project_check'], 'filterOptions'=>['headers'=>'case_project_check'],'order'=>DynaGrid::ORDER_FIX_LEFT],
    ['attribute' => 'id','format' => 'raw','filterOptions'=>['headers'=>'case_project_id'],  'filterInputOptions' => ['title' => 'Filter By Project #'], 'label'=>'Project #','headerOptions'=>['scope'=>'col','title'=>'Project #','id'=>'case_project_id','class'=>'projectno-width'], 'contentOptions' => ['class' => 'first-td text-center projectno-width','headers'=>'case_project_id'],'value' =>  function ($model) { return $model->getTaskInstruction($model->id, $model->task_status); },
	//'filterType'=>GridView::FILTER_SELECT2,
	//'filterWidgetOptions'=>['pluginOptions'=>['ajax' =>['url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id,'params'=>Yii::$app->request->queryParams]),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"id"}; }')]]]
	'filter'=>'<input type="text" class="form-control filter_number_only" name="TaskSearch[id]" value="'.$params['TaskSearch']['id'].'">'
	],
    ['attribute' => 'task_status','filterOptions'=>['headers'=>'case_project_status'],  'filterInputOptions' => ['title' => 'Filter By Project Status'], 'label' => 'Status','headerOptions'=>['scope'=>'col','title'=>'Project Status','class'=>'global-status-width word-break','id'=>'case_project_status'], 'contentOptions' => ['class' => 'first-td text-center global-status-width word-break','headers'=>'case_project_status'], 'format' => 'raw', 'filter' => ['' => 'All', '0'=>'Not Started','1'=>'Started','3'=>'On Hold','4'=>'Complete'], 'filterType'=>GridView::FILTER_SELECT2, 'value' => function ($model) use ($is_accessible_submodule_tracktask) { return $model->imageHelperCase($model,$is_accessible_submodule_tracktask);},
                         'filterWidgetOptions'=>[
                            'pluginOptions'=>[
                                'allowClear'=>false,
                            ]]
                          ],
     ['attribute' => 'task_duedate', 'format'=>'raw', 'filterOptions'=>['headers'=>'case_project_duedate'], 'filterInputOptions' => ['title' => 'Filter By Project Due Date'], 'format' => 'html', 'label' => 'Due Date','headerOptions'=>['scope'=>'col','title'=>'Project Due Date','class'=>'global-datetime-width word-break','id'=>'case_project_duedate'], 'contentOptions' => ['class' => 'global-datetime-width word-break','headers'=>'case_project_duedate'],'filter'=>'<input type="text" class="form-control" name="TaskSearch[task_duedate]" value="">', 'filterType'=>GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ], 'value' => function($model){ return $model->getTaskDuedateobj($model); }],
    ['attribute' => 'priority', 'filterOptions'=>['headers'=>'case_project_priority'], 'filterInputOptions' => ['title' => 'Filter By Project Priority'], 'label' => 'Priority','headerOptions'=>['scope'=>'col','title'=>'Project Priority','id'=>'case_project_priority','class'=>'priority-width'],'contentOptions' => ['class' => 'word-break priority-width','headers'=>'case_project_priority'], 'filterType'=>GridView::FILTER_SELECT2,
  				'filterWidgetOptions'=>[
  					'pluginOptions'=>[
                                            'ajax' =>[
                                                'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id,'params'=>Yii::$app->request->queryParams]),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term,field:"priority"}; }')
                                            ]]],'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[priority]" value="">','value' => function($model) use($pporder){
                                            if($pporder == $model->porder) {
                                                return "<span class='text-danger'><strong>".$model->pname."</strong></span>"; } else {
                                                    return $model->pname;
                                                }
                                            }],
     ['attribute' => 'project_name','filterOptions'=>['headers'=>'case_project_project_name'],  'filterInputOptions' => ['title' => 'Filter By Project Name'], 'label' => 'Project Name','headerOptions'=>['scope'=>'col','title'=>'Project Name','id'=>'case_project_project_name','class'=>'projectname-width'],'contentOptions' => ['class' => 'projectname-width word-break', 'headers'=>'case_project_project_name'], 'filterType'=>GridView::FILTER_SELECT2,
                    'filterWidgetOptions'=>[
                            'pluginOptions'=>[
                                'ajax' =>[
                                    'url' => Url::toRoute(['case-projects/ajax-filter', 'case_id' => $case_id,'params'=>Yii::$app->request->queryParams]),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term,field:"project_name"}; }'),
                                ]]], 'format' => 'raw', 'filter'=>'<input type="text" class="form-control" name="TaskSearch[project_name]" value="'.$params['TaskSearch']['project_name'].'">', 'value' => function($model){ return $model->project_name;}],
    ['attribute' => 'percentage_complete', 'label' => '% Complete','filterOptions'=>['headers'=>'case_project_percentage_complete'], 'headerOptions'=>['class'=>'percomplete-width'], 'filterOptions'=>['headers'=>'case_project_percentage_complete'], 'contentOptions' => function($model) use($flag) {  if($flag != '') {return ['class' => 'percomplete-width word-break','colspan'=>'2','headers'=>'case_project_action']; } else { return ['class' => 'percomplete-width word-break text-center', 'headers'=>'case_project_percentage_complete'];} }, 'format' => 'raw', 'value' => function($model) use($case_id){ return $model->getTaskPercentageCompletedMyCaseGird($model,"case",$case_id);}],
];



		/*$action_column = ['contentOptions' => ['class' => 'empty-td text-center','headers'=>'case_project_action'],'mergeHeader'=>false,'headerOptions'=>['class'=>'empty-header-td','id'=>'case_project_action'],'filterInputOptions'=>['class'=>'empty-filter-td'],'filterOptions'=>['headers'=>'case_project_action']];
		$complete_header = ['scope'=>'col','title'=>'Project Complete %','colspan'=>'2','id'=>'case_project_percentage_complete'];
		//$action_column = [];
		$flag = "no-action-column";*/
	  }
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset caseproject-unique-fildset">
        <div class="table-responsive <?php echo $flag;?>">
    <?php $dynagrid = DynaGrid::begin([
	    'columns'=>$columns,
	    'storage'=>'db',
	    'theme'=>'panel-info',
	    'gridOptions'=>[
	        'id'=>'caseprojects-grid',
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
					'options'=>['id'=>'teamassigneduser-pajax','enablePushState' => false],
					'neverTimeout'=>true,
					'beforeGrid'=>'',
					'afterGrid'=>'',
				],
				'pager' => [
					'options'=>['class'=>'pagination'], // set clas name used in ui list of pagination
					'prevPageLabel' => 'Previous',  // Set the label for the "previous" page button
					'nextPageLabel' =>'Next',  // Set the label for the "next" page button
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
	    'id'=>'dynagrid-caseprojects',
	    ] // a unique identifier is important
	]);
	if (substr($dynagrid->theme, 0, 6) == 'simple') {
	    $dynagrid->gridOptions['panel'] = false;
	}
	DynaGrid::end();
?>
                <?php /*= GridView::widget([
                    'id'=>'caseprojects-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'responsiveWrap' => false,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
                    'columns' => $columns
                    ,'export'=>false,
			'floatHeader'=>true,
			'pjax'=>true,
            'responsive'=>false,
            'floatOverflowContainer'=>true,
            'floatHeaderOptions' => ['top' => 'auto'],
            'pjaxSettings'=>[
                'options'=>['id'=>'teamassigneduser-pajax','enablePushState' => false],
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
                ]);*/ ?>
</div>
</fieldset>
    <div class="button-set text-right">
      <?php $current_url = Url::current(); ?>
      <?php $allprojects_url = Url::toRoute(['case-projects/index', 'case_id' => $case_id]); ?>
		<?php if($current_url!=$allprojects_url) { ?>
		   <?= Html::button('All Projects', ['title'=>"All Projects",'class' => 'btn btn-primary all_filter', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>
		<?php } else { ?>
			<?= Html::button('All Projects', ['title'=>"All Projects",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>
		<?php } ?>

	  <?php if((new User)->checkAccess(4.0822)){ ?>
		<?= Html::button('Bulk Assign', ['title'=>"Bulk Assign",'class' => 'btn btn-primary', 'onclick' => 'bulkassignproject();'])?>
	   <?php } ?>
	   <?php /*if((new User)->checkAccess(4.0811)){ ?>
		<?= Html::button('Canceled Projects', ['title'=>"Canceled Projects View",'class' => 'btn btn-primary', 'onclick'=>'loadCanceledProjects();'])?>
	   <?php //} ?>
	   <?php //if ((new User)->checkAccess(4.081)){ ?>
		<?= Html::button('Closed Projects', ['title'=>"Closed Projects View",'class' => 'btn btn-primary', 'onclick'=>'loadClosedProjects();'])?>
	   <?php //} ?>
		<?= Html::button('Saved Projects', ['title'=>"Saved Projects View",'class' => 'btn btn-primary','onClick' => 'loadSavedProjects();']) */?>

	   <?php if((new User)->checkAccess(4.02)){ ?>
		<?= Html::button('Add Project', ['title'=>"Add Project",'class' => 'btn btn-primary','onclick'=>'addProject('.$case_id.');'])?>
	   <?php } ?>
     </div>
</div>
<script>
var $grid = $('#teamassigneduser-pajax'); // your grid identifier
$grid.css('visibility','hidden');
$('#case_id').val('<?php echo $case_id;?>');
$(document).ready(function() {
	$grid.css('visibility','visible');
});
function readComments(task_id,case_id,comment_type,comment_id,token)
{
 var comment_url=httpPath+"case-projects/post-comment&task_id="+task_id+"&case_id="+case_id;
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
  					   {
  						 window.location.href= comment_url;
  					   }
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
	$form.find('[data-krajee-sortable]').each(function () {
		var $el = $(this);
		if ($el.data('sortable')) {
			$el.sortable('destroy');
		}
		$el.sortable(window[$el.attr('data-krajee-sortable')]);
	});
});
$(document).ready(function() {

	$(document).on('pjax:end',   function(xhr, textStatus, options) {
		if($('.all_filter').is(':visible')){
			$('#close_project_process').hide();
			if($('#tasksearch-task_status').val() == 3){
				$('#close_project_process').show();
			}
		}
	});
});
/*dyangird setting*/

</script>
<noscript></noscript>
