<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
//use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
//use yii\web\JsExpression;

$modeluser=new User();
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SearchPricing */
/* @var $dataProvider yii\data\ActiveDataProvider */

$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$model_datas=$dataProvider->getModels();
$columns=[
	['class' => '\kartik\grid\CheckboxColumn','headerOptions' => ['scope'=>'col','title'=>'Select All/None','class'=>'first-th', 'id' => 'billing_team_price_chk'],'contentOptions' => ['title'=>'Select Row', 'class' => 'text-center first-td', 'headers' => 'billing_team_price_chk'],'filterOptions' => ['headers' => 'billing_team_price_chk'],'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_price_point_'.$key, 'value' => json_encode(array('price_point' => $model->price_point)) ]; },'rowHighlight' => false,'mergeHeader' => false,'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => 'kartik\grid\ActionColumn', 'headerOptions' => ['scope'=>'col','title' => 'Actions','class'=>'third-td internal-action-width', 'id' => 'billing_team_price_action'], 'filterOptions' => ['headers' => 'billing_team_price_action'], 'contentOptions' => ['headers' => 'billing_team_price_action','class' => 'text-center third-td internal-action-width', 'style' => 'padding: 4px 8px;'], 'mergeHeader'=>false,'template' => '{sort}&nbsp;{update}&nbsp;{delete}', 'buttons' => ['update' => function ($url, $model, $key) { return Html::a ( '<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set','title' => Yii::t ( 'yii', 'Edit' ),'aria-label' => Yii::t ( 'yii', 'Edit Price point' ),'onclick' => 'EditPricepoint(' . $key . ',"team","#admin_right");' ] ); },
	'delete' => function ($url, $model, $key) use($team_id,$modeluser){
		$onclick="javascript:alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
		if ($modeluser->checkAccess(7.03)){
			$onclick='javascript:DeletePricepoint('.$team_id.',' . $key . ',"'.$model->price_point.'","team");';
			return Html::a ( '<em title="Delete" class="fa fa-close text-primary"></em>', $onclick, [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Delete' ),'aria-label' => Yii::t ( 'yii', 'Delete' ) ] );
		}
		else
		{
			return "";
		}
	}],'order'=>DynaGrid::ORDER_FIX_LEFT],
	['attribute' => 'price_point', 'headerOptions' => ['scope'=>'col','title' => 'Price Point','class'=>'internal-price-point-width word-break', 'id' => 'billing_team_price_point'],'contentOptions' => ['class'=>'internal-price-point-width word-break','style' => 'padding: 4px 8px;', 'headers' => 'billing_team_price_point'],'filterOptions' => ['headers' => 'billing_team_price_point'],'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Price Point','class'=>'form-control'], 'filterType'=>$filter_type['price_point'],'filterWidgetOptions'=>$filterWidgetOption['price_point']],
	['attribute' => 'pricing_rate','headerOptions' => ['scope'=>'col','title' => 'Internal Rate', 'class'=>'internal-rate-width word-break','id' => 'billing_team_pricing_rate'], 'label' => 'Internal Rate', 'contentOptions' => ['style' => 'padding: 4px 8px;','class'=>'internal-rate-width word-break','headers' => 'billing_team_pricing_rate'],'filterOptions' => ['headers' => 'billing_team_pricing_rate'], 'format' => 'raw','value' => function ($model){return $model->getPriceRatesByLoc($model->id,$model->unit_name,$model->pricing_range,$model->pricing_type);},'filterType'=>$filter_type['pricing_rate'],'filterWidgetOptions'=>$filterWidgetOption['pricing_rate'], 'filterInputOptions' => ['onkeypress' => 'return isNumber(event);']],
	['attribute' => 'description', 'headerOptions' => ['scope'=>'col','title' => 'Description', 'class'=>'internal-description-width word-break','id' => 'billing_team_description'], 'contentOptions' => ['style' => 'padding: 4px 8px;','class'=>'internal-description-width word-break','headers'=>'billing_team_description'],'filterOptions' => ['headers' => 'billing_team_description'],'format' => 'html', 'filterInputOptions' => ['title' => 'Filter By Description','class'=>'form-control']],
	['attribute' => 'accum_cost','filterInputOptions' => ['title' => 'Filter By Accum Cost','placeholder' => ''], 'filterType'=>$filter_type['accum_cost'],'filterWidgetOptions'=>$filterWidgetOption['accum_cost'] , 'headerOptions' => ['scope'=>'col','title' => 'Accumulated Cost','class' => 'internal-accum-cost-width word-break','id' => 'billing_team_accum_cost'], 'label' => 'Accum Cost' , 'contentOptions' => ['class' => 'text-center internal-accum-cost-width word-break', 'style' => 'padding: 4px 8px;', 'headers' => 'billing_team_accum_cost'], 'filterOptions' => ['headers' => 'billing_team_accum_cost'], 'format' => 'raw','value' => function ($model) { if ($model->accum_cost==1) {return '<span tabindex="0" class="fa fa-check text-danger" title="Is an Accumulated Cost"></span>';} else {return false;}} ],
	['attribute' => 'utbms_code_id','filterInputOptions' => ['title' => 'Filter By UTBMS Code'], 'filterType'=>$filter_type['utbms_code_id'],'filterWidgetOptions'=> $filterWidgetOption['utbms_code_id'],'headerOptions' => ['scope'=>'col','title' => 'UTBMS Code','class'=>'internal-utbms-code-width word-break','id' => 'billing_team_utbms_code_id'], 'label' => 'UTBMS' , 'contentOptions' => ['class' => 'text-left internal-utbms-code-width word-break', 'style' => 'padding: 4px 8px;', 'headers' => 'billing_team_utbms_code_id'],'filterOptions' => ['headers' => 'billing_team_utbms_code_id'], 'format' => 'html','value' => function ($model) { if($model->utbms_code_id != ''){return $model->utbmscode;} else {return false;}}],
];
?>
<fieldset class="one-cols-fieldset team-pricing-fieldset internal-shared-fieldset">
	<div class="table-responsive <?= empty($model_datas)?'custom-grid-container':'' ?>">
<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'internal-price-point-grid',
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
					'options'=>['id'=>'internal-price-point-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-internal-price-point',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
	</div>
</fieldset>
	<div class="button-set text-right">
		 <?= Html::button('All Price Points',['title'=>"All Price Points",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'loadTeamPricing('.$team_id.');'])?>
		 <?php if ($modeluser->checkAccess(7.03)){ ?>
		 <?= Html::button('Remove', ['title'=>"Remove",'class' => 'btn btn-primary','onclick'=>'DeleteBulkPricepoint('.$team_id.',"team");'])?>
		 <?php } ?>
		 <?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onclick'=>'AddPricePoint('.$team_id.');']) ?>
	</div>
<script>
jQuery(document).ready(function($) {
	$('input').customInput();
	var fullurl = '<?=$fullUrl ?>';
	$('#module-url').val(fullurl);
	var pajax_container = 'internal-price-point-grid-pajax';
	$('#pajax_container').val(pajax_container);
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
});
/*dyangird setting*/
</script>
<noscript></noscript>
