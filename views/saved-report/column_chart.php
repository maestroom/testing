<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ReportsReportType;
\app\assets\HighchartAsset::register($this);
$modelreportsReportType = ReportsReportType::findOne($post_data['ReportsUserSaved']['report_type_id']);
?>
<style>
table, table tr, table th{
	font-size:13px!important;
	font-family:arial!important;
}
table td{
	font-size:10px!important;
	font-family:arial!important;
}
#datatable_section h6{
	font-size:14px!important;
	font-family:arial!important;
}
#datatable_section table th{
	font-size:12px!important;
	font-family:arial!important;
}
#datatable_section table td{
	font-size:12px!important;
	font-family:arial!important;
}
</style>
<?php
if(!isset($ytable_fields_detail['field_display_name'])){
	if($modelreportsReportType->sp_name='MediaOut')
		$ytable_fields_detail['field_display_name'] = isset($post_data['fielddisp'][$post_data['ReportsUserSaved']['y_data']])?$post_data['fielddisp'][$post_data['ReportsUserSaved']['y_data']]:'TotalMediaOut('.$post_data['ReportsUserSaved']['y_data'].')';
	else
		$ytable_fields_detail['field_display_name'] = isset($post_data['fielddisp'][$post_data['ReportsUserSaved']['y_data']])?$post_data['fielddisp'][$post_data['ReportsUserSaved']['y_data']]:$post_data['ReportsUserSaved']['y_data'];
}
if($post_data['ReportsUserSaved']['datatable_location']=='')
	$style="display:none;1px solid #ccc;";
elseif($post_data['ReportsUserSaved']['datatable_location']=='R' && $post_data['ReportsUserSaved']['legend_location']=='R')
	$style="float:right;width:26%;margin-right:14px;position: absolute;right: 21px;border:1px solid #ccc;display:table;";
elseif($post_data['ReportsUserSaved']['datatable_location']=='R' && $post_data['ReportsUserSaved']['legend_location']=='B')
	$style="float:right;width:25%;margin-right:14px;position: absolute;right: 21px;border:1px solid #ccc;display:table;";	
else
	$style="width:74%;margin-left:14px;border:1px solid #ccc;display:table;";
?>
<div class="chart-box" id="chart-box">
	<div id="dynamic_chart" class="clsperiodchart chart-container" style="<?php if($post_data['ReportsUserSaved']['datatable_location']=='R' && $post_data['ReportsUserSaved']['legend_location']=='R'){?>width:90%;<?php } else if($post_data['ReportsUserSaved']['datatable_location']=='R' && $post_data['ReportsUserSaved']['legend_location']=='B') { ?> float:left;width:75%;<?php } else {?>width:95%;<?php } ?>"></div>
	<div id="datatable_section" style="<?= $style ?>">
	<?php  if(!empty($report_data)){?>
		<h6 class="font14" align="<?php if($post_data['ReportsUserSaved']['title_location']=='TL'){?>left<?php }else {?>center<?php }?>" style="<?php if($post_data['ReportsUserSaved']['title_location']=='TL'){?>margin-left:10px;<?php }?>"><strong>Data Table</strong></h6>
		<?php if(strtolower($modeReportsChartFormat->chart_format)=='column clustered'){
		$series = $post_data['fielddisp'][$post_data['ReportsUserSaved']['series']];
		?>
		<table class="table" style="white-space: nowrap;">
  <thead>
    <th width="30%">&nbsp;</th>
    <th colspan="3"><b><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of <?=$ytable_fields_detail['field_display_name']?> by <?=$series?></b></th>
  </thead>
</table><?php }?>
		<table id="chart_datatable" class="table" style="white-space: nowrap;">
				<?php 
				if(strtolower($modeReportsChartFormat->chart_format)=='column clustered'){?>
					<?=$this->render('column_clustered_stack_table',['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]);?>
				<?php }else{?>
				<thead>
				<tr>
					<th><b><?=ucwords($table_fields_detail['field_display_name'])?></b></th>
					<th><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of <?=$ytable_fields_detail['field_display_name']?></b></th>
				</tr>
				</thead>
				<?php foreach($report_data as $rdata){
						if(isset($post_data['ReportsUserSaved']['x_data_display']) && ($post_data['ReportsUserSaved']['x_data_display']=='Weeks' || $post_data['ReportsUserSaved']['x_data_display']=='Days' || $post_data['ReportsUserSaved']['x_data_display']=='Months' || $post_data['ReportsUserSaved']['x_data_display']=='Years')){}else{if(!isset($rdata[1])){continue;}}
					?>
					<tr>
						<td>
							<?php if(isset($post_data['ReportsUserSaved']['x_data_display']) && ($post_data['ReportsUserSaved']['x_data_display']=='Weeks')){?>
								<?=date("m/d/Y",strtotime($rdata['start_date']))."-".date("m/d/Y",strtotime($rdata['end_date']))?>
							<?php }elseif(isset($post_data['ReportsUserSaved']['x_data_display']) && ($post_data['ReportsUserSaved']['x_data_display']=='Days')){?>
								<?php echo '"'.date("m/d/Y",strtotime($rdata['start_date'])).'"';?>
							<?php }elseif(isset($post_data['ReportsUserSaved']['x_data_display']) && ($post_data['ReportsUserSaved']['x_data_display']=='Months')){?>
								<?php echo date("m/Y",strtotime($rdata['start_date']));?>
							<?php }elseif(isset($post_data['ReportsUserSaved']['x_data_display']) && ($post_data['ReportsUserSaved']['x_data_display']=='Years')){?>
								<?php echo date("Y",strtotime($rdata['start_date']));?>
							<?php }else{?>
									<?=$rdata[1]?>
							<?php }?>
						</td>
						<td>
							<?php if(isset($post_data['ReportsUserSaved']['x_data_display']) && ($post_data['ReportsUserSaved']['x_data_display']=='Weeks' || $post_data['ReportsUserSaved']['x_data_display']=='Days' || $post_data['ReportsUserSaved']['x_data_display']=='Months' || $post_data['ReportsUserSaved']['x_data_display']=='Years')){?>
								<?=$rdata['X']?>
							<?php } else {?>
								<?=$rdata[0]?>
							<?php }?></td>
					</tr>
				<?php }?>
				<?php }?>
		</table>
	<?php }?>
	</div>
</div>
<script language="javascript">
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
var title_align='center';
var fontSizeval = '13px';
var datalabel_align=false;
if('<?=$post_data['ReportsUserSaved']['title_location']?>'=='TL')
title_align='left';
if('<?=$post_data['ReportsUserSaved']['data_label_location']?>'=='Inside')
datalabel_align=true;

var xAxis_gridLineWidth=yAxis_gridLineWidth=0;
<?php if(isset($post_data['ReportsUserSaved']['grid_line']) && in_array("H",$post_data['ReportsUserSaved']['grid_line'])){?>
	yAxis_gridLineWidth=1;
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['grid_line']) && in_array("V",$post_data['ReportsUserSaved']['grid_line'])){?>
	xAxis_gridLineWidth=1
<?php }?>
var legend_location_verticalAlign='top';
var legend_location='right';
var legend_location_layout='vertical';
var legend_y = 27;
<?php if($post_data['ReportsUserSaved']['legend_location']=='B'){?>
	legend_location='center';
	legend_location_verticalAlign='bottom';
	legend_location_layout='horizontal';
	legend_y = 0;
<?php }?>
var datalabel_enable=false;
var datalabel_inside=false;
<?php  if(isset($post_data['ReportsUserSaved']['data_label_location']) && $post_data['ReportsUserSaved']['data_label_location']=='Inside'){?>
datalabel_enable=true;
datalabel_inside=true;
<?php }?>
<?php  if(isset($post_data['ReportsUserSaved']['data_label_location']) && $post_data['ReportsUserSaved']['data_label_location']=='Outside'){?>
datalabel_enable=true;
datalabel_inside=false;
<?php }?>
var rotation=0;
<?php if($post_data['ReportsUserSaved']['x_axis_location']=='Vertical'){?>
	rotation=-90;
<?php }?>
<?php if($post_data['ReportsUserSaved']['x_axis_location']=='Diagonal'){?>
	rotation=-45;
<?php }?>
var options3d_enable=false;
<?php if($post_data['ReportsUserSaved']['dimension']=='3D'){?>
	options3d_enable=true;
<?php }?>
enabled_legend=true;
<?php if($post_data['ReportsUserSaved']['legend_location']==''){?>
enabled_legend=false;
<?php }?>
chart_title='';
<?php if(isset($post_data['ReportsUserSaved']['title']) && trim($post_data['ReportsUserSaved']['title'])!=""){?> 
chart_title='<?=$post_data['ReportsUserSaved']['title'];?>';
<?php }?>
var options = {
			chart: {
				renderTo: 'dynamic_chart',
				type: 'column',
				plotBackgroundColor: null,
				plotBorderColor: '#ccc',
				plotBorderWidth: ((options3d_enable)?0:1),
				options3d: {
					enabled:options3d_enable,
					alpha: 10,
					beta: 5,
					depth: 50,
					viewDistance: 25
				},
				events: {
					redraw: function(event) {
						//console.log(this.options.legend);
						<?php if(isset($post_data['ReportsUserSaved']['title']) && trim($post_data['ReportsUserSaved']['title'])==""){?>
							this.options.chart.marginTop = 37;
							$('#datatable_section').css("top", "37px");
						<?php }?>
						if(legend_location == 'right'){
							$('#datatable_section').css("top", parseInt(this.legend.legendHeight)+40+"px");
							$('#datatable_section').css("left", parseInt(this.plotLeft)+parseInt(this.plotWidth)+20+"px");
							//$('#datatable_section').css("width",parseInt(this.plotLeft)+parseInt(this.plotWidth)-10);
						}
						if(title_align == 'left'){
							this.options.title.x = 20;
						}
						if(title_align == 'center'){
							this.options.title.x = parseInt(this.plotLeft)+parseInt(parseInt(this.plotWidth)/2)-50;
							var x = (parseInt(this.options.legend.width)/2)-35;
							this.legend.title.translate(x, 6);
						}
						if(legend_location == 'right' && '<?=$post_data['ReportsUserSaved']['datatable_location']?>'=='R'){
							$('#datatable_section').css("top", parseInt(this.legend.legendHeight)+40+"px");
							$('#datatable_section').css("left", parseInt(this.plotLeft)+parseInt(this.plotWidth)+20+"px");
							//$('#datatable_section').css("width",parseInt(this.plotLeft)+parseInt(this.plotWidth)-10);
							this.options.legend.width = $('#datatable_section').width()-10;
						}
						if(legend_location_verticalAlign == 'bottom' && '<?=$post_data['ReportsUserSaved']['datatable_location']?>'=='R'){
							$('#datatable_section').css("top", "37px");
							$('#datatable_section').css("left", parseInt(this.plotLeft)+parseInt(this.plotWidth)+20+"px");
							this.options.legend.width = parseInt(this.plotLeft)+parseInt(this.plotWidth)-40;
						}
						<?php if($post_data['ReportsUserSaved']['datatable_location']=='B'){?>
							$('#datatable_section').css("width",parseInt(this.plotLeft)+parseInt(this.plotWidth)-10);
						<?php } ?>
						if(legend_location_verticalAlign == 'bottom'){
							<?php if($post_data['ReportsUserSaved']['datatable_location']=='R'){?>
							this.options.chart.marginTop = 37;
							$('#datatable_section').css("top", "37px");
							<?php }?>
							this.options.legend.width = parseInt(this.plotLeft)+parseInt(this.plotWidth)-40;
					//		this.options.chart.height = $('#datatable_section tr').length * 30 + 120;
						}
					//	if(this.options.chart.height < 500)
					//			this.options.chart.height = 500;
						//console.log('<?= $post_data['ReportsUserSaved']['datatable_location'] ?>');
					}
				} 
			},
			title: {
				 text: chart_title,
				 align: 'left',
				 x: 0,
				 style:{
					 fontFamily: 'Arial',
					 lineHeight: '34px',
					 margin: 0,
					 fontSize: '14px',
					 fontWeight: 'bold',
				 }
			},
			 plotOptions: {
				column: {
					stacking: "<?php if($post_data['ReportsUserSaved']['y_fn_display'] == 'Percentage') { echo 'percent'; } else { echo 'normal';}  ?>",
			        grouping: true,                  
                 	groupPadding:0.1,
                 	pointWidth:20,
			    },
				series: {
					showInLegend:true,
					dataLabels: {
						enabled: datalabel_enable,
						inside: datalabel_inside,
						style:{
							fontFamily: 'Arial',
							fontSize:'12px',
							lineHeight: '34px',
    						margin: 0,
						},
						formatter: function () {
							<?php if($post_data['ReportsUserSaved']['y_fn_display'] == 'Percentage') { ?>
								return Highcharts.numberFormat(this.y) + '%';
							<?php }else{?>
								return this.y;
							<?php }?>
							/*chart=$('#dynamic_chart').highcharts();
							var mytotal = 0;
							<?php if (strpos(strtolower($modeReportsChartFormat->chart_format),"clustered") !== false) {?>
							for (i = 0; i < chart.series.length; i++) {
								if (mychart.series[i].visible) {
									mytotal += parseInt(mychart.series[i].yData[0]);
								}
							}
							<?php } else {?>
							for (i = 0; i < chart.series.length; i++) {
								if (chart.series[i].visible) {
									for (j = 0; j < chart.series[i].yData.length; j++) {
										mytotal += parseInt(chart.series[i].yData[j]);
									}
									console.log("Total : "+ i + " Total : "+ mytotal + " length" + chart.series[i].yData.length);
								}
							}
						<?php }?>
							var pcnt = (this.y / mytotal) * 100;
							return Highcharts.numberFormat(pcnt) + '%';*/
						}
					},
				},
			},
			xAxis: {
				labels: {
					rotation: rotation,
					style: {
						fontFamily: 'Arial',
						fontSize:'12px',
						lineHeight: '34px',
    					margin: 0,
					},
				},
				title: {
					enabled: true,
					text: '<?=ucwords($table_fields_detail['field_display_name'])?>',
					style: {
						fontFamily: 'Arial',
						fontSize:'12px',
						lineHeight: '34px',
						margin: 0,
						fontWeight: 'bold',
						
					},
				},
				gridLineWidth: xAxis_gridLineWidth,
			},
			tooltip: {
				useHTML: true,
				formatter: function () {
					<?php if (strpos(strtolower($modeReportsChartFormat->chart_format),"clustered") !== false) {?>
						<?php if($post_data['ReportsUserSaved']['x_fn_display'] == 'Currency') { ?>
							return '<span style="font-family:Arial;font-size:11px;line-height:34px;margin: 0;"><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of '+this.key+' - '+this.series.legendItem.textStr+': $' + number_format(this.y,2) +'</b></span>';
						<?php } else if($post_data['ReportsUserSaved']['y_fn_display'] == 'Percentage') { ?>
							return '<span style="font-family:Arial;font-size:11px;line-height:34px;margin:0;"><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of '+this.key+' - '+this.series.legendItem.textStr+': ' + this.y +' %</b></span>';	
						<?php }else{?>
							return '<span style="font-family:Arial;font-size:11px;line-height:34px;margin: 0;"><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of '+this.key+' - '+this.series.legendItem.textStr+': ' + this.y +'</b></span>';
						<?php }?>
					<?php }else{ ?>
						<?php if($post_data['ReportsUserSaved']['y_fn_display'] == 'Currency') { ?>
							return '<span style="font-family:Arial;font-size:11px;line-height:34px;margin:0;"><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of '+this.key+': $' + number_format(this.y,2) +'</b></span>';
						<?php } else if($post_data['ReportsUserSaved']['y_fn_display'] == 'Percentage') { ?>
							return '<span style="font-family:Arial;font-size:11px;line-height:34px;margin:0;"><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of '+this.key+': ' + this.y +' %</b></span>';
						<?php }else{?>
							return '<span style="font-family:Arial;font-size:11px;line-height:34px;margin:0;"><b><?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> of '+this.key+': ' + this.y +'</b></span>';
						<?php }?>	
					<?php }?>	


					//return '<span style="font-family:arial, Verdana, sans-serif;font-size:9px;"><b>' + this.series.name + ' : ' + this.y + '</b></span>';
				}
			},
			legend: {
				enabled: enabled_legend,
				margin: 10,
				width: 200,
				symbolHeight: 12,
				symbolWidth: 12,
				symbolRadius: 1,
				padding: 10,
				itemMarginTop: 5,
				y: legend_y,
				title: {
                	text: 'Legend',
					style: {
						fontFamily: 'Arial',
						fontSize:'14px',
						lineHeight: '34px',
						margin: 0,
						textAlign:'center',
						fontWeight: 'Bold',
					},
            	},
				align: legend_location,
				verticalAlign: legend_location_verticalAlign,
				layout: legend_location_layout,
				borderColor: '#ccc',
            	borderWidth: 1,
				labelFormatter: function() {
					<?php if (strpos(strtolower($modeReportsChartFormat->chart_format),"clustered") !== false) {?>
					return this.name;
					<?php }else{?>
					return '<?=$ytable_fields_detail['field_display_name']?>';
					<?php }?>
					/*return str.join(' ');*/
				},
				itemStyle: {
					fontFamily: 'Arial',
					fontSize:'12px',
					lineHeight: '34px',
					margin: 0,
					fontWeight: 'normal !important;',
				}
			},
			yAxis: {
				//tickInterval: 20,
				tickPixelInterval:50,
				allowDecimals: false,
				rotation: rotation,
				labels: {
					style: {
						fontFamily: 'Arial',
						fontSize:'12px',
						lineHeight: '34px',
						margin: 0,
					},
					formatter: function () {
						<?php if($post_data['ReportsUserSaved']['y_fn_display'] == 'Percentage') { ?>
                        	return this.value + '%';
						<?php } else {?>	
							return this.value;
						<?php }?>
                    }
				},
				min: 0,
				title: {
					enabled: true,
					text: '<?=ucwords($post_data['ReportsUserSaved']['y_fn'])?> Of <?=$ytable_fields_detail['field_display_name']?>',
					style: {
						fontFamily: 'Arial',
						fontSize:'12px',
						lineHeight: '34px',
						margin: 0,
						fontWeight: 'bold',
						
					},
				},
				gridLineWidth: yAxis_gridLineWidth
			},
			exporting: {enabled: false},
			credits: {
				enabled: false
			},
			data:{
				table: 'chart_datatable',
			},
			dataLabels:{
				inside:datalabel_align,
			},
	   };
	chart1 = new Highcharts.Chart(options);	

	<?php if($post_data['ReportsUserSaved']['y_fn_display'] == 'Currency'){?>
		$("#datatable_section tbody tr td:nth-child(2)").each(function() {
				$(this).html('$' + number_format($(this).html(),2));
		});
	<?php }?>

	setTimeout(function(){
		chart1.redraw();
		chart1.reflow();
	}, 500);
	var newh = $(".chart-box").height();
	$( window ).resize(function() {
    	chart1.redraw();
		chart1.reflow();
	});
/**
 * Create a global exportCharts method that takes an array of charts as an argument,
 * and exporting options as the second argument
 */
//Export report in pdf format
$('#exportpdf').click(function () {
    if (typeof chart1 != 'undefined') {
        chart1.options.title.style.display = 'block';
    	generateChartPDF("clsperiodchart","dynamic_chart_");
    	chart1.options.title.style.display = 'none';
    }
});
function onlypdf(){
	if (typeof chart1 != 'undefined') {
        chart1.options.title.style.display = 'block';
    	generateChartPDF("clsperiodchart","dynamic_chart_");
    	chart1.options.title.style.display = 'none';
    }
}
function noreport() {
    $("#dynamic_chart").html("<div class='noreport'>No Record found...</div>");
}
</script>
<noscript></noscript>
