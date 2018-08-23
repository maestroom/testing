<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CaseContactsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Contacts';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$model=$dataProvider->getModels();
$params=Yii::$app->request->queryParams;
?>
<div id="form_div" class="tab-inner-fix <?= empty($model)?'custom-grid-container':'' ?>" style="overflow: hidden;">
<?=GridView::widget([
		'id'=>'casecontactsgrid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_management_checkbox','scope'=>'col'],'contentOptions' => ['class' => 'text-center first-td','headers'=>'case_management_checkbox'],'filterOptions'=>['headers'=>'case_management_checkbox'], 'mergeHeader'=>false, 'rowHighlight' => false,'checkboxOptions'=>function($model, $key, $index, $column){ return ['customInput'=>true,'title'=>'Select Contact', 'class' => 'chk_contact_type_'.$key, 'value' => json_encode(array('contact_type' => $model->clientContacts->lname.' '.$model->clientContacts->fname.' '.$model->clientContacts->mi)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
				'headerOptions' => ['title' => 'Actions','id'=>'case_management_actions','scope'=>'col'],
				'contentOptions' => ['class' => 'text-center last-td','headers'=>'case_management_actions'],'filterOptions'=>['headers'=>'case_management_actions'],
				'mergeHeader'=>false,
		  		'template'=>'{delete}',
		  		'buttons'=>[
					'delete'=>function ($url, $model, $key) {
							$fullname = $model->clientContacts->getfullname();
							return Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
		  						'title' => Yii::t('yii', 'Remove'),
		  						'class' => 'icon-set',	
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
		  						'onclick'=>'javascript:deleteCaseContact('.$key.',"'.$fullname.'");']);
		  				},
		  			],
		  		],
				['attribute' => 'contact_type','headerOptions' => ['title' => 'Contact Type','id'=>'case_management_contact_type','scope'=>'col'], 'contentOptions' => ['style' => 'width:20% !important; padding: 4px 8px;','headers'=>'case_management_contact_type','class'=>'word-break'],'filterOptions'=>['headers'=>'case_management_contact_type'], 'filterInputOptions'=>['aria-label'=>'Filter By Contact Type'],'filter'=>'<input type="text" id="contact_type_case" name="CaseContactsSearch[contact_type]" value="'.$params['CaseContactsSearch']['contact_type'].'" class="form-control">','format' => 'raw','label'=>'Contact Type','value' => function ($model) { return $model->clientContacts->contact_type; },
				/* 'filterType'=>GridView::FILTER_SELECT2,
				'filterWidgetOptions'=>[
				'pluginEvents' => [
							"select2:select" => "function(evt) {
							 var abc = evt.params.data.label;
								$(document).on('pjax:end',   function(xhr, textStatus, options) {
								if(options.data.length){
									//$('#select2-casecontactssearch-contact_type-container').html(abc); 
									$('.all_filter').show();
								}else{
									//$('#select2-casecontactssearch-contact_type-container').html(null); 
									$('.all_filter').hide();
								}
								});
							}",
							],
					'pluginOptions'=>[
					'ajax' =>[
							'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
							'dataType' => 'json',
							'data' => new JsExpression('function(params) { return {q:params.term,field:"contact_type"}; }')
		  			]]],*/
					'filterType'=>$filter_type['contact_type'],
					'filterWidgetOptions'=>$filterWidgetOption['contact_type']						
				],
				['attribute' => 'lname', 'headerOptions' => ['title' => 'Contact Name','id'=>'case_management_contact_name','scope'=>'col'], 'contentOptions' => ['style' => 'width:20% !important; padding: 4px 8px;','headers'=>'case_management_contact_name','class'=>'word-break'],'filterOptions'=>['headers'=>'case_management_contact_name'], 'filterInputOptions'=>['aria-label'=>'Filter By Contact Name'], 'filter'=>'<input type="text" name="CaseContactsSearch[lname]" value="'.$params['CaseContactsSearch']['lname'].'" class="form-control">','format' => 'raw','label'=>'Contact Name','value' => function ($model) {return $model->clientContacts->getfullname();},
				/*'filterType'=>GridView::FILTER_SELECT2,
				'filterWidgetOptions'=>[
				'pluginEvents' => [
								"select2:select" => "function(evt) {
								 var abc = evt.params.data.label;
                                    $(document).on('pjax:end',   function(xhr, textStatus, options) {
                                    if(options.data.length){
										//$('#select2-casecontactssearch-lname-container').html(abc); 
										$('.all_filter').show();
									}else{
										//$('#select2-casecontactssearch-lname-container').html(null); 
										$('.all_filter').hide();
									}	
									});
									
								}",
								],
						'pluginOptions'=>[
						'ajax' =>[
						'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
								'dataType' => 'json',
								'data' => new JsExpression('function(params) { return {q:params.term,field:"fullname"}; }')
		  			]]],*/
					'filterType'=>$filter_type['lname'],
					'filterWidgetOptions'=>$filterWidgetOption['lname']						
				],
				['attribute' => 'add_1', 'contentOptions' => ['style' => 'padding: 4px 8px;','headers'=>'case_management_contact_address','class'=>'word-break'],'filterOptions'=>['headers'=>'case_management_contact_address'],'filterInputOptions'=>['aria-label'=>'Filter By Contact Address'], 'headerOptions' => ['title' => 'Contact Address','id'=>'case_management_contact_address','scope'=>'col'], 'filter'=>'<input type="text" name="CaseContactsSearch[add_1]" value="'.$params['CaseContactsSearch']['add_1'].'" class="form-control">','label'=>'Contact Address','format' => 'raw','value' => function ($model) {return $model->clientContacts->displaycontactaddress();}
				,
				/*'filterType'=>GridView::FILTER_SELECT2,
				'filterInputOptions' => ['title'=>'Filter By Contact Address', 'class' => 'form-control'],
				'filterWidgetOptions'=>[
				'pluginEvents' => [
								"select2:select" => "function(evt) {
								 var abc = evt.params.data.label;
                                    $(document).on('pjax:end',   function(xhr, textStatus, options) {
                                    console.log('add');
                                    console.log(options.data);
                                    if(options.data.length){
										//$('#select2-casecontactssearch-add_1-container').html(abc); 
										$('.all_filter').show();
									}else{
										//$('#select2-casecontactssearch-add_1-container').html(null); 
										$('.all_filter').hide();
									}
									});
								}",
								],
						'pluginOptions'=>[
						'ajax' =>[
						'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
								'dataType' => 'json',
								'data' => new JsExpression('function(params) { return {q:params.term,field:"add_1"}; }')
		  								]]],*/
					'filterType'=>$filter_type['add_1'],
					'filterWidgetOptions'=>$filterWidgetOption['add_1']							
				],
				['attribute' => 'notes', 'contentOptions' => ['style' => 'padding: 4px 8px;','headers'=>'case_management_notes','class'=>'word-break'],'filterOptions'=>['headers'=>'case_management_notes'],'filterInputOptions'=>['aria-label'=>'Filter By Contact Notes'], 'headerOptions' => ['title' => 'Notes','id'=>'case_management_notes','scope'=>'col','class'=>'white-space'], 'filter'=>'<input type="text" name="CaseContactsSearch[notes]" value="'.$params['CaseContactsSearch']['notes'].'" class="form-control">','format' => 'raw','value' => function ($model) {return $model->clientContacts->notes;}
				,
				/*'filterType'=>GridView::FILTER_SELECT2,
				'filterWidgetOptions'=>[
				'pluginEvents' => [
								"select2:select" => "function(evt) {
								 var abc = evt.params.data.label;
                                    $(document).on('pjax:end',   function(xhr, textStatus, options) {
                                    if(options.data.length){
										//$('#select2-casecontactssearch-notes-container').html(abc); 
										$('.all_filter').show();
									}else{
										//$('#select2-casecontactssearch-notes-container').html(null); 
										$('.all_filter').hide();
									}
									});
								}",
								],
						'pluginOptions'=>[
						'ajax' =>[
								'url' => Url::toRoute(['case/ajax-filter', 'case_id' => $case_id]),
								'dataType' => 'json',
								'data' => new JsExpression('function(params) { return {q:params.term,field:"notes"}; }')
		  								]]],*/
					'filterType'=>$filter_type['notes'],
					'filterWidgetOptions'=>$filterWidgetOption['notes']	
				],
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'casecontactsgrid-pajax','enablePushState' => false],
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
		//'rowOptions'=>['class'=>'sort'],
]);
?>
</div>
<div class="button-set text-right">
	<?= Html::button('All Contacts',['title'=>"All 
	Contacts",'id'=>'case-contact-all-contact','class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'loadCaseContactList();'])?>
	<?= Html::button('Remove',['title'=>"Remove",'class' => 'btn btn-primary','onclick'=>'deleteSelectedCaseContact();'])?>
	<?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onclick'=>'addCaseContacts('.$client_id.');'])?>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('casecontactsgrid-pajax');

$(document).on('pjax:end',   function(xhr, textStatus, options) {
	$("#casecontactsgrid-pajax :input").each(function(){
		$('.all_filter').hide();
		name=$(this).attr('name');
		if(name.indexOf("Search") > 0){
			if(this.value!=""){
				$('.all_filter').show();
				return false;
			}
		}
	});
});
</script>
<noscript></noscript>
