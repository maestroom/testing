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
	       
	       <?php $form = ActiveForm::begin(['id' => 'sla_turntime_service_report','action' => '@web/index.php?r=accuracy-report/turnaroundtimebyservicedata']); ?>
				<div id="imgdiv" style="display:none;"></div>
				<input type="hidden" id="filtervalue" name="filtervalue" value='<?php echo json_encode($post_data); ?>'>
				<input type="hidden" id="dataexport" name="dataexport" value='export'>
	       <?php ActiveForm::end(); ?>
	    </fieldset>
	    <div class="button-set text-right">
			<button onclick="exportcriteria();" title="Data Source" class="btn btn-primary pull-left" id="" type="button" name="yt0">Data Source</button>
				<?php //if(isset($save) && !empty($save)){ ?>
					<div class="col-sm-3"><input type="text" placeholder="Enter Chart Name" id="filter_name" class="form-control"><div class="help-block" id="chart_name"></div></div>
					<button onclick="saveFilter();" title="Save" class="btn btn-primary" id="" type="button" name="">Save</button>
					<button onclick="back();" title="Back" class="btn btn-primary" id="" type="button" name="">Back</button>
				<?php //} ?>
				<button onclick="" title="PDF" class="btn btn-primary" id="exportpdf" type="button" name="yt1">PDF</button>
	  	</div>	
   </div>
</div>
<script type="text/javascript">   
	function saveFilter() {
		if ($("#filter_name").val() == ""){
	        $('#chart_name').html('Please Enter Chart Name');
	        $('#chart_name').parent().addClass('has-error');
	        $("#filter_name").focus();
	    } else {
			$('#chart_name').empty();
	        $('#chart_name').parent().removeClass('has-error');
	        $.ajax({
	            url: httpPath + 'accuracy-report/saveturntimeservice',
	            type: 'post',
	            data: {'YII_CSRF_TOKEN': $('#YII_CSRF_TOKEN').val(), 'filter_name': $("#filter_name").val(), 'filtervalue': $("#filtervalue").val()},
	            success: function () {
	                alert('Chart Save Successfully');
	                $("#filter_name").val(null);
	            }
	        });
	    }
	}

	function back(){
		$("#sla_turntime_service_report").attr("action", httpPath + 'accuracy-report/sla-turntime-service');
		$("#sla_turntime_service_report").submit();
	}

	function exportcriteria()
	{
		var filter_data = $('#filtervalue').val();
		var dataexport = $('#dataexport').val();
		$("#sla_turntime_service_report").submit();
	}
	
	//Export report in pdf format
    $('#exportpdf').click(function () {
        if (typeof chart1 != 'undefined') {
        	generateChartPDF("clsperiodchart","DataProcessClientCaseChartPDF_");
        }
    });
	
	 function createmultiserperiodChart(data, title, ticks, ser) {
	        //$('#periodchart').html('<center>Loading...<br/><img src="' + imagePath + 'ajaxloader.gif" alt="Loading..." /></center>');
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
	                categories: ticks,
	                title: {
	                    text: 'SLA Business Days (minus Stop Clock)',//'# of Projects with Completed Services',
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
	            legend: {
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
	            yAxis: {
	                min: 0,
	                allowDecimals: false,
	                title: {
	                    enabled: false
	                },
	                labels: {
	                    labels: {
	                        format: '{value:.2f}'
	                    }
	                },
	                title: {
	                    text: '# of Projects with Completed Services',
	                },
	                //gridLineWidth: 1
	            },
	            exporting: {enabled: false},
	            credits: {
	                enabled: false
	            },
	            series: []
	        };

	        var team = [];
	        var teamcolor = [];
	        var chkzero = 0;
	        $.each(ser, function (key, value) {
	            team.push(value['label']);
	            teamcolor.push(getRandomColor());
	        });
	        $.each(data, function (dkey, dvalue) {
	            if (dvalue != 0 && dvalue != "") {
	                chkzero = 1;
	            }
	        });
	        if (chkzero == 1) {
	            $.each(data, function (keys, values) {
	                drawChart(values, team[keys], teamcolor[keys], options);
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
	    var drawChart = function (data, name, teamcolor, options) {
	        var newSeriesData = {
	            name: name,
	            data: data,
	            color: teamcolor,
	        };
	        options.series.color = "#3BBEE3";
	        options.series.push(newSeriesData);
	        chart1 = new Highcharts.Chart(options);
	    };

<?php if (isset($serviceverticalchart) && $serviceverticalchart != "[]") { ?>
	  var ser = [];
	  var tickss = [];
<?php foreach ($teamtrendlinechartsri as $key => $labeldata) { ?>
        var employee = {
        	"label": '<?php echo $key ?>',
    	}
    	ser.push(employee);
<?php } ?>
    	
<?php foreach ($serviceverticalchartticks as $key => $labeldata) { ?>
    	var employee = '<?php echo $key ?>';
    	tickss.push(employee);
<?php } ?>
        createmultiserperiodChart(<?php echo $serviceverticalchart; ?>, 'SLA Turn-time by Service', tickss, ser);
<?php } else { ?>
       noreport();
<?php } ?>	
</script>
<noscript></noscript>

