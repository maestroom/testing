<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceEncryptTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Encrypt Types';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'mediaencryptype-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn','mergeHeader'=>false, 'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>' media_encrypt_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'media_encrypt_checkbox'],'filterOptions'=>['headers'=>'media_encrypt_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_encrypt_'.$key, 'value' => json_encode(array('encrypt' => $model->encrypt)) ];}],
				['class' => 'kartik\grid\ActionColumn',
						'headerOptions' => ['class' => 'third-th','title'=>'Actions','id'=>'media_encrypt_actions','scope'=>'col'],
		  				'contentOptions' => ['class' => 'third-td','headers'=>'media_encrypt_actions'],'filterOptions'=>['headers'=>'media_encrypt_actions'],
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
		  							'class' => 'icon-set',
                                                                        'aria-label' => 'Edit Media Encrypt',
		  							'onclick'=>'UpdateMediaEncrypt('.$key.');'
		  					]);
		  				},
		  				'delete' => function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:DeleteMediaEncrypt('.$key.');', [
		  						'title' => Yii::t('yii', 'Delete'),
		  						'class' => 'icon-set',
                                                                'aria-label' => 'Remove Media Encrypt',
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->encrypt."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'encrypt', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'media_encrypt_name'],'filterOptions'=>['headers'=>'media_encrypt_name'], 'headerOptions' => ['title' => 'Encrypt','id'=>'media_encrypt_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['encrypt'],'filterWidgetOptions'=>$filterWidgetOption['encrypt']],
		  		
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'mediaencryptype-pajax','enablePushState' => false],
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
	<?= Html::button('All Media Encrypt',['title'=>"All Media Encrypt",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'MediaEncrypt();'])?> 	
   <button class="btn btn-primary" title="Remove" onclick="RemoveMediaEncrypt();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddMediaEncrypt();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('mediaencryptype-pajax');
</script>
<noscript></noscript>
