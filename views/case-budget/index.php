<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\grid\GridView;
use app\components\IsataskFormFlag;

\app\assets\HighchartAsset::register($this);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.tabletojson.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$chart_total=$total;
$lbl="Total Spend per Case Budget";
$max=$case_info->budget_value;
$overflow=0;
if($total > intval($case_info->budget_value))
{
	$max=$total;
	$overflow=($total - intval($case_info->budget_value));

	$chart_total=$total;
	//echo $total.'-'.$overflow;
	$lbl="<div>Total Spend per Case Budget </div><div style=color:red;text-align:center;>(OVER BUDGET)</div><div style=color:red;text-align:center;>$ ".number_format(($overflow), 2, '.', ',')."</div>";
}
$maxbudgetVal = $case_info->budget_value > $caseSpendPerProject['total']['total_spent'] ? $case_info->budget_value : $caseSpendPerProject['total']['total_spent'];
//echo $maxbudgetVal; exit;
//$chart_total = number_format($chart_total,2);
//$chart_total = 31307.2589;
/*echo '<pre>';
print_r($client_case_length);
die;*/
?>

<div class="right-main-container">
			<fieldset class="two-cols-fieldset project-comments case-budgets">
			<?php $form = ActiveForm::begin(['id' => $case_info->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
			 <?= IsataskFormFlag::widget(); // change flag ?>
			 <div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Case Budget">Case Budget</a></div>
			 <div class="comments-area">
			 	<div class="col-sm-9">
				<?= $form->field($case_info, 'budget_value',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['id'=>'budget_value','placeholder'=>"Enter Value1"])->widget(MaskMoney::classname(), [
			 		'options'=>['placeholder'=>"Enter Value",'aria-label'=>'Budget Value','maxlength'=>$client_case_length['budget_value']+5],
				    'pluginOptions' => [
				        'prefix' => 'US$',
				        'suffix' => '',
				        'affixesStay' => true,
				        'thousands' => ',',
				        'decimal' => '.',
				        'precision' => 2, 
				        'allowZero' => false,
				        'allowNegative' => false,
				    ]
				]); ?>
			 	<?= $form->field($case_info, 'budget_alert',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['id'=>'budget_alert','placeholder'=>"Enter Value"])->widget(MaskMoney::classname(), [
			 		'options'=>['placeholder'=>"Enter Value",'aria-label'=>'Alert Value','maxlength'=>$client_case_length['budget_value']+5],
			 		'pluginOptions' => [
				        'prefix' => 'US$',
				        'suffix' => '',
				        'affixesStay' => true,
				        'thousands' => ',',
				        'decimal' => '.',
				        'precision' => 2, 
				        'allowZero' => false,
				        'allowNegative' => false,
				    ]
				]); ?>
				<div class="form-group field-budget_alert">
						<div class="row input-field">
							<div class="col-md-3">&nbsp;</div>
							<div class="col-md-9 text-right">
								<button class="btn btn-primary" title="Apply" type="button" onclick="Apply();">Apply</button>			
							</div>
						</div>
				</div>
				<div class="row case-budgets-spend-table">
				  <div class="form-group">
				  <div class="col-md-12" style="margin:0 auto;">
				   <div class="table-responsive">
				   <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" id="casespend_table">
				   	<thead>
				   		<tr>
				   			<th id="case_budget_project" scope="col" class="text-center"><a href="javascript:void(0);" title="Project #" class="tag-header-black"><strong>Project #</strong></a></th>
				   			<th id="case_budget_name" scope="col" title="Project Name"><a href="javascript:void(0);" title="Project Name" class="tag-header-black"><strong>Project Name</strong></a></th>
				   			<th id="case_budget_invoiced" scope="col" title="Invoiced"><a href="javascript:void(0);" title="Invoiced" class="tag-header-black"><strong>Invoiced</strong></a></th>
				   			<th id="case_budget_pending" scope="col" title="Pending"><a href="javascript:void(0);" title="Pending" class="tag-header-black"><strong>Pending</strong></a></th>
				   			<th id="case_budget_totalspend" scope="col" title="Total Spend"><a href="javascript:void(0);" title="Total Spend" class="tag-header-black"><strong>Total Spend</strong></a></th>
				   		</tr>
					</thead>
				   	<tbody>
				   	<?php if(!empty($caseSpendPerProject)){
				   		foreach ($caseSpendPerProject as $key=>$model){ if($key==='total'){continue;}?>
				   			<tr>
				   				<td headers="case_budget_project" class="word-break text-center"><?=Html::a($model['project_id'],null,['href'=>Url::toRoute(['track/index','taskid'=>$model['project_id'],'case_id'=>$case_info->id]),'title'=>'Project #'.$model['project_id']]); ?></td>
				   				<td headers="case_budget_name" class="word-break"><?=$model['project_name'] ?></td>
				   				<td headers="case_budget_invoiced" class="word-break"><?="$".number_format($model['invoiced'], 2, '.', ','); ?></td>
				   				<td headers="case_budget_pending" class="word-break"><?="$".number_format($model['pending'], 2, '.', ',');?></td>
				   				<td headers="case_budget_totalspend" class="word-break"><?="$".number_format($model['total_spent'], 2, '.', ',');?></td>
				   			</tr>
				   		<?php }?>
				   			<tr id="total_tbl">
				   				<td headers="case_budget_project case_budget_name" colspan="2" title="Spend Totals" class="text-right word-break"><a href="javascript:void(0);" class="tag-header-black" title="Spend Totals"><strong>Spend Totals:</strong></a></td>
								<td headers="case_budget_invoiced" class="word-break"><strong><?="$".number_format($caseSpendPerProject['total']['invoiced'], 2, '.', ','); ?></strong></td>
   							   	<td headers="case_budget_pending" class="word-break"><strong><?="$".number_format($caseSpendPerProject['total']['pending'], 2, '.', ',');?></strong></td>
   						   		<td headers="case_budget_totalspend" class="word-break"><strong><?="$".number_format($caseSpendPerProject['total']['total_spent'], 2, '.', ',');?></strong></td>
				   			</tr>
				   		<?php }?>
				   	</tbody>
				   </table>
					</div>
				  </div>
				  </div>
				 </div>
				<input type="hidden" id="pdfimage" name="pdfimage">
			 	</div>
			 	<div class="col-sm-3 chart-box">
			 		<div id="container-speed" class="chart-container"></div>
			 	</div>	
			 </div>
			 <?php ActiveForm::end(); ?>
		<div class="button-set text-right">
		   	<!--<button class="btn btn-primary" title="PDF Export" type="button" onclick="casebudget_pdf(<?=$case_id?>);">PDF</button>-->
			   <button class="btn btn-primary" title="Export" type="button" onclick="exportexcel(<?=$case_id?>);">Export</button>
		</div>
	</fieldset>
</div>
<script>
/* clientcase budget value */	
$('document').ready(function(){
	$("#active_form_name").val('ClientCase'); // active form name
});
$('#clientcase-budget_value-disp').change(function(){ 
	$('#ClientCase #is_change_form').val('1');
	$('#ClientCase #is_change_form_main').val('1');
});	
$('#clientcase-budget_alert-disp').change(function(){ 
	$('#ClientCase #is_change_form').val('1');
	$('#ClientCase #is_change_form_main').val('1');
});
var chart;
//Highchart download pdf file functionality
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
$(function () {
    chart = new Highcharts.Chart({    
        chart: {
    		renderTo: 'container-speed',
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false,
            spacingBottom: 0,
            spacingTop: 0,
            spacingLeft: 0,
            spacingRight: 0,
            marginTop:5,
            marginBottom:0,

            // Explicitly tell the width and height of a chart
            width: null,
            height: null
        },
        exporting: {
            enabled: false
   		},
        credits: {enabled: false},                
        title: {	
            //text: '<?php echo $lbl?>',
            text: '',
            style: {
                    padding: '0px',
                    fontSize: "11px"
            }
        },
        pane: {
            startAngle: -150,
            endAngle: 150,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: <?=$max?>,

            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',

            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto',
				formatter: function() 
				  {
					 return Math.round(this.value/1000) + 'k';
				  }
            },
            title: {
                text: '$'
            },
            plotBands: [{
                from: 0,
                //to: (<?=$case_info->budget_alert?>/2),
                to: (<?php if($case_info->budget_alert > 0){ echo $case_info->budget_alert/2 ; } else { echo '0';}?>),
                color: '#55BF3B' // green
            }, {
                from: (<?=$case_info->budget_alert?>/2),
                //to: (<?=$case_info->budget_alert?>),
                to: (<?php if($case_info->budget_alert > 0){ echo $case_info->budget_alert ; } else { echo '0'; }?>),
                color: '#DDDF0D' // yellow
            }, {
                from: (<?=$case_info->budget_alert?>),
                //to: <?=$maxbudgetVal?>,
                to: <?php if($maxbudgetVal > 0){ echo $maxbudgetVal; } else { echo '0';} ?>,
                color: '#DF5353' // red
            }]
        },
		series: [{
            name: 'Total Spend',
            data: [<?=$chart_total?>],
            dataLabels: {
                enabled: true,
                //format: '{y:.2f}',
                formatter: function () {
					return Highcharts.numberFormat(this.y, 2,'.',',');
				}
			},
            style:{ "fontSize": "11px" }
        }],
        tooltip: {
			formatter: function () {
				return 'Total Spend : $' + Highcharts.numberFormat(this.y, 2,'.',',');
			}
        }

    },
    // Add some life
    function (chart) {
        if (!chart.renderer.forExport) {
            
        }
    });
    var newh = $(".chart-box").height();
    $( window ).resize(function() {
		newh = $(".chart-box").height();
		chart.redraw();
		chart.reflow();
	});
	
    EXPORT_WIDTH = 1000;
    var render_width = EXPORT_WIDTH;
    var render_height = render_width * chart.chartHeight / chart.chartWidth

        // Get the cart's SVG code
        var tempchart = chart;
    	$(tempchart).attr('options').title.text = '';
    	$(tempchart).attr('options').title.style = '';
    	$(tempchart).attr('chartHeight',200)
    	console.log($(tempchart).attr('chartHeight'));
        var svg = tempchart.getSVG({
            exporting: {
                sourceWidth: tempchart.chartWidth,
                sourceHeight: tempchart.chartHeight
            }
        });

    $("#pdfimage").val(window.btoa(svg));

   
        
});
function createChart(lbl,budget_alert,budget_value,chart_total){
	$('#container-speed').html(null);
	maxval=budget_value;
	if(chart_total > budget_value){
		maxval=chart_total;
	}
	 chart = new Highcharts.Chart({    
	        chart: {
	    		renderTo: 'container-speed',
	            type: 'gauge',
	            plotBackgroundColor: null,
	            plotBackgroundImage: null,
	            plotBorderWidth: 0,
	            plotShadow: false,
	            spacingBottom: 0,
	            spacingTop: 0,
	            spacingLeft: 0,
	            spacingRight: 0,
	            marginTop:5,
	            marginBottom:0,

	            // Explicitly tell the width and height of a chart
	            width: null,
	            height: null
	        },
			exporting: {
            	enabled: false
			},
			credits: {	enabled: false	},
	        title: {
	            text: lbl,
	            style: {
	                    padding: '0px',
	                    fontSize: "11px"
	            }
	        },
	        pane: {
	            startAngle: -150,
	            endAngle: 150,
	            background: [{
	                backgroundColor: {
	                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	                    stops: [
	                        [0, '#FFF'],
	                        [1, '#333']
	                    ]
	                },
	                borderWidth: 0,
	                outerRadius: '109%'
	            }, {
	                backgroundColor: {
	                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	                    stops: [
	                        [0, '#333'],
	                        [1, '#FFF']
	                    ]
	                },
	                borderWidth: 1,
	                outerRadius: '107%'
	            }, {
	                // default background
	            }, {
	                backgroundColor: '#DDD',
	                borderWidth: 0,
	                outerRadius: '105%',
	                innerRadius: '103%'
	            }]
	        },

	        // the value axis
	        yAxis: {
	            min: 0,
	            max: maxval,

	            minorTickInterval: 'auto',
	            minorTickWidth: 1,
	            minorTickLength: 10,
	            minorTickPosition: 'inside',
	            minorTickColor: '#666',

	            tickPixelInterval: 30,
	            tickWidth: 2,
	            tickPosition: 'inside',
	            tickLength: 10,
	            tickColor: '#666',
	            labels: {
	                step: 2,
	                rotation: 'auto',
	                formatter: function() 
					  {
						 return Math.round(this.value/1000) + 'k';
					  },
	            },
	            title: {
	                text: '$'
	            },
	            
	            plotBands: [{
	                from: 0,
	                to: (budget_alert/2),
	                color: '#55BF3B' // green
	            }, {
	                from: (budget_alert/2),
	                to: (budget_alert),
	                color: '#DDDF0D' // yellow
	            }, {
	                from: (budget_alert),
	                to: budget_value,
	                color: '#DF5353' // red
	            }]
	        },

	        series: [{
	            name: 'Total Spend',
	            data: [chart_total],
	            tooltip: {
	                valuePreffix: '$ '
	            },
	            style:{ "fontSize": "11px" }
	        }],
			tooltip: {
				formatter: function () {
					return 'Total Spend : $' + Highcharts.numberFormat(this.y, 2,'.',',');
				}
			}

	    });
	 	var newh = $(".chart-box").height();
	    $( window ).resize(function() {
			newh = $(".chart-box").height();
			chart.redraw();
			chart.reflow();
		});
	    EXPORT_WIDTH = 1000;
	    var render_width = EXPORT_WIDTH;
	    var render_height = render_width * chart.chartHeight / chart.chartWidth

	        // Get the cart's SVG code
	        var svg = chart.getSVG({
	            exporting: {
	                sourceWidth: chart.chartWidth,
	                sourceHeight: chart.chartHeight
	            }
	        });
	    $("#pdfimage").val(window.btoa(svg));
}
function Apply()
{
	 var budget_value=$("#clientcase-budget_value").val();
	 var budget_alert=$("#clientcase-budget_alert").val();
	 if(parseInt(budget_value)==0 || parseInt(budget_alert)==0){
		 $('#meter').html('');
		 $("#total_tbl").css("color","#000");
		 alert('Budget alter and budget value must be greater than zero.');
	 }else{ 
	 if(parseFloat(budget_alert) > parseFloat(budget_value))
	 {
		 alert("A Budget Alert value cannot be greater than the Budget value.");
		 $("#budget_alert_text").focus();
		 return false;
	 }
	 if(budget_value!="" && budget_alert!="")
	 {
		 $.ajax({
			 	url:$('#ClientCase').attr('action'),
			 	type:'post',
			 	data:$('#ClientCase').serialize(),
			 	beforeSend:function(){
			 		showLoader();
				},
		        success: function(responseText) {
						 var res = responseText.split("|"); 
						 var chart_total=<?php echo $total?>;
						 var inter1=Math.round((parseInt(res[1]))/2);
						 var inter3=parseInt(res[0]);
						 var inter2=parseInt(res[1]);//(parseInt(responseText)-Math.round(parseInt(responseText)/3));
						 var lbl='';//Total Spend per Case Budget';
						 $("#total_tbl").css("color","#000");
						 $(".lastrow:last").css({Color: "#000"});
					 	if(chart_total > parseInt(res[0]))
						{
					 		overflow=(chart_total - parseInt(res[0]));	
					 		chart_total=parseInt(res[0]);
					 		lbl="";//<div>Total Spend per Case Budget </div><div style=color:red;text-align:center;>(OVER BUDGET)</div><div style=color:red;text-align:center;>$ "+formatDollar(overflow)+"</div>";
					 		$(".lastrow:last").css({Color: "#FF0000"});
					 	}
						createChart(lbl,inter2,inter3,chart_total);
						$('#is_change_form').val(0);
						$('#is_change_form_main').val(0);
						hideLoader();	 
			   },
		}); 
	 }
	}
}
function exportexcel(case_id){
				var imageData = $('#container-speed').highcharts().createCanvas();
				$("body").append('<form id="excel_export_chart">');
				var form = $('#excel_export_chart');
				var element1 = document.createElement("input"); 
			    $(form).attr("method", "POST");
			    $(form).attr("action", baseUrl+"export-excel/case-budget");   
			    $(form).append('<input type="hidden" name="chart_report" value="chart_report" />');
				$(form).append('<input type="hidden" name="image_data" value="'+imageData+'" />');
			    $(form).append('<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />');
			   	var myRows = [];
				var table_title = $('#table_title').html();
				var table = $('#casespend_table').tableToJSON(); 
				$(form).append("<input type='hidden' name='table_data_title' value='"+table_title+"' />");
				$(form).append("<input type='hidden' name='table_data' value='"+JSON.stringify(table)+"' />");
				$(form).append("<input type='hidden' name='case_id' value='"+case_id+"' />");
		        hideLoader();
			    form.submit();
}
<?php if($overflow > 0){?>
	$(".lastrow:last").css({Color: "#FF0000"});
<?php }?>
</script>
<noscript></noscript>
