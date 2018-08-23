<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
?>
<div class="right-main-container">
	 <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?= GridView::widget([
            	'id'=>'tax-classes-grid',
            	'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
				'summary' => "<div class='summary'>Showing <strong>{begin}-{end}</strong>> of <strong id='totalItemCount'>{totalCount}</strong>> items.</div>",
				'columns' => [
					['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'width' => '5%','headerOptions'=>['title'=>'Select All'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['aria-label'=>'Select Row','class' => 'first-td text-center']],
						['attribute' => 'datefrom', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Filter Dates'], 'label'=>'Filter Dates', 'headerOptions'=>['title'=>'Filter Dates'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->datefrom.' - '.$model->dateto; }, 'filterType'=>GridView::FILTER_DATE,
						'filterWidgetOptions'=>[
								'pluginOptions'=>[
								'ajax' =>[
										//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
										//		'dataType' => 'json',
						//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
						]]]],
					['class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>array('customInput'=>true),'width' => '5%','headerOptions'=>['aria-label'=>'Select All'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['aria-label'=>'Select Row','class' => 'first-td text-center']],
						['attribute' => 'display_invoice', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Client/Cases'], 'label'=>'Client/Cases', 'headerOptions'=>['title'=>'Client/Cases'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->display_invoice; }, 'filterType'=>GridView::FILTER_SELECT2,
						'filterWidgetOptions'=>[
								'pluginOptions'=>[
								'ajax' =>[
										//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
										//		'dataType' => 'json',
						//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
						]]]],
					['attribute' => 'display_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Display By'], 'label'=>'Display By', 'headerOptions'=>['title'=>'Display By'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->display_by; }, 'filterType'=>GridView::FILTER_SELECT2,
						'filterWidgetOptions'=>[
								'pluginOptions'=>[
								'ajax' =>[
						//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
						//		'dataType' => 'json',
						//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
		  			]]]],
		  			['attribute' => 'created', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Display By'], 'label'=>'Display By', 'headerOptions'=>['title'=>'Display By'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->display_by; }, 'filterType'=>GridView::FILTER_SELECT2,
		  			'filterWidgetOptions'=>[
		  					'pluginOptions'=>[
		  					'ajax' =>[
		  							//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
		  							//		'dataType' => 'json',
		  			//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
		  			]]]],
		  			['attribute' => 'created_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Display By'], 'label'=>'Display By', 'headerOptions'=>['title'=>'Display By'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->display_by; }, 'filterType'=>GridView::FILTER_SELECT2,
		  			'filterWidgetOptions'=>[
		  					'pluginOptions'=>[
		  					'ajax' =>[
		  							//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
		  							//		'dataType' => 'json',
		  			//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
		  			]]]],
		  			['attribute' => 'modified', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Display By'], 'label'=>'Display By', 'headerOptions'=>['title'=>'Display By'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->display_by; }, 'filterType'=>GridView::FILTER_SELECT2,
		  			'filterWidgetOptions'=>[
		  					'pluginOptions'=>[
		  					'ajax' =>[
		  							//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
		  							//		'dataType' => 'json',
		  			//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
		  			]]]],
		  			['attribute' => 'modified_by', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Display By'], 'label'=>'Display By', 'headerOptions'=>['title'=>'Display By'], 'contentOptions' => ['class' => 'datedue-width'], 'value' =>  function ($model) { return $model->display_by; }, 'filterType'=>GridView::FILTER_SELECT2,
		  			'filterWidgetOptions'=>[
		  					'pluginOptions'=>[
		  					'ajax' =>[
		  							//		'url' => Url::toRoute(['billing-generate-invoice/ajax-generate-invoice-filter']),
		  							//		'dataType' => 'json',
		  			//		'data' => new JsExpression('function(params) { return {q:params.term,field:"display_by"}; }')
		  			]]]],
 				],
				'export'=>false,
				'floatHeader'=>true,
				'floatHeaderOptions' => ['top' => 'auto'],
	            'responsive'=>false,
				'responsiveWrap' => false,
				'pjax'=>true,
	            'pjaxSettings'=>[
	                'options'=>['id' => 'saved-projects-pajax','enablePushState' => false],
	                'neverTimeout' => true,
	                'beforeGrid' => '',
	                'afterGrid' => '',
	            ],
			]); ?>
		</div>
	</fieldset>
</div>
