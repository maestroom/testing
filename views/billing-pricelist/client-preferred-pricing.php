<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\web\JsExpression;

$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<style>
.right-main-container .client-preferred-fieldset{
	left: 1px;
    right: 1px;
    top: 1px;
}
.grid-view #client-preferred-grid-container{
	overflow-x:hidden;
}
</style>
<div class="right-main-container" id="client_preferred_container">
    <fieldset class="one-cols-fieldset client-preferred-fieldset">
		<?= GridView::widget([
    	'id'=>'client-preferred-grid',
        'dataProvider' => $dataProvider,
    	'layout' => '{items}',
        'columns' => [
				['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['billing-pricelist/get-pricepoint-by-clientid','client_id'=>$client_id]),'headerOptions'=>['scope' => 'col', 'title' => 'Expand/Collapse All', 'id' => 'client_preferred_pricing_expand'],'filterOptions' => ['headers' => 'client_preferred_pricing_expand'], 'contentOptions'=>['headers' => 'client_preferred_pricing_expand','title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
				['attribute' => 'team_id', 'filterOptions' => ['headers' => 'client_preferred_pricing_team_id'], 'headerOptions' => ['scope'=>'col','id' => 'client_preferred_pricing_team_id','title' => 'Team'], 'label'=>'Team', 'contentOptions' => ['headers' => 'client_preferred_pricing_team_id','style' => 'padding: 4px 8px;'],'format' => 'raw', 'value' => function ($model) { return $model->pricing_type == 1?'Shared':$model->team->team_name;}],
           	],
				'export'=>false,
				'floatHeader'=>true,
                'pjax'=>true,
                'responsive'=>true,
                'floatHeaderOptions' => ['top' => 'auto'],
                'pjaxSettings'=>[
                'options'=>['id'=>'client-preferred-grid-pajax','enablePushState' => false],
                'neverTimeout'=>true,
                'beforeGrid'=>'',
                'afterGrid'=>'',
			],
			'rowOptions'=>['class'=>'sort'],
        ]); ?>
	</fieldset>
	<?php if($client_id != 0){ ?>
	<div class="button-set text-right">
		<?php if (!empty($models)){ ?>
		 <?= Html::button('Remove Price Points', ['title'=>"Remove Price Points",'class' => 'btn btn-primary','onclick'=>'RemovePricepointByTemplate('.$template_id.','.$client_id.',"client",0,"");'])?>
		 <?= Html::button('Remove Template', ['title'=>"Remove Template",'class' => 'btn btn-primary','onclick'=>'RemoveTemplate('.$template_id.','.$client_id.',"client");'])?>
		 <?php } ?>
		 <?= Html::button('Add Price Points', ['title'=>"Add Price Points",'class' => 'btn btn-primary','onclick'=>'loadPricePointByClient('.$client_id.');'])?>
	</div>
	<?php } ?>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
</script>
<noscript></noscript>
