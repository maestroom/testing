<?php

use yii\helpers\Html;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\DataTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Types';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'datatype-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>' media_datatype_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'media_datatype_checkbox'],'filterOptions'=>['headers'=>'media_datatype_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_data_type_'.$key, 'value' => json_encode(array('data_type' => $model->data_type)) ]; }],
				[ 'class' => 'kartik\grid\ActionColumn',
						'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'media_datatype_actions','scope'=>'col'],
		  				'contentOptions' => ['class' => 'third-td','headers'=>'media_datatype_actions'],'filterOptions'=>['headers'=>'media_datatype_actions'],
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
		  							'class' => 'icon-set',
                                                                        'aria-label' => 'Edit Media Data Type',
		  							'onclick'=>'UpdateMediaDataType('.$key.');'
		  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:DeleteMediaDataType('.$key.');', [
		  						'title' => Yii::t('yii', 'Delete'),
		  						'class' => 'icon-set',
                                                                'aria-label' => 'Remove Media Data Type',
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->data_type."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'data_type', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'media_datatype_name'],'filterOptions'=>['headers'=>'media_datatype_name'], 'headerOptions' => ['title' => 'Data Type'], 'headerOptions' => ['title' => 'Data Type','id'=>'media_datatype_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'],'filterType'=>$filter_type['data_type'],'filterWidgetOptions'=>$filterWidgetOption['data_type']],
		 ],
		'floatHeader'=>true,
		
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'datatype-pajax','enablePushState' => false],
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
   <?= Html::button('All Media Data Type',['title'=>"All Media Data Type",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'MediaDataType();'])?> 	
   <button class="btn btn-primary" title="Remove" onclick="RemoveMediaDataType();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddMediaDataType();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('datatype-pajax');
</script>
<noscript></noscript>
