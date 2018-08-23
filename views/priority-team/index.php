<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\sortable\Sortable;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PriorityTeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Priority Teams';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="table-responsive">
<?= 
 GridView::widget([
 		'id'=>'team-priority-grid',		
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn','mergeHeader'=>false,'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'project_priority_team_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'project_priority_team_checkbox'],'filterOptions'=>['headers'=>'project_priority_team_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_tasks_priority_name'/*.$model->team_id."-".$model->team_loc_id*/, 'value' => json_encode(array('team_id' => $model->team_id, "team_loc" => $model->team_loc_id)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
					'headerOptions' => ['class' => 'third-th','title'=>'Actions','id'=>'project_priority_team_actions','scope'=>'col'],
					'contentOptions' => ['class' => 'third-td','headers'=>'project_priority_team_actions'],'filterOptions'=>['headers'=>'project_priority_team_actions'],
					'mergeHeader'=>false,
					'width'=>'14%',
					'template'=>'{sort}&nbsp;{update}&nbsp;{delete}',
					'buttons'=>[
		  				'update'=>function ($url, $model, $key) {
							return
								Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
									'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Edit Project Priority Point' ),
									'class' => 'icon-set',
									'onclick'=>'UpdateProjectPriorityTeam('.$model->team_id.','.$model->team_loc_id.');'
								]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
		  					return
		  						Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:DeleteProjectPriorityTeam('.$model->team_id.','.$model->team_loc_id.');', [
									'title' => Yii::t('yii', 'Remove'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Remove' ),
									'class' => 'icon-set',
									'data' => [
									'confirm' =>  Yii::t('yii',"Are you sure you want to Remove ?"),
								],
		  					]);
		  				},
		  			],
		  		],
		  		['attribute'=>'team_location', 'label' => 'Team - Location', 'contentOptions' => ['style' => 'padding:4px 7px;', 'headers'=>'project_priority_team_text'],'filterOptions'=>['headers'=>'project_priority_team_text'], 'headerOptions' => ['title' => 'Team - Location', 'id'=>'project_priority_team_text','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['team_loc_id'],'filterWidgetOptions'=>$filterWidgetOption['team_loc_id'], 'value'=>function($model){ return $model->getPriorityTeamLocation($model->team_location);}],
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'team-priority-pajax','enablePushState' => false],
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
	<?= Html::button('All Project Priority - Team',['title'=>"All Project Priority - Team",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'ProjectPriorityTeam();'])?> 
   <button class="btn btn-primary" title="Remove" onclick="RemoveProjectPriorityTeam();">Remove</button>
   <button class="btn btn-primary" title="Add"    onclick="AddProjectPriorityTeam();">Add</button>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('team-priority-pajax');
</script>
<noscript></noscript>
