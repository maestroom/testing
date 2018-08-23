<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\helpers\Url;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\Options;
use app\models\EvidenceProduction;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceProductionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evidence Productions';
$this->params['breadcrumbs'][] = $this->title;

$model_evidProd = new EvidenceProduction();
$labels=$model_evidProd->attributeLabels();
if ((new User)->checkAccess(4.0071) || (new User)->checkAccess(4.0072)) {
$action_column = ['class' => 'kartik\grid\ActionColumn',
                            'contentOptions' => ['class' => 'third-td','headers'=>'case_production_actions'],
                            'headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'case_production_actions','scope'=>'col'],
                            'filterOptions'=>['headers'=>'case_production_actions'],
                            'template'=>'{update}&nbsp;{delete}',
                            'mergeHeader'=>false,
                            'buttons'=>[
                                        'update' => function ($url, $model, $key) {
                                        if((new User)->checkAccess(4.0071)) {	
                                                return Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em><span class="screenreader">Production</span>', 'javascript:void(0);', [
                                                                'title' => Yii::t('yii', 'Edit'),
                                                                'onclick' => 'UpdateProduction('.$key.');'
                                                        ]);
                                                }
                                        },
                                        'delete' => function ($url, $model, $key) {
                                                if((new User)->checkAccess(4.0072)) {
                                                        return Html::a('<em title="Delete" class="fa fa-close text-primary"></em><span class="screenreader">Production</span>', 'javascript:deleteCaseProduction('.$key.');', [
                                                                                'title' => Yii::t('yii', 'Delete'),
                                                            'aria-label' => Yii::t ( 'yii', 'Delete' ),
                                                                                'data' => [
                                                                                                'confirm' =>  Yii::t('yii',"Are you sure you want to Delete Production #".$key."?"),
                                                                                ],
                                                                ]);
                                                }
                                        },
                                ],
                                'order'=>DynaGrid::ORDER_FIX_LEFT
                            ];
			}else{
				$action_column = [];
			}
$columns=[
				['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-production/get-prod-deatail']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'case_production_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'case_production_expand'],'filterOptions'=>['headers'=>'case_production_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span><span class="screenreader">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false,'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
				['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_production_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'case_production_checkbox'],'filterOptions'=>['headers'=>'case_production_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
				$action_column
];
if(!empty($prod_form)) {
	foreach($prod_form as $column){
		if($column=='id'){
			$columns[]=['attribute'=>'id','headerOptions'=>['title'=>'Production #','class'=>'prod_no_th word-break','id'=>'case_production_id','scope'=>'col'],'label'=>'Production #','contentOptions'=>['class' => 'word-break first-td prod_no text-center','headers'=>'case_production_id'],'filterOptions'=>['headers'=>'case_production_id'],'filterInputOptions'=>['title'=>'Filter by Production #'],
			//'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']
			'filter'=>'<input type="text" class="form-control filter_number_only" name="EvidenceProductionSearch[id]" value="'.$params['EvidenceProductionSearch']['id'].'">'
			];
		}else if($column=='has_media'){
			$columns[]=['attribute' => 'has_media', 'format' => 'raw','filterInputOptions' => ['title' => 'Filter By Media','prompt' => ' '],'headerOptions'=>['title'=>'Has Media?','class'=>'has_media_th','id'=>'case_production_media','scope'=>'col'],'header'=>'Media?','contentOptions'=>['class' => 'first-td text-center has_media word-break','headers'=>'case_production_media'],'filterOptions'=>['headers'=>'case_production_media'],'filterType'=>$filter_type['has_media'],'filterWidgetOptions'=>$filterWidgetOption['has_media'], 'value' => function($model){ return $model->getStatus($model->has_media,'has_media');}];
		}else if($column=='has_hold'){
		  	$columns[]=['attribute' => 'has_hold', 'format' => 'raw','filterInputOptions'=>['title'=>'Filter by On Hold','class'=>'form-control','prompt'=>' '],'headerOptions'=>['title'=>'Media on Hold?','class'=>'has_hold_th','id'=>'case_production_hold','scope'=>'col'],'header'=>'Hold?','contentOptions'=>['class' => 'first-td text-center has_hold word-break','headers'=>'case_production_hold'],'filterOptions'=>['headers'=>'case_production_hold'],'filterType'=>$filter_type['has_hold'],'filterWidgetOptions'=>$filterWidgetOption['has_hold'],  'value' => function($model){ return $model->getStatus($model->has_hold,'has_hold');}];
		}else if($column=='has_projects'){
		  	$columns[]=['attribute' => 'has_projects', 'format' => 'raw','filterType'=>$filter_type['has_projects'],'filterWidgetOptions'=>$filterWidgetOption['has_projects'], 'headerOptions'=>['title'=>'Has Projects?','class'=>'has_projects_th','id'=>'case_production_projects','scope'=>'col'],'header'=>'Projects?','contentOptions'=>['class' => 'word-break first-td text-center has_projects','headers'=>'case_production_projects'],'filterOptions'=>['headers'=>'case_production_projects'], 'filterInputOptions' => ['title' => 'Filter By Projects','prompt' => ' '],'value' => function($model){ return $model->getStatus($model->has_projects,'has_projects',$model->id);}];
		}else if($column=='staff_assigned'){
			$columns[]=['attribute'=>'staff_assigned','headerOptions'=>['title'=>'Staff Assigned','class'=>'staff_assigned_th word-break','id'=>'case_production_staff_assigned','scope'=>'col'],'label'=>'Staff Assigned', 'contentOptions'=>['class' => 'staff_assigned word-break','headers'=>'case_production_staff_assigned'],'filterOptions'=>['headers'=>'case_production_staff_assigned'],'filterType'=>$filter_type['staff_assigned'],'filterWidgetOptions'=>$filterWidgetOption['staff_assigned']];
		}else if($column=='prod_date'){
			$columns[]=['attribute' => 'prod_date','headerOptions'=>['title'=>'Production Date','class' => 'global-date-width word-break','id'=>'case_production_date','scope'=>'col'],'label'=>'Prod Date', 'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_date'],'filterOptions'=>['headers'=>'case_production_date'],'filterType'=>$filter_type['prod_date'],'filterWidgetOptions'=>$filterWidgetOption['prod_date'], 'value'=>function($model){ return $model->getProdDate($model->prod_date);}];
		}else if($column=='prod_rec_date'){
			$columns[]=['attribute' => 'prod_rec_date','headerOptions'=>['title'=>'Production Received Date','class' => 'global-date-width word-break','id'=>'case_production_received_date','scope'=>'col'],'filterInputOptions'=>['title'=>'Filter By Production Received Date','class'=>'form-control'],'label'=>'Prod Received', 'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_received_date'],'filterOptions'=>['headers'=>'case_production_received_date'],'filterType'=>$filter_type['prod_rec_date'],'filterWidgetOptions'=>$filterWidgetOption['prod_rec_date'], 'value'=>function($model){return date('m/d/Y',strtotime($model->prod_rec_date));}];
		}else if($column=='prod_party'){
			$columns[]=['attribute'=>'prod_party','headerOptions'=>['title'=>'Producing Party','class'=>'prod_party_th word-break','id'=>'case_production_party','scope'=>'col'],'label'=>'Producing Party',  'contentOptions'=>['class' => 'prod_party word-break','headers'=>'case_production_party'],'filterOptions'=>['headers'=>'case_production_party'],'filterType'=>$filter_type['prod_party'],'filterWidgetOptions'=>$filterWidgetOption['prod_party']];
		}else if($column=='production_desc'){
			$columns[]=['attribute'=>'production_desc','headerOptions'=>['title'=>'Production Description','class'=>'prod_desc_th word-break','id'=>'case_production_description','scope'=>'col'],'label'=>'Prod Description', 'contentOptions'=>['class' => 'prod_desc word-break','headers'=>'case_production_description'],'filterOptions'=>['headers'=>'case_production_description'],'filterType'=>$filter_type['production_desc'],'filterWidgetOptions'=>$filterWidgetOption['production_desc']];
		}else if($column=='cover_let_link'){
			$columns[]=['attribute'=>'cover_let_link','noWrap' => false,'headerOptions'=>['title'=>'Cover Letter Link','class'=>'cover_let_link_th word-break-link','id'=>'case_production_coverletter','scope'=>'col'],'label'=>'Cover Letter Link', 'contentOptions'=>['class' => 'cover_let_link word-break','style'=>'max-width: 240px; overflow: auto; word-wrap: break-word;','headers'=>'case_production_coverletter'],'filterOptions'=>['headers'=>'case_production_coverletter'],'filterType'=>$filter_type['cover_let_link'],'filterWidgetOptions'=>$filterWidgetOption['cover_let_link']];
		}else if($column=='upload_files'){
			$columns[]=['attribute' => 'attach', 'format' => 'raw','headerOptions'=>['title'=>'Attachments','class'=>'attach_th word-break','id'=>'case_production_attachments','scope'=>'col'],'header'=>'Attach','contentOptions'=>['class' => 'attach text-center word-break','headers'=>'case_production_attachments'],'filterOptions'=>['headers'=>'case_production_attachments'], 'value'=>function($model){return $model->getAttachments($model->id,$model->has_attachment);}];
		}else if($column=='prod_orig'){
			$columns[]=['attribute' => 'prod_orig', 'format' => 'raw','headerOptions'=>['title'=>'Production Contains Originals','class'=>'prod_orig_th word-break','id'=>'case_production_contains','scope'=>'col'],'label'=>'Contains Orig','filterInputOptions'=>['title'=>'Filter by Production Contains Originals','class'=>'form-control','prompt'=>' '], 'contentOptions'=>['class' => 'prod_orig text-center word-break','headers'=>'case_production_contains'],'filterOptions'=>['headers'=>'case_production_contains'],'filterType'=>$filter_type['prod_orig'],'filterWidgetOptions'=>$filterWidgetOption['prod_orig'], 'value' => function($model){ return $model->getStatus($model->prod_orig,'prod_orig');}];
		}else if($column=='attorney_notes'){
			$columns[]=['attribute' => 'attorney_notes','headerOptions'=>['title'=>'Attorney Notes','class'=>'attorney_notes_th word-break','id'=>'case_production_notes','scope'=>'col'],'label'=>'Attorney Notes', 'contentOptions'=>['class' => 'attorney_notes word-break','headers'=>'case_production_notes'],'filterOptions'=>['headers'=>'case_production_notes'],'filterType'=>$filter_type['attorney_notes'],'filterWidgetOptions'=>$filterWidgetOption['attorney_notes']];
		}else if($column=='prod_disclose'){
			$columns[]=['attribute'=>'prod_disclose','headerOptions'=>['title'=>'Produced in Initial Disclosures','class'=>'prod_disclose_th word-break','id'=>'case_production_disclosures','scope'=>'col'],'label'=>'Prod in Initial Disclosures','filterInputOptions'=>['title'=>'Filter by Produced in Initial Disclosures','class'=>'form-control'],'contentOptions'=>['class' => 'prod_disclose word-break text-center','headers'=>'case_production_disclosures'],'filterOptions'=>['headers'=>'case_production_disclosures'],'filterType'=>$filter_type['prod_disclose'],'filterWidgetOptions'=>$filterWidgetOption['prod_disclose']];
		}else if($column=='prod_agencies'){
			$columns[]=['attribute' => 'prod_agencies','headerOptions'=>['title'=>'Produced to Other Agencies','class' => 'global-date-width word-break','id'=>'case_production_agencies','scope'=>'col'],'label'=>'Prod to Other Agencies','filterInputOptions'=>['title'=>'Filter by Produced to Other Agencies','class'=>'form-control'],'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_agencies'],'filterOptions'=>['headers'=>'case_production_agencies'],'filterType'=>$filter_type['prod_agencies'],'filterWidgetOptions'=>$filterWidgetOption['prod_agencies'],'value'=>function($model){return $model->getStatus($model->prod_agencies,'prod_agencies');}];
		}else if($column=='prod_access_req'){
			$columns[]=['attribute' => 'prod_access_req','headerOptions'=>['title'=>'Access Request','class' => 'global-date-width word-break','id'=>'case_production_request','scope'=>'col'],'label'=>'Access Request','contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_request'],'filterOptions'=>['headers'=>'case_production_request'],'filterType'=>$filter_type['prod_access_req'],'filterWidgetOptions'=>$filterWidgetOption['prod_access_req'],'value'=>function($model){ return $model->getStatus($model->prod_access_req,'prod_access_req');}];
		}
	}
}else{	
    $columns=[
        ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-production/get-prod-deatail']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'case_production_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'case_production_expand'],'filterOptions'=>['headers'=>'case_production_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false,'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
        ['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_production_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'case_production_checkbox'],'filterOptions'=>['headers'=>'case_production_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
        $action_column,
        ['attribute'=>'id','headerOptions'=>['title'=>'Production #','class'=>'prod_no_th word-break','id'=>'case_production_id','scope'=>'col'],'label'=>'Production #','contentOptions'=>['class' => 'word-break first-td prod_no text-center','headers'=>'case_production_id'],'filterOptions'=>['headers'=>'case_production_id'],'filterInputOptions'=>['title'=>'Filter by Production #'],
		//'filterType'=>$filter_type['id'],'filterWidgetOptions'=>$filterWidgetOption['id']
		'filter'=>'<input type="text" class="form-control filter_number_only" name="EvidenceProductionSearch[id]" value="'.$params['EvidenceProductionSearch']['id'].'">'
		],
        ['attribute' => 'production_type','filterInputOptions' => ['title' => 'Filter By Production Type','prompt' => ' '],'format' => 'raw','headerOptions'=>['title'=>'Production Type','class'=>'prod_type_th word-break','id'=>'case_production_type','scope'=>'col'],'label'=>'Prod Type','contentOptions'=>['class' => 'first-td text-center prod_type','headers'=>'case_production_type'],'filterType'=>$filter_type['production_type'],'filterWidgetOptions'=>$filterWidgetOption['production_type'],'filterOptions'=>['headers'=>'case_production_type'], 'value' => function($model){ return $model->getProdTypeImage($model->production_type);}],
        ['attribute' => 'has_media', 'format' => 'raw','filterInputOptions' => ['title' => 'Filter By Media','prompt' => ' '],'headerOptions'=>['title'=>'Has Media?','class'=>'has_media_th','id'=>'case_production_media','scope'=>'col'],'header'=>'Media?','contentOptions'=>['class' => 'first-td text-center has_media word-break','headers'=>'case_production_media'],'filterOptions'=>['headers'=>'case_production_media'],'filterType'=>$filter_type['has_media'],'filterWidgetOptions'=>$filterWidgetOption['has_media'], 'value' => function($model){ return $model->getStatus($model->has_media,'has_media');}],
        ['attribute' => 'has_hold', 'format' => 'raw','filterInputOptions'=>['title'=>'Filter by On Hold','class'=>'form-control','prompt'=>' '],'headerOptions'=>['title'=>'Media on Hold?','class'=>'has_hold_th','id'=>'case_production_hold','scope'=>'col'],'header'=>'Hold?','contentOptions'=>['class' => 'first-td text-center has_hold word-break','headers'=>'case_production_hold'],'filterOptions'=>['headers'=>'case_production_hold'],'filterType'=>$filter_type['has_hold'],'filterWidgetOptions'=>$filterWidgetOption['has_hold'],  'value' => function($model){ return $model->getStatus($model->has_hold,'has_hold');}],
        ['attribute' => 'has_projects', 'format' => 'raw','filterType'=>$filter_type['has_projects'],'filterWidgetOptions'=>$filterWidgetOption['has_projects'], 'headerOptions'=>['title'=>'Has Projects?','class'=>'has_projects_th','id'=>'case_production_projects','scope'=>'col'],'header'=>'Projects?','contentOptions'=>['class' => 'word-break first-td text-center has_projects','headers'=>'case_production_projects'],'filterOptions'=>['headers'=>'case_production_projects'], 'filterInputOptions' => ['title' => 'Filter By Projects','prompt' => ' '],'value' => function($model){ return $model->getStatus($model->has_projects,'has_projects',$model->id);}],
        ['attribute'=>'staff_assigned','headerOptions'=>['title'=>'Staff Assigned','class'=>'staff_assigned_th word-break','id'=>'case_production_staff_assigned','scope'=>'col'],'label'=>'Staff Assigned', 'contentOptions'=>['class' => 'staff_assigned word-break','headers'=>'case_production_staff_assigned'],'filterOptions'=>['headers'=>'case_production_staff_assigned'],'filterType'=>$filter_type['staff_assigned'],'filterWidgetOptions'=>$filterWidgetOption['staff_assigned']],
        ['attribute' => 'prod_date','headerOptions'=>['title'=>'Production Date','class' => 'global-date-width word-break','id'=>'case_production_date','scope'=>'col'],'label'=>'Prod Date', 'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_date'],'filterOptions'=>['headers'=>'case_production_date'],'filterType'=>$filter_type['prod_date'],'filterWidgetOptions'=>$filterWidgetOption['prod_date'], 'value'=>function($model){ return $model->getProdDate($model->prod_date); /* return date('m/d/Y',strtotime($model->prod_date)); */ }],				
        ['attribute' => 'prod_rec_date','headerOptions'=>['title'=>'Production Received Date','class' => 'global-date-width word-break','id'=>'case_production_received_date','scope'=>'col'],'filterInputOptions'=>['title'=>'Filter By Production Received Date','class'=>'form-control'],'label'=>'Prod Received', 'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_received_date'],'filterOptions'=>['headers'=>'case_production_received_date'],'filterType'=>$filter_type['prod_rec_date'],'filterWidgetOptions'=>$filterWidgetOption['prod_rec_date'], 'value'=>function($model){ return date('m/d/Y',strtotime($model->prod_rec_date)); }],				
        ['attribute'=>'prod_party','headerOptions'=>['title'=>'Producing Party','class'=>'prod_party_th word-break','id'=>'case_production_party','scope'=>'col'],'label'=>'Producing Party',  'contentOptions'=>['class' => 'prod_party word-break','headers'=>'case_production_party'],'filterOptions'=>['headers'=>'case_production_party'],'filterType'=>$filter_type['prod_party'],'filterWidgetOptions'=>$filterWidgetOption['prod_party']],
        ['attribute'=>'production_desc','headerOptions'=>['title'=>'Production Description','class'=>'prod_desc_th word-break','id'=>'case_production_description','scope'=>'col'],'label'=>'Prod Description', 'contentOptions'=>['class' => 'prod_desc word-break','headers'=>'case_production_description'],'filterOptions'=>['headers'=>'case_production_description'],'filterType'=>$filter_type['production_desc'],'filterWidgetOptions'=>$filterWidgetOption['production_desc']],
        ['attribute'=>'cover_let_link','noWrap' => false,'headerOptions'=>['title'=>'Cover Letter Link','class'=>'cover_let_link_th word-break-link','id'=>'case_production_coverletter','scope'=>'col'],'label'=>'Cover Letter Link', 'contentOptions'=>['class' => 'cover_let_link word-break','style'=>'max-width: 240px; overflow: auto; word-wrap: break-word;','headers'=>'case_production_coverletter'],'filterOptions'=>['headers'=>'case_production_coverletter'],'filterType'=>$filter_type['cover_let_link'],'filterWidgetOptions'=>$filterWidgetOption['cover_let_link']],
        ['attribute' => 'attach', 'format' => 'raw','headerOptions'=>['title'=>'Attachments','class'=>'attach_th word-break','id'=>'case_production_attachments','scope'=>'col'],'header'=>'Attach','contentOptions'=>['class' => 'attach text-center word-break','headers'=>'case_production_attachments'],'filterOptions'=>['headers'=>'case_production_attachments'], 'value'=>function($model){return $model->getAttachments($model->id);}],				
        ['attribute' => 'prod_orig', 'format' => 'raw','headerOptions'=>['title'=>'Production Contains Originals','class'=>'prod_orig_th word-break','id'=>'case_production_contains','scope'=>'col'],'label'=>'Contains Orig','filterInputOptions'=>['title'=>'Filter by Production Contains Originals','class'=>'form-control','prompt'=>' '], 'contentOptions'=>['class' => 'prod_orig text-center word-break','headers'=>'case_production_contains'],'filterOptions'=>['headers'=>'case_production_contains'],'filterType'=>$filter_type['prod_orig'],'filterWidgetOptions'=>$filterWidgetOption['prod_orig'], 'value' => function($model){ return $model->getStatus($model->prod_orig,'prod_orig');}],													
        ['attribute' => 'attorney_notes','headerOptions'=>['title'=>'Attorney Notes','class'=>'attorney_notes_th word-break','id'=>'case_production_notes','scope'=>'col'],'label'=>'Attorney Notes', 'contentOptions'=>['class' => 'attorney_notes word-break','headers'=>'case_production_notes'],'filterOptions'=>['headers'=>'case_production_notes'],'filterType'=>$filter_type['attorney_notes'],'filterWidgetOptions'=>$filterWidgetOption['attorney_notes']],
        ['attribute'=>'prod_disclose','headerOptions'=>['title'=>'Produced in Initial Disclosures','class'=>'prod_disclose_th word-break','id'=>'case_production_disclosures','scope'=>'col'],'label'=>'Prod in Initial Disclosures','filterInputOptions'=>['title'=>'Filter by Produced in Initial Disclosures','class'=>'form-control'],'contentOptions'=>['class' => 'prod_disclose word-break text-center','headers'=>'case_production_disclosures'],'filterOptions'=>['headers'=>'case_production_disclosures'],'filterType'=>$filter_type['prod_disclose'],'filterWidgetOptions'=>$filterWidgetOption['prod_disclose']],	
        ['attribute' => 'prod_agencies','headerOptions'=>['title'=>'Produced to Other Agencies','class' => 'global-date-width word-break','id'=>'case_production_agencies','scope'=>'col'],'label'=>'Prod to Other Agencies','filterInputOptions'=>['title'=>'Filter by Produced to Other Agencies','class'=>'form-control'],'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_agencies'],'filterOptions'=>['headers'=>'case_production_agencies'],'filterType'=>$filter_type['prod_agencies'],'filterWidgetOptions'=>$filterWidgetOption['prod_agencies'],'value'=>function($model){return $model->getStatus($model->prod_agencies,'prod_agencies');}],
        ['attribute' => 'prod_access_req','headerOptions'=>['title'=>'Access Request','class' => 'global-date-width word-break','id'=>'case_production_request','scope'=>'col'],'label'=>'Access Request','contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_request'],'filterOptions'=>['headers'=>'case_production_request'],'filterType'=>$filter_type['prod_access_req'],'filterWidgetOptions'=>$filterWidgetOption['prod_access_req'],'value'=>function($model){ return $model->getStatus($model->prod_access_req,'prod_access_req');}],

    ];
}
?>
<div class="right-main-container" id="caseproduction_container">
	<fieldset class="one-cols-fieldset case-project-fieldset">
        
        <div class="table-responsive">
            
            <input type="hidden" name="case_id" value="<?=$case_id?>" />
            <?php $dynagrid = DynaGrid::begin([
        'columns'=>$columns,
        'storage'=>'db',
        'theme'=>'panel-info',
        'gridOptions'=>[
        'id'=>'caseproduction-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'panel'=>false,
		'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{dynagridSort}{dynagrid}{pager}</div>',
		'responsiveWrap' => false,
		'export'=>false,
		'floatHeader'=>true, 
		'floatHeaderOptions' => ['top' => 'auto'],
		'persistResize'=>false,
		'resizableColumns'=>false,
		'pjax'=>true,
                'pjaxSettings'=>[
                    'options'=>['id'=>'dynagrid-caseproduction-pajax','enablePushState' => false],
                    'neverTimeout'=>true,
                    'beforeGrid'=>'',
                    'afterGrid'=>'',
                ],
                'pager' => [
                    'options'=>['class'=>'pagination'], // set clas name used in ui list of pagination
                    'prevPageLabel' => 'Previous',  // Set the label for the "previous" page button
                    'nextPageLabel' => 'Next',  // Set the label for the "next" page button
                    'firstPageLabel'=>'First',  // Set the label for the "first" page button
                    'lastPageLabel'=>'Last',  // Set the label for the "last" page button
                    'nextPageCssClass'=>'next',  // Set CSS class for the "next" page button
                    'prevPageCssClass'=>'prev',  // Set CSS class for the "previous" page button
                    'firstPageCssClass'=>'first',  // Set CSS class for the "first" page button
                    'lastPageCssClass'=>'last',  // Set CSS class for the "last" page button
                    'maxButtonCount'=>5,  // Set maximum number of page buttons that can be displayed
                ],
                'responsive'=>true,    
                'floatOverflowContainer'=>true,
			
    ],
    'allowThemeSetting'=>false,
    'allowFilterSetting'=>false,
    'allowPageSetting'=>false,
    'enableMultiSort'=>true,
    'toggleButtonGrid'=>['class'=>'btn btn-info btn-sm'],
    'toggleButtonSort'=>['class'=>'btn btn-sm'],
    'options'=>[
    'id'=>'dynagrid-caseproduction',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
				<?php /*GridView::widget([
					'id'=>'caseproduction-grid',    
                    'containerOptions'=>['class' =>'test'],
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{pager}</div>',
					'columns' => [
                        ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case-production/get-prod-deatail']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'case_production_expand','scope'=>'col'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'case_production_expand'],'filterOptions'=>['headers'=>'case_production_expand'], 'expandIcon' => '<a href="javascript:void(0);"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false,'value' => function ($model) { return 1;}],
						['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_production_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'case_production_checkbox'],'filterOptions'=>['headers'=>'case_production_checkbox']],
						$action_column,
						['attribute'=>'id','filterType'=>GridView::FILTER_SELECT2,'headerOptions'=>['title'=>'Production #','class'=>'prod_no_th word-break','id'=>'case_production_id','scope'=>'col'],'label'=>'Production #','contentOptions'=>['class' => 'word-break first-td prod_no text-center','headers'=>'case_production_id'],'filterOptions'=>['headers'=>'case_production_id'],'filterInputOptions'=>['title'=>'Filter by Production #'],
		  				'filterWidgetOptions'=>[
							'pluginOptions'=>[
									'ajax' =>[
										'url' => Url::toRoute(['case-production/ajax-filter']),
										'dataType' => 'json',
										'data' => new JsExpression('function(params) { return {q:params.term,field:"id",case_id:'.$case_id.'}; }')
									]]]],
		  				['attribute' => 'production_type','filterInputOptions' => ['title' => 'Filter By Production Type','prompt' => ' '], 'filterType'=>GridView::FILTER_SELECT2,'format' => 'raw','filter' => [ 1=>'Incoming',2=>'Outgoing'],'headerOptions'=>['title'=>'Production Type','class'=>'prod_type_th word-break','id'=>'case_production_type','scope'=>'col'],'label'=>'Prod Type','contentOptions'=>['class' => 'first-td text-center prod_type','headers'=>'case_production_type'],'filterOptions'=>['headers'=>'case_production_type'], 'value' => function($model){ return $model->getProdTypeImage($model->production_type);}],
		  				['attribute' => 'has_media', 'format' => 'raw','filterInputOptions' => ['title' => 'Filter By Media','prompt' => ' '],'filterType'=>GridView::FILTER_SELECT2,'filter' => [ 1=>'Yes',0=>'No'],'headerOptions'=>['title'=>'Has Media?','class'=>'has_media_th','id'=>'case_production_media','scope'=>'col'],'header'=>'Media?','contentOptions'=>['class' => 'first-td text-center has_media word-break','headers'=>'case_production_media'],'filterOptions'=>['headers'=>'case_production_media'], 'value' => function($model){ return $model->getStatus($model->has_media,'has_media');}],
		  				['attribute' => 'has_hold','filterType'=>GridView::FILTER_SELECT2, 'format' => 'raw','filter' => [ 1=>'Yes',0=>'No'],'filterInputOptions'=>['title'=>'Filter by On Hold','class'=>'form-control','prompt'=>' '],'headerOptions'=>['title'=>'Media on Hold?','class'=>'has_hold_th','id'=>'case_production_hold','scope'=>'col'],'header'=>'Hold?','contentOptions'=>['class' => 'first-td text-center has_hold word-break','headers'=>'case_production_hold'],'filterOptions'=>['headers'=>'case_production_hold'],  'value' => function($model){ return $model->getStatus($model->has_hold,'has_hold');}],
		  				['attribute' => 'has_projects', 'format' => 'raw','filterType'=>GridView::FILTER_SELECT2,'filter' => [ 1=>'Yes',0=>'No'], 'headerOptions'=>['title'=>'Has Projects?','class'=>'has_projects_th','id'=>'case_production_projects','scope'=>'col'],'header'=>'Projects?','contentOptions'=>['class' => 'word-break first-td text-center has_projects','headers'=>'case_production_projects'],'filterOptions'=>['headers'=>'case_production_projects'], 'filterInputOptions' => ['title' => 'Filter By Projects','prompt' => ' '],'value' => function($model){ return $model->getStatus($model->has_projects,'has_projects',$model->id);}],
		  				['attribute'=>'staff_assigned','filterType'=>GridView::FILTER_SELECT2,'headerOptions'=>['title'=>'Staff Assigned','class'=>'staff_assigned_th word-break','id'=>'case_production_staff_assigned','scope'=>'col'],'label'=>'Staff Assigned', 'contentOptions'=>['class' => 'staff_assigned word-break','headers'=>'case_production_staff_assigned'],'filterOptions'=>['headers'=>'case_production_staff_assigned'],
		  				'filterWidgetOptions'=>[
							'initValueText' => $filter_display["staff_assigned"], 
							'pluginOptions'=>[
								'ajax' =>[
									'url' => Url::toRoute(['case-production/ajax-filter']),
									'dataType' => 'json',
									'data' => new JsExpression('function(params) { return {q:params.term,field:"staff_assigned",case_id:'.$case_id.'}; }')
								]]]],
		  				['attribute' => 'prod_date','filterType'=>GridView::FILTER_DATE,'headerOptions'=>['title'=>'Production Date','class' => 'global-date-width word-break','id'=>'case_production_date','scope'=>'col'],'label'=>'Prod Date', 'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_date'],'filterOptions'=>['headers'=>'case_production_date'],'filterWidgetOptions'=>[
							'pluginEvents'=>[
								"changeDate" => "function(e) { $('.datepicker').hide(); }",
							],
						], 'value'=>function($model){return date('m/d/Y',strtotime($model->prod_date));}],				
		  				['attribute' => 'prod_rec_date','filterType'=>GridView::FILTER_DATE,'headerOptions'=>['title'=>'Production Received Date','class' => 'global-date-width word-break','id'=>'case_production_received_date','scope'=>'col'],'filterInputOptions'=>['title'=>'Filter By Production Received Date','class'=>'form-control'],'label'=>'Prod Received', 'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_received_date'],'filterOptions'=>['headers'=>'case_production_received_date'],'filterWidgetOptions'=>[
							'pluginEvents'=>[
									"changeDate" => "function(e) { $('.datepicker').hide(); }",
								],
						], 'value'=>function($model){return date('m/d/Y',strtotime($model->prod_rec_date));}],				
		  				['attribute'=>'prod_party','filterType'=>GridView::FILTER_SELECT2,'headerOptions'=>['title'=>'Producing Party','class'=>'prod_party_th word-break','id'=>'case_production_party','scope'=>'col'],'label'=>'Producing Party',  'contentOptions'=>['class' => 'prod_party word-break','headers'=>'case_production_party'],'filterOptions'=>['headers'=>'case_production_party'],
		  				'filterWidgetOptions'=>[
							'pluginOptions'=>[
								'ajax' =>[
									'url' => Url::toRoute(['case-production/ajax-filter']),
									'dataType' => 'json',
									'data' => new JsExpression('function(params) { return {q:params.term,field:"prod_party",case_id:'.$case_id.'}; }')
								]]]],
						['attribute'=>'production_desc','filterType'=>GridView::FILTER_SELECT2,'headerOptions'=>['title'=>'Production Description','class'=>'prod_desc_th word-break','id'=>'case_production_description','scope'=>'col'],'label'=>'Prod Description', 'contentOptions'=>['class' => 'prod_desc word-break','headers'=>'case_production_description'],'filterOptions'=>['headers'=>'case_production_description'],
		  				'filterWidgetOptions'=>[
								'initValueText' => $filter_display["production_desc"], 
		  						'pluginOptions'=>[
		  								'ajax' =>[
		  									'url' => Url::toRoute(['case-production/ajax-filter']),
		  									'dataType' => 'json',
		  									'data' => new JsExpression('function(params) { return {q:params.term,field:"production_desc",case_id:'.$case_id.'}; }')
		  								]]]],
		  				['attribute'=>'cover_let_link',
		  				'noWrap' => false,
		  				'filterType'=>GridView::FILTER_SELECT2,
		  				'headerOptions'=>['title'=>'Cover Letter Link','class'=>'cover_let_link_th word-break-link','id'=>'case_production_coverletter','scope'=>'col'],
		  				'label'=>'Cover Letter Link', 
		  				'contentOptions'=>['class' => 'cover_let_link word-break','style'=>'max-width: 240px; overflow: auto; word-wrap: break-word;','headers'=>'case_production_coverletter'],
		  				'filterOptions'=>['headers'=>'case_production_coverletter'],
		  				'filterWidgetOptions'=>[
							'initValueText' => $filter_display["cover_let_link"], 
							'pluginOptions'=>[
								'ajax' =>[
									'url' => Url::toRoute(['case-production/ajax-filter']),
									'dataType' => 'json',
									'data' => new JsExpression('function(params) { return {q:params.term,field:"cover_let_link",case_id:'.$case_id.'}; }')
								]]]],
		  				['attribute' => 'attach', 'format' => 'raw','headerOptions'=>['title'=>'Attachments','class'=>'attach_th word-break','id'=>'case_production_attachments','scope'=>'col'],'header'=>'Attach','contentOptions'=>['class' => 'attach text-center word-break','headers'=>'case_production_attachments'],'filterOptions'=>['headers'=>'case_production_attachments'], 'value'=>function($model){return $model->getAttachments($model->id);}],				
		  				['attribute' => 'prod_orig', 'format' => 'raw','filterType'=>GridView::FILTER_SELECT2,'filter' => [ 1=>'Yes',0=>'No'],'headerOptions'=>['title'=>'Production Contains Originals','class'=>'prod_orig_th word-break','id'=>'case_production_contains','scope'=>'col'],'label'=>'Contains Orig','filterInputOptions'=>['title'=>'Filter by Production Contains Originals','class'=>'form-control','prompt'=>' '], 'contentOptions'=>['class' => 'prod_orig text-center word-break','headers'=>'case_production_contains'],'filterOptions'=>['headers'=>'case_production_contains'], 'value' => function($model){ return $model->getStatus($model->prod_orig,'prod_orig');}],													
		  				['attribute' => 'attorney_notes', 'filterType'=>GridView::FILTER_SELECT2,'headerOptions'=>['title'=>'Attorney Notes','class'=>'attorney_notes_th word-break','id'=>'case_production_notes','scope'=>'col'],'label'=>'Attorney Notes', 'contentOptions'=>['class' => 'attorney_notes word-break','headers'=>'case_production_notes'],'filterOptions'=>['headers'=>'case_production_notes'],
		  				'filterWidgetOptions'=>[
							'initValueText' => $filter_display["attorney_notes"],
		  					'pluginOptions'=>[
		  						'ajax' =>[
		  							'url' => Url::toRoute(['case-production/ajax-filter']),
		  							'dataType' => 'json',
		  							'data' => new JsExpression('function(params) { return {q:params.term,field:"attorney_notes",case_id:'.$case_id.'}; }')
		  						]]]],
		  				['attribute'=>'prod_disclose','filterType'=>GridView::FILTER_SELECT2,'headerOptions'=>['title'=>'Produced in Initial Disclosures','class'=>'prod_disclose_th word-break','id'=>'case_production_disclosures','scope'=>'col'],'label'=>'Prod in Initial Disclosures','filterInputOptions'=>['title'=>'Filter by Produced in Initial Disclosures','class'=>'form-control'],'contentOptions'=>['class' => 'prod_disclose word-break text-center','headers'=>'case_production_disclosures'],'filterOptions'=>['headers'=>'case_production_disclosures'],
		  				'filterWidgetOptions'=>[
							'initValueText' => $filter_display["prod_disclose"],
							'pluginOptions'=>[
								'ajax' =>[
									'url' => Url::toRoute(['case-production/ajax-filter']),
									'dataType' => 'json',
									'data' => new JsExpression('function(params) { return {q:params.term,field:"prod_disclose",case_id:'.$case_id.'}; }')
								]]]],	
		  				['attribute' => 'prod_agencies','filterType'=>GridView::FILTER_DATE,'headerOptions'=>['title'=>'Produced to Other Agencies','class' => 'global-date-width word-break','id'=>'case_production_agencies','scope'=>'col'],'label'=>'Prod to Other Agencies','filterInputOptions'=>['title'=>'Filter by Produced to Other Agencies','class'=>'form-control'],'contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_agencies'],'filterOptions'=>['headers'=>'case_production_agencies'],'filterWidgetOptions'=>[
							'pluginEvents'=>[
								"changeDate" => "function(e) { $('.datepicker').hide(); }",
							],
						],'value'=>function($model){return $model->getStatus($model->prod_agencies,'prod_agencies');}],
		  				['attribute' => 'prod_access_req','filterType'=>GridView::FILTER_DATE,'headerOptions'=>['title'=>'Access Request','class' => 'global-date-width word-break','id'=>'case_production_request','scope'=>'col'],'label'=>'Access Request','contentOptions'=>['class' => 'global-date-width word-break','headers'=>'case_production_request'],'filterOptions'=>['headers'=>'case_production_request'],'filterWidgetOptions'=>[
							'pluginEvents'=>[
								"changeDate" => "function(e) { $('.datepicker').hide(); }",
							],
						],'value'=>function($model){ return $model->getStatus($model->prod_access_req,'prod_access_req');}],
                                                               
					],
					'export'=>false,
					'floatHeader'=>true,  
					'floatHeaderOptions' => ['top' => 'auto'],  
					'pjax'=>true,
					'pjaxSettings'=>[
							'options'=>['id'=>'caseproductiongrid-pajax','enablePushState' => false,'class'=>'d'],
							'neverTimeout'=>true,
							'beforeGrid'=>'',
							'afterGrid'=>'',
					],
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
                    'responsive'=>false,
                    'floatOverflowContainer'=>true,
				]); */?>            
		    </div>
            
	</fieldset>
<div class="button-set text-right">
	<?php if(isset($prod_id) && $prod_id!=""){?>
	<?= Html::a('All Productions', ['/case-production/index','case_id'=>$case_id], ['class'=>'btn btn-primary all_filter','title'=>"All Production"]) ?>
	<?php } else{?>
    <?= Html::a('All Productions', ['/case-production/index','case_id'=>$case_id], ['class'=>'btn btn-primary all_filter','title'=>"All Production",'style'=>'display:none;']) ?>
    <?php }?>
    <?php if ((new User)->checkAccess(4.0075)) { ?>
    <?= Html::button('Export Log',['title'=>"Export Log",'class' => 'btn btn-primary','onclick'=>'export_log();'])?>
    <?php } ?>
    <?php if ((new User)->checkAccess(4.007)) { ?>
		<?= Html::button('Add Production',['title'=>"Add Production",'class' => 'btn btn-primary','onclick'=>'addproduction();'])?>
    <?php } ?>
</div>
</div>
<form id="frm_case_production123" action="<?= Url::toRoute(['pdf/runproductionexcel'])?>" method="post" autocomplete="off" style="display:none;"/>		
</form> 
<div id="dialog_probates"></div>
<script>
    var $grid = $('#caseproductiongrid-pajax');
    $grid.css('visibility','hidden');
    $grid.on('kvexprow.beforeLoad', function (event, ind, key, extra) {
            
        //$('input[rel='+key+']').customInput();
    });
    $(document).ready(function(){
		$grid.css('visibility','visible');
	});
    //$('input').customInput();
    function export_log()
    {
        //location.href=baseUrl+"pdf/runproductionexcel&case_id=<?php echo $case_id;?>";
        
        //alert('ee');
		$('#frm_case_production123').html(null);
		$('#frm_case_production123').html("<table>"+$('#caseproduction_container').find('.table-responsive').find('tr.filters').html()+"</table><input type='hidden' name='<?= Yii::$app->request->csrfParam; ?>' value='<?= Yii::$app->request->csrfToken; ?>' /><input type='hidden' name='case_id' value='<?=$case_id?>' />");
		setTimeout(function(){
			$('#frm_case_production123').submit();
		},100);
        
    }
/*dyangird setting*/
$('#dynagrid-<?=$dynagrid->gridOptions['id']?>-modal').on('shown.bs.modal', function () { 
	
	//var self = this,
	$element = $('input[name="<?=$dynagrid->options['id']?>-dynagrid');
	$form = self.$element.closest('form');
	$form.find('select[data-krajee-select2]').each(function () {
		var $el = $(this), settings = window[$el.attr('data-krajee-select2')] || {};
		if ($el.data('select2')) {
			$el.select2('destroy');
		}
		$.when($el.select2(settings)).done(function () {
			initS2Loading($el.attr('id'), '.select2-container--krajee'); // jshint ignore:line
		});
	});
	
	$form.find('[data-krajee-sortable]').each(function () {
		var $el = $(this);
		if ($el.data('sortable')) {
			$el.sortable('destroy');
		}
		$el.sortable(window[$el.attr('data-krajee-sortable')]);
	});
});
/*dyangird setting*/
</script>
<noscript></noscript>
