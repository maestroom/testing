<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Categories';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'mediacategory-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn','mergeHeader'=>false, 'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'media_category_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'media_category_checkbox'],'filterOptions'=>['headers'=>'media_category_checkbox'], 'checkboxOptions' =>function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_category_'.$key, 'value' => json_encode(array('category' => $model->category)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
						'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'media_category_actions','scope'=>'col'],
		  				'contentOptions' => ['class' => 'third-td','headers'=>'media_category_actions'],'filterOptions'=>['headers'=>'media_category_actions'],
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
		  							'class' => 'icon-set',
                                                                        'aria-label' => 'Edit Media Category',
		  							'onclick'=>'UpdateMediaCategory('.$key.');'
		  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:DeleteMediaCategory('.$key.');', [
		  						'title' => Yii::t('yii', 'Delete'),
		  						'class' => 'icon-set',
                                                                'aria-label' => 'Remove Media Category',
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->category."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'category', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'media_category_name'],'filterOptions'=>['headers'=>'media_category_name'],'headerOptions' => ['title' => 'Category'], 'headerOptions' => ['title' => 'Category','id'=>'media_category_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'],'filterType'=>$filter_type['category'],'filterWidgetOptions'=>$filterWidgetOption['category']],
		  		
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'mediacategory-pajax','enablePushState' => false],
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
	 <?= Html::button('All Media Category',['title'=>"All Media Category",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'MediaCategory();'])?>  
   <button class="btn btn-primary" title="Remove" onclick="RemoveMediaCategory();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddMediaCategory();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('mediacategory-pajax');
</script>
<noscript></noscript>
