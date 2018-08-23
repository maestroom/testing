<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
\app\assets\HighchartAsset::register($this);
?>
<div class="right-main-container">			
	<div class="sub-heading"><a href="javascript:void(0);" title="Productions Producing Parties" class="tag-header-black">Productions Producing Parties</a></div>
	<fieldset class="one-cols-fieldset">
		<div class="administration-form">
			<div class="chart-box">
				<div id="periodchart" class="clsperiodchart chart-container"></div>
			</div>
		</div>
	</fieldset>
	<div class="button-set text-right">
	<?php // Html::button('PDF', ['title'=>"PDF Export",'class' => 'btn btn-primary','id'=>'exportpdf']) ?>
    <?= Html::button('Export', ['title'=>"Export",'class' => 'btn btn-primary','id'=>'exportpdf']) ?>
	</div>
</div>
<script language="javascript">
//Highchart download pdf file functionality
Highcharts.getSVG = function (charts) {
    var svgArr = [],
            top = 0,
            width = 0;
    $.each(charts, function (i, chart) {
        var svg = chart.getSVG();
        svg = svg.replace('<svg', '<g transform="translate(0,' + top + ')" ');
        svg = svg.replace('</svg>', '</g>');
        top += chart.chartHeight;
        width = Math.max(width, chart.chartWidth);
        svgArr.push(svg);
    });
    return '<svg height="' + top + '" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + svgArr.join('') + '</svg>';
};
/**
 * Create a global exportCharts method that takes an array of charts as an argument,
 * and exporting options as the second argument
 */

function createHorizontalBarChart(data, title) {
    var options = {
        chart: {
            renderTo: 'periodchart',
            type: 'bar',
        },
        title: {
            text: title,
            style: {"fontSize": "15px",'display':'none'},
            x: 50
        },
        xAxis: {
            title: {
                text: null
            },
            gridLineWidth: 1,
        },
        tooltip: {
            formatter: function () {
                if (this.series.name == 'val') {
                    return '<strong>' + this.x + '</strong> : ' + this.y;
                }
            }
        },
        colors: ["#2975A7", "#EAA228", "#C5B47F", "#A2001F"],
        plotOptions: {
            bar: {
                cursor: 'cursor',
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        return this.y;
                    }
                }
            },
            series: {
                colorByPoint: true,
                // pointWidth: 11
            }
        },
        yAxis: {
        	allowDecimals: false,
            min: 0,
            title: {
                enabled: false
            },
            labels: {
                rotation: -45,
                style: {
                    //fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                },
                formatter: function () {
                    return this.value;
                }
            },
            //gridLineWidth: 1,
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                name: 'val',
                data: [],
            }]
    };

    var chartData = [];
    var category = [];
    var chkzero = 0;
    $.each(data, function (keys, values) {
        if (values != 0 && values != "") {
            chkzero = 1;
        }
    });

    $.each(data, function (key, value) {
        chartData.push([parseFloat(value)]);
        category.push([key]);

    });
    if (chkzero == 1) {
        options.series[0].showInLegend = false;
        options.series[0].data = chartData;
        options.xAxis.categories = category;
        chart1 = new Highcharts.Chart(options);

        var newh = $(".chart-box").height();
        $( window ).resize(function() {
    		newh = $(".chart-box").height();
    		chart1.redraw();
    		chart1.reflow();
    	});
    } else {
        noreport();
    }
}


//Export report in pdf format
$('#exportpdf').click(function () {
    if (typeof chart1 != 'undefined') {
         var casename = "<?php echo $caseInfo->case_name; ?>";
            var imageData = $('#periodchart').highcharts().createCanvas();
            $("body").append('<form id="excel_export_chart">');
            var form = $('#excel_export_chart');
            $(form).attr("method", "POST");
            $(form).attr("action", baseUrl+"export-excel/production-producing-parties");   
            $(form).append('<input type="hidden" name="chart_report" value="productionproducingparties" />');
            $(form).append('<input type="hidden" name="image_data" value="'+imageData+'" />');
            $(form).append('<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />');
            $(form).append('<input type="hidden" name="casename" value="'+casename+'" />');
            hideLoader();
            form.submit(); 	
        //Highcharts.exportCharts([chart1]);
        //chart1.options.title.style.display = 'block';
       // generateChartPDF("clsperiodchart", "ProductionsProducingPartiesChartPDF_");
       // chart1.options.title.style.display = 'none';
    }
});

function noreport() {
    $("#periodchart").html("<div class='noreport'>No production producing parties found...</div>");
}

<?php if (isset($productionjson) && $productionjson != "" && $productionjson != "[]") { ?>
    var casename = "<?php echo $caseInfo->case_name; ?>";
    createHorizontalBarChart(<?php echo $productionjson; ?>, casename + " Productions Producing Parties");
<?php } else { ?>
    noreport();
<?php } ?>
</script>
<noscript></noscript>
