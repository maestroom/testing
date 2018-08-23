<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Assigned Users';
$this->params['breadcrumbs'][] = $this->title;
/*$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="tab-inner-fix tab-fix-without-btnset">
<?= 
 GridView::widget([
 		'id'=>'caseassigneduser',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
 			['attribute'=> 'usr_type', 'headerOptions' => ['title' => 'User Type','id'=>'team_assigned_user_user_type','scope'=>'col'], 'contentOptions' => ['class' => 'text-center first-td','headers'=>'team_assigned_user_user_type'],'filterOptions'=>['headers'=>'team_assigned_user_user_type'], 'header'=>'','format'=>'raw','value'=>function($model){ if($model->usr_type == '3') { $textclass = ' text-primary '; $texttitle = ' Active Directory ';} else if($model->usr_type == '1') { $textclass = ' text-gray '; $texttitle = ' Internal '; } else { $textclass = ' text-danger '; $texttitle = ' External '; } return '<span tabindex="0" title="'.$texttitle.' User" class="fa fa-user '.$textclass.'"></span>';},'filter'=>false],
			['attribute'=>'usr_username', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'case_assigned_username'],'filterOptions'=>['headers'=>'case_assigned_username'], 'headerOptions'=>['hAlign'=>GridView::ALIGN_CENTER, 'title' => 'User Name','id'=>'case_assigned_username','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter...', 'class' => 'form-control'],'filterType'=>$filter_type['usr_username'],'filterWidgetOptions'=>$filterWidgetOption['usr_username']],
			['attribute'=>'role_name', 'header' => '<a href="javascript:void(0);" title="User Role" class="tag-header-black">User Role</a>', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'case_assigned_role_name'],'filterOptions'=>['headers'=>'case_assigned_role_name'], 'headerOptions' => ['title' => 'User Role','id'=>'case_assigned_role_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter...', 'class' => 'form-control'],'format'=>'raw','value'=>function($model){ return $model->role->role_name; },'filterType'=>$filter_type['role_name'],'filterWidgetOptions'=>$filterWidgetOption['role_name']]
		 ],
		'floatHeader'=>true,
		'pjax'=>true,
		'pjaxSettings'=>[
			'options'=>['id'=>'caseassigneduser-pajax','enablePushState' => false],
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
		]
]);
		  ?>
</div>
<script>
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('caseassigneduser-pajax');
</script>
<noscript></noscript>
<?php */ 
if(!empty($userList)){
?>
<div class="user-access-mycase top10">
    <a href="#" id="btnExpandAll">Expand all</a> - <a href="#" id="btnCollapseAll">Collapse all</a> 
    <div id="treeClientCaseUser" class="tree-class"></div>
</div>
<script>
var treeData = <?= json_encode($userList); ?>;
    $(function(){
        $("#treeClientCaseUser").dynatree({
            checkbox: false,
			selectMode: 3,
			children: treeData,
			onSelect: function(select, node) {
                var clientcaseAr = [];
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
                    if(node.childList===null)
					    return node.data.key.toString();
                });
                //console.log(selKeys);
                mystring = JSON.stringify(selKeys);
                newTemp = mystring.replace(/"/g, "'");
                //$('#clientCasesToInput').val(newTemp);
                
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
	$("#btnCollapseAll").click(function(){
      $("#treeClientCaseUser").dynatree("getRoot").visit(function(node){
        node.expand(false);
      });
      return false;
    });
    $("#btnExpandAll").click(function(){
      $("#treeClientCaseUser").dynatree("getRoot").visit(function(node){
        node.expand(true);
      });
      return false;
    });
</script>
<?php }else{ ?>No Record Found...<?php } ?>