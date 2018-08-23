<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceToSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Tos';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'mediato-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'media_to_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'media_to_checkbox'],'filterOptions'=>['headers'=>'media_to_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_to_name_'.$key, 'value' => json_encode(array('to_name' => $model->to_name)) ]; }],
				[ 'class' => 'kartik\grid\ActionColumn',
		  				'contentOptions' => ['class' => 'third-td','headers'=>'media_to_actions'],'filterOptions'=>['headers'=>'media_to_actions'],
						'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'media_to_actions','scope'=>'col'],
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
		  							'class' => 'icon-set',
                                                                        'aria-label' => 'Edit Media To',
		  							'onclick'=>'UpdateMediaTo('.$key.');'
		  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteMediaTo('.$key.');', [
		  						'title' => Yii::t('yii', 'Remove'),
		  						'class' => 'icon-set',
                                                                'aria-label' => 'Remove Media To',
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->to_name."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'to_name', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'media_to_name'],'filterOptions'=>['headers'=>'media_to_name'],'headerOptions' => ['title' => 'Media To','id'=>'media_to_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['to_name'],'filterWidgetOptions'=>$filterWidgetOption['to_name']],
		  	
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'mediato-pajax','enablePushState' => false],
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
    <?= Html::button('All Media To',['title'=>"All Media To",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'MediaTo();'])?> 
   <button class="btn btn-primary" title="Remove" onclick="RemoveMediaTo();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddMediaTo();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('mediato-pajax');
</script>
<noscript></noscript>
