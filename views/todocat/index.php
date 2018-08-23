<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TodoCatsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Todo Cats';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;

?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'todocat-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'todo_followup_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'todo_followup_checkbox'],'filterOptions'=>['headers'=>'todo_followup_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_todo_cat_'.$key, 'value' => json_encode(array('todo_cat' => $model->todo_cat)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
				'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'todo_followup_actions','scope'=>'col'],
		  		'contentOptions' => ['class' => 'third-td','headers'=>'todo_followup_actions'],'filterOptions'=>['headers'=>'todo_followup_actions'],	
				'mergeHeader'=>false,
		  		'template'=>'{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
		  					return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Edit To Do Follow up Category' ),
		  							'class' => 'icon-set',
		  							'onclick'=>'UpdateToDoFollowupCategory('.$key.');'
		  					]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteToDoFollowupCategory('.$key.');', [
		  						'title' => Yii::t('yii', 'Remove'),
                                                                'aria-label' => Yii::t ( 'yii', 'Remove' ),
		  						'class' => 'icon-set',		
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->todo_cat."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'todo_cat', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'todo_followup_category'],'filterOptions'=>['headers'=>'todo_followup_category'],'headerOptions' => ['title'=>'ToDo Category','id'=>'todo_followup_category','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'],'label'=>'ToDo Category','filterType'=>$filter_type['todo_cat'],'filterWidgetOptions'=>$filterWidgetOption['todo_cat']],
		  		['attribute'=>'todo_desc', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'todo_followup_description'],'filterOptions'=>['headers'=>'todo_followup_description'],'headerOptions' => ['title'=>'ToDo Description','id'=>'todo_followup_description','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'],'label'=>'ToDo Description'],
		  		['attribute' => 'stop', 'header' => 'Stop Clock','contentOptions' => ['style' => 'padding:4px 7px;','class' => 'text-center','headers'=>'todo_followup_clock'],'filterOptions'=>['headers'=>'todo_followup_clock'],'headerOptions' => ['title'=>'Stop Clock','id'=>'todo_followup_clock','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'],'filterType'=>$filter_type['stop'],'filterWidgetOptions'=>$filterWidgetOption['stop'],'format' => 'raw','value' => function ($model) {if ($model->stop) {return '<span tabindex="0" class="fa fa-hand-stop-o" title="stop clock"></span>';} else {return false;}}],
		 ],
		 'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'todocat-pajax','enablePushState' => false],
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
   <?= Html::button('All ToDo Follow-up Category',['title'=>"All ToDo Follow-up Category",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'ToDoFollowupCategory();'])?>	
   <button class="btn btn-primary" title="Remove" onclick="RemoveToDoFollowupCategory();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddToDoFollowupCategory();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('todocat-pajax');
</script>
<noscript></noscript>
