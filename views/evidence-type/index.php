<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Types';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'mediatype-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false, 'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'media_type_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'media_type_checkbox'],'filterOptions'=>['headers'=>'media_type_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_evidence_name_'.$key, 'value' => json_encode(array('evidence_name' => $model->evidence_name)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
				'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'media_type_actions','scope'=>'col'],
		  		'contentOptions' => ['class' => 'third-td','headers'=>'media_type_actions'],'filterOptions'=>['headers'=>'media_type_actions'],
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
			  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
			  							'title' => Yii::t('yii', 'Edit'),
                                                                                'aria-label' => Yii::t ( 'yii', 'Edit Media Type' ),
			  							'class' => 'icon-set',
			  							'onclick'=>'UpdateMediaType('.$key.');'
			  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteMediaType('.$key.');', [
		  						'title' => Yii::t('yii', 'Remove'),
                                                                'aria-label' => Yii::t ( 'yii', 'Remove' ),
		  						'class' => 'icon-set',
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->evidence_name."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'evidence_name', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'media_type_name'],'filterOptions'=>['headers'=>'media_type_name'], 'headerOptions' => ['title' => 'Evidence Name','id'=>'media_type_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['evidence_name'],'filterWidgetOptions'=>$filterWidgetOption['evidence_name']],
		  		['attribute'=>'est_size', 'contentOptions' => ['class' => 'media-type-grid-column text-center','style' => 'padding:4px 7px;','headers'=>'media_type_size'],'filterOptions'=>['headers'=>'media_type_size'], 'headerOptions' => ['title' => 'Est Size','id'=>'media_type_size','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['est_size'],'filterWidgetOptions'=>$filterWidgetOption['est_size']],
		  		['attribute' => 'unit', 'contentOptions' => ['class' => 'media-type-grid-column','style' => 'padding:4px 7px;','headers'=>'media_type_unit'],'filterOptions'=>['headers'=>'media_type_unit'], 'headerOptions' => ['title' => 'Default Unit','id'=>'media_type_unit','scope'=>'col'], 'value' => 'unit.unit_name','filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['unit_name'],'filterWidgetOptions'=>$filterWidgetOption['unit_name']],
		  	
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'mediatype-pajax','enablePushState' => false],
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
    <?= Html::button('All Media Type',['title'=>"All Media Type",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'MediaType();'])?> 	
   <button class="btn btn-primary" title="Remove" onclick="RemoveMediaType();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddMediaType();">Add</button>
</div>
<style>
	.media-type-grid-column {width:15%;}
</style>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('mediatype-pajax');
</script>
<noscript></noscript>
