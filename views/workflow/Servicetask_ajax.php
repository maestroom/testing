<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\sortable\Sortable;
use app\models\Teamservice;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicetask';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>

<?=GridView::widget([
		'id'=>'servicetaskgrid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
                                ['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false, 'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th'],'contentOptions'=>['title'=>'Select Row', 'class' => 'text-center first-td'],'checkboxOptions'=>function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_service_task_'.$key, 'value' => json_encode(array('service_task' => $model->service_task)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
		  		'contentOptions' => ['class' => 'text-center third-td'],
		  		'headerOptions' => ['title' => 'Actions','class'=>'third-th'],
				'mergeHeader'=>false,
		  		'template'=>'{sort}&nbsp;{update}&nbsp;{delete}',
		  		'buttons'=>[
		  				'sort' => function($url, $model, $key){
		  					return Html::a('<em class="fa fa-arrows text-primary" title="Move"></em>', 'javascript:void(0);', ['title' => Yii::t('yii', 'Move'),'class' => 'handel_sort icon-set','data-key' => $model->id]);
		  				},
 		  				'update'=>function ($url, $model, $key) {
		  					return
								Html::a('<em class="fa fa-pencil text-primary" title="Edit"></em>', 'javascript:void(0);', [
									'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Edit Service Task' ),
									'class' => 'icon-set',
									'onclick'=>'UpdateServiceTask('.$key.','.$model->teamId.','.$model->teamservice_id.');'
								]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em class="fa fa-close text-primary" title="Remove"></em>', 'javascript:DeleteServiceTask('.$key.','.$model->teamservice_id.');', [
			  						'title' => Yii::t('yii', 'Remove'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Remove' ),
			  						'class' => 'icon-set',
			  						'data' => [
			  						'confirm' => Yii::t('yii',"Are you sure you want to Remove ".$model->service_task."?"),
		  						],
		  					]);
		  				},
		  			],
		  		],
				['attribute' => 'service_task', 'headerOptions' => ['title' => 'Service Task'], 'contentOptions' => ['style' => 'padding: 4px 8px;'],'format' => 'raw', 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control','aria-label'=>'Filter By Service Tasks'],'filterType'=>$filter_type['service_task'],'filterWidgetOptions'=>$filterWidgetOption['service_task']],
		  		['attribute' => 'hasform', 'headerOptions' => ['title' => 'Instruction Form?'], 'header' => 'Instructions?', 'contentOptions' => ['class' => 'text-center', 'style'=>'width:110px;padding: 4px 8px;'], 'format' => 'raw','filterType'=>$filter_type['hasform'],'filterWidgetOptions'=>$filterWidgetOption['hasform'],'value' => function ($model) {if ($model->publish=="1") {return '<a href="#" title="Form Published" id="serviceTaskForm" data-id='.$model->id.' data-team-id = '.$model->teamId.' data-name="instruction" data-team-serviceid = '.$model->teamservice_id.' class="tag-header-black"><em class="fa fa-check text-danger" title="Form Published"></em></a>';}else if($model->hasform=="1") {return '<a href="#" id="serviceTaskForm" data-id='.$model->id.' data-name="instruction" data-team-id = '.$model->teamId.' data-team-serviceid = '.$model->teamservice_id.' class="tag-header-black"><em class="fa fa-save text-danger" title="Form Saved"></em></a>';}else {return false;}},'filterInputOptions'=>['title'=>'Filter By Instruction Form','class'=>'form-control']],
		  		['attribute' => 'billable_item', 'headerOptions' => ['title' => 'Billable?'], 'header' => 'Billable?', 'contentOptions' => ['class' => 'text-center', 'style'=>'width:110px;padding: 4px 8px;'],'format' => 'raw','filterType'=>$filter_type['billable_item'],'filterWidgetOptions'=>$filterWidgetOption['billable_item'],'value' => function ($model, $key) {if($model->billable_item=="2"){return '<a href="#" title="Billable Force" class="tag-header-black" onClick="UpdateServiceTask('.$key.','.$model->teamId.','.$model->teamservice_id.')"><em class="fa fa-check text-danger" title="Billable Force"></em></a>';}else if ($model->billable_item=="1") {return '<a href="#" title="Billable Option" class="tag-header-black" onClick="UpdateServiceTask('.$key.','.$model->teamId.','.$model->teamservice_id.')"><em class="fa fa-check text-gray" title="Billable Option"></em></a>';}else {return false;}},'filterInputOptions'=>['aria-label'=>'Filter By Billabel','class'=>'form-control']],
		  		['attribute' => 'data_hasform', 'headerOptions' => ['title' => 'Task Outcome Form?'], 'header' => 'Outcome?', 'contentOptions' => ['class' => 'text-center', 'style'=>'width:110px;padding: 4px 8px;'],'format' => 'raw','filterType'=>$filter_type['data_hasform'],'filterWidgetOptions'=>$filterWidgetOption['data_hasform'],'value' => function ($model) {if($model->data_publish=="1"){ return '<a href="#" title="Form Published" id="serviceTaskForm" data-id='.$model->id.' data-team-serviceid = '.$model->teamservice_id.' data-team-id = '.$model->teamId.' data-name="data" class="tag-header-black"><em class="fa fa-check text-danger" title="Form Published"></em></a>';} else if ($model->data_hasform=="1") {return '<a href="#" id="serviceTaskForm" title="Form Saved" data-id='.$model->id.' data-team-serviceid = '.$model->teamservice_id.' data-team-id = '.$model->teamId.' class="tag-header-black" data-name="data"><em title="Form Saved" class="fa fa-save text-danger"></em></a>';} else {return false;}},'filterInputOptions'=>['title'=>'Filter By Task Outcome Form','class'=>'form-control']],
		 ],
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'servicetask-pajax','enablePushState' => false],
			'neverTimeout'=>true,
			'beforeGrid'=>'',
        	'afterGrid'=>'',
    	],
    	'export'=>false,
		'hover'=>true,
		'panel'=>false,
		//'responsiveWrap' => false,
		'export'=>false,
		'floatHeader'=>true,
		'floatHeaderOptions' => ['top' => 'auto'],
		//'persistResize'=>false,
		//'resizableColumns'=>false,
		'responsive'=>true,
		//'floatOverflowContainer'=>true,
		'pager' => [
				'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
				'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
				'nextPageLabel' => 'Next',   // Set the label for the "next" page button
				'firstPageLabel' => 'First',   // Set the label for the "first" page button
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
<input type="hidden" id="teamservice_id_d" value="<?php echo $teamservice_id; ?>">
<script type="text/javascript">
$(document).ready(function(){
if($('.table-responsive').length>0 && $('.kv-panel-pager').length>0) {
	if($('.sub-heading').length > 0) {
		var grid_height = $('.table-responsive').height()-$('.kv-panel-pager').height()-$('.sub-heading').height()-5;
	}else{
		var grid_height = $('.table-responsive').height()-$('.kv-panel-pager').height()-5;
	}
	$('.kv-grid-container').height(grid_height);
}
});
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('servicetask-pajax');
$('input').customInput();
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
			$("#servicetaskgrid .sort ").each(function(i){ //new code for sorting
					sort_arr[i]=$(this).data('key');
					if(sorder == "")
						sorder = $(this).data('key');
					else
						sorder = sorder + ','  + $(this).data('key');
			});
			jQuery.ajax({
			       url: baseUrl +'/workflow/sortservicetask',
			       data:{sort_ids: sorder},
			       type: 'post',
			       beforeSend:function (data) {showLoader();},
			       success: function (data) {
			    	   hideLoader();
			    	 /*  if(data != 'OK')
			    		  alert('Error');*/
			       }
			  });
		}
	}).disableSelection();
</script>
<noscript></noscript>
