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
            <div id="page-title" role="heading" class="page-header default-header"><em title="Select Case" class="fa fa-briefcase"></em> <span><a href="javascript:void(0);" title="Select Case" class="tag-header-red">Select Case</a></span></div>
        </div>
      </div>
      <div class="row two-cols-right">
        <div class="col-xs-12 col-sm-8 col-md-9 left-side">
          <div class="right-main-container">
            <div class="select-gridview-head">
              <div class="col-sm-4 last">
              <label class="sr-only" for="client_id">Select Client</label>
			  <?php 
                   //Html::dropDownList(ArrayHelper::map($clientDropdownData, 'id', 'client_name'), ['id'=>'client_id', 'class' => 'form-control', 'prompt' => 'Select Client']);
                   echo Select2::widget([
	                    'model' => $searchModel,
	                    'attribute' => 'client_name',
	                    'data' => ArrayHelper::htmlDecode(ArrayHelper::map($clientDropdownData, 'id', 'client_name')),
	                    'options' => ['prompt' => 'Select Client', 'title' => 'Select Client', 'id' => 'client_id','nolabel'=>true,'aria-label'=>'Select Client', 'class' => 'form-control'],
	                    /*'pluginOptions' => [
	                      'allowClear' => true
	                    ]*/
	                    
	                   
                    ]);
              ?>
              </div>
              <div class="col-sm-4 last">
              <label class="sr-only" for="client_case_id">Select Case</label>
                <?php  
                    echo DepDrop::widget([
                        'type' => 2,
                        'model' => $searchModel,
                        'name' => 'case_name',
                        'options' => ['title' => 'Select Case','id' => 'client_case_id', 'class' => 'form-control','nolabel'=>true,'aria-label'=>'Select Case',],
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
            <style>
            .kv-expand-icon a span.ectext, 
            .kv-expand-header-icon a span.ectext{
				 display: none;
			}
            </style>
            <?= GridView::widget([
                    'id'=>'clientcase-grid',
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                         ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['mycase/getcasedetailsbyclient']),'headerOptions'=>['title'=>'Case Expand/Collapse All','id'=>'case_expand','aria-label'=>'Case Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row','headers'=>'case_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="screenreader">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Row"><span class="glyphicon glyphicon-minus"></span><span class="screenreader">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false,'value' => function ($model) { return 1;}],
                         ['attribute' => 'client_name', 'label' => 'Clients', 'format' => 'raw','headerOptions'=>['title'=>'Clients','id'=>'case_title'],'contentOptions'=>['headers'=>'case_title'], 'value' => function($model) use($case_project_permission) { 
							 $ret = Html::a("<em title='View Chart Information' class='fa fa-pie-chart'></em><span class='screenreader'>".$model->client_name."</span>", null, ["title" => "View Chart Information", "href" => "javascript:void(0);",'aria-label'=>'Client/Case, ',"onclick" => "updateChart($model->id,this,'client','','',$case_project_permission);", 'class' => 'changechart clientnames client_'.$model->id]); 
							 $ret.=($model->description!="")?"<a href='javascript:void(0);' class='tag-header-black' title='".$model->client_name.'-'.$model->description."'>$model->client_name - $model->description</a>":"<a href='javascript:void(0);' class='tag-header-black' title='".$model->client_name."'>$model->client_name</a>"; 
							 return $ret;
						 }],
                    ],
                    'export'=>false,
                    'floatHeader'=>true,    
                    'pjax'=>false,
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
              <div class="col-sm-4 search-item-set">
              <label class="sr-only" for="search_term">Enter Search Term</label>
              <input type="text" class="form-control" size="30" name="search_term" id="search_term" title="Enter Search Term" placeholder="Enter Search Term"></div>
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
/* Start : Update chart data when user click on chart icon for particular client / case in My Cases landing page */
function updateChart(caseid, obj, type,taskId,firstload,case_project_permission)//caseid may caseid or teamid
{
        ////My Case tab: CHART:1
        //chart1
        
        // Recode By Nelson 25-11-16 starts
        $('.changechart').children('em').removeClass('text-dark');
        
        if(type == 'case'){
            $('.caseid_'+caseid).children('em').addClass('text-dark');
        } else if( type == 'client'){
            $('.client_'+caseid).children('em').addClass('text-dark');
        }            
        // Recode By Nelson 25-11-16 Ends
        if (type == 'case')
        {            
            $('#current_tabs').val('case');
            
            if (caseid == "")
            {
                $('#container-horizontal').html('');
                $('#container-vertical').html('');
                return false;
            }            
            //chart1 : Project Status
            $.ajax({
                url: httpPath + "mycase/getprojectstatuschartdata",
                type: "post",
                data: {'caseId': caseid, 'type': 'case'},
                async: false,
                dataType: 'json',
                success: function (data)
                {
                    $('#container-horizontal').html('');
		    
		    // Create the chart
			$('#container-horizontal').highcharts({
			    exporting: { enabled: false },
                            credits: {enabled: false},                            
			    chart: {
				type: 'column',
				marginTop:23
			    },
			    title: {
				text: ''
			    },
			    subtitle: {
				text: ''
			    },
			    xAxis: {
				type: 'category'
			    },
			    yAxis: {
				title: {
				    text: ''
				}
			    },
			    plotOptions: {
				series: {
					//colorByPoint: true,
					cursor: 'pointer',
				    borderWidth: 0,
						    column: {
					    cursor: 'pointer',
					stacking: 'normal'
					    },
				    dataLabels: {
					enabled: true,
					format: '{point.y:.1f}' //%
				    },
				    point: {
					events: {
					    click: function (e) {
						if(this.y>0 && case_project_permission != 0){
						var pointIndex = this.x;
						var seriesIndex = this.series.index;
						if (pointIndex == 0) {
						    if (seriesIndex == 0) {
							location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status][]=0&due=past";
						    } else if (seriesIndex == 1) {
							location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status][]=0&due=notpastdue";
						    }
						} else if (pointIndex == 1) {
						    if (seriesIndex == 0) {
							location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status][]=1&due=past";
						    } else if (seriesIndex == 1) {
							location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status][]=1&due=notpastdue";
						    }
						} else if (pointIndex == 2) {
								if (seriesIndex == 0) {
								location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status][]=3&due=past";
								} else if (seriesIndex == 1) {
								location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status][]=3&due=notpastdue";
								}
							}
					     }
					    
						}
					}
				    }
				}
			    },
			    legend: {
				align: 'right',
				verticalAlign: 'top',
				x: 10,
				y: -12,
				floating: true,
				borderWidth:0,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#e9e7e8'),
				shadow: false,
					    
			    },
				    
			    tooltip: {
				headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <strong>{point.y:.2f}</strong> of total<br/>'
			    },

			    series: [{
					name: 'Past Due',
					color: '#c52d2e',
					data: [{
						 name: 'Not Started',
						 color: '#c52d2e',
						 y: data["past_due"][0].y,
						}, 
						{
						  name: 'Started',
						  color: '#c52d2e',
						  y: data["past_due"][1].y,
						}, 
						{
						  name: 'On Hold',
						  color: '#c52d2e',
						  y: data["past_due"][3].y,
						}]
				     },
				     {
					name: 'Active',
					color: '#0b2e55',
					data: [{
						 name: 'Not Started',
						 color: '#0b2e55',
						 y: data["active"][0].y,
						}, 
						{
						  name: 'Started',
						  color: '#0b2e55',
						  y: data["active"][1].y,
						}, 
						{
						  name: 'On Hold',
						  color: '#0b2e55',
						  y: data["active"][3].y,
						}]
				    }]
			});
                }
            });
	    
	    
	    //chart2 : Project Priority by Case
            $.ajax({
                url: httpPath + "mycase/getprojectprioritychartdata",
                type: "post",
                data: {'caseId': caseid, 'type': 'case'},
                cache: false,
                dataType: 'json',
                success: function (data)
                {		    
		    var ccats = [];
		    var cdata = [];
		    $.each(data, function(k, v) {
			    ccats.push(v[1]);
			    cdata.push(v[0]);    
		    });
		    
                    $('#container-vertical').html('');
		    $('#container-vertical').highcharts({
			exporting: { enabled: false },
			chart: {
			    type: 'bar'
			},
			title: {
			    text: ''
			},
			xAxis: {
			    
			    categories: ccats,
			    title: {
				text: null
			    },
			    style: {
					font: '9px Arial, sans-serif',
				}
			},
			yAxis: {
			    min: 0,
			    title: {
				text: '',
				align: 'high'
			    },
			    labels: {
				overflow: 'justify'
			    }
			},
			tooltip: {
			    valueSuffix: '',
			    formatter: function () {
				if (this.series.name == 'val') {
				    return '<strong>' + this.x + '</strong>: ' + this.y;
				}
			    }
			},
			plotOptions: {
			    bar: {
				dataLabels: {
				    enabled: true
				}
			    },
					series: {
				colorByPoint: true,
				cursor: 'pointer',
				point: {
					events: {
					    click: function (e) {
												
						if (this.series.name == 'val' && case_project_permission != 0) {
						var res = ccats[this.x];
						location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[priority][]="+res+"&active=active";
						}
					    }
					}
				    }
			    }
			},
			legend: {
			    layout: 'vertical',
			    align: 'right',
			    verticalAlign: 'top',
			    x: -20,
			    y: -10,
			    floating: true,
			    borderWidth: 1,
			    backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
			    shadow: true,
					enabled: false
			},
			credits: {
			    enabled: false
			},
			series: [{
			    name: 'val',
			    data: cdata
			}]
		    });
                }
            });
	    
        }   
        if (type == 'client') {
            
            $('#current_tabs').val('case');
            $('#container-horizontal').html('Project Status');
            $('#container-vertical').html('Project Priority');
            if (caseid == "")
            {
                $('#container-horizontal').html('');
                $('#container-vertical').html('');
                return false;
            }
            
            if(firstload)
           	 $('#firstload').val(caseid);
            else
             $('#firstload').val(0);   
             
	    
	    //chart1 : Project Status by Client
            $.ajax({
                url: httpPath + "mycase/getprojectstatuschartdata",
                type: "post",
                data: {'caseId': caseid, 'type': 'client'},
                async: false,
                dataType: 'json',
                success: function (data)
                {
                    $('#container-horizontal').html('');
		    
		    // Create the chart
			$('#container-horizontal').highcharts({
			    exporting: { enabled: false },
                            credits: {enabled: false},
			    chart: {
				type: 'column',
				marginTop:23,
			    },
			    title: {
				text: ''
			    },
			    subtitle: {
				text: ''
			    },
			    xAxis: {
				type: 'category',
				 labels:{
					style: {
						font: '9px Arial, sans-serif'
					}
				}
			    },
			    yAxis: {
				title: {
				    text: ''
				}
			    },
			    plotOptions: {
				series: {
				    borderWidth: 0,
						    column: {
					    cursor: 'pointer',
					stacking: 'normal'
					    },
				    dataLabels: {
					enabled: true,
					format: '{point.y:.1f}' //%
				    }
				}
			    },
			    legend: {
				align: 'right',
				verticalAlign: 'top',
				x: 10,
				y: -12,
				floating: true,
				borderWidth:0,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#e9e7e8'),
				shadow: false,
					    
			    },
				    
			    tooltip: {
				headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <strong>{point.y:.2f}</strong> of total<br/>'
			    },

			    series: [{
					name: 'Past Due',
					color: '#c52d2e',
					data: [{
						 name: 'Not Started',
						 color: '#c52d2e',
						 y: data["past_due"][0].y,
						}, 
						{
						  name: 'Started',
						  color: '#c52d2e',
						  y: data["past_due"][1].y,
						}, 
						{
						  name: 'On Hold',
						  color: '#c52d2e',
						  y: data["past_due"][3].y,
						}]
				     },
				     {
					name: 'Active',
					color: '#0b2e55',
					data: [{
						 name: 'Not Started',
						 color: '#0b2e55',
						 y: data["active"][0].y,
						}, 
						{
						  name: 'Started',
						  color: '#0b2e55',
						  y: data["active"][1].y,
						}, 
						{
						  name: 'On Hold',
						  color: '#0b2e55',
						  y: data["active"][3].y,
						}]
				    }]
			});
                }
            });
	    
            //chart2 : Project Priority by Client
            $.ajax({
                url: httpPath + "mycase/getprojectprioritychartdata",
                type: "post",
                data: {'caseId': caseid, 'type': 'client',"firstload":firstload},
                cache: false,
                dataType: 'json',
                success: function (data)
                {
		    var ccats = [];
		    var cdata = [];
		    $.each(data, function(k, v) {
			    ccats.push(v[1]);
			    cdata.push(v[0]);    
		    });
                    $('#container-vertical').html('');
                    $('#container-vertical').highcharts({
			exporting: { enabled: false },
			chart: {
			    type: 'bar'
			},
			title: {
			    text: ''
			},
			xAxis: {
			    
			    categories: ccats,
			    title: {
				text: null
			    },
			},
			yAxis: {
			    min: 0,
			    title: {
				text: '',
				align: 'high'
			    },
			    labels: {
				overflow: 'justify',
				
			    }
			},
			tooltip: {
			    valueSuffix: '',
			    formatter: function () {
				if (this.series.name == 'val') {
				    return '<strong>' + this.x + '</strong>: ' + this.y;
				}
			    }
			},
			plotOptions: {
			    bar: {
				dataLabels: {
				    enabled: true
				}
			    },
					series: {
				colorByPoint: true,
				cursor: 'pointer',
				point: {
					events: {
					    click: function (e) {
						
					    }
					}
				    }
			    }
			},
			legend: {
			    layout: 'vertical',
			    align: 'right',
			    verticalAlign: 'top',
			    x: -20,
			    y: -10,
			    floating: true,
			    borderWidth: 1,
			    backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
			    shadow: true,
				enabled: false
			},
			credits: {
			    enabled: false
			},
			series: [{
			    name: 'val',
			    data: cdata
			}]
		    });
                }
            });
      
        }
}
/* End : Update chart data when user click on chart icon for particular client / case in My Cases landing page */
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
function showsearchSummarycomment(sel_row,obj) {
    if (sel_row != "") {
         $.ajax({
            url: baseUrl+'summary-comment/getsummary',
            data: {'case_id': sel_row,'ismagnified':1},
            type: 'Post',
            success: function (data)
            {
                $('#searchsummarycomment_'+sel_row).find('.fa-search').css('color', 'red');
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
                                updateSummarycommentreadstatus(sel_row);
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
function updateSummarycommentreadstatus(caseId){
$.ajax({
            url: httpPath+'summary-comment/updatecommentstatus',
            data: {'case_d': caseId},
            type: 'Post',
            success: function (data)
            {

            }
        });
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