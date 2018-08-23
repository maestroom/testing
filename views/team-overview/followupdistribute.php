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
	<div class="sub-heading"><a href="javascript:void(0);" title="Follow-up Distribution" class="tag-header-black">Follow-up Distribution</a></div>
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
                    <?= Html::button('PDF',['title'=>"PDF Export",'class' => 'btn btn-primary','onclick'=>'export_distribute();'])?>
                </div>
            </div>
        </div>
</div>
<script>
var team='<?php echo $teaminfo->team->team_name.' - '.$teaminfo->teamlocationMaster->team_location_name;?>';
var ccats = [];
var drilldata =<?php echo  preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $drill_data);?>;
var cdata =<?php echo  preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data);?>;
$(document).ready(function(){
if(cdata != ''){
    $.each(cdata, function (index, row) {
		//ccats.push(row.user);
		ccats.push(row.name);
	});
$('#container-vertical').highcharts({
        exporting: { enabled: false },
        chart: {
            type: 'bar',
            /*marginLeft:'93',*/
            height : (ccats.length * 20) + 240,
        },
        title: {
            text: 'Active Follow-up Distribution by Team Member ('+team+')',
            style: {"fontSize": "15px"},
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            //categories: ccats,
            type:'category',
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
            }
        },
        tooltip: {
            headerFormat: '',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <strong>{point.y}</strong><br/>'
        },
        /*tooltip: {
            valueSuffix: '',
            formatter: function () {
                //if (this.series.name == 'val') {
                    return '<strong>' + this.name + '</: ' + this.y;
                //}
            }
        },*/
         plotOptions: {
            series: {
                borderWidth: 0,
                colorByPoint: false,
                dataLabels: {
                    enabled: true,
                    //format: '{point.y}'
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -10,
            y: 15,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true,
            enabled: true,
            labelFormatter: function() {
				/*var legendName = this.name;
				var match = legendName.match(/.{1,25}/g);
				return match.toString().replace(/\,/g,"<br/>");*/
				var words = this.name.split(/[\s]+/);
				var numWordsPerLine = 4;
				var str = [];

				for (var word in words) {
					if (word > 0 && word % numWordsPerLine == 0)
						str.push('<br>');

					str.push(words[word]);
				}

				return str.join(' ');
            }

        },
        credits: {
            enabled: false
        },
         series: [{
                  name: 'ToDos',
				  data: cdata,
				  color: '#434348'
        }],
        drilldown: {
            series:drilldata
        },
    });
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
    	var highchartobj = $(this).highcharts();
    	highchartobj.options.title.style.fontSize = '13px';
    	highchartobj.options.title.align = "left";
        var imageData = highchartobj.createCanvas();
        doc.addImage(imageData, 'JPEG', 45, (index * chartHeight) + 40, 120, chartHeight);
        highchartobj.options.title.style.fontSize = '15px';
    	highchartobj.options.title.align = "center";
    });


    //save with name
    doc.save('Follow up Distribution.pdf');
}
</script>
<noscript></noscript>
