<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectRequestTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Request Types';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'projectreqtype-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'project_request_type_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'project_request_type_checkbox'],'filterOptions'=>['headers'=>'project_request_type_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_request_type_'.$key, 'value' => json_encode(array('request_type' => $model->request_type)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
						'headerOptions' => ['class' => 'third-th','title'=>'Actions','id'=>'project_request_type_actions','scope'=>'col'],
		  				'contentOptions' => ['class' => 'third-td','headers'=>'project_request_type_actions'],'filterOptions'=>['headers'=>'project_request_type_actions'],
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{update_role}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Edit Project Request Type' ),
		  							'class' => 'icon-set',
		  							'onclick'=>'UpdateProjectRequestType('.$key.');'
		  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteProjectRequestType('.$key.');', [
		  						'title' => Yii::t('yii', 'Remove'),
                                                                'aria-label' => Yii::t ( 'yii', 'Remove' ),
		  						'class' => 'icon-set',		
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->request_type."?"),
		  						],
		  					]);
		  				},
		  				'update_role'=>function ($url, $model, $key) {
                                                        $hoverText = $model->getRequestRolesStatus($key);
                                                        if($hoverText == 'eye'){
                                                            return 
                                                            Html::a('<em title="Apply Field Security" class="fa fa-'.$hoverText.' text-primary"></em>', 'javascript:void(0);', [
                                                                            'title' => Yii::t('yii', 'Apply Field Security'),
                                                                            'aria-label' => Yii::t('yii', 'Apply Field Security'),
                                                                            'class' => 'icon-set',
                                                                            'onclick'=>'getAllRoleTypes('.$key.');'
                                                            ]);
                                                        }else{
                                                             return 
                                                            Html::a('<em title="'.$hoverText[1].'" class="fa fa-'.$hoverText[0].' text-primary"></em>', 'javascript:void(0);', [
                                                                            'title' => Yii::t('yii', $hoverText[1]),
                                                                            'aria-label' => Yii::t('yii', $hoverText[1]),
                                                                            'class' => 'icon-set',
                                                                            'onclick'=>'getAllRoleTypes('.$key.');'
                                                            ]);
                                                        }
		  					
		  				},
		  			],
		  		],
		  		['attribute'=>'request_type', 'contentOptions' => ['style' => 'padding:4px 7px;'], 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'project_request_type_text'],'filterOptions'=>['headers'=>'project_request_type_text'],'headerOptions' => ['title' => 'Request Type','id'=>'project_request_type_text','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['request_type'],'filterWidgetOptions'=>$filterWidgetOption['request_type']],
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'projectreqtype-pajax','enablePushState' => false],
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
   <?= Html::button('All Project Request Type',['title'=>"All Project Request Type",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'ProjectRequestType();'])?>	
   <button class="btn btn-primary" title="Remove" onclick="RemoveProjectRequestType();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddProjectRequestType();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('projectreqtype-pajax');
</script>
<noscript></noscript>
