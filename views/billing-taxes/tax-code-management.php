<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TaxClassSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$modeluser=new User();
$this->title = 'Select Invoice Criteria';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
['class' => '\kartik\grid\CheckboxColumn','checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_tax_code_'.$key, 'value' => json_encode(array('tax_code' => $model->tax_code))];},'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'tax_codes_checkbox','scope'=>'col'], 'rowHighlight' => false, 'mergeHeader'=>false,'contentOptions'=>['title'=>'Select Row','class' => 'first-td text-center','headers'=>'tax_codes_checkbox'],'filterOptions'=>['headers'=>'tax_codes_checkbox'],'order'=>DynaGrid::ORDER_FIX_LEFT],
['class' => 'kartik\grid\ActionColumn', 'headerOptions' => ['title' => 'Actions','class'=>'internal-action-width third-th','id'=>'tax_codes_actions','scope'=>'col'], 'contentOptions' => ['class' => 'action text-center third-td','headers'=>'tax_codes_actions'],'filterOptions'=>['headers'=>'tax_codes_actions'], 'mergeHeader'=>false,'template' => '{sort}&nbsp;{update}&nbsp;{delete}', 'buttons' => [ 'update' => function ($url, $model, $key) { return Html::a ( '<em title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [ 'class' => 'icon-set','title' => Yii::t ( 'yii', 'Edit' ),'aria-label' => Yii::t ( 'yii', 'Edit Tac Code' ),'onclick' => 'edittaxcode('.$model->id.');' ] ); },
'delete' => function ($url, $model, $key)use($modeluser){
	//$onclick="javascript:alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
	if ($modeluser->checkAccess(7.11)){
		$onclick='javascript:removetaxcoderow('.$model->id.',"'.$model->tax_code.'");';
		return Html::a ( '<em title="Delete" class="fa fa-close text-primary"></em>', $onclick, [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Delete' ),'aria-label' => Yii::t ( 'yii', 'Delete' ) ] );
	}
	else
	{
		return "";
	}
	
} 
],'order'=>DynaGrid::ORDER_FIX_LEFT],
['attribute' => 'tax_code', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Tax Code Name','class'=>'form-control'], 'label'=>'Tax Code Name', 'headerOptions'=>['title'=>'Tax Code Name','id'=>'tax_codes_name','scope'=>'col'], 'contentOptions' => ['class' => 'tax_code_name_width','headers'=>'tax_codes_name'],'filterOptions'=>['headers'=>'tax_codes_name'], 'value' =>  function ($model) { return $model->tax_code; }, 'filterType'=>$filter_type['tax_code'],'filterWidgetOptions'=>$filterWidgetOption['tax_code']],
['attribute' => 'tax_class_id', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Tax Class'], 'label'=>'Tax Class', 'headerOptions'=>['title'=>'Tax Class','id'=>'tax_codes_class','scope'=>'col'], 'contentOptions' => ['class' => 'tax_id_width','headers'=>'tax_codes_class'],'filterOptions'=>['headers'=>'tax_codes_class'], 'value' =>  function ($model) { return $model->class_name; }, 'filterType'=>$filter_type['tax_class_id'],'filterWidgetOptions'=>$filterWidgetOption['tax_class_id']],
['attribute' => 'tax_code_desc', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Tax Code Description','class'=>'form-control'], 'label'=>'Tax Code Description', 'headerOptions'=>['title'=>'Tax Code Description','id'=>'tax_codes_description','scope'=>'col'], 'contentOptions' => ['class' => 'tax_code_width','headers'=>'tax_codes_description'],'filterOptions'=>['headers'=>'tax_codes_description'], 'value' =>  function ($model) { return $model->tax_code_desc;}],
['attribute' => 'tax_rate', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Tax'], 'label'=>'Tax %', 'headerOptions'=>['title'=>'Tax %','id'=>'tax_codes_percent','scope'=>'col'], 'contentOptions' => ['class' => 'tax-rate-width text-center','headers'=>'tax_codes_percent'],'filterOptions'=>['headers'=>'tax_codes_percent'], 'value' =>  function ($model) { return $model->tax_rate.'%'; },'filterType'=>$filter_type['tax_rate'],'filterWidgetOptions'=>$filterWidgetOption['tax_rate']],	
['attribute' => 'client', 'format' => 'raw', 'filterInputOptions' => ['title' => 'Filter By Client'], 'label'=>'# Clients', 'headerOptions'=>['title'=>'# Clients','id'=>'tax_codes_clients','scope'=>'col'], 'contentOptions' => ['class' => 'client-billing-width text-center','headers'=>'tax_codes_clients'],'filterOptions'=>['headers'=>'tax_codes_clients'], 'value' =>  function ($model) { return $model->getClientListsLink($model->id, $model->client); },'filterType'=>$filter_type['client'],'filterWidgetOptions'=>$filterWidgetOption['client']],
];
?>
<div class="right-main-container" id="media_container">
	<div class="sub-heading"><?= Html::encode($this->title) ?></div>
    <fieldset class="one-cols-fieldset case-project-fieldset">
        <div class="table-responsive">
			<?php $dynagrid = DynaGrid::begin([
    'columns'=>$columns,
    'storage'=>'db',
    'theme'=>'panel-info',
    'gridOptions'=>[
        'id'=>'tax-codes-grid',
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
					'options'=>['id'=>'tax-codes-pajax','enablePushState' => false],
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
    'id'=>'dynagrid-tax-codes',
    ] // a unique identifier is important
]);
if (substr($dynagrid->theme, 0, 6) == 'simple') {
    $dynagrid->gridOptions['panel'] = false;
}
DynaGrid::end();
?>
		</div>
	</fieldset>
	<div id="bulkreopen-closed-dialog" class="bulkreopentasks hide">
		<fieldset>
			<legend class="sr-only"></legend>
			<div class="custom-inline-block-width">
				<input aria-setsize="2" aria-posinset="1" type="radio" name="bulkreopen" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkreopen"/><label for="rdo_bulkreopen">All <span id="alltask">0</span> Projects in Grid</label>
				<input aria-setsize="2" aria-posinset="2" type="radio" name="bulkreopen" class="bulkreopen" value="selectedtask" id="rdo_selectedreopen"><label for="rdo_selectedreopen">Selected <span id="selectedtask">0</span> Projects in Grid</label>
			</div>
		</fieldset>
	</div>
    <div class="button-set text-right">
    	<?= Html::button('All Tax Codes',['title'=>"All Tax Codes",'class' => 'btn btn-primary all_filter', 'style' => 'display:none', 'onclick' => 'loadercodepage();'])?>
		<?php if ($modeluser->checkAccess(7.11)){?>
    	<?= Html::button('Delete', ['title'=>"Delete",'class' => 'btn btn-primary','onClick' => 'removetaxcode();']) ?>
		<?php }?>
        <?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onClick' => 'addtaxcode();']) ?>
     </div>
</div>
<script>
	$('#case_id').val('<?php echo $case_id;?>');
	function loadSaveProjects(case_id){
		location.href = baseUrl +'case-projects/index&case_id='+case_id;
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
