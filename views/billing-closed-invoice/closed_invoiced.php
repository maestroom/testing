<?php
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
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\InvoiceFinalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoice Closed';
$this->params['breadcrumbs'][] = $this->title;

/* Full URL */
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$columns=[
		['class' => '\kartik\grid\CheckboxColumn','checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_finalized_invoiced_'.$key, 'value' => json_encode(array('client_id' => $model->id))];},'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'finalized_invoices_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions' => ['title'=>'Select Row','class' => 'first-td text-center','headers'=>'finalized_invoices_checkbox'],'filterOptions'=>['headers'=>'finalized_invoices_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
		['class' => '\kartik\grid\ActionColumn','headerOptions'=>['title'=>'Actions', 'class' => 'action third-th','id'=>'finalized_invoices_actions','scope'=>'col'],'contentOptions' => ['class' => 'action third-td','headers'=>'finalized_invoices_actions'],'filterOptions'=>['headers'=>'finalized_invoices_actions'],'mergeHeader'=>false, 'buttons' =>  ['view' => function ($url, $model) { return '<a href="javascript:void(0);" onclick="preview('.$model->id.',\'closed\')" title="Preview Invoice"><em title="Preview Invoice" class="fa fa-file-text-o"></em></a>'; }, 'update' => function ($url, $model) { return ''; }, 'delete' => function ($url, $model) { return ''; }],'order'=>DynaGrid::ORDER_FIX_LEFT],
		['attribute' => 'id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Invoice #'], 'label'=>'Invoice #', 'headerOptions'=>['title'=>'Invoice #','class'=>'invoice-width-th','id'=>'finalized_invoices_id','scope'=>'col'], 'contentOptions' => ['class' => 'invoice-width-td text-center','headers'=>'finalized_invoices_id'],'filterOptions'=>['headers'=>'finalized_invoices_id'], 'value' =>  function ($model) { return $model->id; }, 'filterType'=>$filter_type['id'],'filterWidgetOptions' => $filterWidgetOption['id']],
		['attribute' => 'client_id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Client'], 'label'=>'Client', 'headerOptions'=>['title'=>'Client','class'=>'client-width-td','id'=>'finalized_invoices_client_id','scope'=>'col'], 'contentOptions' => ['class' => 'client-width-td','headers'=>'finalized_invoices_client_id'],'filterOptions'=>['headers'=>'finalized_invoices_client_id'], 'value' =>  function ($model) {
			$onclick="preview(".$model->id.",'closed')";
			//return $model->getClientDetails($model->id, $model->display_by);
			//$html = '<a href="index.php?r=billing-finalized-invoice/preview-invoice&invoice_id='.$model->id.'&flag=preview" title="Preview '.$model->client_name.' Invoice">'.$model->client_name.'</a>';
			$html = '<a href="javascript:void(0);" onclick="'.$onclick.'" title="Preview '.$model->client_name.' Invoice">'.$model->client_name.'</a>';
        	return $html;


		}, 'filterType'=>$filter_type['client_id'],'filterWidgetOptions' => $filterWidgetOption['client_id']],
		['attribute' => 'client_case_id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Case'], 'label'=>'Case', 'headerOptions'=>['title'=>'Case','class'=>'client_case_id-width-td','id'=>'finalized_invoices_client_case_id','scope'=>'col'], 'contentOptions' => ['class' => 'client_case_id-width-td','headers'=>'finalized_invoices_client_case_id'],'filterOptions'=>['headers'=>'finalized_invoices_client_case_id'], 'value' =>  function ($model) {
			//return $model->getCaseDetails($model->id, $model->display_by);
			if($model->display_by==1){
			$onclick="preview(".$model->id.",'closed')";
			$html = '<a href="javascript:void(0);" onclick="'.$onclick.'" title="Preview '.$model->case_name.' Invoice">'.$model->case_name.'</a>';
        	return $html;
			}
		}, 'filterType'=>$filter_type['client_case_id'],'filterWidgetOptions' => $filterWidgetOption['client_case_id']],
		['attribute' => 'created_date', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Invoice Date','class'=>'form-control'],'label'=>'Invoice Date', 'headerOptions'=>['title'=>'Invoice Date','class' => 'global-date-width','id'=>'finalized_invoices_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-date-width','headers'=>'finalized_invoices_date'],'filterOptions'=>['headers'=>'finalized_invoices_date'], 'value' =>  function ($model) { return $model->getFinalInvoicedate($model->created_date); }, 'filterType'=>$filter_type['created_date'],'filterWidgetOptions' => $filterWidgetOption['created_date']],
		['attribute' => 'closed_date', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Closed Date','class'=>'form-control'],'label'=>'Closed Date', 'headerOptions'=>['title'=>'Closed Date','class' => 'global-date-width','id'=>'closed_invoices_date','scope'=>'col'], 'contentOptions' => ['class' => 'global-date-width','headers'=>'closed_invoices_date'],'filterOptions'=>['headers'=>'closed_invoices_date'], 'value' =>  function ($model) { return  $model->getClosedInvoicedate($model->closed_date); }, 'filterType'=>$filter_type['closed_date'],'filterWidgetOptions' => $filterWidgetOption['closed_date']],
		['attribute' => 'closed_by', 'filterInputOptions' => ['title' => 'Filter By Closed By'], 'label' => 'Closed By','headerOptions'=>['scope'=>'col','title'=>'Closed By','class'=>'closedby-td','id' => 'invoice_closed_by'], 'contentOptions' => ['class' => 'text-left closedby-td','headers'=>'invoice_closed_by'], 'filterOptions'=>['headers'=>'invoice_closed_by'], 'format' => 'raw','value' => function ($model) {
			return $model->closedUser->usr_first_name." ".$model->closedUser->usr_lastname;
		}, 'filterType'=>GridView::FILTER_SELECT2,'filterWidgetOptions'=>['showToggleAll' => false,'options'=>['multiple'=>true],'pluginOptions'=>['ajax' =>['url' => Url::toRoute(['billing-closed-invoice/ajax-closed-invoice-filter']),'dataType' => 'json','data' => new JsExpression('function(params) { return {q:params.term,field:"closed_by"}; }')]]],],
		['attribute' => 'totalinvoiceamt', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Total','onkeypress' => 'return isNumber(event);'], 'label'=>'Total', 'headerOptions'=>['title'=>'Total','class'=>'totalinvoice-width-td text-center','id'=>'closed_invoices_total','scope'=>'col'], 'contentOptions' => ['class' => 'totalinvoice-width-td text-right','headers'=>'finalized_invoices_total'],'filterOptions'=>['headers'=>'finalized_invoices_total','class'=>'totalinvoice-width-td'],'filterType'=>$filter_type['totalinvoiceamt'],'filterWidgetOptions'=>$filterWidgetOption['totalinvoiceamt'], 'value' =>  function ($model) { return "$".number_format($model->totalinvoiceamt,2);}]
];
?>
<!-- Grid Final Invoice -->
<div class="right-main-container" id="closed_invoices_grid">
	 <fieldset class="two-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'closed-invoiced-grid',
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
					'options'=>['id'=>'closed-invoiced-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-closed-invoiced',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
			<?php /* GridView::widget([
					'id'=>'final-invoiced-grid',
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
					'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong> of <strong id='totalItemCount'>{totalCount}</strong> items</div>",
					'columns' => [
						],
						'export' => false,
						'floatHeader' => true,
						'floatHeaderOptions' => ['top' => 'auto'],
						'responsive' => false,
						'responsiveWrap' => false,
						'pjax' => true,
						'pjaxSettings'=>[
						'options'=>['id' => 'saved-projects-pajax','enablePushState' => false],
						'neverTimeout' => true,
						'beforeGrid' => '',
						'afterGrid' => '',
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
        	]); */ ?>
		</div>
		<form method="post" id="reopen-invoice-form" style="height:100%;display:none;" action="<?php echo Url::to(['export-excel/team-tasks-export',Yii::$app->request->queryParams]) ?>" autocomplete="off">
		</form>
	</fieldset>
	<div class="button-set text-right">
		<?php $allprojects_url = Url::toRoute(['billing-closed-invoice/closed-invoices','grid_id'=>'dynagrid-closed-invoiced']); ?>
		<?= Html::button('All Invoices', ['title'=>"All Invoices",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>

	</div>
	<div id="bulkreopeninvoice-reopen-dialog" class="bulkreopeninvoices hide">
			<fieldset>
	             <legend class="sr-only">Bulk ReOpen Invoices</legend>
				<div class="custom-inline-block-width">
					<input type="radio" aria-setsize="2" aria-posinset="1" name="bulkreopeninvoice" class="bulkreopen" value="selectedinvoice" id="rdo_selectedreopeninvoice"><label for="rdo_selectedreopeninvoice">Selected <span id="selectedinvoice">0</span> Invoices in Grid</label>
					<input type="radio" aria-setsize="2" aria-posinset="2" name="bulkreopeninvoice" class="bulkreopen" value="allinvoice" checked="checked" id="rdo_bulkreopeninvoice"/><label for="rdo_bulkreopeninvoice">All Filtered <span id="allbulkreopeninvoice">0</span> Invoices in Grid</label>
				</div>
			</fieldset>
		</div>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?= $fullUrl ?>');
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
//$('.page-header').find('span.pull-right').html('<?php echo 'Total Revenue: $'.$sum;?>');
/*dyangird setting*/
$('body').find('select[data-krajee-select2]').on("click mouseup mousedown touchstart touchend focusin",
$.proxy(function(e) {
    e.stopPropagation();
    if(e.originalEvent && e.originalEvent.srcElement && e.originalEvent.srcElement.id === 'select2-drop') {
        if (this.opts.shouldFocusInput(this)) {
            this.focusSearch()
        }
    }
}, this));
</script>
<noscript></noscript>
