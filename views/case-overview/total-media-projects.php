<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
\app\assets\HighchartAsset::register($this);
?>
<div class="right-main-container">			
	<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Total Media">Total Media</a></div>
	<fieldset class="one-cols-fieldset">
		<div class="administration-form">
			<div class="chart-box">
				<div id="periodchart" class="clsperiodchart chart-container"></div>
			</div>
		</div>
	</fieldset>
	<div class="button-set text-right">
	<?php /* Html::button('PDF', ['title'=>"PDF Export",'class' => 'btn btn-primary','id'=>'exportpdf']) */?>
    <?= Html::button('Export', ['title'=>"Export",'class' => 'btn btn-primary','id'=>'exportpdf']) ?>
	</div>
</div>
<script language="javascript">
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
    
    function createPieChart(title){
        var checkedIn = "<?php echo $checkedIn; ?>";
        var checkedOut = '<?php echo $checkedOut ?>';
        var destroyed = "<?php echo $destroyed; ?>";
        var moved = "<?php echo $moved; ?>";
        var returned = "<?php echo $returned; ?>";
        
        checkedIn = parseFloat(checkedIn);
        checkedOut = parseFloat(checkedOut);
        destroyed = parseFloat(destroyed);
        moved = parseFloat(moved);
        returned = parseFloat(returned);
        
    var options = {    
            chart: {
                renderTo: 'periodchart',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: title,
                style: {"fontSize": "15px",'display':'none'},
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
                    ["Checked In",   checkedIn],
                    ['Checked Out',       checkedOut],
                    ['Destroyed',    destroyed],
                    ['Moved',   moved],
                    ['Returned',   returned]
                ]
            }]
        };
        
        chart1 = new Highcharts.Chart(options);

        var newh = $(".chart-box").height();
        $( window ).resize(function() {
            console.log($(".chart-box").height());
    		newh = $(".chart-box").height();
    		chart1.redraw();
    		chart1.reflow();
    	});
    }
    

    function noreport() {
        $("#periodchart").html("<div class='noreport'>No media found..</div>");
    }

    //Export report in pdf format
    $('#exportpdf').click(function () {
        if (typeof chart1 != 'undefined') {
             var casename = "<?php echo $caseInfo->case_name; ?>";
            var imageData = $('#periodchart').highcharts().createCanvas();
            $("body").append('<form id="excel_export_chart">');
            var form = $('#excel_export_chart');
            $(form).attr("method", "POST");
            $(form).attr("action", baseUrl+"export-excel/total-projects");   
            $(form).append('<input type="hidden" name="chart_report" value="totalmedia" />');
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
    
var casename = "<?php echo $caseInfo->case_name; ?>";

<?php if (!empty($totalcount) && $totalcount != 0) { ?>
	createPieChart('Total Media for ' + casename);
<?php } else { ?>
	noreport();
<?php } ?>
</script>
<noscript></noscript>
