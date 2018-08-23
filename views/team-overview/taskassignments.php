<?php
\app\assets\HighchartAsset::register($this);
use yii\helpers\Html;
//use kartik\grid\datetimepicker;
//echo "<pre>";echo $drill_data;die;
?>

<div class="right-main-container">
	<div class="sub-heading"><a href="javascript:void(0);" title="Task Assignments" class="tag-header-black">Task Assignments - Active</a></div>
	<fieldset class="one-cols-fieldset">
		<div class="administration-form">
			<div class="chart-box custom-remove-color">
				<div id="container-vertical" class="clsperiodchart chart-container"></div>
			</div>
		</div>
	</fieldset>
        <div class="button-set text-right calender-group">
            <div class="col-sm-12 pull-right">
                <div class="row">
                    <div class="">
						<label for="search_doc" class="form_label">ToDo/Task Assign Date: </label>
						<span class="search-item-set" style="position:relative;">
                        	<input type="text" title="Search" class="form-control" name="search_doc" id="search_doc" value="<?php echo $search_date; ?>" placeholder="Select Date" />
						</span>
						<?= Html::button('Search',['title'=>"Go",'class' => 'btn btn-primary','onclick'=>'search_task();'])?>
						<?= Html::button('PDF',['title'=>"PDF Export",'class' => 'btn btn-primary','onclick'=>'export_distribute();'])?>
					</div>
                </div>
            </div>
        </div>
        <div id="blank"></div>
</div>

<!--<div class="right-main-container" id="caseproduction_container">
    <div id="container-vertical"></div>
    <div class="button-set text-right calender-group">
        <div class="col-sm-10 pull-right">
            <div class="row">
                <div class="col-sm-5"><label class="form_label">Tasks or Todo assign date :</label></div>
                <div class="col-sm-3 search_datepicker">
                    <input type="text" class="form-control" name="search_doc" id="search_doc" value="<?php echo $search_date;?>" placeholder="" />
                </div>

                <?= Html::button('Search',['title'=>"Go",'class' => 'btn btn-primary','onclick'=>'search_task();'])?>
                <?= Html::button('Pdf',['title'=>"PDF Export",'class' => 'btn btn-primary','onclick'=>'export_distribute();'])?>

            </div>
        </div>
    </div>
</div>    -->
<script>
//var team='<?php echo $teaminfo->team->team_name.' - '.$teaminfo->teamlocationMaster->team_location_name;?>';
var team_id= jQuery("#team_id").val();
var team_loc= jQuery("#team_loc").val();
var team=$("#team_loc_id option[value='"+team_id+"_"+team_loc+"']").text();
var ccats = [];
var task_pending = [];
var task_working = [];
var task_notstarted = [];
var todo=[];
var cdata =<?php echo preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data); ?>;
function search_task(){
    var search_date=$('#search_doc').val();
    var team_id= jQuery("#team_id").val();
    var team_loc= jQuery("#team_loc").val();
    location.href=baseUrl+"team-overview/taskassignments&team_id="+team_id+"&team_loc="+team_loc+"&search_date="+search_date;
}
function getchart(data)
{
    $.each(data, function (index, row) {
		    ccats.push(row.user);
            task_pending.push(parseInt(row.task_pending));
            todo.push(parseInt(row.todo));
            task_working.push(parseInt(row.task_working));
            task_notstarted.push(parseInt(row.task_notstarted));

    });

     $('#container-vertical').highcharts({
        exporting: { enabled: false },
        chart: {
            type: 'bar',
            //marginLeft:'93',
            height : ccats.length * 20 + 240,
        },
        title: {
            text: 'Active Assignments per Team Member ('+team+')',
            style: {"fontSize": "15px"},
        },
        xAxis: {
            categories: ccats,
            title: {
                text: null
            },
            labels: {
                overflow:'justify',
                step: 1,
                width:'100%',
                formatter: function () {
                    // return this.value;
                    $('#blank').html(this.value);
                    var href_task=$('#blank a').attr('href');
                    href_task=$('#blank a').attr('href')+'&taskActive=1';
                    var text = $("#blank a").text();
                    this.value = '<a href="'+href_task+'">'+text+'</a>';
                    return this.value;
                },
                useHTML: true,
            },
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: '',
                align: 'high'
            },
            labels: {
                overflow: 'justify',
            }
        },
        legend: {
            reversed: true,
            layout: 'horizontal',
            align: 'right',
            verticalAlign: 'top',
            x: -10,
            y: 15,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true,
            enabled: true,

        },
         tooltip: {
            headerFormat: '',
            pointFormat: '<span style="color:{point.color}">{series.name}</span>: <strong>{point.y}</strong><br/>Total: {point.stackTotal}'
        },
        plotOptions: {
             column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            },
            series: {
                stacking: 'normal',
                colorByPoint: false,
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            $('#blank').html(this.category);
                            var href_task=$('#blank a').attr('href');
                            if(this.series.name == 'ToDos') {
								href_task=$('#blank a').attr('href')+'&statusFilter=8&taskActive=1';
                                href_task = href_task.replace('&notin_unit_status=0','');
                            }
							if(this.series.name == 'Task Not Started') {
								href_task=$('#blank a').attr('href')+'&statusFilter=0&taskActive=1';
                                href_task = href_task.replace('&notin_unit_status=0','');

							}
							if(this.series.name == 'Task Pending') {
								href_task=$('#blank a').attr('href')+'&statusFilter=7&taskActive=1';
                                href_task = href_task.replace('&notin_unit_status=0','');
							}
                            if(this.series.name == 'Task Working') {
                               href_task=$('#blank a').attr('href')+'&notin_unit_status=0&taskActive=1';
                            }
                            location.href=href_task;
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                }
            }
        },
        series: [{
            name: 'Task Pending',
            data: task_pending
        },
        {
            name: 'Task Working',
            data: task_working
        },
        {
            name: 'Task Not Started',
            data: task_notstarted
        },
        {
            name: 'ToDos',
            data: todo
        }],
        credits: {
            enabled: false
        },
    });
}


$(document).ready(function(){
	if(cdata != ''){
		getchart(cdata);
    }else{
			$('.one-cols-fieldset').attr('class', 'one-cols-fieldset no-record');
		$('#container-vertical').html('<h5 class="text-center">No Records Found</h5>');
	}
});

function export_distribute()
{
    var doc = new jsPDF();

    // chart height defined here so each chart can be palced
    // in a different position
    var chartHeight = 80;

    // All units are in the set measurement for the document
    // This can be changed to "pt" (points), "mm" (Default), "cm", "in"
    //doc.setFontSize(40);
    //doc.text(35, 25, "My Exported Charts");

    //loop through each chart
    $('#container-vertical').each(function (index) {
        /*var imageData = $(this).highcharts().createCanvas();
        doc.addImage(imageData, 'JPEG', 45, (index * chartHeight) + 40, 120, chartHeight);*/
    	var highchartobj = $(this).highcharts();
    	highchartobj.options.title.style.fontSize = '13px';
    	highchartobj.options.title.align = "left";
        var imageData = highchartobj.createCanvas();
        doc.addImage(imageData, 'JPEG', 45, (index * chartHeight) + 40, 120, chartHeight);
        highchartobj.options.title.style.fontSize = '15px';
    	highchartobj.options.title.align = "center";
    });


    //save with name
    doc.save('Taskassignments.pdf');
}
datePickerController.createDatePicker({
	formElements: { "search_doc": "%m/%d/%Y"},
	rangeLow:"19700313",
	rangeHigh:'<?php echo $curr_date;?>'
});

</script>
<noscript></noscript>
