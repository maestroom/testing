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
						'action' => '@web/index.php?r=status-report/projectbyteamservice']); ?>
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

<script language="javascript">
	/* Function For export Excel File*/
    function exportcriteria() {
        $("#dataexport").val("export");
        $("#frm_pdf").attr("action", baseUrl + 'export-excel/teamserviceexcel');
        $("#frm_pdf").submit();
       // $("#dataexport").val("");
    }
    /* Function For Save Filter Data */
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
   /* Function For Back Process */
    function back() {
        $("#frm_pdf").attr("action", baseUrl + 'status-report/projectbyteamservice');
        $("#frm_pdf").submit();
    }
 
  

    /**
     * Create a global exportCharts method that takes an array of charts as an argument,
     * and exporting options as the second argument
     */


    function verticallinechart(category, series, title) {
        $('#periodchart').html('<center>Loading...<br/></center>');
        var options = {
            chart: {
                renderTo: 'periodchart',
                type: 'line',
            },
            title: {
                text: title,
               	align: 'center',
            },
            xAxis: {
                categories: category['categories'],
                labels: {
                    rotation: -45,
                    style: {
                        //fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
                title: {
                    text: null
                },
                gridLineWidth: 1,
            },
            yAxis: {
                min: 0,
                //tickInterval: 1,
                title: {
                    enabled: false
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    }
                },
                cursor: 'pointer',
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }],
                gridLineWidth: 1,
            },
            plotOptions: {
                series: {
                    cursor: 'pointer'
                }
            },
            tooltip: {
                formatter: function () {
                    return '<strong>' + this.x + '</strong>: ' + this.y;
                }
            },
            exporting: {enabled: false},
            credits: {
                enabled: false
            },
            legend: {
                enabled: true,
                labelFormatter: function () {
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
            series: series['series']
        };
        chart1 = new Highcharts.Chart(options);	
    }

    

    //Export report in pdf format
    $('#exportpdf').click(function () {
        if (typeof chart1 != 'undefined') {
        	generateChartPDF("clsperiodchart","ProjectbyTeamserviceChartPDF_");
        }
    });
	/* Function For display No Reports are available */
    function noreport() {
        $("#periodchart").html("<div class='noreport'>There are no results for your selected criteria...</div>");
    }
<?php 
if (isset($arr_data) && $arr_data != "" && $arr_data != "[]") {  
    ?>
        verticallinechart(<?php echo $arrkeys_cat ?>,<?php echo $arr_data ?>, 'Total Projects by Team Services');
<?php } else { ?>
        noreport();
    <?php
}
?>

</script>
<noscript></noscript>
