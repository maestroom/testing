<?php

use yii\helpers\Html;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\IndustrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Industries';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'industry-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'client_industries_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'client_industries_checkbox'],'filterOptions'=>['headers'=>'client_industries_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_industry_name_'.$key, 'value' => json_encode(array('industry_name' => $model->industry_name)) ]; }],
				[ 'class' => 'kartik\grid\ActionColumn',
		  				'contentOptions' => ['class' => 'third-td','headers'=>'client_industries_actions'],'filterOptions'=>['headers'=>'client_industries_actions'],
		  				'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'client_industries_actions','scope'=>'col'],
		  		'template'=>'{update}&nbsp;{delete}',
				'mergeHeader'=>false,
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
		  							'class' => 'icon-set',
                                                                        'aria-label' => 'Edit Industry',
		  							'onclick'=>'UpdateIndustry('.$key.');'
		  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteIndustry('.$key.');', [
		  						'title' => Yii::t('yii', 'Delete'),
		  						'class' => 'icon-set',
                                                                'aria-label' => 'Remove Industry',
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->industry_name."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'industry_name', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'client_industries_name'],'filterOptions'=>['headers'=>'client_industries_name'], 'headerOptions' => ['title' => 'Industry Name','id'=>'client_industries_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['industry_name'],'filterWidgetOptions'=>$filterWidgetOption['industry_name']],
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'industry-pajax','enablePushState' => false],
			'neverTimeout'=>true,
			'beforeGrid'=>'',
        	'afterGrid'=>'',
    	],
    	'export'=>false,
		'responsive'=>true,
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
		]
]);
		  ?>
</div>
<div class="button-set button-set text-right">
	 <?= Html::button('All Client Industries',['title'=>"All Client Industries",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'Industries();'])?>  
   <button class="btn btn-primary" title="Remove" onclick="RemoveIndustry();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddIndustry();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('industry-pajax');
</script>
<noscript></noscript>
