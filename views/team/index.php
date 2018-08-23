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
$team_project_permission = 0;
if((new User)->checkAccess(5.01)){
	$team_project_permission = 1;
}
$first_client_id = 0;
$first_client_id = 0;



foreach ($dataProvider->getModels() as $client) {
	$first_client_id = $client->id;
     break;
}
$this->title = 'My Teams';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id='select_cases'></div>
<div class="row">
        <div class="col-md-12">
            <div id="page-title" role="heading" class="page-header"><em class="fa fa-briefcase" title="Select Team"></em> <span><a href="javascript:void(0);" title="Select Team" class="tag-header-red">Select Team</a></span></div>
        </div>
      </div>
      <div class="row two-cols-right">
        <div class="col-xs-12 col-sm-8 col-md-9 left-side">
          <div class="right-main-container">
            <div class="select-gridview-head">
              <div class="col-sm-10 last">
				  <?php 
				  if(!empty($dropdown_data)){
                   echo Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'team_name',
                    'data' => ArrayHelper::map($dropdown_data, 'id', 'team_name'),
                    'options' => ['prompt' => 'Select Team to Enter Portal', 'title' => 'Select Team to Enter Portal', 'id' => 'team_location_dropdown', 'class' => 'form-control','nolabel'=>true,'aria-label'=>'Select Team to Enter Portal'],
                    /*'pluginOptions' => [
                      'allowClear' => true
                    ]*/
                    ]); ?>
              </div>
              	  
              <?php }  ?>
              <div class="col-sm-2">
				   <?= Html::button('Enter Portal',['title'=>"Enter Team Portal",'class' => 'btn btn-primary btn-block','onclick'=>'showmyselectedteamloc();'])?>   
                 
              </div>
            </div>
            <fieldset class="one-cols-fieldset fieldset-bottom">
				<div class="team-select-portal-table">
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
                         ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['team/getteamdetails']),'headerOptions'=>['title'=>'Expand/Collapse All','id'=>'team_expand','aria-label'=>'Team Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row','headers'=>'team_expand'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Row"><span class="glyphicon glyphicon-plus"></span><span class="ectext">Expand</span></a>', 'collapseIcon' => '<a href="javascript:void(0);"  aria-label="Collapse Row"><span class="glyphicon glyphicon-minus"></span><span class="ectext">Collapse</span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
                         ['attribute' => 'team_name', 'label' => 'Teams', 'format' => 'raw','headerOptions'=>['title'=>'Teams','id'=>'team_title'],'contentOptions'=>['headers'=>'team_title'],'value' => function($model) use($team_project_permission) { $ret = Html::a("<em title='View Chart Information' class='fa fa-pie-chart'></em><span style='display:none;'>Chart Image</span>", null, ["title" => "View Chart Information", "href" => "javascript:void(0);","onclick" => "updateteamChart($model->id,'',this,'team',$team_project_permission);", 'class' => 'changechart', 'data-id'=>'t_'.$model->id])." <a href='javascript:void(0);' class='tag-header-black' title='".$model->team_name."'>$model->team_name</strong>"; return $ret;}],
                    ],
                    'export'=>false,
                    'floatHeader'=>true,    
                    //'pjax'=>true,
                    'responsive'=>false,
                    'floatHeaderOptions' => ['top' => 'auto'],
                    'pjaxSettings'=>[
                            'options'=>['id'=>'teamassigneduser-pajax','enablePushState' => false],
                            'neverTimeout'=>true,
                            'beforeGrid'=>'',
                            'afterGrid'=>'',
                    ],
                ]); ?>
				</div>
            </fieldset>
            <input type="hidden" name="search_comment_for_selected_team_id" id="search_comment_for_selected_team_id" value="0" />
            <input type="hidden" id="firstload"  value="0" />
          </div>
          <div id="search_results"></div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3 right-side">
          <div class="projects-main">
            <div class="project-block">
              <h3 class="block-title"><a href="javascript:void(0);" title="Team Status" class="tag-header-black">Team Status</a></h3>
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
    updateteamChart('<?php echo $first_client_id; ?>', '', '','team','<?php echo $team_project_permission; ?>');
});
/*  Code Portal Dropdown */
function showmyselectedteamloc(){
	var team_loc = $('#team_location_dropdown').val();
	if(team_loc != '' && team_loc != 0){
		var temp = team_loc.split("_");
            team_id = temp[0];
            team_loc = temp[1];
            location.href = baseUrl + "team-overview/taskassignments&team_id="+team_id+"&team_loc="+team_loc;
	}else{
		var error = "Please select Team to perform this action.";
		alert(error);
	}
}


	
/* Code For Search Comment */	
function showsearchSummarycommentteam (sel_row,team_loc,obj){
if (sel_row != "") {
         $.ajax({
            url: baseUrl+'summary-comment/getsummary',
            data: {'team_id': sel_row,'team_loc': team_loc,'ismagnified':1},
            type: 'Post',
            success: function (data)
            {
                $('#searchsummarycomment_'+sel_row+'_'+team_loc).find('.fa-search').css('color', 'red');
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
                                updateSummarycommentreadstatus(sel_row,team_loc);
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
function updateSummarycommentreadstatus(team_id,team_loc){
$.ajax({
            url: httpPath+'summary-comment/updatecommentstatus',
            data: {'team_id': team_id,'team_loc':team_loc},
            type: 'Post',
            success: function (data)
            {

            }
        });
}
function showsearchcommentteam(sel_row,obj) {
	//alert(sel_row);
    var host = window.location.href; //.hostname
    var httPpath = "";
    if (host.indexOf('index.php')) {
        httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
    }
   // updateChart(sel_row, obj, 'case', taskId);
  
    $('#search_comment_for_selected_team_id').val(sel_row);
    $('#searchcomment_'+sel_row).find('.fa-search').css('color', 'grey');
    if (sel_row != "") {
        term = "comment_search";
        $('#search_results').html("");
        $.ajax({
            url: httpPath+'team/showserachmyteam',
            data: {'term': term, 'team_id': sel_row},
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
                            $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
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
                                    updatecommentreadstatus(sel_row);
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

function updatecommentreadstatus(teamId)
{
	$.ajax({
		url: httpPath+'team/update-comment-status',
		data: {'teamId': teamId},
		type: 'POST',
		success: function (data){}
	});
}

function showselectedteamloc() {
        var error = "Please Fix Below Given Error:-\n";
        if ($("#team_location_dropdown").val() == '') {
            error += "-Please Select Team to Enter Portal \n";
        }
        if (error != "" && error != 'Please Fix Below Given Error:-\n') {
            aler
            t(error);
        } else {
            var teamdata = $("#team_location_dropdown").val();
            var result = teamdata.split('_');
            location.href = httpPath + "team-overview/task-assignments&team_id="+result[0]+"&team_loc="+result[1]; 
        }
}


function updateteamChart(team_id,team_loc,obj,type,team_project_permission)
{
	if(type == 'team'){
		var compare = 't_'+team_id;
	}else{
		var compare = 'l_'+team_loc;
	}
	 $(".changechart").children('em').removeClass('text-dark');
	 $(".changechart").each(function (index) {
            if ($(this).data('id') == compare)
            {
				$(this).children('em').addClass('text-dark');
            }
        });
        
        if(type == 'team_loc'){
			
			if (team_loc == "")
            {
                $('#container-horizontal').html('');
                $('#container-vertical').html('');
                return false;
            }
            
			//chart1 : Project Status
            $.ajax({
                url: httpPath + "team/getprojectstatuschartdata",
                type: "post",
                data: {'team_id':team_id,'team_loc': team_loc, 'type': 'team_loc'},
                async: false,
                dataType: 'json',
                success: function (data)
                {
                    $('#container-horizontal').html(data);
		    
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
						if(this.y>0 && team_project_permission != 0){
						var pointIndex = this.x;
						var seriesIndex = this.series.index;
						if (pointIndex == 0) {
						    if (seriesIndex == 0) {
							location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[task_status][]=0&due=past";
						    } else if (seriesIndex == 1) {
							location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[task_status][]=0&due=notpastdue";
						    }
						} else if (pointIndex == 1) {
						    if (seriesIndex == 0) {
							location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[task_status][]=1&due=past";
						    } else if (seriesIndex == 1) {
							location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[task_status][]=1&due=notpastdue";
						    }
						} else if (pointIndex == 2) {
						    if (seriesIndex == 0) {
							location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[task_status][]=3&due=past";
						    } else if (seriesIndex == 1) {
							location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[task_status][]=3&due=notpastdue";
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
                url: httpPath + "team/getprojectprioritychartdata",
                // async: false,
                type: "post",
                data: {'team_id':team_id,'team_loc': team_loc, 'type': 'team_loc'},
                cache: false,
                dataType: 'json',
                success: function (data)
                {
		    //console.log(data);
		    var ccats = [];
		    var cdata = [];
		    $.each(data, function(k, v) {
			    ccats.push(v[1]);
			    cdata.push(v[0]);    
		    });		    
                    $('#container-vertical').html('');
		    $('#container-vertical').highcharts({
			exporting: { enabled: false },
                        credits: {enabled: false},
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
												
						if (this.series.name == 'val' && team_project_permission!=0) {
						var res = ccats[this.x];
						location.href = httpPath + "team-projects/index&team_id=" + team_id + "&team_loc="+team_loc+"&TeamSearch[priority][]="+res+"&active=active";
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
		
	  if(type == 'team'){
		 //chart1 : Project Status
            $.ajax({
                url: httpPath + "team/getprojectstatuschartdata",
                type: "post",
                data: {'team_id':team_id, 'type': 'team'},
                async: false,
                dataType: 'json',
                success: function (data)
                {
                    $('#container-horizontal').html(data);
		    
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
				    },
				    point: {
					events: {
					    click: function (e) {
						if(this.y>0){
						var pointIndex = this.x;
						var seriesIndex = this.series.index;
						if (pointIndex == 0 && team_project_permission!=0) {
						    /* if (seriesIndex == 0) {
								location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status]=0&due=past";
							} else if (seriesIndex == 1) {
								location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status]=0&due=notpastdue";
						    } */
						} else if (pointIndex == 1) {
						   /* if (seriesIndex == 0) {
								location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status]=1&due=past";
						    } else if (seriesIndex == 1) {
								location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status]=1&due=notpastdue";
						    } */
						} else if (pointIndex == 2) {
								/* if (seriesIndex == 0) {
									location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status]=3&due=past";
								} else if (seriesIndex == 1) {
									location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[task_status]=3&due=notpastdue";
								} */
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
						}],
						
				    }]
			});
                }
            });
            
             //chart2 : Project Priority by Case
            $.ajax({
                url: httpPath + "team/getprojectprioritychartdata",
                // async: false,
                type: "post",
                data: {'team_id':team_id, 'type': 'team'},
                cache: false,
                dataType: 'json',
                success: function (data)
                {
					console.log(data);
		    var ccats = [];
		    var cdata = [];
		    $.each(data, function(k, v) {
			    ccats.push(v[1]);
			    cdata.push(v[0]);    
		    });
		    
                    $('#container-vertical').html('');
		    $('#container-vertical').highcharts({
			exporting: { enabled: false },
                        credits: {enabled: false},
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
					cursor: 'arrow',
					/*point: {
						events: {
						    click: function (e) {
								if (this.series.name == 'val' && team_project_permission!=0) {
									var res = ccats[this.x];
									location.href = httpPath + "case-projects/index&case_id=" + caseid + "&TaskSearch[priority]=" + res+"&active=active";
								}
						    }
						}
				    }*/
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

</script>
<noscript></noscript>
