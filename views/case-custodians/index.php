<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\web\JsExpression;
use app\models\User;
use app\models\EvidenceCustodians;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CaseCloseTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Custodians';
$this->params['breadcrumbs'][] = $this->title;
$filterinputTitle="Filter By ";
if ((new User)->checkAccess(4.003) || (new User)->checkAccess(4.004)) {
$action_column = ['class' => 'kartik\grid\ActionColumn',
				'headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'case_custodians_actions','scope'=>'col'],
				'contentOptions' => ['class' => 'third-td','headers'=>'case_custodians_actions'],
				'filterOptions'=>['headers'=>'case_custodians_actions'],
				'template'=>'{update}&nbsp;{delete}',
				'mergeHeader'=>false,
				'buttons'=>[
						'update'=>function ($url, $model, $key) {
						if((new User)->checkAccess(4.003)){	
							return
								Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label'=> 'Edit Custodian',
									'onclick'=>'UpdateCust('.$key.');'
							]);
						}	
						},
						'delete'=>function ($url, $model, $key){
							if((new User)->checkAccess(4.004)){	
								return
									Html::a('<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:DeleteCust('.$key.',"'.$model->getCustName().'");', [
		  						'title' => Yii::t('yii', 'Delete'),
                                                                'aria-label'=> 'Delete Custodian',
                                                                'data' => ['confirm' =>  Yii::t('yii',"Are you sure you want to Delete Custodian ".$model->getCustName()."?"),],
		  								]);
		  					}			
		  				},
					],
					'order'=>DynaGrid::ORDER_FIX_LEFT
				];
}else{
	$action_column = [];
}
$model_evid = new EvidenceCustodians();
$labels=$model_evid->attributeLabels();
$columns=[
	['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['media/get-details']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'media_grid_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'media_grid_expand'],'expandIcon' => '<a href="javascript:void(0);" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span><span class="hide">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span><span class="hide">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All ', 'filterOptions'=>['header'=>'media_grid_expand'],'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'media_grid_checkbox'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'media_grid_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
	$action_column
];
$columns=[
	['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::toRoute(['case-custodians/getdetails', 'case_id' => $case_id]),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'case_custodians_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class'=>'first-td','headers'=>'case_custodians_expand'],'filterOptions'=>['headers'=>'case_custodians_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span><span class="hide">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span><span class="hide">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All ', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => '\kartik\grid\CheckboxColumn','mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_custodians_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'case_custodians_checkbox'],'filterOptions'=>['headers'=>'case_custodians_checkbox'], 'checkboxOptions' => ['customInput'=>true],'order'=>DynaGrid::ORDER_FIX_LEFT],
	$action_column
];
if(!empty($cust_form)){
	foreach($cust_form as $column){
		if($column=='media'){
			$columns[]=['attribute'=>'media','filterInputOptions' => ['title' => 'Filter By Media'],'headerOptions'=>['title'=>'Media','class'=>'custodian_media_th','id'=>'case_custodians_media','scope'=>'col'],'contentOptions'=>['class'=>'word-break text-center custodian_media_td','headers'=>'case_custodians_media'],'filterOptions'=>['headers'=>'case_custodians_media'],'format' => 'raw','header'=>'Media','filterType'=>$filter_type['custodians_media'],'filterWidgetOptions'=>$filterWidgetOption['custodians_media'],'value'=>function($model){ return $model->isMedia($model->cust_id);}];
		}else if($column=='project'){
		  	$columns[]=['attribute'=>'project','filterInputOptions' => ['title' => 'Filter By Project'],'headerOptions'=>['title'=>'Project','class'=>'custodian_project_th','id'=>'case_custodians_project','scope'=>'col'],'contentOptions'=>['class'=>'word-break text-center custodian_project_td','headers'=>'case_custodians_project'],'filterOptions'=>['headers'=>'case_custodians_project'],'format' => 'raw','header'=>'Project','filterType'=>$filter_type['custodians_project'],'filterWidgetOptions'=>$filterWidgetOption['custodians_project'],'value'=>function($model) use ($case_id){ return $model->isProjects($case_id, $model->cust_id, "show");}];
		}else if($column=='form'){
		  	$columns[]=['attribute'=>'form','filterInputOptions' => ['title' => 'Filter By Form'],'headerOptions'=>['title'=>'Form','class'=>'custodian_form_th','id'=>'case_custodians_form','scope'=>'col'],'contentOptions'=>['class'=>'word-break text-center custodian_form_td','headers'=>'case_custodians_form'],'filterOptions'=>['headers'=>'case_custodians_form'],'format' => 'raw','header'=>'Form','filterType'=>$filter_type['custodians_form'],'filterWidgetOptions'=>$filterWidgetOption['custodians_form'],'value'=>function($model) use($case_id){ return $model->isForm($model,'',$case_id);}];
		}else{
			$columns[]=['attribute'=>$column,'filterType'=>$filter_type[$column],'headerOptions'=>['title'=>$labels[$column],'class'=>'word-break'],'contentOptions'=>['class' => 'word-break'],'filterWidgetOptions'=>$filterWidgetOption[$column]];
		}
	}
}else{
$columns=[
				['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::toRoute(['case-custodians/getdetails', 'case_id' => $case_id]),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'case_custodians_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class'=>'first-td','headers'=>'case_custodians_expand'],'filterOptions'=>['headers'=>'case_custodians_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span><span class="hide 123456">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span><span class="hide">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
				['class' => '\kartik\grid\CheckboxColumn','mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_custodians_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'case_custodians_checkbox'],'filterOptions'=>['headers'=>'case_custodians_checkbox'], 'checkboxOptions' => ['customInput'=>true],'order'=>DynaGrid::ORDER_FIX_LEFT],
				$action_column,
		  		['attribute'=>'cust_lname','headerOptions'=>['title'=>'Custodian Name','class'=>'custodian_name_th','id'=>'case_custodians_name','scope'=>'col'],'contentOptions'=>['class'=>'custodian_name_td word-break','headers'=>'case_custodians_name'],'filterOptions'=>['headers'=>'case_custodians_name'],'filterInputOptions' => ['title' => 'Filter By Custodian Name'],'format' => 'raw','label'=>'Custodian Name','value'=>function($model){ return $model->getCustName();}, 'filterType'=>$filter_type['cust_lname'],'filterWidgetOptions'=>$filterWidgetOption['cust_lname']],
		  		['attribute'=>'title','filterInputOptions' => ['title' => 'Filter By Title'],'headerOptions'=>['title'=>'Title','class'=>'custodian_title_th','id'=>'case_custodians_title','scope'=>'col'],'contentOptions'=>['class'=>'word-break custodian_title_td','headers'=>'case_custodians_title'],'filterOptions'=>['headers'=>'case_custodians_title'], 'filterType'=>$filter_type['title'],'filterWidgetOptions'=>$filterWidgetOption['title']],
		  		['attribute'=>'dept','filterInputOptions' => ['title' => 'Filter By Department'],'headerOptions'=>['title'=>'Department','class'=>'custodian_dept_th','id'=>'case_custodians_department','scope'=>'col'],'contentOptions'=>['class'=>'word-break custodian_dept_td','headers'=>'case_custodians_department'],'filterOptions'=>['headers'=>'case_custodians_department'], 'filterType'=>$filter_type['dept'],'filterWidgetOptions'=>$filterWidgetOption['dept']],
		  		['attribute'=>'cust_email','filterInputOptions' => ['title' => 'Filter By Email'],'headerOptions'=>['title'=>'Email','class'=>'custodian_dept_th','id'=>'case_custodians_email','scope'=>'col'],'contentOptions'=>['class'=>'word-break custodian_dept_td','headers'=>'case_custodians_department'],'filterOptions'=>['headers'=>'case_custodians_department'], 'filterType'=>$filter_type['cust_email'],'filterWidgetOptions'=>$filterWidgetOption['cust_email']],
				['attribute'=>'media','filterInputOptions' => ['title' => 'Filter By Media'],'headerOptions'=>['title'=>'Media','class'=>'custodian_media_th','id'=>'case_custodians_media','scope'=>'col'],'contentOptions'=>['class'=>'word-break text-center custodian_media_td','headers'=>'case_custodians_media'],'filterOptions'=>['headers'=>'case_custodians_media'],'format' => 'raw','header'=>'Media','filterType'=>$filter_type['custodians_media'],'filterWidgetOptions'=>$filterWidgetOption['custodians_media'],'value'=>function($model){ return $model->isMedia($model->cust_id);}],
		  		['attribute'=>'project','filterInputOptions' => ['title' => 'Filter By Project'],'headerOptions'=>['title'=>'Project','class'=>'custodian_project_th','id'=>'case_custodians_project','scope'=>'col'],'contentOptions'=>['class'=>'word-break text-center custodian_project_td','headers'=>'case_custodians_project'],'filterOptions'=>['headers'=>'case_custodians_project'],'format' => 'raw','header'=>'Project','filterType'=>$filter_type['custodians_project'],'filterWidgetOptions'=>$filterWidgetOption['custodians_project'],'value'=>function($model) use ($case_id){ return $model->isProjects($case_id, $model->cust_id, "show");}],
		  		['attribute'=>'form','filterInputOptions' => ['title' => 'Filter By Form'],'headerOptions'=>['title'=>'Form','class'=>'custodian_form_th','id'=>'case_custodians_form','scope'=>'col'],'contentOptions'=>['class'=>'word-break text-center custodian_form_td','headers'=>'case_custodians_form'],'filterOptions'=>['headers'=>'case_custodians_form'],'format' => 'raw','header'=>'Form','filterType'=>$filter_type['custodians_form'],'filterWidgetOptions'=>$filterWidgetOption['custodians_form'],'value'=>function($model) use($case_id){ return $model->isForm($model,'',$case_id);}],
		  		
];
}
?>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'casecustodians-grid',
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
					'options'=>['id'=>'casecustodians-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-casecustodians',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
<?php /*
 GridView::widget([
 		'id'=>'casecustodians-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
		'columns' =>[
				
		  		
		  		
		  		
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'floatOverflowContainer' => true,
		'pjaxSettings'=>[
			'options'=>['id'=>'casecustodians-pajax','enablePushState' => false],
			'neverTimeout'=>true,
			'beforeGrid'=>'',
        	'afterGrid'=>'',
    	],
    	'export'=>false,
		'responsive'=>false,
		'hover'=>true,
		'bordered'=>false,
		'headerRowOptions'=>['bordered'=>false],
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
		]
]);*/
?>
</div>
</fieldset>
<div class="button-set text-right">
	<?= Html::button('All Custodians',['title'=>"All Custodians",'class' => 'btn btn-primary all_filter','onclick'=>'allcustodian();','style'=>'display:none;']);?>
	<?php if ((new User)->checkAccess(4.002)){ ?>
		<?= Html::button('Add Custodian',['title'=>"Add Custodian",'class' => 'btn btn-primary','onclick'=>'openaddcustodiant();'])?>
	<?php } else { ?>
		<?= Html::button('Add Custodian',['title'=>"Add Custodian",'class' => 'btn btn-primary','onclick'=>'openaddcustodiant();','style'=>'visibility:hidden'])?>
		<?php } ?>
</div>
</div>
<div id="addevidcust"></div>
<script>
var $grid = $('#casecustodians-pajax'); // your grid identifier 
$grid.css('visibility','hidden');

$(document).ready(function(){
	$('input').customInput();
	$grid.css('visibility','visible');
	//$("#list_custodian").addClass('active');
	//$("#accordion-container").accordion({'active':1});
});	
function allcustodian(){
	window.location.reload();
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
