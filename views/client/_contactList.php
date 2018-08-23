<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\grid\GridView;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientContactsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
$count_model=$dataProvider->getModels();
$params=Yii::$app->request->queryParams;
//echo "<pre>"; print_r($dataProvider->getModels()); exit;
?>
<div class="tab-inner-fix <?= empty($count_model)?'custom-grid-container':'' ?>"  style="overflow: hidden;">
<?php 
	echo GridView::widget([
		'id'=>'contactsgrid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'client_management_checkbox','scope'=>'col'],'contentOptions' => ['class' => 'text-center word-break first-td','headers'=>'client_management_checkbox'],'filterOptions'=>['headers'=>'client_management_checkbox'], 'mergeHeader'=>false,'rowHighlight' => false,'checkboxOptions'=>function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_contact_type_'.$key,'title'=>'Select Contact', 'value' => json_encode(array('contact_type' => $model->lname.' '.$model->fname.' '.$model->mi)) ]; }],
				[ 'class' => 'kartik\grid\ActionColumn',
		  		'headerOptions' => ['title' => 'Actions','class'=>'third-th','id'=>'client_management_actions','scope'=>'col'],
		  		'mergeHeader'=>false,
		  		'contentOptions' => ['class' => 'word-break text-center third-td','headers'=>'client_management_actions'],'filterOptions'=>['headers'=>'client_management_actions'],
		  		'template'=>'{sort}&nbsp;{update}&nbsp;{delete}',
		  		'buttons'=>[
		  			'sort'	=> function($url, $model, $key){
		  				
		  			},
					'update'=>function ($url, $model, $key) {
		  				return
		  					Html::a('<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
		  						'title' => Yii::t('yii', 'Edit'),
                                                                'aria-label' => Yii::t ( 'yii', 'Edit Client Contact' ),
		  						'class' => 'icon-set',	
		  						'onclick'=>'updateClientContact('.$key.');'
						]);
					},
		  			'delete'=>function ($url, $model, $key) {
		  			$fullname=$model->getfullname();
		  				return
		  					Html::a('<em title="Remove" class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
		  						'title' => Yii::t('yii', 'Remove'),
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
		  						'class' => 'icon-set',
		  						'onclick'=>'javascript:deleteClientContact('.$model->client_id.','.$key.',"'.$fullname.'");'
							]);
		  				},
		  			],
		  		],
				['attribute' => 'contact_type', 'headerOptions' => ['title' => 'Contact Type','id'=>'client_management_contact_type','scope'=>'col'], 'contentOptions' => ['style' => 'width:20% !important; padding:4px 7px;','headers'=>'client_management_contact_type','class'=>'word-break'],'filterOptions'=>['headers'=>'client_management_contact_type'], 'filterInputOptions' => ['class' => 'form-control','aria-label'=>'Contact Type'],'filterType'=>$filter_type['contact_type'],'filterWidgetOptions'=>$filterWidgetOption['contact_type']],	
				['attribute' => 'fullname', 'headerOptions' => ['title' => 'Contact Name','id'=>'client_management_contact_name','scope'=>'col'], 'contentOptions' => ['style' => 'width:20% !important; padding:4px 7px;','headers'=>'client_management_contact_name','class'=>'word-break'],'filterOptions'=>['headers'=>'client_management_contact_name'], 'filterInputOptions' => ['class' => 'form-control'],'format' => 'raw','value' => function ($model) {return $model->getfullname();},'filterType'=>$filter_type['fname'],'filterWidgetOptions'=>$filterWidgetOption['fname']
				],
				//['attribute' => 'title','filterInputOptions' => ['placeholder' => 'Title', 'class' => 'form-control']],
				//['attribute' => 'phone_o','filterInputOptions' => ['placeholder' => 'Phone Office', 'class' => 'form-control']],
				//['attribute' => 'phone_m','filterInputOptions' => ['placeholder' => 'Phone Mobile', 'class' => 'form-control']],
				//['attribute' => 'email','filterInputOptions' => ['placeholder' => 'Email', 'class' => 'form-control']],
				['attribute' => 'add_1', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'client_management_contact_address','class'=>'word-break'],'filterOptions'=>['headers'=>'client_management_contact_address'], 'headerOptions' => ['title' => 'Contact Address','class' => 'text-nowrap','id'=>'client_management_contact_address','scope'=>'col'], 
				'filter'=>'<input type="text" name="ClientContactsSearch[add_1]" value="'.$params['ClientContactsSearch']['add_1'].'"  class="form-control">',
				'format' => 'raw','value' => function ($model) {return $model->displaycontactaddress();}
                                ,//'filterType'=>GridView::FILTER_SELECT2,
				'filterInputOptions' => ['title'=>'Filter By Contact Address', 'class' => 'form-control'],
//				'filterWidgetOptions'=>[
//						'pluginOptions'=>[
//						'ajax' =>[
//								'url' => Url::toRoute(['client/ajax-filter', 'client_id' => $client_id]),
//										'dataType' => 'json',
//										'data' => new JsExpression('function(params) { return {q:params.term,field:"add_1"}; }')
//		  								]]],
                                'filterType'=>$filter_type['add_1'],'filterWidgetOptions'=>$filterWidgetOption['add_1']	
				],
				['attribute' => 'notes', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'client_management_notes','class'=>'word-break'],'filterOptions'=>['headers'=>'client_management_notes'], 'headerOptions' => ['title' => 'Notes','id'=>'client_management_notes','scope'=>'col'], 'filterInputOptions' => ['class' => 'form-control']
//                                    ,'filterType'=>GridView::FILTER_SELECT2,
//						'filterWidgetOptions'=>[
//								'pluginOptions'=>[
//								'ajax' =>[
//										'url' => Url::toRoute(['client/ajax-filter', 'client_id' => $client_id]),
//												'dataType' => 'json',
//												'data' => new JsExpression('function(params) { return {q:params.term,field:"notes"}; }')
//		  								]]]
                                    ,'filterType'=>$filter_type['notes'],'filterWidgetOptions'=>$filterWidgetOption['notes']						
				],
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'contactsgrid-pajax','enablePushState' => false],
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
<div class="button-set text-right">
	<?= Html::button('All Contacts',['title'=>"All Contacts",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'loadClientContactList();'])?>
	<?= Html::button('Remove',['title'=>"Remove",'class' => 'btn btn-primary','onclick'=>'deleteSelectedClientContact();'])?>
	<?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onclick'=>'addClientContacts('.$client_id.');'])?>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('contactsgrid-pajax');

$(document).on('pjax:end',   function(xhr, textStatus, options) {
	$("#contactsgrid-pajax :input").each(function(){
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
