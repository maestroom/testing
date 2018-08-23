<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\sortable\Sortable;
use app\models\Teamservice;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teamservice';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<fieldset class="two-cols-fieldset">
	<div id="wf-tabs">
		<ul>
			<li><a href="#tabs-teamservice" class="case-manager-header" title="Team Services">Team Services</a></li>
			<li><a href="#tabs-servicetask" class="case-manager-header" title="Service Tasks">Service Tasks</a></li>
			<li><a href="#tabs-assigneduser" class="case-manager-header" title="Assigned Users">Assigned Users</a></li>
			<?php if($teamId!=1) { ?><li><a href="#tabs-editeam" class="case-manager-header" title="Edit Team">Edit Team</a></li><?php }?>
		</ul>
		<div id="tabs-teamservice">
			<div class="tab-inner-fix">
					<?= GridView::widget ( 
					[ 'id' => 'teamservicegrid',
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
						'columns' => [ 
							[
								'class' => '\kartik\grid\CheckboxColumn',
								'headerOptions' => ['title'=>'Select All/None','id'=>'team_service_checkbox','scope'=>'col'],
								'contentOptions' => ['title'=>'Select Row', 'class' => 'text-center first-td','headers'=>'team_service_checkbox'],
								'filterOptions'=>['headers'=>'team_service_checkbox'],
								'checkboxOptions' => function($model, $key, $index, $column){ return ['customInput'=>true, 'class' => 'chk_service_name_'.$key, 'value' => json_encode(array('service_name' => $model->service_name)) ]; },
								'rowHighlight' => false,
								'mergeHeader' => false
							],
							['class' => 'kartik\grid\ActionColumn', 'headerOptions' => ['title' => 'Actions','class'=>'third-th','id'=>'team_service_actions','scope'=>'col'], 'contentOptions' => ['class' => 'text-center third-td', 'style' => 'padding: 4px 8px;','headers'=>'team_service_actions'],'filterOptions'=>['headers'=>'team_service_actions'], 'mergeHeader'=>false,'width'=>'15%','template' => '{sort}&nbsp;{update}&nbsp;{delete}', 'buttons' => [ 'sort' => function ($url, $model, $key) { return Html::a('<em class="fa fa-arrows text-primary" title="Move"></em>', 'javascript:void(0);', ['title' => Yii::t('yii', 'Move'),'aria-label' => Yii::t ( 'yii', 'Move' ),'class' => 'handel_sort icon-set','data-key' => $model->id]); }, 'update' => function ($url, $model, $key) { return Html::a ( '<em class="fa fa-pencil text-primary" title="Edit"></em>', 'javascript:void(0);', [ 'class' => 'icon-set','title' => Yii::t ( 'yii', 'Edit' ),'aria-label' => Yii::t ( 'yii', 'Edit Team Service' ),'onclick' => 'UpdateTeamService(' . $key . ',' . $model->teamid . ');' ] ); },'delete' => function ($url, $model, $key) {return Html::a ( '<em class="fa fa-close text-primary" title="Remove"></em>', 'javascript:DeleteTeamService(' . $key . ',' . $model->teamid . ');', [ 'class' => 'icon-set', 'title' => Yii::t ( 'yii', 'Remove' ),'aria-label' => Yii::t ( 'yii', 'Remove' ),'data' => [ 'confirm' => Yii::t ( 'yii', "Are you sure you want to Remove ".$model->service_name."?" ) ] ] );} ] ],
							['attribute' => 'service_name', 'contentOptions' => ['style' => 'padding: 4px 8px;','headers'=>'team_service_service_name'],'filterOptions'=>['headers'=>'team_service_service_name'],'headerOptions' => ['title' => 'Service Name','id'=>'team_service_service_name','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control'],'filterType'=>$filter_type['service_name'],'filterWidgetOptions'=>$filterWidgetOption['service_name']],
							[ 'attribute' => 'service_description', 'contentOptions' => ['class' => 'mytest','headers'=>'team_service_description'],'filterOptions'=>['headers'=>'team_service_description'], 'headerOptions' => ['title' => 'Service Description','id'=>'team_service_description','scope'=>'col'], 'filterInputOptions' => ['placeholder' => 'Filter List', 'class' => 'form-control']],
							['attribute' => 'hastasks', 'headerOptions' => ['title' => 'Tasks','id'=>'team_service_tasks','scope'=>'col'], 'header' => 'Tasks?' , 'contentOptions' => ['class' => 'text-center', 'style' => 'padding: 4px 8px;','headers'=>'team_service_tasks'],'filterOptions'=>['headers'=>'team_service_tasks'], 'format' => 'raw', 'filterType' => $filter_type['hastasks'],'filterWidgetOptions' => $filterWidgetOption['hastasks'], 'value' => function ($model) {if ($model->hastasks==1) {return '<a href="javascript:void(0);" title="Tasks" aria-label="Tasks? yes" class="tag-header-black"><em title="Tasks" class="fa fa-check text-danger"></em></a>';} else {return false;}} ],],
								'floatHeader' => true,
								'pjax' => true,
								'pjaxSettings' => [ 'options' => ['id' => 'teamservice-pajax','enablePushState' => false ],'neverTimeout' => true,'beforeGrid' => '','afterGrid' => '' ],
								'export' => false,
								'responsive' => false,
								'hover' => true,
								'pager' => [ 'options' => [ 'class' => 'pagination' ],   /* set clas name used in ui list of pagination*/
									'prevPageLabel' => 'Previous',   /* Set the label for the "previous" page button */
									'nextPageLabel' => 'Next',   /* Set the label for the "next" page button*/
									'firstPageLabel' => 'First',   /* Set the label for the "first" page button*/
									'lastPageLabel' => 'Last',    /* Set the label for the "last" page button*/
									'nextPageCssClass' => 'next',    /* Set CSS class for the "next" page button*/
									'prevPageCssClass' => 'prev',    /* Set CSS class for the "previous" page button*/
									'firstPageCssClass' => 'first',    /* Set CSS class for the "first" page button*/
									'lastPageCssClass' => 'last',    /* Set CSS class for the "last" page button*/
									'maxButtonCount' => 5,    /* Set maximum number of page buttons that can be displayed*/
								],
								'rowOptions' => [ 'class' => 'sort' ] 
						  ] 
						);
				?>
			</div>
			<div class="button-set text-right">
					 <?= Html::button('Remove', ['title'=>"Remove",'class' => 'btn btn-primary','onclick'=>'RemoveCaseTeamService('.$teamId.');'])?>
					 <?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onclick'=>'AddCaseTeamService('.$teamId.');'])?>
			</div>

		</div>
		<div id="tabs-servicetask"></div>
		<div id="tabs-assigneduser"></div>
		<?php if($teamId!=1) {?><div id="tabs-editeam"></div><?php }?>
	</div>
</fieldset>
<script type="text/javascript">
$(function() {
	$('input').customInput();
	$('#module-url').val('<?=$fullUrl ?>');
	$('#pajax_container').val('teamservice-pajax');
    $( "#wf-tabs" ).tabs({
      beforeActivate: function (event, ui) {
			var chk_status = checkformstatus(event); // check form status
			if(chk_status == true) { // check status form
				if(ui.newPanel.attr('id') == 'tabs-servicetask'){
					jQuery.ajax({
					   url: baseUrl +'/workflow/servicetask',
					   data:{team_id: <?= $teamId?>},
					   type: 'post',
					   beforeSend:function (data) {showLoader();},
					   success: function (data) {
						 hideLoader();
                                                 $('#maincontainer').removeClass('slide-close');
						 $('#tabs-servicetask').html(data);
						 $('#is_change_form').val('0'); 
						 $('#is_change_form_main').val('0');
					   }
				  });
				}
				if(ui.newPanel.attr('id') == 'tabs-assigneduser'){
					jQuery.ajax({
					   url: baseUrl +'/workflow/teamusers',
					   data:{team_id: <?= $teamId?>},
					   type: 'get',
					   beforeSend:function (data) {showLoader();},
					   success: function (data) {
						 hideLoader();
						 $('#tabs-assigneduser').html(data);
						 $('#is_change_form').val('0'); 
						 $('#is_change_form_main').val('0');
					   }
				  });
				}
				if(ui.newPanel.attr('id') == 'tabs-editeam'){
					jQuery.ajax({
					   url: baseUrl +'/workflow/editeam',
					   data:{team_id: <?= $teamId?>},
					   type: 'post',
					   beforeSend:function (data) {showLoader();},
					   success: function (data) {
						 hideLoader();
						 $('#tabs-editeam').html('<div id="form_div">'+data+'</div>');
						 $('#is_change_form').val('0'); 
						 $('#is_change_form_main').val('0');
					   }
				  });
				}
				if(ui.newPanel.attr('id') == 'tabs-teamservice'){
					updateTeam(<?= $teamId?>);
				}
			}
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });
 });
 var fixHelper = function(e, ui) {
		ui.children().each(function() {
		$(this).width($(this).width());
		});
		return ui;
};
		
  $(".sort").parent().sortable({
	    handle:'.handel_sort',
		helper: fixHelper,
		update: function(e,ui) {
			var sorder="";
			var sort_arr = new Array();
			$(".sort ").each(function(i){ //new code for sorting
					sort_arr[i]=$(this).data('key');
					if(sorder == "")
						sorder = $(this).data('key');
					else
						sorder = sorder + ','  + $(this).data('key');

			});
			jQuery.ajax({
			       url: baseUrl +'/workflow/sortteamservice',
			       data:{sort_ids: sorder},
			       type: 'post',
			       beforeSend:function (data) {showLoader();},
			       success: function (data) {
			    	   hideLoader();
			    	/*   if(data != 'OK')
			    		  alert('Error');*/
			       }
			  });
		}
	}).disableSelection(); 

</script>
<noscript></noscript>
