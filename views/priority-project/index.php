<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\sortable\Sortable;


/* @var $this yii\web\View */
/* @var $searchModel app\models\PriorityProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Priority Projects';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'project-priority-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'project_priority_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'project_priority_checkbox'],'filterOptions'=>['headers'=>'project_priority_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_priority_'.$key, 'value' => json_encode(array('priority' => $model->priority)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
				'headerOptions' => ['class' => 'third-th','title'=>'Actions','id'=>'project_priority_actions','scope'=>'col'],
		 		'contentOptions' => ['class' => 'third-td','headers'=>'project_priority_actions'],'filterOptions'=>['headers'=>'project_priority_actions'],
				'mergeHeader' => false,
				'width'=>'14%',
		  		'template'=>'{sort}&nbsp;{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'sort' => function($url, $model, $key){
		  					return Html::a('<em title="Move" class="fa fa-arrows text-primary"></em>', 'javascript:void(0);', ['title' => Yii::t('yii', 'Move'),'class' => 'icon-set handel_sort ','data-key' => $model->id]);
		  				},
		  				'update'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
			  							'title' => Yii::t('yii', 'Edit'),
                                                                                'aria-label' => Yii::t ( 'yii', 'Edit Project Priority' ),
			  							'class' => 'icon-set',
			  							'onclick'=>'UpdateProjectPriority('.$key.');'
		  						]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteProjectPriority('.$key.');', [
			  						'title' => Yii::t('yii', 'Remove'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Remove' ),
			  						'class' => 'icon-set',
			  						'data' => [
			  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->priority."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'priority', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'project_priority_text'],'filterOptions'=>['headers'=>'project_priority_text'], 'headerOptions' => ['title' => 'Priority','id'=>'project_priority_text','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['priority'],'filterWidgetOptions'=>$filterWidgetOption['priority']],
		  		['attribute'=>'project_priority_order', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'project_priority_order_text'],'filterOptions'=>['headers'=>'project_priority_order_text'], 'headerOptions' => ['title' => 'Project Priority Order','id'=>'project_priority_order_text','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['project_priority_order'],'filterWidgetOptions'=>$filterWidgetOption['project_priority_order']],
		 ],
		 'options' => [
		 		'data' => [
		 				'sortable-widget' => 1,
		 				'sortable-url' => \yii\helpers\Url::toRoute(['sorting']),
		 		],
		 		'class'=>'grid-view',
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'project-priority-pajax','enablePushState' => false],
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
		],
		'rowOptions'=>['class'=>'sort'],
]);
		  ?>
</div>
<div class="button-set button-set text-right">
   <?= Html::button('All Project Priority',['title'=>"All Project Priority",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'ProjectPriority();'])?>  	
   <button class="btn btn-primary" title="Remove" onclick="RemoveProjectPriority();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddProjectPriority();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('project-priority-pajax');
var fixHelper = function(e, ui) {
	ui.children().each(function() {
	$(this).width($(this).width());
	});
	return ui;
};
$(".sort").parent().sortable({
  	handle:'.handel_sort',
	helper: fixHelper,
	update: function(e,ui) { 
		var sorder="";
		var sort_arr = new Array();
		$(".sort ").each(function(i){ //new code for sorting
				sort_arr[i]=$(this).data('key');
				if(sorder == "")
					sorder = $(this).data('key');
				else
					sorder = sorder + ','  + $(this).data('key');
		});
		jQuery.ajax({
		       url: baseUrl +'/system/projectprioriry',
		       data:{sort_ids: sorder},
		       type: 'post',
		       beforeSend:function (data) {showLoader();},
		       success: function (data) {
		    	   hideLoader();
		    	   /*if(data != 'OK')
		    		  alert('Error');*/
		       }
		  });
	}
}).disableSelection(); 
</script>
<noscript></noscript>
