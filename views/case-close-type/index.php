<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CaseCloseTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Close Types';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 	GridView::widget([
 		'id'=>'caseclosetype-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader' => false, 'rowHighlight' => false, 'headerOptions' => ['title'=>'Select All/None','class'=>'first-th','id'=>'case_close_checkbox','scope'=>'col'], 'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'case_close_checkbox'],'filterOptions'=>['headers'=>'case_close_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_close_type_'.$key, 'value' => json_encode(array('close_type' => $model->close_type)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
				'headerOptions'=>['class'=>'third-th','title'=>'Actions','id'=>'case_close_actions','scope'=>'col'],
		  		'contentOptions' => ['class' => 'third-td','headers'=>'case_close_actions'],'filterOptions'=>['headers'=>'case_close_actions'],
		  		'template'=>'{update}&nbsp;{delete}',
		  		'mergeHeader'=>false,
		  		'buttons'=>[
                                            'update'=>function ($url, $model, $key) {
                                                return
                                                    Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
                                                        'title' => Yii::t('yii', 'Edit'),
                                                        'class' => 'icon-set',
                                                        'aria-label' => 'Edit Case Close Type',
                                                        'onclick'=>'UpdateCaseCloseType('.$key.');'
                                                    ]);
                                            },
                                            'delete'=>function ($url, $model, $key) {
                                                return
                                                    Html::a('<em title="Delete" class="fa fa-close text-primary"></em>', 'javascript:DeleteCaseCloseType('.$key.');', [
                                                        'title' => Yii::t('yii', 'Delete'),
                                                        'class' => 'icon-set',
                                                        'aria-label' => 'Delete Case Close Type',
                                                        'data' => [
                                                            'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->close_type."?"),
                                                        ],
                                                    ]);
                                            },
		  			],
		  		],
		  		['attribute' => 'close_type', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'case_close_type'],'filterOptions'=>['headers'=>'case_close_type'], 'headerOptions' => ['title' => 'Close Type','id'=>'case_close_type','scope'=>'col'], 'format' => 'raw', 'filterType'=>$filter_type['close_type'],'filterWidgetOptions'=>$filterWidgetOption['close_type'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control', 'id' => 'close_type']],
		  		
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'floatHeaderOptions' => ['top' => 'auto'],
		'pjaxSettings'=>[
			'options' => ['id'=>'caseclosetype-pajax','enablePushState' => false],
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
   
            <?= Html::button('All Case Close Type',['title'=>"All Case Close Type",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'CaseCloseType();'])?>  	
   <button class="btn btn-primary" title="Remove" onclick="RemoveCaseCloseType();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddCaseCloseType();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('caseclosetype-pajax');
</script>
<noscript></noscript>	 
