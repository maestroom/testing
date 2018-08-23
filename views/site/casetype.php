<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="right-main-container slide-open" id="maincontainer">
		<fieldset class="two-cols-fieldset">
			<div class="administration-main-cols">
		<div class="administration-lt-cols pull-left">
			<button id="controlbtn" title="Expand/Collapse" aria-label="Expand or Collapse" class="slide-control-btn" onclick="WorkflowToggle();"><span>&nbsp;</span></button>
			<ul>
				<li><a href="#" title="Dropdowns" class="admin-main-title"><em title="Dropdowns" class="fa fa-folder-open text-danger"></em>Dropdowns</a>
			    	<div class="manage-admin-left-module-list">
						<ul class="sub-links">
					 		<li class="dropdown" id="CaseCloseType"><a href="javascript:void(0);" onclick="SelectManageDropdown('CaseCloseType');" class="dropdown" title="Case Close Type"><em title="Case Close Type" class="fa fa-list text-danger"></em> Case Close Type</a></li>
					 		<li class="dropdown active" id="CaseType"><a href="javascript:void(0);" onclick="SelectManageDropdown('CaseType');" class="dropdown" title="Case Type"><em title="Case Type" class="fa fa-list text-danger"></em> Case Type</a></li>
					 		<li class="dropdown" id="Industries"><a href="javascript:void(0);" onclick="SelectManageDropdown('Industries');" class="dropdown" title="Client Industries"><em title="Client Industries" class="fa fa-list text-danger"></em> Client Industries</a></li>
					 		<li class="dropdown" id="MediaCategory"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaCategory');" class="dropdown" title="Media Category"><em title="Media Category" class="fa fa-list text-danger"></em> Media Category</a></li>
					 		<li class="dropdown" id="MediaDataType"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaDataType');" class="dropdown" title="Media Data Type"><em title="Media Data Type" class="fa fa-list text-danger"></em> Media Data Type</a></li>
					 		<li class="dropdown" id="MediaEncrypt"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaEncrypt');" class="dropdown" title="Media Encrypt"><em title="Media Encrypt" class="fa fa-list text-danger"></em> Media Encrypt</a></li>
					 		<li class="dropdown" id="MediaTo"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaTo');" class="dropdown" title="Media To"><em title="Media To" class="fa fa-list text-danger"></em> Media To</a></li>
					 		<li class="dropdown" id="MediaType"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaType');" class="dropdown" title="Media Type"><em title="Media Type" class="fa fa-list text-danger"></em> Media Type</a></li>
					 		<li class="dropdown MediaLocation"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaLocation');" class="dropdown MediaLocation" title="Media Location"><em title="Media Location" class="fa fa-list text-danger"></em> Media Location</a></li>
					 		<li class="dropdown" id="ProjectPriority"><a href="javascript:void(0);" onclick="SelectManageDropdown('ProjectPriority');" class="dropdown" title="Project Priority"><em title="Project Priority" class="fa fa-list text-danger"></em> Project Priority</a></li>
					 		<li class="dropdown ProjectPriorityTeam"><a href="javascript:void(0);" onclick="SelectManageDropdown('ProjectPriorityTeam');" class="dropdown ProjectPriorityTeam" title="Project Priority - Team"><em title="Project Priority - Team" class="fa fa-list text-danger"></em> Project Priority - Team</a></li>
					 		<li class="dropdown" id="ProjectRequestType"><a href="javascript:void(0);" onclick="SelectManageDropdown('ProjectRequestType');" class="dropdown" title="Project Request Type"><em title="Project Request Type" class="fa fa-list text-danger" id=""></em> Project Request Type</a></li>
					 		<li class="dropdown" id="TeamLocations"><a href="javascript:void(0);" onclick="SelectManageDropdown('TeamLocations');" class="dropdown" title="Team Locations"><em title="Team Locations" class="fa fa-list text-danger" id=""></em> Team Locations</a></li>
					 		<li class="dropdown" id="ToDoFollowupCategory"><a href="javascript:void(0);" onclick="SelectManageDropdown('ToDoFollowupCategory');" class="dropdown" title="ToDo Follow-up Category"><em title="ToDo Follow-up Category" class="fa fa-list text-danger" id=""></em> ToDo Follow-up Category</a></li>
					 		<li class="dropdown" id="MediaDataUnits"><a href="javascript:void(0);" onclick="SelectManageDropdown('MediaDataUnits');" class="dropdown" title="Units"><em title="Units" class="fa fa-list text-danger"></em> Units</a></li>
						</ul>
					</div>
			   </li>
			</ul>
		</div>
		<div class="administration-rt-cols pull-right" id="admin_right">
		  <div class="table-responsive">
		  <?= 
 GridView::widget([
 		'id'=>'casetype-grid',
		'dataProvider'=> $dataProvider,
		'filterModel' => $searchModel,
		'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
		'columns' =>[
				['class' => '\kartik\grid\CheckboxColumn', 'mergeHeader'=>false, 'rowHighlight' => false,'headerOptions'=>['title'=>'Select All/None','class'=>'first-th','id'=>'case_type_checkbox','scope'=>'col'],'contentOptions'=>['title'=>'Select Row','class'=>'first-td','headers'=>'case_type_checkbox'],'filterOptions'=>['headers'=>'case_type_checkbox'], 'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_case_type_name_'.$key, 'value' => json_encode(array('case_type_name' => $model->case_type_name)) ]; }],
				['class' => 'kartik\grid\ActionColumn',
				'headerOptions' => ['class'=>'third-th','title'=>'Actions','id'=>'case_type_actions','scope'=>'col'],
		  		'contentOptions' => ['class' => 'third-td','headers'=>'case_type_actions'],'filterOptions'=>['headers'=>'case_type_actions'],
		  		'template'=>'{update}&nbsp;{delete}',
		  		'mergeHeader'=>false,
		  		'buttons' => [
		  				'update'=>function ($url, $model, $key) {
                                                    return
                                                        Html::a('<em  title="Edit" class="fa fa-pencil text-primary"></em>', 'javascript:void(0);', [
                                                            'title' => Yii::t('yii', 'Edit'),
                                                            'class' => 'icon-set',
                                                            'aria-label' => 'Edit Case Type',
                                                            'onclick' =>'UpdateCaseType('.$key.');'
                                                        ]);
		  				},
		  				'delete'=>function ($url, $model, $key) {
                                                    return
                                                        Html::a('<em  title="Delete" class="fa fa-close text-primary"></em>', 'javascript:DeleteCaseType('.$key.');', [
                                                            'title' => Yii::t('yii', 'Delete'),
                                                            'class' => 'icon-set',
                                                            'aria-label' => 'Remove Case Type',
                                                            'data' => [
                                                                'confirm' =>  Yii::t('yii',"If you delete this dropdown option, it will also then be removed from where it has been tagged historically.\nAre you sure you want to Remove ".$model->case_type_name."?"),
                                                            ],
                                                    ]);
		  				},
		  			],
		  		],
		  		['attribute'=>'case_type_name', 'contentOptions' => ['style' => 'padding:4px 7px;','headers'=>'case_type_name'],'filterOptions'=>['headers'=>'case_type_name'], 'headerOptions' => ['title' => 'Case Type','id'=>'case_type_name','scope'=>'col'],'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'], 'filterType'=>$filter_type['case_type_name'],'filterWidgetOptions'=>$filterWidgetOption['case_type_name']],
		  		
		 ],
		'floatHeader'=>true,    
		'pjax'=>true,
		'pjaxSettings'=>[
                'options'=>['id'=>'casetype-pajax','enablePushState' => false],	
                    'neverTimeout'=>true,
                    'beforeGrid'=>'',
                    'afterGrid'=>'',
                ],
                'export'=>false,
		'responsive'=>false,
		'floatHeaderOptions' => ['top' => 'auto'],
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
				<div class="button-set text-right">
				   <!--  <button class="btn btn-primary" title="Update Button">Edit</button>-->
				 <?= Html::button('All Case Type',['title'=>"All Case Type",'class' => 'btn btn-primary all_filter', 'style' => 'display:none','onclick'=>'CaseType();'])?>  	  
				 <button class="btn btn-primary" title="Remove" onclick="RemoveCaseType();">Remove</button>
				 <button class="btn btn-primary" title="Add"    onclick="AddCaseType();">Add</button>
				</div>
			 </div>
			</div>
		</fieldset>
</div>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('casetype-pajax');
</script>
<noscript></noscript>
