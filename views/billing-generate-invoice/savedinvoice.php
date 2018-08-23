<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
$modeluser=new User();
// full-url
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$columns=[
					['class' => '\kartik\grid\CheckboxColumn','checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_invoice_id_'.$key, 'value' => json_encode(array('id' => $model->id))];},'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'saved_invoices_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'saved_invoices_checkbox'],'filterOptions'=>['headers'=>'saved_invoices_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
					['class' => '\kartik\grid\ActionColumn', 'headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'saved_invoices_actions','scope'=>'col'],'mergeHeader'=>false, 'contentOptions' => ['class' => 'saved-invoice-action third-td','headers'=>'saved_invoices_actions'],'filterOptions'=>['headers'=>'saved_invoices_actions'], 'buttons' =>  ['update' => function ($url, $model) { return '<a href="javascript:void(0);" onclick="loadereditsavedinvoice('.$model->id.')" title="Edit" aria-label="edit  saved invoice"><em title="Edit" class="fa fa-pencil text-primary"></em></a>'; }, 'view' => function ($url, $model) { return ''; }, 
					'delete' => function ($url, $model)use($modeluser) { 
						//$onclick="alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
						if ($modeluser->checkAccess(7.14)){
							$onclick="removesavedinvoices(".$model->id.")";
							return '<a href="javascript:void(0);" onclick="'.$onclick.'" title="Delete" aria-label="Delete"><em title="Delete" class="fa fa-close text-primary"></em></a>'; 
						}
						else
						{
							return "";
						}
						
					}],'order'=>DynaGrid::ORDER_FIX_LEFT],
					['attribute' => 'id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Batch'], 'label'=>'Batch #', 'headerOptions'=>['title'=>'Batch #','id'=>'saved_invoices_batch_id','scope'=>'col'], 'contentOptions' => ['class' => 'saved-invoice-id text-center','headers'=>'saved_invoices_batch_id'],'filterOptions'=>['headers'=>'saved_invoices_batch_id'], 'value' =>  function ($model) { return $model->id; }, 'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']],
					['attribute' => 'datefrom', 'format' => 'raw', 'label'=>'Filter Dates', 'headerOptions'=>['title'=>'Filter Dates','id'=>'saved_invoices_filter_date','scope'=>'col'], 'contentOptions' => ['class' => 'saved-invoice-datefilter','headers'=>'saved_invoices_filter_date'],'filterOptions'=>['headers'=>'saved_invoices_filter_date'], 'value' =>  function ($model) { return $model->getInvoicedate($model->datefrom).' - '.$model->getInvoicedate($model->dateto); },'filterType'=>$filter_type['datefrom'],'filterWidgetOptions'=>$filterWidgetOption['datefrom']],
					['attribute' => 'display_invoice', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Client - Cases'], 'label'=>'Client - Cases', 'headerOptions'=>['title'=>'Client - Cases','id'=>'saved_invoices_client_cased','scope'=>'col'], 'contentOptions' => ['class' => 'saved-invoice-display','headers'=>'saved_invoices_client_cased'],'filterOptions'=>['headers'=>'saved_invoices_client_cased'], 'value' =>  function ($model) { if($model->display_invoice=='Selected'){return $model->getDisplayInvoiceDetails($model->id, $model->display_invoice);}else{return $model->display_invoice;}}, 'filterType'=>$filter_type['display_invoice'],'filterWidgetOptions'=>$filterWidgetOption['display_invoice']],
					['attribute' => 'display_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Display By'], 'label'=>'Display By', 'headerOptions'=>['title'=>'Display By','id'=>'saved_invoices_display_by','scope'=>'col'], 'contentOptions' => ['class' => 'saved-invoice-display','headers'=>'saved_invoices_display_by'],'filterOptions'=>['headers'=>'saved_invoices_display_by'], 'value' =>  function ($model) {  if($model->display_by==1){return 'Itemized';}else{return 'Consolidated';}}, 'filterType'=>$filter_type['display_by'],'filterWidgetOptions'=>$filterWidgetOption['display_by']],
		  			['attribute' => 'created', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Submitted','class'=>'form-control'], 'label'=>'Created Date', 'headerOptions'=>['title'=>'Created Date','id'=>'saved_invoices_created_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-date-width','headers'=>'saved_invoices_created_date'],'filterOptions'=>['headers'=>'saved_invoices_created_date'], 'value' =>  function ($model) { return $model->getInvoicedatedefault($model->created); }, 'filterType'=>$filter_type['created'],'filterWidgetOptions' => $filterWidgetOption['created']],
		  			['attribute' => 'created_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Submitted By'], 'label'=>'Created By', 'headerOptions'=>['title'=>'Created By','id'=>'saved_invoices_created_by','scope'=>'col'], 'contentOptions' => ['class' => 'submit-modify-by','headers'=>'saved_invoices_created_by'],'filterOptions'=>['headers'=>'saved_invoices_created_by'], 'value' =>  function ($model) { return $result = $model->created_user; }, 'filterType'=>$filter_type['created_by'],'filterWidgetOptions'=>$filterWidgetOption['created_by']],
		  			['attribute' => 'modified', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Modified','class'=>'form-control'], 'label'=>'Modified Date', 'headerOptions'=>['title'=>'Modified Date','id'=>'saved_invoices_modified_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-date-width','headers'=>'saved_invoices_modified_date'],'filterOptions'=>['headers'=>'saved_invoices_modified_date'], 'value' =>  function ($model) { return $model->getInvoicedatedefault($model->modified); }, 'filterType'=>$filter_type['modified'],'filterWidgetOptions' => $filterWidgetOption['modified']],
		  			['attribute' => 'modified_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Modified By'], 'label'=>'Modified By', 'headerOptions'=>['title'=>'Modified By','id'=>'saved_invoices_modified_by','scope'=>'col'], 'contentOptions' => ['class' => 'submit-modify-by','headers'=>'saved_invoices_modified_by'],'filterOptions'=>['headers'=>'saved_invoices_modified_by'], 'value' =>  function ($model) { return $result = $model->modified_user; }, 'filterType'=>$filter_type['modified_by'],'filterWidgetOptions'=>$filterWidgetOption['modified_by']],
];
?> 
<div class="right-main-container">
	 <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'saved-invoice-grid',
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
					'options'=>['id'=>'saved-invoice-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-saved-invoice',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
		</div>
	</fieldset>
	<div class=" button-set text-right">
		<button onclick="savedinvoicebackbtnToGI();" title="Back" class="btn btn-primary" id="backrequestinvoiced" type="button" name="yt1">Back</button>
	</div>
</div>
<script>
$('input').customInput();
$('#saved-projects-pajax').val('<?=$fullUrl ?>');

/*
 * Back saved invoice button
 */
function savedinvoicebackbtnToGI(){
    showLoader();
    location.href = baseUrl +'billing-generate-invoice/billing-invoice-management';
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
