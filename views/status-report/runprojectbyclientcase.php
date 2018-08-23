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
		   <!--<form id="frm_pdf" action="" method="post">-->
					<?php $form = ActiveForm::begin([
						'id' => 'frm_pdf',
						'action' => '@web/index.php?r=status-report/requestbyclientcase']); ?>
					<div id="imgdiv" style="display:none;">
					</div>
					<input type="hidden" id="filtervalue" name="filtervalue" value=<?php echo json_encode($post_data); ?>>
					<input type="hidden" id="dataexport" name="dataexport" value="">
					<?php ActiveForm::end(); ?>
		</fieldset>
		<div class=" button-set text-right">
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
<script type="text/javascript">    
	
	function exportcriteria(){
		$("#dataexport").val("export");
		$("#frm_pdf").attr("action", baseUrl + 'export-excel/clientcaseexcel');
		$("#frm_pdf").submit();
		//$("#dataexport").val("");
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
	
	$('#exportpdf').click(function () {
        if (typeof chart1 != 'undefined') {
            //Highcharts.exportCharts([chart1]);
        	generateChartPDF("clsperiodchart","ProjectsbyClientCaseChartPDF_");
        }
    });
    
    function back(){
		/*var host = window.location.href;// .hostname
		if (host.indexOf('index.php')){
			httpPath = host.substr(0, (host.indexOf('index.php') + 9)) + '?r=';
		}
		console.log('path');
		console.log(httpPath);
		var path = httpPath + 'status-report/requestbyclientcase';
		console.log(path);*/
		$("#frm_pdf").attr("action", httpPath + 'status-report/requestbyclientcase');
		$("#frm_pdf").submit();
	}
	
    function createclientcasechart(category, series, title) {
        $('#periodchart').html('<center>Loading...<br/></center>');
        var chkzero = 0;
        //console.log(series['series']);
        
        	//$('#periodchart').highcharts({
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
<?php
/* By Client */
if (isset($arr_data) && $arr_data != "" && $arr_data != "[]") {
?>
        createclientcasechart(<?php echo $arrkeys_cat ?>,<?php echo $arr_data ?>, 'Total Projects by <?php echo $client_by_case; ?>');
<?php } else { ?>
        noreport();
<?php } ?>	
</script>
<noscript></noscript>

