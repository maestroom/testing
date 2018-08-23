<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\grid\GridView;

\app\assets\HighchartAsset::register($this);
?>

<div class="right-main-container">
	<fieldset class="two-cols-fieldset project-comments est-reports">
		<div class="sub-heading">Estimated Report</div>
		<div class="comments-area" style="overflow:auto;">
			<div class="col-sm-12">
				<div class="col-sm-7 chart-box">
		 			<div id="periodchart" class="chart-container"></div>
				</div>
				<div class="col-sm-5">
		 			<div class="row">
						<div class="form-group">
							<div class="col-md-12" style="margin:0 auto;">
								<div class="table-responsive">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" id="casespend_table">
					   					<tbody>
					   						<tr>
						   						<td width="50%"><strong>Project #</strong></td>
						   						<td><?= $taskmodel->id; ?></td>
					   						</tr>
					   						<tr>
								 				<td width="50%"><strong>Project Name</strong></td>
								 				<td><?= $taskinstruct->project_name; ?></td>
								 			</div>
								 			<tr>
								 				<td width="50%"><strong>Submitted Date</strong></td>
								 				<td><?= $taskinstruct->project_name; ?></td>
								 			</div>
								 			<tr>
								 				<td width="50%"><strong>% Complete</strong></td>
								 				<td><?= $taskinstruct->project_name; ?></td>
								 			</div>
								 			<tr>
								 				<td width="50%"><strong>Project Due Date</strong></td>
								 				<td><?= $taskinstruct->project_name; ?></td>
								 			</div>
								 			<tr>
								 				<td width="50%"><strong>Project Completed Date</strong></td>
								 				<td><?= $taskinstruct->project_name; ?></td>
								 			</div>
								 			<tr>
								 				<td width="50%"><strong>Project Past Due</strong></td>
								 				<td><?= $taskinstruct->project_name; ?></td>
								 			</div>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>
			<div class="col-sm-12">
				<div class="row case-budgets-spend-table">
					<div class="form-group">
						<div class="col-md-12" style="margin:0 auto;">
							<div class="table-responsive">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" id="casespend_table">
									<thead>
										<tr>
								   			<th title="Status" class="text-center"><strong>Status</strong></th>
								   			<th title="Task Name"><strong>Task Name</strong></th>
								   			<th title="Task Started"><strong>Task Started</strong></th>
								   			<th title="Task Completed"><strong>Task Completed</strong></th>
								   			<th title="Est"><strong>Est</strong></th>
								   			<th title="Actual"><strong>Actual</strong></th>
				   						</tr>
									</thead>
				   					<tbody>
				   						
				   					</tbody>
				   				</table>
							</div>
						</div>
					</div>
				</div>
			</div>			
		</div>
		<div class="button-set text-right">
			<button class="btn btn-primary" title="Back" type="button" onclick="estreport_back(<?=$case_id;?>,'<?=$qryString;?>');">Back</button>
			<button class="btn btn-primary" title="Refresh" type="button" onclick="javascript:location.reload();">Refresh</button>
		   	<button class="btn btn-primary" title="PDF" type="button" onclick="estreport_pdf(<?=$case_id;?>);">PDF</button>
		</div>
	</fieldset>
</div>
<script>
function createVerticalBarChart(category, series, title) {
	$('#periodchart').html('<center>Loading...<br/></center>');
	var options = {
    	chart: {
			renderTo: 'periodchart',
			type: 'column',
		},
		title: {
			text: title,
		},
		xAxis: {
			type: 'category',
            labels: {
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
		},
		tooltip: {
			formatter: function () {
				return '<strong>' + this.series.name + '</strong> : ' + this.y;
			}
		},
		plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.2f}'
                }
            }
        },
		yAxis: {
            min: 0,
			title: {
            	enabled: false
			},
        },
        legend: {
            enabled: false
        },
        exporting: {enabled: false},
		credits: {
        	enabled: false
    	},
		series: series['series']//[{name:'Teams',data:[20]}]
	}
	chart1 = new Highcharts.Chart(options);	

    var newh = $(".chart-box").height();
    $( window ).resize(function() {
		newh = $(".chart-box").height();
		chart1.redraw();
		chart1.reflow();
	});
}

createVerticalBarChart(<?= $categories; ?>, <?= $series;?>, 'Estimated Projected/Actual Hours');

</script>
<noscript></noscript>