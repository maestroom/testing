<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use kartik\grid\datetimepicker;
use yii\web\JsExpression; 
use app\models\Options;
$columns = [];

if(!empty($coc_form)){
	foreach($coc_form as $column){
		if($column=='trans_type'){
			$columns[] = ['attribute' => 'trans_type','headerOptions'=>['title'=>'Transaction Type','id'=>'chain_custody_transaction_type'],'filterInputOptions'=>['title'=>'Filter By Transaction Type','class'=>'form-control word-break','prompt'=>' '],'filterType'=>$filter_type['trans_type'],'contentOptions'=>['class' => 'qty-width word-break','headers'=>'chain_custody_transaction_type'],'filterOptions'=>['headers'=>'chain_custody_transaction_type'], 'format' => 'raw','filterWidgetOptions'=>$filterWidgetOption['trans_type'], 'value' => function($model){ return $model->getStatusImage($model->trans_type);}];
		} else if($column == 'trans_date') {
			$columns[] = ['attribute' => 'trans_date','headerOptions'=>['title'=>'Transaction Date','class'=>'global-datetime-width word-break','id'=>'chain_custody_transaction_date'],'filterInputOptions'=>['title'=>'Filter By Transaction Date','class'=>'form-control'],'contentOptions'=>['class' => 'global-datetime-width word-break','headers'=>'chain_custody_transaction_date'],'filterOptions'=>['headers'=>'chain_custody_transaction_date'],'filterType'=>$filter_type['trans_date'],'filterWidgetOptions'=>$filterWidgetOption['trans_date'],'value'=>function($model){return (new Options)->ConvertOneTzToAnotherTz($model->trans_date, 'UTC', $_SESSION['usrTZ']);}];
		}else if($column == 'trans_by'){
			$columns[] = ['attribute' => 'trans_by','filterWidgetOptions'=>$filterWidgetOption['trans_by'],'filterType'=>$filter_type['trans_by'],'headerOptions'=>['title'=>'Transaction By','id'=>'chain_custody_transaction_by'],'filterInputOptions'=>['title'=>'Filter By Transaction By','class'=>'form-control','prompt'=>' '],'contentOptions'=>['class' => 'transby-width word-break','headers'=>'chain_custody_transaction_by'],'filterOptions'=>['headers'=>'chain_custody_transaction_by'], 'value'=>function($model){return $model->transby->usr_first_name.' '.$model->transby->usr_lastname;}]; 
		}else if($column == 'trans_requested_by'){
			$columns[] = ['attribute' => 'trans_requested_by','filterWidgetOptions'=>$filterWidgetOption['trans_requested_by'],'headerOptions'=>['title'=>'Transaction Requested By', 'class' => 'text-nowrap','id'=>'chain_custody_transaction_requested_date'],'filterInputOptions'=>['title'=>'Filter By Transaction Requested By','class'=>'form-control word-break','prompt'=>' '],'filterType'=>$filter_type['trans_requested_by'],'contentOptions'=>['class' => 'transreq-width word-break','headers'=>'chain_custody_transaction_requested_date'],'filterOptions'=>['headers'=>'chain_custody_transaction_requested_date'], 'value'=>function($model){return $model->transRequstedby->usr_first_name.' '.$model->transRequstedby->usr_lastname;}];
		}else if($column == 'moved_to'){
			$columns[] = ['attribute' => 'moved_to','filterWidgetOptions'=>$filterWidgetOption['moved_to'],'headerOptions'=>['title'=>'Moved To','id'=>'chain_custody_moveto'],'filterType'=>$filter_type['moved_to'],'contentOptions'=>['class' => 'movedto-width word-break','headers'=>'chain_custody_moveto'],'filterOptions'=>['headers'=>'chain_custody_moveto'],'filterInputOptions'=>['title'=>'Filter By Moved To','class'=>'form-control','prompt'=>' '], 'value'=>function($model){return $model->storedLoc->stored_loc;}];
		}else if($column == 'Trans_to'){
			$columns[] = ['attribute' => 'Trans_to','filterWidgetOptions'=>$filterWidgetOption['Trans_to'], 'headerOptions'=>['title'=>'Transaction To','id'=>'chain_custody_transaction_to'],'filterInputOptions'=>['title'=>'Filter By Transaction To','class'=>'form-control','prompt'=>' '],'filterOptions'=>['headers'=>'team_task_expand'],'filterType'=>$filter_type['Trans_to'],'contentOptions'=>['class' => 'transto-width word-break','headers'=>'chain_custody_transaction_to'],'filterOptions'=>['headers'=>'chain_custody_transaction_to'] , 'value'=>function($model){return $model->evidenceTo->to_name;}];
		}else if($column == 'trans_reason'){
			$columns[] =['attribute' => 'trans_reason','headerOptions'=>['title'=>'Reason for Transaction','id'=>'chain_custody_reason_for_transaction'],'filterInputOptions'=>['title'=>'Filter By Reason for Transaction','class'=>'form-control'],'contentOptions'=>['class' => 'reason-width word-break','headers'=>'chain_custody_reason_for_transaction'],'filterOptions'=>['headers'=>'chain_custody_reason_for_transaction']];
		}
	}	
}else{ 
	$columns=[
		['attribute' => 'trans_type','headerOptions'=>['title'=>'Transaction Type','id'=>'chain_custody_transaction_type'],'filterInputOptions'=>['title'=>'Filter By Transaction Type','class'=>'form-control word-break','prompt'=>' '],'filterType'=>$filter_type['trans_type'],'contentOptions'=>['class' => 'qty-width word-break','headers'=>'chain_custody_transaction_type'],'filterOptions'=>['headers'=>'chain_custody_transaction_type'], 'format' => 'raw','filterWidgetOptions'=>$filterWidgetOption['trans_type'], 'value' => function($model){ return $model->getStatusImage($model->trans_type);}],
		['attribute' => 'trans_date','headerOptions'=>['title'=>'Transaction Date','class'=>'global-datetime-width word-break','id'=>'chain_custody_transaction_date'],'filterInputOptions'=>['title'=>'Filter By Transaction Date','class'=>'form-control'],'contentOptions'=>['class' => 'global-datetime-width word-break','headers'=>'chain_custody_transaction_date'],'filterOptions'=>['headers'=>'chain_custody_transaction_date'],'filterType'=>$filter_type['trans_date'],'filterWidgetOptions'=>$filterWidgetOption['trans_date'],'value'=>function($model){return (new Options)->ConvertOneTzToAnotherTz($model->trans_date, 'UTC', $_SESSION['usrTZ']);}],
		['attribute' => 'trans_by','filterWidgetOptions'=>$filterWidgetOption['trans_by'],'filterType'=>$filter_type['trans_by'],'headerOptions'=>['title'=>'Transaction By','id'=>'chain_custody_transaction_by'],'filterInputOptions'=>['title'=>'Filter By Transaction By','class'=>'form-control','prompt'=>' '],'contentOptions'=>['class' => 'transby-width word-break','headers'=>'chain_custody_transaction_by'],'filterOptions'=>['headers'=>'chain_custody_transaction_by'], 'value'=>function($model){return $model->transby->usr_first_name.' '.$model->transby->usr_lastname;}],
		['attribute' => 'trans_requested_by','filterWidgetOptions'=>$filterWidgetOption['trans_requested_by'],'headerOptions'=>['title'=>'Transaction Requested By', 'class' => 'text-nowrap','id'=>'chain_custody_transaction_requested_date'],'filterInputOptions'=>['title'=>'Filter By Transaction Requested By','class'=>'form-control word-break','prompt'=>' '],'filterType'=>$filter_type['trans_requested_by'],'contentOptions'=>['class' => 'transreq-width word-break','headers'=>'chain_custody_transaction_requested_date'],'filterOptions'=>['headers'=>'chain_custody_transaction_requested_date'], 'value'=>function($model){return $model->transRequstedby->usr_first_name.' '.$model->transRequstedby->usr_lastname;}],
		['attribute' => 'moved_to','filterWidgetOptions'=>$filterWidgetOption['moved_to'],'headerOptions'=>['title'=>'Moved To','id'=>'chain_custody_moveto'],'filterType'=>$filter_type['moved_to'],'contentOptions'=>['class' => 'movedto-width word-break','headers'=>'chain_custody_moveto'],'filterOptions'=>['headers'=>'chain_custody_moveto'],'filterInputOptions'=>['title'=>'Filter By Moved To','class'=>'form-control','prompt'=>' '], 'value'=>function($model){return $model->storedLoc->stored_loc;}],
		['attribute' => 'Trans_to','filterWidgetOptions'=>$filterWidgetOption['Trans_to'], 'headerOptions'=>['title'=>'Transaction To','id'=>'chain_custody_transaction_to'],'filterInputOptions'=>['title'=>'Filter By Transaction To','class'=>'form-control','prompt'=>' '],'filterOptions'=>['headers'=>'team_task_expand'],'filterType'=>$filter_type['Trans_to'],'contentOptions'=>['class' => 'transto-width word-break','headers'=>'chain_custody_transaction_to'],'filterOptions'=>['headers'=>'chain_custody_transaction_to'] , 'value'=>function($model){return $model->evidenceTo->to_name;}],
		['attribute' => 'trans_reason','headerOptions'=>['title'=>'Reason for Transaction','id'=>'chain_custody_reason_for_transaction'],'filterInputOptions'=>['title'=>'Filter By Reason for Transaction','class'=>'form-control'],'contentOptions'=>['class' => 'reason-width word-break','headers'=>'chain_custody_reason_for_transaction'],'filterOptions'=>['headers'=>'chain_custody_reason_for_transaction']],
	];
}
?>
<div class="sub-heading">Chain of Custody (<?php echo "#".$evidNum;?>)</div>
<input type="hidden" name="evidNum" id="evidNum" value="<?php echo $evidNum;?>" >
<fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'transaction-grid',
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
					'options'=>['id'=>'dynagrid-transaction-pjax','enablePushState' => false],
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
    'id'=>'dynagrid-transaction',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
			<?php /*GridView::widget([
				'id'=>'transaction-grid',
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
				'responsiveWrap' => false,
				'columns' => [
                       
				],
				'export'=>false,
				'floatHeader'=>true,  
				'floatHeaderOptions' => ['top' => 'auto'],
				'persistResize'=>false,
				'resizableColumns'=>false,
				'pjax'=>true,
				'pjaxSettings'=>[
					'options'=>['id'=>'transactiongrid-pajax','enablePushState' => false],
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
				'responsive'=>false,
				'floatOverflowContainer'=>true,            
			]); */?>
</div>
</fieldset>  
<div class="button-set text-right">
       <?= Html::a('All Transactions',  NULL,['href'=>'javascript:void(0);','class'=>'btn btn-primary all_filter','title'=>"All Transactions",'onclick'=>'chain_of_custody("all");','style'=>'display:none;']) ?>
	   <?= Html::a('Back', ['/media/index'], ['class'=>'btn btn-primary','title'=>"Back"]) ?>
	   <?= Html::button('PDF', ['title'=>"PDF Export",'class' => 'btn btn-primary','onclick'=>'pdfchangeofcustody();'])?>
 </div>
<!-- Right Panel Ends Here -->
<script>
$('input').customInput();
</script>
<script type="text/javascript">
function pdfchangeofcustody()
{
	
	var id=document.getElementById('evidNum').value;
	
	var Url=baseUrl+"pdf/chainofcustody&id="+id;
	location.href=Url;	
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
