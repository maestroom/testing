<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use app\components\IsataskFormFlag;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\CaseXTeam */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Select Team Services To Exclude From New Project Process';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;

?>
<div id="excludeserviceform_div" class="tab-inner-fix">
<?php $form = ActiveForm::begin(['id' => 'excludecaseservices-form','enableAjaxValidation' => false,'enableClientValidation' => true]);
?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	<div class="user-access-mycase" style="top:10px!important">
	<a href="#" id="btnSelectAll">Select all</a> - <a href="#" id="btnDeselectAll">Deselect all</a> 
	<div id="treecasexteam" class="tree-class"></div>
    <textarea name="excludedservicelist" id="excludedservicelist" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ><?php echo json_encode($selected);?></textarea>
	</div>
	<?php /*echo GridView::widget([
		'id'=>'excludedservicelist-grid',
		'dataProvider'=> $dataProvider,
		'headerRowOptions' => ['title' => 'Team Services'],
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
			['attribute' => 'teamservices','header' => '<a href="javascript:void(0);" title="Team Service" class="tag-header-black">Team Service</a>' , 'header' => 'Team Service'],
			['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false, 'name' => 'excludedservicelist','headerOptions'=>['title'=>'Select All or None Team Service','class' => 'first-th'], 'checkboxOptions' => function($model, $key, $index, $column) { return [ 'checked' => $model['isserviceexcluded'], 'customInput'=>true,'class'=>'ckeck_excluded','data-teamservice_id'=>$model['id'],'data-team_loc'=>$model['teamservice_loc'], 'value' => json_encode(array('client_case_id'=>$model['client_case_id'],'team_loc'=>$model['teamservice_loc'],'teamservice_id'=>$model['id'])),'aria-label'=>$model['teamservices'] ]; } ],			
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'excludedservicelist-grid-pajax','enablePushState' => false],
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
	]);*/
	?>	
</div>
<div class="button-set text-right">
	<?= Html::button('Update', ['title' => 'Update','class' =>  'btn btn-primary','onclick'=>'postExcludedCasesServies("excludecaseservices-form",this,"loadExcludedServiceList()","excludeserviceform_div");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('excludedservicelist-grid-pajax');
$('body').on('change', '#excludedservicelist-grid', function () {
	$('#excludecaseservices-form #is_change_form').val('1'); 
	$('#excludecaseservices-form #is_change_form_main').val('1'); 
});
$('document').ready(function(){
	$('#active_form_name').val('excludecaseservices-form');
});
var treeData = <?= json_encode($serviceList); ?>;
    
    $(function(){
        $("#treecasexteam").dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData,
			onSelect: function(select, node) {
                var clientcaseAr = [];
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
                    if(node.childList===null)
					    return node.data.key.toString();
                });
                mystring = JSON.stringify(selKeys);
                newTemp = mystring.replace(/"/g, "'");
                $('#excludedservicelist').val(newTemp);  
			},
			onDblClick: function(node, event) {
				node.toggleSelect();
			},
			onKeydown: function(node, event) {
				if( event.which == 32 ) {
					node.toggleSelect();
					return false;
				}
			},
		});
    });
	$("#btnDeselectAll").click(function(){
        $("#treecasexteam").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });

    $("#btnSelectAll").click(function(){
        $("#treecasexteam").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    });         
</script>
<noscript></noscript>
