<?php 
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\JsExpression;
?>
<style>
.pricing-sub-grid-table-responsive .grid-view .kv-grid-container{
	bottom:auto;
	top:auto;
	position: relative;
}
</style>
<div class="table-responsive pricing-sub-grid-table-responsive">
<?= GridView::widget([
    'id'=>'pricing-sub-grid-'.$team_id,
	'dataProvider' => $dataProvider,
    'layout' => '{items}',
        'columns' => [
			['class' => '\kartik\grid\CheckboxColumn','filterOptions' => ['headers' => 'pricepoint_by_clientid_chk'], 'checkboxOptions'=>function($model, $key, $index, $column) { return ['customInput'=>true, 'class' => 'chk_price_point_'.$key , 'data-id' => $key,'value' => $model->price_point ];},'headerOptions'=>['scope'=>'col', 'id' => 'pricepoint_by_clientid_chk','title'=>'Select All','class'=>' first-td'], 'header'=>false, 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['headers' => 'pricepoint_by_clientid_chk', 'title'=>'Select Row', 'class'=>' first-td', 'style' => 'padding-left:0px!important;']],
			[ 'attribute' => 'price_point', 'filterOptions' => ['headers' => 'pricepoint_by_clientid_price_point'], 'headerOptions' => ['scope'=>'col', 'title' => 'Price Point','headers' => 'pricepoint_by_clientid_price_point'],'contentOptions' => ['headers' => 'pricepoint_by_clientid_price_point','style' => 'padding: 4px 8px;'],'format' => 'html'],
			[ 'attribute' => 'pricing_rate', 'filterOptions' => ['headers' => 'pricepoint_by_clientid_pricing_rate'],'headerOptions' => ['scope'=>'col', 'title' => 'Internal Rate','headers' => 'pricepoint_by_clientid_pricing_rate'], 'filter'=>'','header' => 'Internal Rate' , 'contentOptions' => ['headers' => 'pricepoint_by_clientid_price_point','style' => 'padding: 4px 8px;width:300px;'], 'format' => 'raw','value' => function ($model) use($client_id){ return $model->getPriceRatesByLoc($model->id,$model->unit->unit_name,$model->pricing_range,$model->pricing_type);} ],
			[ 'attribute' => 'pricing_rate', 'filterOptions' => ['headers' => 'pricepoint_by_clientid_pricing_rate'],'headerOptions' => ['scope'=>'col', 'title' => 'Client Rate','headers' => 'pricepoint_by_clientid_pricing_rate'], 'filter'=>'','header' => 'Client Rate' , 'contentOptions' => ['headers' => 'pricepoint_by_clientid_price_point', 'style' => 'padding: 4px 8px;width:300px;'], 'format' => 'raw','value' => function ($model) use($client_id){ return $model->getPriceClientRatesByLoc($model->id,$model->unit->unit_name,$model->pricing_range,$model->pricing_type, $client_id);} ],
			[ 'class' => 'kartik\grid\ActionColumn', 'headerOptions' => ['scope'=>'col', 'title' => 'Actions','headers' => 'pricepoint_by_clientid_action'], 'contentOptions' => ['headers' => 'pricepoint_by_clientid_action','class' => 'text-center', 'style' => 'padding: 4px 8px;'], 'mergeHeader'=>false,'template' => '{sort}&nbsp;{update}&nbsp;{delete}', 'buttons' => [ 'update' => function ($url, $model, $key) use($client_id,$team_id){ return Html::a ( '<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set','title' => Yii::t ( 'yii', 'Edit' ),'aria-label' => Yii::t ( 'yii', 'Edit Price point' ),'onclick' => 'LoadPricepointByTemplate('.$key.','.$client_id.','.$team_id.',"client");' ] ); },'delete' => function ($url, $model, $key) use($client_id,$template_id){return Html::a ( '<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:RemovePricepointByTemplate('.$template_id.','.$client_id.',"client",'.$key.',"'.$model->price_point.'");', [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Delete' ),'aria-label' => Yii::t ( 'yii', 'Delete' ) ] );} ] ]
		],
		'export'=>false,
		'responsive'=>true,
		'floatHeader'=>true,
		'floatHeaderOptions' => ['top' => 'auto'],
		'pjax'=>true,
		'pjaxSettings'=>[
           	'options'=>['id'=>'pricing-sub-grid-'.$team_id.'-pajax','enablePushState' => false],
           	'neverTimeout'=>true,
           	'beforeGrid'=>'',
           	'afterGrid'=>'',
		],
		'rowOptions'=>['class'=>'sort'],
		'containerOptions' => ['class'=>'pricing-sub-grid','style'=>'width:99%;']
	]); ?>
</div>
<script>
$('input').customInput();
</script>
<noscript></noscript>
