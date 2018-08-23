<?php 
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\JsExpression;
use app\models\User;

$modeluser=new User();
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
			[ 'class' => '\kartik\grid\CheckboxColumn','checkboxOptions'=>function($model, $key, $index, $column) { return ['customInput'=>true, 'class' => 'chk_price_point_'.$key , 'data-id' => $key,'value' => $model->price_point ];},'headerOptions'=>['scope'=>'col','title'=>'Select All','class'=>' first-td'], 'header'=>false, 'rowHighlight' => false, 'mergeHeader'=>false, 'filterOptions' => ['headers' => 'pricepoint_by_type_chk'], 'contentOptions'=>['headers' => 'pricepoint_by_type_chk', 'title'=>'Select Row','class'=>' first-td','style' => 'padding-left:0px!important;']],
			[ 'attribute' => 'price_point', 'filterOptions' => ['headers' => 'pricepoint_by_type_price_point'], 'headerOptions' => ['scope'=>'col','title' => 'Price Point','class' => 'text-left price_point-width'],'contentOptions' => ['headers' => 'pricepoint_by_type_price_point', 'class'=>'price_point-width'],'format' => 'html'],
			[ 'attribute' => 'pricing_rate', 'filterOptions' => ['headers' => 'pricepoint_by_type_pricing_rate'], 'headerOptions' => ['scope'=>'col', 'title' => 'Internal Rate','class' => 'text-left pricing_rate-width'], 'filter'=>'','header' => '<a href="javascript:void(0);" title="Internal Rate" class="tag-header-black">Internal Rate</a>' , 'contentOptions' => ['headers' => 'pricepoint_by_type_pricing_rate','class'=>'pricing_rate-width'], 'format' => 'raw','value' => function ($model) use($client_id){ return $model->getPriceRatesByLoc($model->id,$model->unit->unit_name,$model->pricing_range,$model->pricing_type);} ],
			[ 'attribute' => 'pricing_rate', 'filterOptions' => ['headers' => 'pricepoint_by_type_pricing_rate'], 'headerOptions' => ['scope'=>'col','id' => 'pricepoint_by_type_pricing_rate', 'title' => $type=='client'?'Client Rate':'Case Rate','class' => 'text-left type_pricing_rate-width'], 'filter'=>'','header' =>  $type=='client'?'<a href="javascript:void(0);" title="Client Rate" class="tag-header-black">Client Rate</a>':'<a href="javascript:void(0);" title="Case Rate" class="tag-header-black">Case Rate</a>' , 'contentOptions' => ['headers' => 'pricepoint_by_type_pricing_rate','class'=>'type_pricing_rate-width'], 'format' => 'raw','value' => function ($model) use($type, $client_id, $client_case_id){ return $type=='client'?$model->getPriceClientRatesByLoc($model->id,$model->unit->unit_name,$model->pricing_range,$model->pricing_type, $client_id):$model->getPriceClientCaseRatesByLoc($model->id,$model->unit->unit_name,$model->pricing_range,$model->pricing_type, $client_case_id);}],
			[ 'class' => 'kartik\grid\ActionColumn', 'header' => '<a href="javascript:void(0);" title="Action" class="tag-header-black">Action</a>', 'filterOptions' => ['headers' => 'pricepoint_by_type_action'], 'headerOptions' => ['scope'=>'col', 'title' => 'Actions','class' => 'text-center third-th'], 'contentOptions' => ['headers' => 'pricepoint_by_type_action', 'class' => 'text-center third-td'], 'mergeHeader'=>false,'template' => '{sort}&nbsp;{update}&nbsp;{delete}', 'buttons' => [ 'update' => function ($url, $model, $key) use($client_id,$client_case_id,$type,$team_id){ return Html::a ( '<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set','title' => Yii::t ( 'yii', 'Edit' ),'aria-label' => Yii::t ( 'yii', 'Edit Price point' ),'onclick' => 'AdjustPricepointByClientcase('.$key.','.$client_id.','.$client_case_id.','.$team_id.',"'.$type.'");' ] ); },
			'delete' => function ($url, $model, $key) use($client_id,$client_case_id,$type,$template_id,$modeluser){
				//$onclick="alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
				if ($type=='case')
				{
					if($modeluser->checkAccess(7.07))
					{
						$onclick='RemovePricepointByTemplate('.$template_id.','.$client_id.','.$client_case_id.',"'.$type.'",'.$key.',"'.$model->price_point.'");';
						return Html::a ( '<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Delete' ),'aria-label' => Yii::t ( 'yii', 'Delete' ), 'onClick' => $onclick ] );
					}
					else
					{
						return "";
					}
				}
				elseif($type=='client')
				{
					if($modeluser->checkAccess(7.09))
					{
						$onclick='RemovePricepointByTemplate('.$template_id.','.$client_id.','.$client_case_id.',"'.$type.'",'.$key.',"'.$model->price_point.'");';
						return Html::a ( '<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Delete' ),'aria-label' => Yii::t ( 'yii', 'Delete' ), 'onClick' => $onclick ] );
					}
					else
					{
						return "";
					}
				}
				
				} 
			] ]
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
