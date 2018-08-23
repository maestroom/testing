<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use app\components\IsataskFormFlag;

use app\models\ClientContacts;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientContacts */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Add Client Contacts To Case';
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="clientcontactform_div" class="tab-inner-fix">
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	<?=GridView::widget([
		'id'=>'clientcontactslist-grid',
		'dataProvider'=> $dataProvider,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
			['attribute'=>'fullname', 'header' => '<a href="javascript:void(0);" title="Contact Name" class="tag-header-black">Contact Name</a>','headerOptions' => ['title' => 'Contact Name']],
			['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false, 'name' => 'clientcontactslist','headerOptions'=>['title'=>'Select All or None Contact Name','class' => 'first-th'], 'checkboxOptions' => function($model, $key, $index, $column) { return [ 'checked' => $model->iscontactexist==1?true:false, 'value' => $model->id, 'customInput'=>true ,'title'=>$model->fullname]; } ],	
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'clientcontactslist-grid-pajax','enablePushState' => false],
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
	<?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary', 'onclick'=> 'loadCaseContactListCancel();']) ?>
	<?= Html::button('Update', ['title' => 'Update','class' =>  'btn btn-primary','onclick'=>'SubmitAjaxForm("'.$model->formName().'",this,"loadCaseContactList()","clientcontactform_div");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
$('input').customInput();
$('body').on('change', '#clientcontactslist-grid', function () {
	$('#CaseContacts #is_change_form').val('1'); 
	$('#CaseContacts #is_change_form_main').val('1'); 
});
$('document').ready(function(){ $('#active_form_name').val('CaseContacts'); });
</script>
<noscript></noscript>
