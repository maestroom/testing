<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\grid\GridView;
use app\models\Options;
use app\models\Tasks;
use app\models\TasksUnits;
\app\assets\HighchartAsset::register($this);
?>
<div class="right-main-container">
	<fieldset class="two-cols-fieldset project-comments est-reports">
		<div class="sub-heading"><a href="javascript:void(0);" title="Team % Complete" class="tag-header-black">Team % Complete</a></div>
		<div class="comments-area">
				<div class="row">
					<div class="col-sm-7 chart-box">
			 			<div id="periodchart" class="chart-container"></div>
					</div>
					<div class="col-sm-5">
								<div class="form-group">
									<div class="table-responsive">
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table borderless" id="casespend_table">
						   					<tbody>
						   						<tr>
							   						<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="Project #"><strong>Project #</strong></a></td>
							   						<td><?= $taskmodel->id; ?></td>
						   						</tr>
						   						<tr>
									 				<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="Project Name"><strong>Project Name</strong></a></td>
									 				<td><?= $taskinstruct->project_name; ?></td>
									 			</div>
									 			<tr>
									 				<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="Submitted Date"><strong>Submitted Date</strong></a></td>
									 				<td><?= (new Options)->ConvertOneTzToAnotherTz($taskmodel->created,"UTC",$_SESSION["usrTZ"]); ?></td>
									 			</div>
									 			<tr>
									 				<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="% Complete"><strong>% Complete</strong></a></td>
									 				<td><?= (new Tasks)->getTaskPercentageCompleted($taskmodel->id,"case",0,0,0,'NUM',array(),$perc_complete); ?></td>
									 			</div>
									 			<tr>
									 				<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="Project Due Date"><strong>Project Due Date</strong></a></td>
									 				<td><?= (new Options)->ConvertOneTzToAnotherTz($taskinstruct->task_duedate." ".$taskinstruct->task_timedue, 'UTC', $_SESSION['usrTZ']); ?></td>
									 			</div>
									 			<tr>
									 				<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="Project Completed Date"><strong>Project Completed Date</strong></a></td>
									 				<td><?= ($taskmodel->task_complete_date !='0000-00-00 00:00:00' && $taskmodel->task_status==4)?(new Options)->ConvertOneTzToAnotherTz($taskmodel->task_complete_date, 'UTC', $_SESSION['usrTZ']):''; ?></td>
									 			</div>
									 			
									 				<?php
									 					$is_pastdue=(new Tasks)->ispastduetask($taskmodel->id);
									 					if($is_pastdue) {
															$diff=(new TasksUnits)->dateDiff(time(),strtotime($taskinstruct->task_duedate." ".$taskinstruct->task_timedue));
														$letters = array('days','hours','minutes');
														$fruit   = array('d', 'h','m');
														$output  = str_replace($letters, $fruit, $diff);
									 				?>
									 				<tr>
										 				<td width="50%"><a href="javascript:void(0);" class="tag-header-black" title="Project Past Due"><strong>Project Past Due</strong></a></td>
										 				<td>
									 				<?php 
									 					echo $output;
									 				?>
									 					</td>
									 				</tr>
									 				<?php } ?>
									 			</div>
											</tbody>
										</table>
									</div>
								</div>
					</div>	
				</div>
				<div class="row est-report-spend-table">
						<div class="col-md-12 form-group">
							<div class="table-responsive">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" id="casespend_table">
									<thead>
										<tr>
								   			<th class="text-center"><a href="javascript:void(0);" class="tag-header-black" title="Status"><strong>Status</strong></a></th>
								   			<th><a href="javascript:void(0);" class="tag-header-black" title="Task Name"><strong>Task Name</strong></a></th>
								   			<th><a href="javascript:void(0);" class="tag-header-black" title="Task Started"><strong>Task Started</strong></a></th>
								   			<th><a href="javascript:void(0);" class="tag-header-black" title="Task Completed"><strong>Task Completed</strong></a></th>
								   			<?php if($est_hours > 0) {?>
                                                                                            <th><a href="javascript:void(0);" class="tag-header-black" title="Est"><strong>Est</strong></a></th>
                                                                                        <?php }?>
								   			<th><a href="javascript:void(0);" class="tag-header-black" title="Actual"><strong>Actual</strong></a></th>
				   						</tr>
									</thead>
				   					<tbody>
				   						<?php
					           		   	if(!empty($myfinal_arr))
					           		   	{
					           		   		foreach ($myfinal_arr as $data)
					           		   		{ 
					           		   		?>
					           		   		<tr>
                                                                                    <td class="text-center"><?php echo $data['status'];?></td>
                                                                                    <td><?php echo $data['task_name'];?></td>
                                                                                    <td><?php echo $data['started']?></td>
                                                                                    <td><?php echo $data['completed']?></td>
                                                                                    <?php if($est_hours > 0) {?>
                                                                                        <td><?php echo $data['est']?></td>
                                                                                    <?php } ?>
                                                                                    <td class="<?php if($data['actualHr'] > $data['estHr']) echo " past_due_est";?>"><?php echo $data['actual']?></td>
					           		   		</tr>	
					           		   		<?php 
					           		   		}
					           		   	}?>
				   					</tbody>
				   				</table>
							</div>
					</div>
				</div>
		</div>
		<div class="button-set text-right">
			<input type="hidden" id="pdfimage" name="pdfimage">
			<button class="btn btn-primary" title="Back" type="button" onclick="estreport_back('<?=$qryString;?>');">Back</button>
			<button class="btn btn-primary" title="Refresh" type="button" onclick="javascript:location.reload();">Refresh</button>
		   	<button class="btn btn-primary" title="PDF Export" type="button" onclick="estreport_pdf(<?=$case_id;?>,<?= $team_id ?>,<?= $team_loc ?>,<?= $task_id ?>, '<?= $type ?>');">PDF</button>
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
			plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false,
            spacingBottom: 0,
            spacingTop: 0,
            spacingLeft: 0,
            spacingRight: 0,
            marginTop:30,
		},
		title: {
			text: title,
			style: {
                padding: '0px',
                fontSize: "13px"
        	}
		},
		xAxis: {
			type: 'category',
            /*labels: {
                style: {
                    fontSize: '13px',
                }
            }*/
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

 	// Get the cart's SVG code
    var svg = chart1.getSVG({
        exporting: {
            sourceWidth: chart1.chartWidth,
            sourceHeight: chart1.chartHeight
        }
    });

	$("#pdfimage").val(window.btoa(svg));
}

createVerticalBarChart(<?= $categories; ?>, <?= $series;?>, 'Projected vs. Actual Hours');

/* Start : To redirect Back on previous page from Est Report */
function estreport_back(qrystring){
	location.href = baseUrl + qrystring; 
}
/* End : To redirect Back on previous page from Est Report */

/* Start : To generate PDF */
function estreport_pdf(case_id,team_id,team_loc, task_id, type){
	if(type=='case')
		url = baseUrl +'pdf/est-report&task_id='+task_id+'&case_id='+case_id;
	else 
		url = baseUrl +'pdf/est-report&task_id='+task_id+'&team_id='+team_id+'&team_loc='+team_loc;
	
	var form = document.createElement("form");
	document.body.appendChild(form);
	form.method = "POST";
	form.action = url;
	imageval=$("#pdfimage").val();
	var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", 'pdfimage');
    hiddenField.setAttribute("value", imageval);
    form.appendChild(hiddenField);
    var hiddenField1 = document.createElement("input");
    hiddenField1.setAttribute("type", "hidden");
    hiddenField1.setAttribute("name", '_csrf');
    hiddenField1.setAttribute("value", yii.getCsrfToken());
    form.appendChild(hiddenField1);
	form.submit();
}
/* End : To generate PDF */

</script>
<noscript></noscript>
