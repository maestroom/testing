<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\User;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
\app\assets\HighchartAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$case_project_permission = 0;
if((new User)->checkAccess(4.01)){
	$case_project_permission = 1;
}
 if ((new User)->checkAccess(4.13)) {
	$portal_url = Url::toRoute(['case-overview/total-projects','case_id'=>'']);
 }else if((new User)->checkAccess(4.001)){
	$portal_url = Url::toRoute(['case-custodians/index','case_id'=>'']);
 }else if((new User)->checkAccess(4.006)){
	$portal_url = Url::toRoute(['case-production/index','case_id'=>'']);
 }else if((new User)->checkAccess(4.01)){
	$portal_url = Url::toRoute(['case-projects/index','case_id'=>'']);
 }else if((new User)->checkAccess(4.09)){
	$portal_url = Url::toRoute(['case-budget/index','case_id'=>'']);
 }else if((new User)->checkAccess(4.10)){
	$portal_url = Url::toRoute(['case-documents/index','node_id'=>0,'case_id'=>'']);
 }
$first_client_id = 0;
foreach ($dataProvider->getModels() as $client) {
    $first_client_id = $client->id;
    break;
}
$this->title = 'My Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>label {display:none;}</style>
<div id='select_cases'></div>
<div class="row">
        <div class="col-md-12">
          <div id="page-title" role="heading" class="page-header default-header"><em title="Select Case" class="fa fa-briefcase"></em> <span>Select Case</span></div>
        </div>
      </div>
      <div class="row two-cols-right">
        <div class="col-xs-12 col-sm-8 col-md-9 left-side">
          <div class="right-main-container">
            <div class="select-gridview-head">
              <div class="col-sm-4 last">
			  <?php 
                   //Html::dropDownList(ArrayHelper::map($clientDropdownData, 'id', 'client_name'), ['id'=>'client_id', 'class' => 'form-control', 'prompt' => 'Select Client']);
                   echo Select2::widget([
	                    'model' => $searchModel,
	                    'attribute' => 'client_name',
	                    'data' => ArrayHelper::htmlDecode(ArrayHelper::map($clientDropdownData, 'id', 'client_name')),
	                    'options' => ['prompt' => 'Select Client', 'title' => 'Select Client', 'id' => 'client_id', 'class' => 'form-control'],
	                    /*'pluginOptions' => [
	                      'allowClear' => true
	                    ]*/
                    ]);
              ?>
              </div>
              <div class="col-sm-4 last">
				  <?php  
				 	echo DepDrop::widget([
				 		'type' => 2,
				 		'model' => $searchModel,
						'name' => 'case_name',
				 		'options' => ['title' => 'Select Case','id' => 'client_case_id', 'class' => 'form-control'],
				 		'pluginOptions' => [
						 // 'allowClear' => true,
						  'depends'=>['client_id'],
						  'placeholder' => 'Select Case',
						  'url' => Url::toRoute(['mycase/getcasesbyclient'])
				 		]
                    ]); 
                 ?>
              </div>
              <div class="col-sm-2 last">
               <?= Html::button('Enter Portal',['title'=>"Enter Case Portal",'class' => 'btn btn-primary btn-block', 'onclick'=>'showselectedcase("'.$portal_url.'");'])?>   
              </div>
              
			  <div class="col-sm-2">
                <?= ((new User)->checkAccess(4.02))?Html::button('New Project',['title'=>"Add New Project",'class' => 'btn btn-primary btn-block', 'onclick'=>'newProjects();']):"";?>     
              </div>
            </div>
            <fieldset class="one-cols-fieldset fieldset-top">
            <?= GridView::widget([
                    'id'=>'clientcase-grid',
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                         ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['mycase/getcasedetailsbyclient']),'headerOptions'=>['title'=>'Expand/Collapse All','id'=>'case_expand'],'contentOptions'=>['title'=>'Expand/Collapse Row','headers'=>'case_expand'], 'expandIcon' => '<a href="#" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                         ['attribute' => 'client_name', 'label' => 'Clients', 'format' => 'raw','headerOptions'=>['title'=>'Clients','id'=>'case_title'],'contentOptions'=>['headers'=>'case_title'], 'value' => function($model) use($case_project_permission) { 
							 $model_id=$model['id'];
							 $description=$model['description'];
							 $client_name=$model['client_name'];
							 
							 $ret = Html::a("<em title='View Chart Information' class='fa fa-pie-chart'></em>", null, ["title" => "View Chart Information", "href" => "javascript:void(0);","onclick" => "updateChart($model_id,this,'client','','',$case_project_permission);", 'class' => 'changechart clientnames client_'.$model_id,'id'=>$model_id]); 
							 $ret.=($description!="")?"<a href='javascript:void(0);' class='tag-header-black' title='".$client_name.'-'.$description."'>$client_name - $description</a>":"<a href='javascript:void(0);' class='tag-header-black' title='".$client_name."'>$client_name</a>"; 
							 return $ret;
						 }],
                    ],
                    'export'=>false,
                    'floatHeader'=>true,    
                    'pjax'=>true,
                    'responsive'=>false,
                    'floatHeaderOptions' => ['top' => 'auto'],
                    'pjaxSettings'=>[
						'options'=>['id'=>'teamassigneduser-pajax','enablePushState' => false],
						'neverTimeout'=>true,
						'beforeGrid'=>'',
						'afterGrid'=>'',
                    ],
                ]); ?>
            </fieldset>
            <div class="button-set text-right">
              <div class="col-sm-4 search-item-set"><input type="text" class="form-control" size="30" name="search_term" id="search_term" title="Enter Search Term" placeholder="Enter Search Term"></div>
			  <button class="btn btn-primary" title="Select Cases" onclick="selectCases();">Select Cases</button>
			  <button class="btn btn-primary" title="Search Cases" onclick="searchCases();" >Search</button>
			  <!--<button class="btn btn-primary" title="Clear" onclick="clearall();">Clear</button>-->
                <div id="rcases">
                    <div id="names" style="text-align: right"></div>
                    <input type="hidden"  id="rselectedclient" />
                    <input type="hidden"  id="rselectedcases" />
                    <input type="hidden" name="search_comment_for_selected_case_id" id="search_comment_for_selected_case_id" value="0" />
                    <input type="hidden" id="firstload"  value="0" />
                </div>
            </div>
            
          </div>
          <div id="search_results"></div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3 right-side">
          <div class="projects-main">
            <div class="project-block">
              <h3 class="block-title"><a href="javascript:void(0);" title="Project Status" class="tag-header-black">Project Status</a></h3>
              <div class="block-content">
			    <div id="container-horizontal" style="width:100%; height:100%; margin:0px auto"></div>
			  </div>
            </div>
            <div class="project-block">
              <h3 class="block-title"><a href="javascript:void(0);" title="Project Priority" class="tag-header-black">Project Priority</a></h3>
              <div class="block-content">
			    <div id="container-vertical" style="width:100%; height:100%; margin:0px auto"></div>
			  </div>
            </div>
          </div>
        </div>
      </div>
<script>
$(document).ready(function() {
    updateChart('<?php echo $first_client_id; ?>', '', "client",0,1,'<?php echo $case_project_permission; ?>');
});
var host = window.location.href; //.hostname
var httPpath = "";
if (host.indexOf('index.php')) {
   httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
}
function clearall() {
    $('#search_term').val("");
    $('#search_results').html("");
    $('#rcases').find('#names').html("");
    $('#rselectedcases').val('');
   
    $(this).removeAttr('checked');
   
}
function updatecommentreadstatus(caseId) {
        $.ajax({
            url: httpPath+'mycase/updatecommentstatus',
            data: {'caseId': caseId},
            type: 'Post',
            success: function (data)
            {

            }
        });
}
function searchCases()
{  
    var selected_cases = "";
    selected_cases = $("#rselectedcases").val();

    if (selected_cases == "" || selected_cases==0) {
        alert("Please select 1+ Case to perform this action.");
        return false;
    }

    var term = $('#search_term').val();
    if (term != '')
    {
            $('#search_results').html("");
            $.ajax({
                url: httpPath+'mycase/searchcases',
                data: {'term': term, 'caseId': selected_cases},
                type: 'Post',
                success: function (data)
                {
                    
                    $('#search_results').html(data);
                    
                    $( "#search_results" ).dialog({
                          autoOpen: false,
                          resizable: false,
                          height:456,
                          width: "50em",
                          modal: true,
                          show: {
                        effect: "fade",
                        duration: 500
                          },
                          hide: {
                        effect: "fade",
                        duration: 500
                          },
                                create: function(event, ui) { 
                         $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
                         $('.ui-dialog-titlebar-close').attr("title", "Close");
                         $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
                                },
                        buttons: [
                        {
                            text: "Cancel",
                            "title":"Cancel",
                            "class": 'btn btn-primary',
                            click: function() {
                            $( this ).dialog( "close" );
                            }
                        },
                        /*{
                            text: "Clear",
                            "title": "Clear",
                            "class": "btn btn-primary",
                            click: function() {
                                $(this).dialog("close");
                                clearall();
                               // $.pjax.reload({container:'#clientcase-grid-container'});
                            }
                        }*/
                        ],
                        close: function() {
                        // Close code here (incidentally, same as Cancel code)
                        }
                        }).parent().find('.ui-dialog-title').html("View Search Results");
                        $( "#search_results" ).dialog( "open" );
                }
            });
    }
    else
    {
        $('#search_term').focus();
        alert("Please enter a Search Term to perform this action.");
        return false;
    }
        
}
function showsearchcomment(sel_row,taskId,obj) {
    var host = window.location.href; //.hostname
    var httPpath = "";
    if (host.indexOf('index.php')) {
        httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
    }
    updateChart(sel_row, obj, 'case', taskId);
  
    $('#search_comment_for_selected_case_id').val(sel_row);
    $('#searchcomment_'+sel_row).find('.fa-search').css('color', 'grey');
    if (sel_row != "") {
        term = "comment_search";
        $('#search_results').html("");
        $.ajax({
            url: httpPath+'mycase/searchcases',
            data: {'term': term, 'caseId': sel_row,'ismagnified':1},
            type: 'Post',
            success: function (data)
            {
                $('#searchcomment_'+sel_row).find('.fa-search').css('color', 'red');
                $('#search_results').html(data);
                
                $( "#search_results" ).dialog({
                          autoOpen: false,
                          resizable: false,
                          width: "80em",
                          height:692,
                          modal: true,
                          
                          show: {
                            effect: "fade",
                            duration: 500
                          },
                          hide: {
                            effect: "fade",
                            duration: 500
                          },
                        create: function(event, ui) { 
                            $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
                            $('.ui-dialog-titlebar-close').attr("title", "Close");
                            $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
                            
                        },
                        buttons: [
                        {
                            text: "Cancel",
                            "title":"Cancel",
                            "class": 'btn btn-primary',
                            click: function() {
                            $( this ).dialog( "close" );
                                
                            }
                        },
                        {
                            text: "Clear",
                            "title": "Clear",
                            "class": "btn btn-primary",
                            click: function() {
                                 $(this).dialog("close");
                                clearall();
                                if ($('#search_comment_for_selected_case_id').val() != 0) {
                                    updatecommentreadstatus($('#search_comment_for_selected_case_id').val());
                                }
                                $.pjax.reload({container:'#clientcase-grid-container'});
                            }
                        }
					],
					close: function() {
							// Close code here (incidentally, same as Cancel code)
						}
					}).parent().find('.ui-dialog-title').html("View Unread Comments");
                $( "#search_results" ).dialog( "open" );
                        
                
            }
        });
    } else {
        alert("Please Select Record  to Perform This action.");
        return false;
    }
       
}
</script>
<noscript></noscript>
