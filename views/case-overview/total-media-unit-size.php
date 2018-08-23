<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
\app\assets\HighchartAsset::register($this);
?>
<div class="right-main-container">			
    <div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Media Type By Size">Media Type By Size</a></div>
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
function createVerticalBarChart(data, mediasizeobj, title) {

    var mediaTypes = [];
    var drilldownSeries = [];
    $.each(data, function (name, y) {

        mediaTypes.push({
            name: name,
            y: parseInt(y),
            drilldown: name
        });
    });
    // Using Highchart library   
    var options = {
        chart: {
            renderTo: 'periodchart',
            type: 'column',
        },
        title: {
            text: title,
            style: {"fontSize": "15px",'display':'none'},
        },
        xAxis: {
            labels: {
                rotation: -45,
                style: {
                    //fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            },
            type: 'category'
        },
        yAxis: {
        	allowDecimals: false,
            title: {
                enabled: false
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}"><strong>{point.name}</span>: {point.y}</strong>'
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                name: title,
                colorByPoint: true,
                data: mediaTypes
            }],
        drilldown: {
            series: mediasizeobj
        }
    };
    chart1 = new Highcharts.Chart(options);

    var newh = $(".chart-box").height();
    $( window ).resize(function() {
		newh = $(".chart-box").height();
		chart1.redraw();
		chart1.reflow();
	});
}

//Export report in pdf format
$('#exportpdf').click(function () {
	//chart1.options.title.style.display = 'block';
    //generateChartPDF("clsperiodchart", "MediaTypebySizeChartPDF_");
    //chart1.options.title.style.display = 'none';
    if (typeof chart1 != 'undefined') {
             var casename = "<?php echo $caseInfo->case_name; ?>";
            var imageData = $('#periodchart').highcharts().createCanvas();
            $("body").append('<form id="excel_export_chart">');
            var form = $('#excel_export_chart');
            $(form).attr("method", "POST");
            $(form).attr("action", baseUrl+"export-excel/total-media-unit-size");   
            $(form).append('<input type="hidden" name="chart_report" value="totalmediaunitsize" />');
            $(form).append('<input type="hidden" name="image_data" value="'+imageData+'" />');
            $(form).append('<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />');
            $(form).append('<input type="hidden" name="casename" value="'+casename+'" />');
            hideLoader();
            form.submit(); 	
            //Highcharts.exportCharts([chart1]);
        /*    chart1.options.title.style.display = 'block';
        	generateChartPDF("clsperiodchart","TotalMediaChartPDF_");
        	chart1.options.title.style.display = 'none';
        */   
        }
});

function noreport() {
    $("#periodchart").html("<div class='noreport'>No Media Found...</div>");
}

<?php if (isset($evidtypejson) && $evidtypejson != "") { ?>
    var casename = "<?php echo $caseInfo->case_name; ?>";
    createVerticalBarChart(<?php echo $evidtypejson; ?>,<?php echo $mdeiaunitsizejson; ?>, 'Media Type by Size for ' + casename);
<?php } else { ?>
    noreport();
<?php } ?>
</script>
<noscript></noscript>
