<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use app\models\User;
use app\models\Client;
use app\models\ClientCase;
kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);
kartik\widgets\WidgetAsset::register($this);
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$resultsJs = <<< JS
function (data, params) {
	params.page = params.page || 1;
    return {
        results: data.items,
        pagination: {
            more: (params.page * 50) < data.total_count
        }
    };
}
JS;
$client_name="Select Client Preferred Pricing";
$client_case_name="Select Case Preferred Pricing";
if($client_id > 0)
$client_name=Client::findOne($client_id)->client_name;
if($client_case_id > 0){
$case_data=ClientCase::findOne($client_case_id);
$client_case_name=$case_data->client->client_name." - ".$case_data->case_name;
}
?>

<div class="right-main-container" id="get_preferred_container">
    <fieldset class="one-cols-fieldset get-preferred-fieldset">
		<?php echo GridView::widget([
    	'id'=>'get-preferred-grid',
        'dataProvider' => $dataProvider,
    	'layout' => '{items}',
        'columns' => [
				['class' => '\kartik\grid\ExpandRowColumn', 'filterOptions' => ['headers' => 'preferred_by_pricing_expand'], 'detailUrl' => Url::to(['billing-pricelist/get-pricepoint-by-type','client_id'=>$client_id,'client_case_id'=>$client_case_id,'type'=>$type]),'headerOptions'=>['id' => 'preferred_by_pricing_expand','title'=>'Expand/Collapse All'],'contentOptions'=>['headers' => 'preferred_by_pricing_expand', 'title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
				//['attribute' => 'team_id','filterOptions' => ['headers' => 'preferred_by_pricing_team_id'], 'headerOptions'=>['id' => 'preferred_by_team_id','class'=>'text-right'],'header'=> $type=='client'?Html::dropDownList('client_id', $client_id, $clientList, ['class'=>'form-control billing-dropdown-filterlist','onchange'=>"getTemplatesByID(this.value,0,'client');",'id'=>'get-preferred-pricing']) : Html::dropDownList('client_case_id', $client_case_id, $clientCaseList, ['class'=>'form-control billing-dropdown-filterlist','onchange'=>"getTemplatesByID(0,this.value,'case');",'id'=>'get-preferred-pricing']), 'label'=>'Team', 'contentOptions' => ['headers' => 'preferred_by_pricing_team_id','style' => 'padding: 4px 8px;'],'format' => 'html', 'value' => function($model) { $result = $model->pricing_type == 1?'Shared':$model->pricingteam_name; return '<a href="#" class="tag-header-black" title="'.$result.'">'.$result.'</a>'; }],
				['attribute' => 'team_id','filterOptions' => ['headers' => 'preferred_by_pricing_team_id'], 'headerOptions'=>['id' => 'preferred_by_team_id','class'=>'text-right'],'header'=> 
				$type=='client'?
					 Select2::widget([
							 	'id'=>'get-preferred-pricing',
								'name' => 'client_id',
								'initValueText' => $client_name,
								'value'=>$client_id,
								'pluginOptions' => [
									'allowClear' => false,
									'ajax' => [
										'url' => Url::to(['billing-pricelist/clientjsonlist','client_case_id'=>$client_case_id]),
										'dataType' => 'json',
										'delay' => 250,
										'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
										'processResults' => new JsExpression($resultsJs),
										'cache' => true
									],
								],
								'options'=>['class'=>'form-control billing-dropdown-filterlist','style'=>'width:250px !important;'],
								'pluginEvents' => [
								'change' => "function() {
									getTemplatesByID(this.value,0,'client');
									
								}",
								]
							])
					//Html::dropDownList('client_id', $client_id, $clientList, ['class'=>'form-control billing-dropdown-filterlist','onchange'=>"getTemplatesByID(this.value,0,'client');",'id'=>'get-preferred-pricing']) 
				:
						Select2::widget([
							 	'id'=>'get-preferred-pricing',
								'name' => 'client_case_id',
								'initValueText' => $client_case_name,
								'value'=>$client_case_id,
								'pluginOptions' => [
									'allowClear' => false,
									'ajax' => [
										'url' => Url::to(['billing-pricelist/clientcasejsonlist','client_id'=>$client_id]),
										'dataType' => 'json',
										'delay' => 250,
										'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
										'processResults' => new JsExpression($resultsJs),
										'cache' => true
									],
								],
								'options'=>['class'=>'form-control billing-dropdown-filterlist','style'=>'width:250px !important;'],
								'pluginEvents' => [
								'change' => "function() {
									getTemplatesByID(0,this.value,'case');
									
								}",
								]
							])
					//Html::dropDownList('client_case_id', $client_case_id, $clientCaseList, ['class'=>'form-control billing-dropdown-filterlist','onchange'=>"getTemplatesByID(0,this.value,'case');",'id'=>'get-preferred-pricing'])
				
				, 
				
				'label'=>'Team', 
				'contentOptions' => ['headers' => 'preferred_by_pricing_team_id','style' => 'padding: 4px 8px;'],'format' => 'html', 'value' => function($model) { $result = $model->pricing_type == 1?'Shared':$model->pricingteam_name; return '<a href="#" class="tag-header-black" title="'.$result.'">'.$result.'</a>'; }],
           	],
            'export'=>false,
			'floatHeader'=>true,
			'pjax'=>true,
			'responsive'=>true,
			'floatHeaderOptions' => ['top' => 'auto'],
			'persistResize'=>false,
			'resizableColumns'=>false,
			'pjaxSettings'=>[
			'options'=>['id'=>'get-preferred-grid-pajax','enablePushState' => false],
			'neverTimeout'=>true,
			'beforeGrid'=>'',
			'afterGrid'=>'',
			],
			'rowOptions'=>['class'=>'sort'],
        ]); ?>
	</fieldset>
	<?php if(($type == 'client' && $client_id != 0) || ($type == 'case' && $client_case_id != 0)){ ?>
	<div class="button-set text-right">
		<?php if (!empty($models)){ ?>
		 <?php //$onclick_template=$onclick_pp="alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
		 $onclick_template='RemoveTemplate('.$template_id.','.$client_id.','.$client_case_id.',"'.$type.'");';
		 $onclick_pp='RemovePricepointByTemplate('.$template_id.','.$client_id.','.$client_case_id.',"'.$type.'",0,"");';
			if (((new User())->checkAccess(7.07) && $type=='case')){
				
		?>
		<?= Html::button('Delete Template', ['title'=>"Delete Template",'class' => 'btn btn-primary','onclick'=>$onclick_template])?>
		<?= Html::button('Delete Price Points', ['title'=>"Delete Price Points",'class' => 'btn btn-primary','onclick'=>$onclick_pp])?>
			<?php } ?>
		<?php if((new User())->checkAccess(7.09) && $type=='client') {?>
		<?= Html::button('Delete Template', ['title'=>"Delete Template",'class' => 'btn btn-primary','onclick'=>$onclick_template])?>
		<?= Html::button('Delete Price Points', ['title'=>"Delete Price Points",'class' => 'btn btn-primary','onclick'=>$onclick_pp])?>
		<?php }?>
		<?php } ?>
		 <?= Html::button('Add Price Points', ['title'=>"Add Price Points",'class' => 'btn btn-primary','onclick'=>'loadRemainingPricePoint('.$client_id.','.$client_case_id.',"'.$type.'");'])?>
	</div>
	<?php } ?>
</div>
<script>
$(document).ready(function(){
	$("#get-preferred-pricing").select2();
});
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
</script>
<noscript></noscript>
