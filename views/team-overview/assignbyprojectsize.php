<?php
\app\assets\HighchartAsset::register($this);
use yii\helpers\Html;
//echo "<pre>";echo $drill_data;die;
?>
<style>
.custom-remove-color span{
	color:#167fac !important;
	text-decoration:none !important;
	font-weight:normal !important;
}
</style>
<div class="right-main-container">
	<div class="sub-heading"><a href="javascript:void(0);" title="Assignments by Project Size" class="tag-header-black">Assignments by Project Size</a></div>
	<fieldset class="one-cols-fieldset">
		<div class="administration-form">
			<div class="chart-box custom-remove-color">
				<div id="container-vertical" class="clsperiodchart chart-container"></div>
			</div>
		</div>
	</fieldset>
        <div class="button-set text-right calender-group">
            <div class="col-sm-10 pull-right">
                <div class="row">
                	<!--<label for="search_doc" class="form_label">Assignments by Project Size Date: </label>
					<span class="search-item-set" style="position:relative;">
                        <input type="text" title="Search" class="form-control" name="search_doc" id="search_doc" value="<?php echo $search_date; ?>" placeholder="Select Date" />
					</span>-->
					<?php // Html::button('Search',['title'=>"Go",'class' => 'btn btn-primary','onclick'=>'search_task();'])?>
                    <?= Html::button('PDF',['title'=>"PDF Export",'class' => 'btn btn-primary','onclick'=>'export_distribute();'])?>
                </div>
            </div>
        </div>
        <div id="blank"></div>
</div>

<script>
var team='<?php echo $teaminfo->team->team_name.' - '.$teaminfo->teamlocationMaster->team_location_name;?>';
var ccats = [];
var series = [];
var unit_size = [];
var unit_name = [];
var cdata =<?php echo $data; ?>;
var series =<?php echo $series; ?>;

function search_task(){
    var search_date=$('#search_doc').val();
    var team_id= jQuery("#team_id").val();
    var team_loc= jQuery("#team_loc").val();
    location.href=baseUrl+"team-overview/assignbyprojectsize&team_id="+team_id+"&team_loc="+team_loc+"&search_date="+search_date;
}

$(document).ready(function(){
	if(cdata != ''){
        Highcharts.setOptions({
            lang: {
                thousandsSep: ','
            }
        });
		getchart(cdata,series);
    }else{
			$('.one-cols-fieldset').attr('class', 'one-cols-fieldset no-record');
		$('#container-vertical').html('<h5 class="text-center">No Records Found</h5>');
	}
});

/* series */
function getchart(data, series)
{

    var dataUnit = JSON.stringify(<?= $dataUnit ?>);

	$.each(data, function (index, row) {
		//ccats.push(row.user);
		unit_size.push(row.user);
    });

	$('#container-vertical').highcharts({
        exporting: { enabled: false },
        chart: {
            type: 'bar',
            /*marginLeft:'93',*/
            height : unit_size.length * 20 + 240,
        },
        title: {
            text: 'Active Assignments per Team Member ('+team+')',
            style: {"fontSize": "15px"},
        },
        xAxis: {
            categories: unit_size,
            title: {
                text: null
            },
            labels: {
                formatter: function () {
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
                overflow: 'justify'
            },
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
            pointFormat: '<span style="color:{point.color}">{series.name}</span>: <strong>{point.y}</strong><br/>Total: {point.stackTotal}',
            formatter: function() {
                //return '<b>' + this.x + '</b><br/>' + this.series.name + ': $' + Highcharts.numberFormat(this.y, 0);
                return '<span style="color:'+this.point.color+'">'+this.series.name+'</span>: <strong>'+Highcharts.numberFormat(this.y,0)+'</strong><br/>Total: '+Highcharts.numberFormat(this.point.stackTotal,0)
            }
         },
        plotOptions: {
             bar: {
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
                            var varseries = this.series.name;
                            var obj = JSON.parse(dataUnit);
                            var href_task = $('#blank a').attr('href')+'&'+obj[varseries];
                            //console.log(obj);
                            //console.log(varseries);
                            location.href=href_task;
                         }
                    }
                },
                dataLabels: {
                    enabled: true,
                },
                /*stackLabels: {
                    enabled: true,
                    formatter: function() {
                    return Highcharts.numberFormat(this.total,1);
                    }
                },*/
            }
        },
        series: series,
        credits: {
            enabled: false
        },
    });
}


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
