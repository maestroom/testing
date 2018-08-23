<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

\app\assets\HighchartAsset::register($this);
$this->title = 'Data processed Report';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="right-main-container" id="media_container">
	<div class="">
	    <fieldset class="two-cols-fieldset workflow-management">
	       <center>
				<div id="chatContainer">
					<div id="periodchart" class="clsperiodchart" style="width:auto;height:auto;"></div>
				</div>
	       </center>
	       <?php $form = ActiveForm::begin(['id' => 'data_processservice_report','action' => '@web/index.php?r=processing-report/data-service']); ?>
				<div id="imgdiv" style="display:none;"></div>
				<input type="hidden" id="filtervalue" name="filtervalue" value='<?php echo json_encode($post_data); ?>'>
				<input type="hidden" id="dataexport" name="dataexport" value='<?php echo json_encode($export_data); ?>'>
	       <?php ActiveForm::end(); ?>
	    </fieldset>
	    <div class="button-set text-right">
			<button onclick="exportcriteria();" title="Data Source" class="btn btn-primary pull-left" id="" type="button" name="yt0">Data Source</button>
			<?php if(isset($save) && !empty($save)){ ?>
				<div class="col-sm-3"><input type="text" placeholder="Enter Chart Name" id="filter_name" class="form-control"><div class="help-block" id="chart_name"></div></div>
				<button onclick="saveFilter();" title="Save" class="btn btn-primary" id="" type="button" name="">Save</button>
				<button onclick="back();" title="Back" class="btn btn-primary" id="" type="button" name="">Back</button>
			<?php } ?>
			<button onclick="" title="PDF" class="btn btn-primary" id="exportpdf" type="button" name="yt1">PDF</button>
	  	</div>	
   </div>
</div>
<script type="text/javascript">    
	function exportcriteria(){
			$("#data_processservice_report").attr("action", baseUrl + 'processing-report/export-dataservice');
			console.log($("#data_processservice_report").attr("action"));
			$("#data_processservice_report").submit();
	}
	
	function saveFilter() {
	    if ($("#filter_name").val() == ""){
	        $('#chart_name').html('Please Enter Chart Name:');
	        $('#chart_name').parent().addClass('has-error');
	        $("#filter_name").focus();
	    } else {
			$('#chart_name').empty();
	        $('#chart_name').parent().removeClass('has-error');
	        $.ajax({
	            url: httpPath + 'processing-report/savedataprocessfilter',
	            type: 'post',
	            data: {'YII_CSRF_TOKEN': $('#YII_CSRF_TOKEN').val(), 'filter_name': $("#filter_name").val(), 'filtervalue': $("#filtervalue").val()},
	            success: function () {
	                alert('Chart Save Successfully');
	                $("#filter_name").val(null);
	            }
	        });
	    }
	}
	
    //Export report in pdf format
    $('#exportpdf').click(function () {
        if (typeof chart1 != 'undefined') {
            //Highcharts.exportCharts([chart1]);
        	generateChartPDF("clsperiodchart","DataProcessClientCaseChartPDF_");
        }
    });

    function back(){
		$("#data_processservice_report").attr("action", httpPath + 'processing-report/data-service');
		$("#data_processservice_report").submit();
	}
	
    function createclientcasechart(category, series, title) {
        $('#periodchart').html('<center>Loading...<br/></center>');
        	var chkzero = 0;
        	var options = {
        		chart: {
	                renderTo: 'periodchart',
	                type: 'column',
	            },
	            title: {
	                text: title,
	                x: 10
	            },
	            xAxis: {
	                labels: {
	                        rotation: -45,
	                        style: {
	                            //fontSize: '13px',
	                            fontFamily: 'Verdana, sans-serif'
	                        }
	                    },
	                categories: category['categories'],
	                title: {
	                    text: null
	                },
	                gridLineWidth: 1,
	            },
	            tooltip: {
	                formatter: function () {
	                    //if(this.series.name == 'val'){
	                    return '<strong>' + this.series.name + '</strong> : ' + this.y;
	                    //}
	                }
	            },
	            plotOptions: {
	                series: {
	                    //colorByPoint: true,
	                    //pointWidth: 11
	                }
	            },
	            legend: {
	                labelFormatter: function() {
		            	var words = this.name.split(/[\s]+/);
	                    var numWordsPerLine = 3;
	                    var str = [];
	                    for (var word in words) {
	                        if (word > 0 && word % numWordsPerLine == 0)
	                            str.push('<br>');
	                        str.push(words[word]);
	                    }
	                    return str.join(' ');	                    
	                },
	                itemStyle: {
	                    //width: 160
	                }
	            },
	            yAxis: {
	                min: 0,
	                title: {
	                    enabled: false
	                },
	                //gridLineWidth: 1
	            },
	            exporting: {enabled: false},
	            credits: {
	                enabled: false
	            },
	            series: series['series']//[{name:'Teams',data:[20]}]
           }
           chart1 = new Highcharts.Chart(options);	
    }	
<?php if (isset($clientchartSeries) && $clientchartSeries != "" && $clientchartSeries != "[]") { // && $clientchartSeries != "" ?>
        createclientcasechart(<?php echo $clientchartCategories ?>,<?php echo $clientchartSeries ?>,'<?php echo 'Data Processed By '. $teamserviceval . ' (GB) - '.$element_name; ?>');
<?php } else { ?>
        noreport();
<?php } ?>	
</script>
<noscript></noscript>

