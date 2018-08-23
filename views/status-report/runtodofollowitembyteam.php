<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
\app\assets\HighchartAsset::register($this);
$this->title = 'StatusReport';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="right-main-container" id="media_container">
	<div class="">
		<fieldset class="two-cols-fieldset workflow-management">
		   <center>
				<div id="chatContainer">
					<div id="periodchart" class="clsperiodchart" style="width:100%;height:auto;"></div>
				</div>
		   </center>
					<?php $form = ActiveForm::begin([
						'id' => 'frm_pdf',
						'action' => '@web/index.php?r=status-report/todofollowitembyteam']); ?>
					<div id="imgdiv" style="display:none;"></div>
					<input type="hidden" id="filtervalue" name="filtervalue" value=<?php echo json_encode($post_data); ?>>
					<input type="hidden" id="dataexport" name="dataexport" value="">
		   <?php ActiveForm::end(); ?>
		</fieldset>
		<div class="button-set text-right">
			<button onclick="exportcriteria();" title="Data Source" class="btn btn-primary pull-left" id="" type="button" name="yt0">Data Source</button>
			<?php if(isset($save) && !empty($save)){ ?>
			<div class="col-sm-3">
				<input type="text" placeholder="Enter Chart Name" id="filter_name" class="form-control">
				<div class="col-md-9">
					<div class="help-block" id="chart_name"></div>
				</div>
			</div>	
			<button onclick="saveFilter();" title="Save" class="btn btn-primary" id="" type="button" name="">Save</button>
			<button onclick="back();" title="Back" class="btn btn-primary" id="" type="button" name="">Back</button>
			<?php } ?>
			<button onclick="" title="PDF" class="btn btn-primary" id="exportpdf" type="button" name="yt1">PDF</button>
				
		</div>	
	</div>	
 </div>
<!-- Right Panel Ends Here -->
<script language="javascript">
    	function saveFilter() {
			if ($("#filter_name").val() == ""){
				$('#chart_name').html('Please Enter Chart Name:');
				$('#chart_name').parent().addClass('has-error');
				$("#filter_name").focus();
			} else {
				$('#chart_name').empty();
				$('#chart_name').parent().removeClass('has-error');
				$.ajax({
					url: httpPath + 'status-report/savebillingfilter',
					type: 'post',
					data: {'YII_CSRF_TOKEN': $('#YII_CSRF_TOKEN').val(), 'filter_name': $("#filter_name").val(), 'filtervalue': $("#filtervalue").val()},
					success: function () {
						alert('Chart Save Successfully');
						$("#filter_name").val(null);
					}
				});
			}
	}
   function exportcriteria() {
        $("#dataexport").val("export");
        $("#frm_pdf").attr("action", baseUrl + 'export-excel/todoserviceexcel');
        $("#frm_pdf").submit();
       // $("#dataexport").val("");
    }
    function back() {
        $("#frm_pdf").attr("action", baseUrl + 'status-report/todofollowitembyteam');
        $("#frm_pdf").submit();
    }
 
    
    // Highchart download pdf file functionality
    Highcharts.getSVG = function (charts) {
        var svgArr = [],
                top = 0,
                width = 0;

        $.each(charts, function (i, chart) {
            var svg = chart.getSVG();
            svg     = svg.replace('<svg', '<g transform="translate(0,' + top + ')" ');
            svg     = svg.replace('</svg>', '</g>');

            top     += chart.chartHeight;
            width   = Math.max(width, chart.chartWidth);

            svgArr.push(svg);
        });
        return '<svg height="' + top + '" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + svgArr.join('') + '</svg>';
    };

    /**
     * Create a global exportCharts method that takes an array of charts as an argument,
     * and exporting options as the second argument
     */
    Highcharts.exportCharts = function (charts, options) {
        var form
        svg = Highcharts.getSVG(charts);
        // merge the options
        options = Highcharts.merge(Highcharts.getOptions().exporting, options);
        // create the form
        form = Highcharts.createElement('form', {
            method: 'post',
            action: options.url
        }, {
            display: 'none'
        }, document.body);
        var curDate = '<?php echo date('m_d_Y__h:i_A'); ?>'
        // add the values
        Highcharts.each(['filename', 'type', 'width', 'svg'], function (name) {
            Highcharts.createElement('input', {
                type: 'hidden',
                name: name,
                value: {
                    filename: 'ToDoFollow-upItemsbyServiceChartPDF_' + curDate,
                    type: 'application/pdf',
                    width: '1000px',
                    height: '2080px',
                    svg: svg
                }[name]
            }, null, form);
        });
        
        form.submit();
        form.parentNode.removeChild(form);
    };


    function createBarChart(data, title, ticks){
        
        $('#periodchart').html('<center>Loading...<br/></center>');
        var options = {
            chart: {
                renderTo: 'periodchart',
                type: 'bar',
            },
            title: {
                text: title,
              	align: 'center',
            },
            xAxis: {
                
                categories: ticks,
                title: {
                    text: null
                },
                gridLineWidth: 1,
            },
            tooltip: {
                formatter: function () {
                    //if(this.series.name == 'val'){
                    return '<strong>' + this.x + '</strong> : ' + this.y;
                    //}
                }
            },
            plotOptions: {
                bar: {
                    //pointPadding: 0.2,
                    //cursor: 'pointer',
                   /*  dataLabels: {
                        //y: -15,
                        enabled: true,
                        formatter: function () {
                            return this.y;
                        },
                    }, */
                },
                series: {
                    ///colorByPoint: true,
                    //pointWidth: 11
                }
            },
            legend: {
            	enabled: true,
               // layout: 'vertical',
                //align: 'right',
                //verticalAlign: 'top',
                //x: -40,
                //y: 100,
                //floating: true,
                //borderWidth: 1,
               // backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                //shadow: true
            },
            yAxis: {
                min: 0,
                allowDecimals: false,
                title: {
                    enabled: false
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    }
                },
               //gridLineWidth: 1
            },
            exporting: {enabled: false},
            credits: {
                enabled: false
            },
            series: []
        };

        var chkzero     = 0;
        $.each(data, function (dkey, dvalue) {
            if (dvalue != 0 && dvalue != "") {
                chkzero = 1;
            }
        });
        if (chkzero == 1) {
            $.each(data, function (keys, value) {
                
                var dataval = value['data'];
                if(dataval == "" || dataval == 0){
                    dataval = 0;
                }
                drawChart(dataval, value['name'],getRandomColor(), options);
            }); 
        } else {
            noreport();
        }
    }

    function getRandomColor() {
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    var drawChart = function (data, name, servicecolor,options) {
        var newSeriesData = {
            name: name,
            data: data,
            color: servicecolor,
        };
        
        options.series.push(newSeriesData);
        chart1 = new Highcharts.Chart(options);

    };
    
    //Export report in pdf format
   $('#exportpdf').click(function () {
        if (typeof chart1 != 'undefined') {
        	generateChartPDF("clsperiodchart","ToDoFollow-upItemsbyServiceChartPDF_");
        }
    });

    function noreport() {
        $("#periodchart").html("<div class='noreport'>There are no results for your selected criteria...</div>");
    }
<?php

if (isset($todobarchart) && $todobarchart != "" && $todobarchart != "[]") {
    ?>
        var tickss = [];
    <?php foreach ($todobarchartticks as $key => $labeldata) {
        ?>
            var employee = '<?php echo $labeldata ?>';
            tickss.push(employee);
    <?php } ?>
        createBarChart(<?php echo $todobarchart; ?>, 'Follow-up Issue Category Breakdown', tickss);
<?php } else { ?>
        noreport();
<?php
}
?>
</script>
<noscript></noscript>
