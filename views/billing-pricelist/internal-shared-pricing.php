<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\web\JsExpression;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SearchPricing */
/* @var $dataProvider yii\data\ActiveDataProvider */
$modeluser=new User();
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$model_datas=$dataProvider->getModels();
$columns=[
	['class' => '\kartik\grid\CheckboxColumn','headerOptions' => ['scope'=>'col','title'=>'Select All/None','class'=>'first-th', 'id' => 'billing_team_shared_chk'],'contentOptions' => ['title'=>'Select Row', 'class' => 'text-center first-td word-break', 'headers' => 'billing_team_shared_chk'],'filterOptions' => ['headers' => 'billing_team_shared_chk'],'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_price_point_'.$key, 'value' => json_encode(array('price_point' => $model->price_point)) ]; },'rowHighlight' => false,'mergeHeader' => false,'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => 'kartik\grid\ActionColumn', 'filterOptions' => ['headers' => 'billing_team_shared_action'],'headerOptions' => ['scope'=>'col','title' => 'Actions','class'=>' third-th', 'id' => 'billing_team_shared_action'], 'contentOptions' => ['class' => 'text-center  third-td word-break', 'headers' => 'billing_team_shared_action'], 'mergeHeader'=>false,'template' => '{sort}&nbsp;{update}&nbsp;{delete}', 'buttons' => [ 'update' => function ($url, $model, $key) { return Html::a ( '<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set','title' => Yii::t ( 'yii', 'Edit' ),'aria-label' => Yii::t ( 'yii', 'Edit Price point' ),'onclick' => 'EditPricepoint(' . $key . ',"shared",".right-main-container");' ] ); },
	'delete' => function ($url, $model, $key) use($team_id,$modeluser){
		$onclick="javascript:alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
		if ($modeluser->checkAccess(7.05)){
			$onclick='javascript:DeletePricepoint(0,' . $key . ',"'.$model->price_point.'","shared");';
			return Html::a ( '<em title="Delete" class="fa fa-close text-primary"></em>', $onclick, [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Delete' ),'aria-label' => Yii::t ( 'yii', 'Delete' ) ] );
		}
		else {
			return "";
		}
		
		} ]
	,'order'=>DynaGrid::ORDER_FIX_LEFT] ,
	['attribute' => 'price_point','filterOptions' => ['headers' => 'billing_team_shared_price_point'],'headerOptions' => ['scope'=>'col','title' => 'Price Point','class'=>'internal-price-point-shared-width word-break first-th','id'=>'billing_team_shared_price_point'],'contentOptions' => ['class'=>'internal-price-point-shared-width first-td word-break','headers'=>'billing_team_shared_price_point'],'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Price Point','class'=>'form-control'], 'filterType'=>$filter_type['price_point'],'filterWidgetOptions'=>$filterWidgetOption['price_point']],
	['attribute' => 'pricing_rate', 'filterOptions' => ['headers' => 'billing_team_shared_pricing_rate'], 'headerOptions' => ['scope'=>'col','title' => 'Rate','class'=>'internal-rate-shared-width word-break','id'=>'billing_team_shared_pricing_rate'], 'label' => 'Rate' , 'contentOptions' => ['headers'=>'billing_team_shared_pricing_rate','style' => 'padding: 4px 8px;','class'=>'internal-rate-shared-width word-break'], 'format' => 'raw','value' => function ($model) {
		return $model->getPriceRatesByLoc($model->id,$model->unit_name,$model->pricing_range,$model->pricing_type);
		/*if(is_numeric($model->pricing_rates)){
			return "$".number_format($model->pricing_rates, 2, '.', ',')." ".$model->unit_name." ".$model->pricing_range;
		}else{
			return "$".$model->pricing_rates." ".$model->unit_name." ".$model->pricing_range;
		}*/
		//return $model->getPriceRatesByLoc($model->id,$model->unit_name,$model->pricing_range,$model->pricing_type);

	}, 'filterInputOptions' => ['onkeypress' => 'return isNumber(event);'], 'filterType'=>$filter_type['pricing_rate'],'filterWidgetOptions'=>$filterWidgetOption['pricing_rate']],
	['attribute' => 'description', 'filterOptions' => ['headers' => 'billing_team_shared_description'], 'headerOptions' => ['scope'=>'col','id' => 'billing_team_shared_description', 'title' => 'Description','class'=>'internal-description-shared-width'], 'contentOptions' => ['headers' => 'billing_team_shared_description', 'style' => 'padding: 4px 8px;', 'class'=>'internal-description-shared-width word-break'],'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Description','class'=>'form-control']],
	['attribute' => 'utbms_code_id', 'filterOptions' => ['headers' => 'billing_team_shared_utbms_code_id'], 'filterInputOptions' => ['title' => 'Filter By UTBMS CODE'], 'filterType'=>$filter_type['utbms_code_id'],'filterWidgetOptions'=>$filterWidgetOption['utbms_code_id'],'headerOptions' => ['scope'=>'col','id' => 'billing_team_shared_utbms_code_id', 'title' => 'UTBMS Code','class'=>'internal-utbms-code-width'], 'label' => 'UTBMS Code' , 'contentOptions' => ['headers' => 'billing_team_shared_utbms_code_id', 'class' => 'text-left internal-utbms-code-width word-break', 'style' => 'padding: 4px 8px;'], 'format' => 'html','value' => function ($model) { if($model->utbms_code_id != ''){return $model->utbmscode;} else {return false;}}]
];
?>
<style>
.internal-shared-fieldset .grid-view .kv-panel-pager .pagination {margin: 10px 10px 10px 0;}
.internal-shared-fieldset .grid-view .kv-panel-pager .summary { float:left; margin:10px; font-size:13px; line-height: 30px;}
</style>
<div class="right-main-container" id="admin_right">
    <fieldset class="one-cols-fieldset internal-shared-fieldset">
		<div class="table-responsive <?= empty($model_datas)?'custom-grid-container':'' ?>">
	    <?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'shared-price-point-grid',
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
					'options'=>['id'=>'shared-price-point-pajax','enablePushState' => false],
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
    'id'=>'shared-price-point-transaction',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
	    <?php /*GridView::widget([
	    	'id'=>'shared-price-point-grid',
	        'dataProvider' => $dataProvider,
	    	'filterModel' => $searchModel,
	    	'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
	        'columns' => [


	           	],
	           	'floatHeaderOptions' => ['top' => 'auto'],
	            'floatHeader'=>true,
				'pjax'=>true,
				'pjaxSettings'=>[
					'options'=>['id'=>'shared-price-point-grid-pajax','enablePushState' => false],
					'neverTimeout'=>true,
					'beforeGrid'=>'',
		        	'afterGrid'=>'',
		    	],
		    	'export'=>false,
				'responsive'=>false,
		    	'persistResize'=>false,
				'resizableColumns'=>false,
				'floatOverflowContainer'=>true,
				'hover'=>true,
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
				'rowOptions'=>['class'=>'sort'],
	        ]);*/?>
		</div>
	</fieldset>
	<div class="button-set text-right">
		 <?= Html::button('All Price Points',['title'=>"All Price Points",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'loadSharedPricing();'])?>
		 <?php if ($modeluser->checkAccess(7.05)){?>
		 <?= Html::button('Remove', ['title'=>"Remove",'class' => 'btn btn-primary','onclick'=>'DeleteBulkPricepoint(0,"shared");'])?>
		 <?php }?>
		 <?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onclick'=>'AddSharedPricePoint();'])?>
	</div>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('shared-price-point-grid-pajax');
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
