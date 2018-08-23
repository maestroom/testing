<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
\app\assets\HighchartAsset::register($this);
?>
<style>
/*.highcharts-title{
width: 50% !important;
white-space: normal !important;
left: 0 !important;
right: 0 !important;
margin: 0 auto !important;
word-break: break-all;
}*/
</style>
<div class="right-main-container">			
	<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Total Projects">Total Projects</a></div>
	<fieldset class="one-cols-fieldset">
		<div class="administration-form">
			<div class="chart-box">
				<div id="periodchart" class="clsperiodchart chart-container"></div>
			</div>
		</div>
	</fieldset>
	<div class="button-set text-right">
	<?= Html::button('Export', ['title'=>"Export",'class' => 'btn btn-primary','id'=>'exportpdf']) ?>
	</div>
</div>
<script>
// Highchart download pdf file functionality
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

function createPieChart(title) {
    var started = "<?php echo $started; ?>";
    var notstarted = '<?php echo $notstarted ?>';
    var onhold = "<?php echo $onhold; ?>";
    var complate = "<?php echo $complate; ?>";
    var closed = "<?php echo $closed; ?>";
    var cancelled = "<?php echo $cancelled; ?>";
    started = parseFloat(started);
    notstarted = parseFloat(notstarted);
    onhold = parseFloat(onhold);
    complate = parseFloat(complate);
    closed = parseFloat(closed);
    cancelled = parseFloat(cancelled);

    var options = {
        chart: {
            renderTo: 'periodchart',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text:title,
            useHTML: true,
            style: {"fontSize": "15px",'display':'block',
            'width':'50% !important',
'white-space':'normal !important',
'left':'0 !important',
'right':'0 !important',
'margin':'0 auto !important',
'word-break':'break-all',
'display':'none'
            },
        },
        tooltip: {
            pointFormat: '<strong>{point.percentage:.1f}%</strong>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<strong>{point.name}</strong>: {point.percentage:.1f}%',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        exporting: {enabled: false},
        credits: {
            enabled: false
        },
        series: [{
                type: 'pie',
                data: [
                    ["Not Started", notstarted],
                    ['Started', started],
                    ['OnHold', onhold],
                    ['Completed', complate],
                    ['Closed', closed],
                    ['Cancelled', cancelled]
                ]
            }]
    };
    chart1 = new Highcharts.Chart(options);

    //var newh = $(".chart-box").height();
    $( window ).resize(function() {
    	//console.log($(".chart-box").height());
		//newh = $(".chart-box").height();
		chart1.redraw();
		chart1.reflow();
	});
}

//Export report in pdf format
$('#exportpdf').click(function () {
    if (typeof chart1 != 'undefined') {
        //Highcharts.exportCharts([chart1]);
        //chart1.options.title.style.display = 'block';
        var casename = "<?php echo $caseInfo->case_name; ?>";
        var imageData = $('#periodchart').highcharts().createCanvas();
        $("body").append('<form id="excel_export_chart">');
		var form = $('#excel_export_chart');
        $(form).attr("method", "POST");
        $(form).attr("action", baseUrl+"export-excel/total-projects");   
        $(form).append('<input type="hidden" name="chart_report" value="chart_report" />');
        $(form).append('<input type="hidden" name="image_data" value="'+imageData+'" />');
        $(form).append('<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />');
        $(form).append('<input type="hidden" name="casename" value="'+casename+'" />');
        hideLoader();
        form.submit(); 	
        //generateChartPDF("clsperiodchart","TotalProjectsChartPDF_");	

        //chart1.options.title.style.display = 'none';
    }
});


function noreport() {
    $("#periodchart").html("<div class='noreport'>No projects found...</div>");
}


var casename = "<?php echo $caseInfo->case_name; ?>";

<?php if (!empty($totalcount) && $totalcount != 0) { ?>
    createPieChart('Total Projects for ' + casename);
<?php } else { ?>
    noreport();
<?php } ?>
</script>
<noscript></noscript>
