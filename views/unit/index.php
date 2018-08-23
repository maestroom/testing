<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UnitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Units';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>

<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'unit-pajax',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 
					'mergeHeader'=>false,
					'rowHighlight' => false,
					'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'media_dataunits_checkbox','scope'=>'col'],
					'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'media_dataunits_checkbox'],
					'filterOptions'=>['headers'=>'media_data_units_checkbox'], 
					'checkboxOptions' => function($model, $key, $index, $column){ 
						return [
							'customInput'=>true, 
							'class' => 'chk_unit_name_'.$key, 
							'value' => json_encode(array('unit_name' => $model->unit_name)) 
						]; 
					}
				],
				['attribute'=>'unit_name', 
					'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'media_data_units_name','class'=>'media_data_units_name'],
					'filterOptions'=>['headers'=>'media_data_units_name'], 
					'headerOptions' => ['title' => 'Unit Name','id'=>'media_data_units_name','scope'=>'col'], 
					'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 
					'filterType'=>$filter_type['unit_name'],
					'filterWidgetOptions'=>$filterWidgetOption['unit_name']
					],
		  		['attribute'=>'default_unit', 
					'header'=>'Default Unit?',
					'contentOptions' => ['style' => 'padding:4px 7px;','class'=>'text-center media_data_default_unit'],
					'filterOptions'=>['headers'=>'media_data_default_unit'], 
					'headerOptions' => ['title' => 'Default Unit?','id'=>'media_data_default_unit','scope'=>'col'],
					'filterInputOptions' => ['placeholder' => 'Filter Default Unit', 'class' => 'form-control'],
					'filterType'=>$filter_type['default_unit'],
					'filterWidgetOptions'=>$filterWidgetOption['default_unit'],
					'format' => 'raw',
					'value' => function ($model) {if ($model->default_unit==1) {return '<a href="javascript:void(0);" title="Default Unit?" class="tag-header-black" aria-label="Default Unit?"><em title="Default Unit?" class="fa fa-check text-danger"></em></a>';} else {return false;}}
				],
				['class' => 'kartik\grid\ActionColumn',
					'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'media_dataunits_actions','scope'=>'col'],
					'contentOptions' => ['class' => 'third-td text-left-important','headers'=>'media_dataunits_actions'],'filterOptions'=>['headers'=>'media_dataunits_actions'],
					'mergeHeader'=>false,
					'template'=>'{sort}&nbsp;{update}&nbsp;{delete}',
					'buttons'=>[
						'sort' => function ($url, $model, $key) { 
							return 
							Html::a('<em title="Move" class="fa fa-arrows text-primary"></em>', 'javascript:void(0);', [
								'title' => Yii::t('yii', 'Move'),
								'class' => 'handel_sort icon-set',
								'data-key' => $model->id]); 
						},
		  				'update'=>function ($url, $model, $key) {
		  					return $model->default_unit==0?	  					
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Edit Media Data Units' ),
		  							'class' => 'icon-set',
		  							'onclick'=>'UpdateMediaDataUnits('.$key.');'
		  					]):'<span class="custom-icon-set"></span>';
		  				},
		  				/*'hidden'=>function ($url, $model, $key) {
		  					return $model->is_hidden == 0 ? 
		  					Html::a('<em class="fa fa-eye text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Available'),
		  							'class' => 'icon-set',
		  							'onclick'=>'UpdateUnitHidden('.$key.',1);'
		  					]):Html::a('<em class="fa fa-eye-slash text-primary"></em>', 'javascript:void(0);', [
		  							'title' => Yii::t('yii', 'Hidden'),
		  							'class' => 'icon-set',
		  							'onclick'=>'UpdateUnitHidden('.$key.',0);'
		  					]);
		  				},*/
		  				'delete'=>function ($url, $model, $key) {
		  					return $model->default_unit==0?	  
								Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteMediaDataUnits('.$key.');', [
		  						'title' => Yii::t('yii', 'Remove'),
                                                                'aria-label' => Yii::t ( 'yii', 'Remove' ),
		  						'class' => 'icon-set',		
		  						'data' => [
		  						'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->unit_name."?"),
		  						],
		  					]):'<span class="custom-icon-set"></span>';
		  				},
		  			],
		  		],
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'unit-pajax-1','enablePushState' => false],
			'neverTimeout'=>true,
			'beforeGrid'=>'',
        	'afterGrid'=>'<script>$(\'.default-tr input[type="checkbox"]\').each(function(){
                                $(this).next(\'label\').remove();				
                                $(this).remove();                                
			});</script>',
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
		//'rowOptions' => [ 'class' => 'sort'],
		'rowOptions' => function ($model, $index, $widget, $grid) use ($task_id,$case_id,$team_id,$team_loc,$modelTaksInstruction,$belongtocurr_team){
			if($model['default_unit'] == 1){ 
				return ['class'=>'sort default-tr'];
			} else {
				return ['class'=>'sort'];
			}
		},
]);
		  ?>
</div>
<div class="button-set button-set text-right">
    <?= Html::button('All Media Data Units',['title'=>"All Media Data Units",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'MediaDataUnits();'])?> 		
   <button class="btn btn-primary" title="Remove" onclick="RemoveMediaDataUnits();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddMediaDataUnits();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('unit-pajax');

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
			   url: baseUrl +'/unit/sortunits',
			   data:{sort_ids: sorder},
			   type: 'post',
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   hideLoader();
			   }
		  });
	}
}).disableSelection(); 

$(document).ready(function(){
	$('.default-tr input[type="checkbox"]').each(function(){            
            $(this).next('label').remove();
            $(this).remove();
	});	
});
</script>
<noscript></noscript>
