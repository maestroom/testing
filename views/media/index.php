<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\Options;
use app\models\Evidence;
use app\models\User;
use app\models\ClientCaseEvidence;
use kartik\widgets\Select2;
use kartik\dynagrid\DynaGridStore;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sources';
$this->params['breadcrumbs'][] = $this->title;
//echo (new User)->checkAccess(3.041);die;
if((new User)->checkAccess(3.009)) {
if((new User)->checkAccess(3.03) || (new User)->checkAccess(3.04) || (new User)->checkAccess(3.041)) {
$action_column = ['class' => 'kartik\grid\ActionColumn',
				'headerOptions'=>['title'=>'Actions','class'=>'third-th','id'=>'media_grid_action'],
				'contentOptions' => ['class' => 'third-td','headers'=>'media_grid_action'],
				'template'=>'{copy}&nbsp;{update}&nbsp;{delete}',
				'mergeHeader'=>false,
				//'width' => '24%',
				'buttons'=>[
                        'copy'=>function ($url, $model, $key) {
							if((new User)->checkAccess(3.041)){
								return Html::a('<em title="Copy" class="fa fa-copy text-primary"></em><span class="hide">Copy</span>', 'javascript:void(0);', [
										'title' => Yii::t('yii', 'Copy'),
										'onclick'=>'copymedia('.$key.');'
									]);
								}
							},
							'update'=>function ($url, $model, $key) {
							if((new User)->checkAccess(3.03)) {
								return Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em><span class="hide">Edit</span>', 'javascript:void(0);', [
									'title' => Yii::t('yii', 'Edit'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Edit Media' ),
									'onclick'=>'editmedia('.$key.');'
									]);
								}
							},
						'delete'=>function ($url, $model, $key) {
							if((new User)->checkAccess(3.04)){
								return Html::a('<em title="Delete" class="fa fa-close text-primary"></em><span class="hide">Delete</span>', 'javascript:deletemedia('.$key.');', [
									'title' => Yii::t('yii', 'Delete'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Delete' ),
									'data' => [
											'confirm' =>  Yii::t('yii',"Are you sure you want to Delete Media #$key?"),
										],
									]);
							 	}
							},
						],
						'order'=>DynaGrid::ORDER_FIX_LEFT,
				];
			} else {
			$action_column  = [];
		}
$queryParams=Yii::$app->request->queryParams;
$barcode = 	'';
if(isset($queryParams['EvidenceSearch']['barcode']) && $queryParams['EvidenceSearch']['barcode']!='blank')
{
	$barcode = $queryParams['EvidenceSearch']['barcode'];
}
$model_evid = new Evidence();
$labels=$model_evid->attributeLabels();
$columns=[
	['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['media/get-details']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'media_grid_expand','aria-label'=>'Media Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'media_grid_expand'],'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="ectext">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Row"><span class="glyphicon glyphicon-minus"></span><span class="ectext">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'filterOptions'=>['header'=>'media_grid_expand'],'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
	['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true,'title'=>'Select Row'),'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'media_grid_checkbox'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'media_grid_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
	$action_column
];
if(!empty($media_form)){
	foreach($media_form as $column) {
		/*if($column=='client_id') {
			$columns[]=['attribute'=>'client_id','format' => 'html','filterType'=>$filter_type['client_id'], 'headerOptions'=>['title'=>'Client', 'id'=>'media_grid_client_id', 'class'=>'client-id-width word-break'],'contentOptions'=>['class' => 'first-td client_id-width word-break','headers' => 'media_grid_client_id'],'filterWidgetOptions' => $filterWidgetOption['client_id'],'value'=>function($model){ return $model->client_id;}];
		}*/
		/*if($column=='client_id') {
			$columns[]=['attribute'=>'client_id','format' => 'html','filterType'=>$filter_type['client_id'], 'header'=>'<a href="javascript:void(0);">Client</a>', 'headerOptions'=>['title'=>'Client', 'id'=>'media_grid_client_id', 'class'=>'client-id-width word-break'],'contentOptions'=>['class' => 'first-td client_id-width word-break','headers' => 'media_grid_client_id'],'filterWidgetOptions' => $filterWidgetOption['client_id'],'value'=>function($model){ return $model->client_id;/*(new ClientCaseEvidence)->getEvidenceClients($model->id);*//*}];
		}elseelse if($column=='client_case_id'){
			$columns[]=['attribute'=>'client_case_id','filterType'=>$filter_type['client_case_id'], 'header'=>'<a href="javascript:void(0);">Case</a>', 'headerOptions'=>['title'=>'Case', 'id'=>'media_grid_case_id', 'class'=>'case-id-width word-break'],'contentOptions'=>['class' => 'first-td case-id-width word-break','headers' => 'media_grid_case_id'],'filterWidgetOptions' => $filterWidgetOption['client_case_id'],'value'=>function($model){ return $model->client_case_id;/*(new ClientCaseEvidence)->getEvidenceCases($model->id);*//*}];
		}*/
		if($column=='status') {
			$columns[]=['attribute' => 'status', 'format' => 'raw','headerOptions'=>['title'=>'Status','class'=>'global-status-width word-break','id'=>'media_grid_status'],'filterInputOptions' => ['title' => 'Filter By Status'],'filterWidgetOptions'=>$filterWidgetOption['status'],'contentOptions'=>['class' => 'first-td global-status-width text-center word-break','headers'=>'media_grid_status'],'filterType'=>$filter_type['status'],'value' => function($model){ return $model->getStatusImage($model->status);}];
		}/*else if($column=='client_case_id'){
			$columns[]=['attribute'=>'client_case_id','filterType'=>$filter_type['client_case_id'], 'headerOptions'=>['title'=>'Case', 'id'=>'media_grid_case_id', 'class'=>'case-id-width word-break'],'contentOptions'=>['class' => 'first-td case-id-width word-break','headers' => 'media_grid_case_id'],'filterWidgetOptions' => $filterWidgetOption['client_case_id'],'value'=>function($model){ return $model->client_case_id;}];
		}*/else if($column=='received_date'){
			$columns[]=['attribute' => 'received_date', 'filterType'=>$filter_type['received_date'],'headerOptions'=>['title'=>'Received Date','class'=>'global-datetime-width word-break','id' => 'media_grid_received_time'], 'contentOptions'=>['class' => 'global-datetime-width word-break','headers'=>'media_grid_received_time'],'filterWidgetOptions'=>$filterWidgetOption['received_date'], 'value'=>function($model){
				return date("m/d/Y",strtotime($model->received_date));

			//(new Options)->ConvertOneTzToAnotherTz($model->received_date, 'UTC', $_SESSION['usrTZ'],"date");

			}];
		}else if($column=='evid_type') {
			$columns[]=['attribute' => 'evid_type','filterType'=>$filter_type['evid_type'],'headerOptions'=>['title'=>'Media Type','id'=>'media_grid_evid_typ','class' => 'mediatype-width word-break'],'contentOptions'=>['class' => 'mediatype-width word-break','headers'=>'media_grid_evid_typ'],'filterWidgetOptions'=>$filterWidgetOption['evid_type'],'value'=>function($model){return $model->evidence_name;}];
		}else if($column=='cat_id') {
			$columns[]=['attribute' => 'cat_id','filterType'=>$filter_type['cat_id'],'headerOptions'=>['title'=>'Category','id'=>'media_grid_cat_id','class' => 'mediacategory-width word-break'],'contentOptions'=>['class' => 'mediacategory-width word-break','headers'=>'media_grid_cat_id'],'filterWidgetOptions'=>$filterWidgetOption['cat_id'],'value'=>function($model){return $model->category;}];
		}else if($column=='unit'){
			$columns[]=['attribute' => 'unit','filterType'=>$filter_type['unit'],'headerOptions'=>['title'=>'Total Size Units','id'=>'media_grid_content_size','class' => 'totalsize-width word-break'],'contentOptions'=>['class' => 'totalsize-width word-break','headers'=>'media_grid_content_size'],'filterWidgetOptions'=>$filterWidgetOption['unit'],'value'=>function($model){return $model->evidenceunitunit_name;}];
		}else if($column=='comp_unit'){
			$columns[]=['attribute' => 'comp_unit','filterType'=>$filter_type['comp_unit'],'label'=>'Comp Unit Size','headerOptions'=>['title'=>'Comp Unit Size','id'=>'media_grid_total_size','class' => 'totalsize-width word-break'],'contentOptions'=>['class' => 'totalsize-width word-break','headers'=>'media_grid_total_size'],'filterWidgetOptions'=>$filterWidgetOption['comp_unit'],'value'=>function($model){return $model->evidencecompunit_name;}];
		}else if($column=='dup_evid'){
			$columns[]=['attribute' => 'dup_evid','filterType'=>$filter_type['dup_evid'],'headerOptions'=>['title'=>'Dup Evid','id'=>'dup_evid','class' => ' word-break'],'contentOptions'=>['class' => ' word-break','headers'=>''],'filterWidgetOptions'=>$filterWidgetOption['dup_evid'],'value'=>function($model){return ($model->dup_evid==1?'Yes':'No');}];
		}else if($column=='upload_files'){
			$columns[]=['attribute' => 'upload_files', 'format' => 'raw', 'label'=>'Attachment', 'headerOptions'=>['title'=>'Attachment','class'=>'text-center'], 'contentOptions' => ['class' => 'text-center project-attachedment-width'], 'value' =>  function ($model) { return $model->getAttachments($model->id); }];
		}else if($column=='id'){
			$columns[]=['attribute'=>'id', 'format'=>'raw','headerOptions'=>['title'=>'Media#','id'=>'media_grid_media_id','class'=>'mediano-width word-break'],'contentOptions'=>['class' => 'first-td text-center mediano-width word-break','headers'=>'media_grid_media_id'],
			//'filterWidgetOptions'=>$filterWidgetOption['id'],
			//'filterType'=>$filter_type['id']
			'filter'=>'<input type="text" class="form-control filter_number_only" name="EvidenceSearch[id]" value="'.$params['EvidenceSearch']['id'].'">'
			];
		}else if($column=='org_link'){
			$columns[]=['attribute'=>'org_link', 'format'=>'raw','headerOptions'=>['title'=>'Org Link','id'=>'media_grid_org_link','class'=>'mediano-width word-break'],'contentOptions'=>['class' => 'first-td text-center mediano-width word-break','headers'=>'media_grid_org_link'],
			'filter'=>'<input type="text" class="form-control filter_number_only" name="EvidenceSearch[org_link]" value="'.$params['EvidenceSearch']['org_link'].'">'
			];
		}else if($column=='created_by'){
			$columns[]=['attribute'=>'created_by','filterType'=>$filter_type['created_by'],'headerOptions'=>['title'=>'Created By','id'=>'media_grid_created_by','class'=>'mediano-width word-break'],'contentOptions'=>['class' => 'first-td mediano-width word-break','headers'=>'media_grid_created_by'],'filterWidgetOptions'=>$filterWidgetOption['created_by'],'value' => function($model){ return $model->evidcreateduser; }];
		}
		elseif($column=='received_time'){
                    $columns[]=['attribute'=>'received_time','filterType'=>$filter_type['received_time'],'headerOptions'=>['title'=>'Time Received','id'=>'media_grid_received_time','class'=>'mediano-width word-break'],'contentOptions'=>['class' => 'first-td mediano-width word-break','headers'=>'media_grid_received_time'],'filterWidgetOptions'=>$filterWidgetOption['received_time'],'value' => function($model){
                    if($model->received_time != ''){
                        $received_date_time = $model->received_date.' '.$model->received_time;
                        $model->received_time=date("h:i A",strtotime($model->received_date));
					   //(new Options)->ConvertOneTzToAnotherTz($received_date_time, 'UTC', $_SESSION['usrTZ'],'time');
        			}
                    return $model->received_time;
                    }];

		}
		else {
			$columns[]=['attribute'=>$column,'filterType'=>$filter_type[$column],'headerOptions'=>['title'=>$labels[$column],'class'=>'word-break'],'contentOptions'=>['class' => 'word-break'],'filterWidgetOptions'=>$filterWidgetOption[$column]];
		}
	}
	//echo '<pre>',print_r($columns);die;
}else{
	//echo '<pre>fff',print_r($columns);die;
    $columns = [
            ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['media/get-details']),'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th','id'=>'media_grid_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td','headers'=>'media_grid_expand'],'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span><span class="ectext">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span><span class="ectext">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'filterOptions'=>['header'=>'media_grid_expand'],'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;},'order'=>DynaGrid::ORDER_FIX_LEFT],
            ['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'checkboxOptions'=>array('customInput'=>true),'headerOptions'=>['title'=>'Select All/None','class' => 'first-th','id'=>'media_grid_checkbox'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td','headers'=>'media_grid_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
            $action_column,
            ['attribute'=>'id','headerOptions'=>['title'=>'Media#','id'=>'media_grid_media_id','class'=>'mediano-width word-break'],'contentOptions'=>['class' => 'first-td text-center mediano-width word-break','headers'=>'media_grid_media_id'],
			//'filterWidgetOptions'=>$filterWidgetOption['id'],'filterType'=>$filter_type['id'],
			'filter'=>'<input type="text" class="form-control filter_number_only" name="EvidenceSearch[id]" value="'.$params['EvidenceSearch']['id'].'">'
			],
            ['attribute'=>'created_by','filterType'=>$filter_type['created_by'],'headerOptions'=>['title'=>'Created By','id'=>'media_grid_created_by','class'=>'mediano-width word-break'],'contentOptions'=>['class' => 'first-td mediano-width word-break','headers'=>'media_grid_created_by'],'filterWidgetOptions'=>$filterWidgetOption['created_by'],'value' => function($model){ return $model->evidcreateduser; }],
            ['attribute' => 'status', 'format' => 'raw','headerOptions'=>['title'=>'Status','class'=>'global-status-width word-break','id'=>'media_grid_status'],'filterInputOptions' => ['title' => 'Filter By Status'],'filterWidgetOptions'=>$filterWidgetOption['status'],'contentOptions'=>['class' => 'first-td global-status-width text-center word-break','headers'=>'media_grid_status'],'filterType'=>$filter_type['status'],'value' => function($model){ return $model->getStatusImage($model->status);}],
            ['attribute'=>'barcode','filterType'=>$filter_type['barcode'],'headerOptions'=>['title'=>'Barcode', 'id'=>'media_grid_barcode', 'class'=>'bacode-width word-break'],'contentOptions'=>['class' => 'first-td bacode-width word-break','headers' => 'media_grid_barcode'],'filterWidgetOptions' => $filterWidgetOption['barcode']],
            //['attribute'=>'client_id','filterType'=>$filter_type['client_id'], 'header'=>'<a href="javascript:void(0);">Client</a>','headerOptions'=>['title'=>'Client', 'id'=>'media_grid_client_id', 'class'=>'global-datetime-width word-break'],'contentOptions'=>['class' => 'first-td global-datetime-width word-break','headers' => 'media_grid_client_id'],'filterWidgetOptions' => $filterWidgetOption['client_id'],'value'=>function($model){ return $model->client_id;/*(new ClientCaseEvidence)->getEvidenceClients($model->id);*/}],
            //['attribute'=>'case_name','filterType'=>$filter_type['client_case_id'], 'header'=>'<a href="javascript:void(0);">Case</a>','headerOptions'=>['title'=>'Case', 'id'=>'media_grid_case_id', 'class'=>'global-datetime-width word-break'],'contentOptions'=>['class' => 'first-td global-datetime-width word-break','headers' => 'media_grid_case_id'],'filterWidgetOptions' => $filterWidgetOption['client_case_id'],'value'=>function($model){ return $model->client_case_id;/*(new ClientCaseEvidence)->getEvidenceCases($model->id);*/}],
            ['attribute' => 'received_date', 'filterType'=>$filter_type['received_date'],'headerOptions'=>['title'=>'Received Date','class'=>'global-datetime-width word-break','id'=>'media_grid_received_time'],'contentOptions'=>['class' => 'global-datetime-width word-break','headers'=>'media_grid_received_time'],'filterWidgetOptions'=>$filterWidgetOption['received_date'],'value'=>function($model){
				return date("m/d/Y",strtotime($model->received_date));
			 //(new Options)->ConvertOneTzToAnotherTz($model->received_date.' '.$model->received_time, 'UTC', $_SESSION['usrTZ']);
			 }],
            ['attribute' => 'evid_type','filterType'=>$filter_type['evid_type'],'headerOptions'=>['title'=>'Media Type','id'=>'media_grid_evid_typ','class' => 'mediatype-width word-break'],'contentOptions'=>['class' => 'mediatype-width word-break','headers'=>'media_grid_evid_typ'],'filterWidgetOptions'=>$filterWidgetOption['evid_type'],'value'=>function($model){return $model->evidence_name;}],
            ['attribute' => 'cat_id','filterType'=>$filter_type['cat_id'],'headerOptions'=>['title'=>'Category','id'=>'media_grid_cat_id','class' => 'mediacategory-width word-break'],'contentOptions'=>['class' => 'mediacategory-width word-break','headers'=>'media_grid_cat_id'],'filterWidgetOptions'=>$filterWidgetOption['cat_id'],'value'=>function($model){return $model->category;}],
            ['attribute'=>'quantity','filterType'=>$filter_type['quantity'],'headerOptions'=>['title'=>'Quantity','id'=>'media_grid_quantity','class'=>'qty-width word-break'],'contentOptions'=>['class' => 'first-td text-center qty-width word-break','headers'=>'media_grid_quantity'],'filterWidgetOptions'=>$filterWidgetOption['quantity']],
            ['attribute'=>'evid_desc','headerOptions'=>['title'=>'Media Description','id'=>'media_grid_evid_desc','class' => 'mediadesc-width word-break'],'contentOptions'=>['class' => 'mediadesc-width word-break','headers'=>'media_grid_evid_desc']],
            ['attribute' => 'contents_total_size','filterType'=>$filter_type['contents_total_size'],'headerOptions'=>['title'=>'Size','id'=>'media_grid_content_size','class' => 'totalsize-width word-break'],'contentOptions'=>['class' => 'totalsize-width word-break','headers'=>'media_grid_content_size'],'filterWidgetOptions'=>$filterWidgetOption['contents_total_size'],'value'=>function($model){return $model->contents_total_size.' '.$model->evidenceunitunit_name;}],
            ['attribute' => 'contents_total_size_comp','filterType'=>$filter_type['contents_total_size'],'label'=>'Comp Size','headerOptions'=>['title'=>'Size','id'=>'media_grid_total_size','class' => 'totalsize-width word-break'],'contentOptions'=>['class' => 'totalsize-width word-break','headers'=>'media_grid_total_size'],'filterWidgetOptions'=>$filterWidgetOption['contents_total_size_comp'],'value'=>function($model){return $model->contents_total_size_comp.' '.$model->evidencecompunit_name;}],
        ];
}
?>
<style>
.kv-expand-icon a span.ectext,
.kv-expand-header-icon a span.ectext{
	display: none;
}
</style>
<div class="right-main-container" id="media_container">
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php /* $this->render('_search', ['model' => $searchModel,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'columns'=>$columns]); */?>
			<?php $dynagrid = DynaGrid::begin([
		    'columns'=>$columns,
		    'storage'=>'db',
		    'theme'=>'panel-info',
		    'enableMultiSort'=>true,
		    'gridOptions'=>[
		        'id'=>'media-grid',
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'panel'=>false,
				//'layout' => '{items}<div class="kv-panel-pager text-right">{summary}<button type="button" class="btn btn-sm btn btn-primary" title="Filter grid"  data-toggle="modal" data-target="#modalMediaFilter"><em class="fa fa-search" aria-hidden="true"></em></button>&nbsp;{dynagridSort}{dynagrid}{pager}</div>',
				'layout' => '{items}<div class="kv-panel-pager text-right">{summary}{dynagridSort}{dynagrid}{pager}</div>',
				'responsiveWrap' => false,
				'export'=>false,
				'floatHeader'=>true,
				'floatHeaderOptions' => ['top' => 'auto'],
				'persistResize'=>false,
				'resizableColumns'=>false,
				'pjax'=>true,
					'pjaxSettings'=>[
                                            'options'=>['id'=>'dynagrid-media-pajax','enablePushState' => false],
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
			'allowSortSetting'=>true,
		    'enableMultiSort'=>true,
		    'toggleButtonGrid'=>['class'=>'btn btn-info btn-sm'],
		    'toggleButtonSort'=>['class'=>'btn btn-sm'],
		    'options'=>[
		    'id'=>'dynagrid-media',
		    'clientOptions' => ['method' => 'GET', 'url' => Url::to(['media/index'])]
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
</div>
</fieldset>
    <div class="button-set text-right">
        <!--<span class="text-left pull-left">
		<a href="javascript:void(0);" title="Total Media Returned - <?= $returns ?>"> Total Media Returned - <?= $returns ?> </a>
		</span>-->
            <?php $current_url = Url::current();
             $allprojects_url = Url::toRoute(['media/index']); ?>
            <?php if($current_url!=$allprojects_url) {  ?>
            <?= Html::button('All Media', ['class'=>'btn btn-primary all_filter','onclick'=>'displayAllMedias();','title'=>"All Media"]) ?>
            <?php } else { ?>
                 <?= Html::button('All Media', ['class'=>'btn btn-primary all_filter','onclick'=>'displayAllMedias();','title'=>"All Media",'style'=>'display:none;']) ?>
             <?php } ?>
            <?php if ((new User)->checkAccess(3.01)) { ?>
            <?= Html::button('Add Media', ['title'=>"Add Media", 'class' => 'btn btn-primary', 'onclick'=>'addMedia();'])?>
             <?php }else{ ?>
             <?= Html::button('Add Media', ['title'=>"Add Media", 'class' => 'btn btn-primary', 'onclick'=>'addMedia();', 'style'=>'visibility:hidden;'])?>
             <?php } ?>
     </div>
</div>
<script>
//	$('#evidencesearch-received_date').focus(function() {
//  	setTimeout(function(){ $('.daterangepicker .ranges ul li.active').focus(); }, 800);
//       console.log('nelson');
//  });
/*$('#evidencesearch-received_date').on('apply.daterangepicker', function(ev, picker) {
  //do something, like clearing an input
	//console.log(picker);
  $('#evidencesearch-received_date').val(picker.startDate.format('MM/DD/YYYY')+' - '+picker.endDate.format('MM/DD/YYYY'));
	$('#evidencesearch-received_date').trigger('change');

});*/
var $grid = $('#dynagrid-media-pjax'); // your grid identifier
var $grid = $('#media-grid'); // your grid identifier
$grid.on('kvexprow:toggle', function (event, ind, key, extra, state) {
   alert('Toggled expand row');
});
$('input').customInput();
<?php if(isset($params['id'])&& $params['id']!='') { ?>
    $('.all_filter').show();
<?php } ?>
$('#media-inventory-filter').change(function(event){
	var id = $(this).val();
	if(id!=0 && id!=""){
		var Url = httpPath + "media/index&client_case_id=" + id;
		location.href = Url;
	}else{
		var Url = httpPath + "media/index";
		location.href = Url;
	}
});

/*dyangird setting*/
$('#dynagrid-<?=$dynagrid->gridOptions['id']?>-modal').on('shown.bs.modal', function () {
	//var self = this,
       $element = $('input[name="<?=$dynagrid->options['id'] ?>-dynagrid');
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
<?php }?>
