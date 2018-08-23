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
.table_step6 td{
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
.highcharts-container {
    width:100% !important;
    height:100% !important;
}
</style>
<?php
$stylechart="";
if($post_data['ReportsUserSaved']['datatable_location']==''){
	$style="display:none;1px solid #ccc;";
}elseif($post_data['ReportsUserSaved']['datatable_location']=='R' && $post_data['ReportsUserSaved']['legend_location']=='R'){
	$stylechart="width:95%;";
	$style="float:right;width:26%;margin-right:14px;position: absolute;right: 21px;border:1px solid #ccc;display:table;";
}elseif($post_data['ReportsUserSaved']['datatable_location']=='R' && $post_data['ReportsUserSaved']['legend_location']=='B'){
	$stylechart="width:75%;";
	$style="float:right;width:25%;margin-right:14px;position: absolute;right: 21px;border:1px solid #ccc;display:table;";	
}else{
	$style="width:74%;margin-left:14px;border:1px solid #ccc;display:table;";
}

if(!isset($ytable_fields_detail['field_display_name'])){
	if($modelreportsReportType->sp_name='MediaOut')
		$ytable_fields_detail['field_display_name'] = 'TotalMediaOut('.$post_data['ReportsUserSaved']['y_data'].')';
	else
		$ytable_fields_detail['field_display_name'] = $post_data['ReportsUserSaved']['y_data'];
}
?>
<div class="chart-box" id="chart-box">
	<div id="dynamic_chart" class="clsperiodchart chart-container" style="<?php  if($post_data['ReportsUserSaved']['datatable_location']=='R'){?>float:left;<?php }?><?=$stylechart?>"></div>
	<div id="datatable_section" style="<?php echo $style;?>">
	<?php  if(!empty($report_data)){?>
		<h6 class="font14" align="<?php if($post_data['ReportsUserSaved']['title_location']=='TL'){?>left<?php }else {?>center<?php }?>" style="<?php if($post_data['ReportsUserSaved']['title_location']=='TL'){?>margin-left:10px;<?php }?>"><strong>Data Table</strong></h6>
		<table id="chart_datatable" class="table" style="white-space: nowrap;" >
				<thead>
				<tr>
					<th><b><?=ucwords($post_data['fielddisp'][$post_data['ReportsUserSaved']['series']])?></b></th>
					<th id="table_title"><b>Proportion  of <?=$ytable_fields_detail['field_display_name']?></b></th>
				</tr>
				</thead>
				<?php foreach($report_data as $rdata){
						if(isset($post_data['ReportsUserSaved']['series1_display']) && ($post_data['ReportsUserSaved']['series1_display']=='Weeks' || $post_data['ReportsUserSaved']['series1_display']=='Days' || $post_data['ReportsUserSaved']['series1_display']=='Months' || $post_data['ReportsUserSaved']['series1_display']=='Years')){}else{if(!isset($rdata[1])){continue;}}
					?>
					<tr>
						<td>
							<?php if(isset($post_data['ReportsUserSaved']['series1_display']) && ($post_data['ReportsUserSaved']['series1_display']=='Weeks')){?>
							<?=date("m/d/Y",strtotime($rdata['start_date']))."-".date("m/d/Y",strtotime($rdata['end_date']))?>
							<?php }elseif(isset($post_data['ReportsUserSaved']['series1_display']) && ($post_data['ReportsUserSaved']['series1_display']=='Days')){?>
								<?php echo '"'.date("m/d/Y",strtotime($rdata['start_date'])).'"';?>
							<?php }elseif(isset($post_data['ReportsUserSaved']['series1_display']) && ($post_data['ReportsUserSaved']['series1_display']=='Months')){?>
								<?php echo date("m/Y",strtotime($rdata['start_date']));?>
							<?php }elseif(isset($post_data['ReportsUserSaved']['series1_display']) && ($post_data['ReportsUserSaved']['series1_display']=='Years')){?>
								<?php echo date("Y",strtotime($rdata['start_date']));?>
							<?php }else{?>
									<?=$rdata[1]?>
							<?php }?>
						</td>
						<td>
							<?php if(isset($post_data['ReportsUserSaved']['series1_display']) && ($post_data['ReportsUserSaved']['series1_display']=='Weeks' || $post_data['ReportsUserSaved']['series1_display']=='Days' || $post_data['ReportsUserSaved']['series1_display']=='Months' || $post_data['ReportsUserSaved']['series1_display']=='Years')){?>
								<?=$rdata['X']?>
							<?php } else {?>
								<?=$rdata[0]?>
							<?php }?></td>
					</tr>
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
	xAxis_gridLineWidth=1
<?php }?>
<?php if(isset($post_data['ReportsUserSaved']['grid_line']) && in_array("V",$post_data['ReportsUserSaved']['grid_line'])){?>
	yAxis_gridLineWidth=1;
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
var datalabel_enable=true;
var datalabel_distance=30;
var datalabel_color='black';
<?php  if(isset($post_data['ReportsUserSaved']['data_label_location']) && $post_data['ReportsUserSaved']['data_label_location']=='Inside'){?>
datalabel_enable=true;
datalabel_distance=-30;
datalabel_color='black'
<?php }?>
var rotation=0;
<?php if($post_data['ReportsUserSaved']['y_axis_location']=='Vertical'){?>
	rotation=-90;
<?php }?>
<?php if($post_data['ReportsUserSaved']['y_axis_location']=='Diagonal'){?>
	rotation=-45;
<?php }?>
var opmarker_enable=false;
var opmarker='';
<?php if(isset($post_data['ReportsUserSaved']['markers']) && $post_data['ReportsUserSaved']['markers']!=''){?>
	opmarker_enable=true;
	opmarker='<?=$post_data['ReportsUserSaved']['markers']?>'
<?php }?>
var options3d_enable=false;
<?php if($post_data['ReportsUserSaved']['dimension']=='3D'){?>
	options3d_enable=true;
<?php }?>
var opdashStyles='Solid';
var isslicesected=false;
<?php if(isset($post_data['ReportsUserSaved']['fill']) || $post_data['ReportsUserSaved']['fill']=='Gradient'){?>
	var opdashStyles='Dash';
<?php }
if(isset($post_data['ReportsUserSaved']['slice_position']) && $post_data['ReportsUserSaved']['slice_position']=="Exploded"){?>
	isslicesected=true;
<?php }?>
var innersize=0;
<?php if(isset($modeReportsChartFormat->chart_format) && (strtolower($modeReportsChartFormat->chart_format)=='circle donut')){?>
	innersize="50%";
<?php }?>
enabled_legend=true;
<?php if($post_data['ReportsUserSaved']['legend_location']==''){?>
enabled_legend=false;
<?php }?>
chart_title='';
<?php if(isset($post_data['ReportsUserSaved']['title']) && trim($post_data['ReportsUserSaved']['title'])!=""){?> 
chart_title='<?=$post_data['ReportsUserSaved']['title'];?>';
<?php }?>
var pie_slices=[];
if(legend_location_verticalAlign == 'bottom' && '<?=$post_data['ReportsUserSaved']['datatable_location']?>'=='R'){
		legend_location = 'left';
		legend_location_layout='horizontal';
}
events = {
					redraw: function(event) {
						<?php if(isset($post_data['ReportsUserSaved']['title']) && trim($post_data['ReportsUserSaved']['title'])==""){?>
							this.options.chart.marginTop = 37;
							$('#datatable_section').css("top", "37px");
						<?php }?>
						if(legend_location == 'right' && '<?=$post_data['ReportsUserSaved']['datatable_location']?>'=='R'){
							$('#datatable_section').css("top", parseInt(this.legend.legendHeight)+40+"px");
							$('#datatable_section').css("left", (parseInt(this.plotLeft)+parseInt(this.plotWidth)+10)+"px");
							//this.options.legend.width = $('#datatable_section').width()-10;
						}
						if(legend_location_verticalAlign == 'bottom' && '<?=$post_data['ReportsUserSaved']['datatable_location']?>'=='R'){
							$('#datatable_section').css("top", "37px");
							$('#datatable_section').css("left", parseInt(this.plotLeft)+parseInt(this.plotWidth)+20+"px");
							this.options.legend.width = parseInt(this.plotLeft)+parseInt(this.plotWidth)-40;
						}
						if(title_align == 'left'){
							this.options.title.x = 20;
						}
						if(title_align == 'center'){
							this.options.title.x = parseInt(this.plotLeft)+parseInt(parseInt(this.plotWidth)/2)-50;
							var x = (parseInt(this.options.legend.width)/2)-35;
							this.legend.title.translate(x, 6);
						}
						<?php if($post_data['ReportsUserSaved']['datatable_location']=='B'){?>
							$('#datatable_section').css("width",parseInt(this.plotLeft)+parseInt(this.plotWidth)-10);
						<?php } ?>
						if(legend_location_verticalAlign == 'bottom' && '<?=$post_data['ReportsUserSaved']['datatable_location']?>'=='B'){
							<?php if($post_data['ReportsUserSaved']['datatable_location']=='R'){?>
							this.options.chart.marginTop = 37;
							$('#datatable_section').css("top", "37px");
							<?php }?>
							this.options.legend.width = parseInt(this.plotLeft)+parseInt(this.plotWidth)-40;
						}
					}
				};
chart_options={
				renderTo: 'dynamic_chart',
				type: 'pie',
				defaultSeriesType: 'pie',
				plotBorderColor: '#ccc',
				plotBorderWidth: 1,
				options3d: {
					enabled:options3d_enable,
					alpha: 45,
				},
				events:events
};
var options = {
			chart: chart_options,
			title: {
				 text: chart_title,
				 align: 'left',
				 x:0,
				 style:{
					 fontFamily: 'Arial',
					 fontSize:'14px',
					 lineHeight: '34px',
					 margin: 0,
					 fontWeight: 'bold',
				 }
			},
			plotOptions: {
				pie: {
					slicedOffset:20,
					allowPointSelect: true,
					useHTML:true,
					borderWidth: ((isslicesected)?0.5:1),
					borderColor: ((isslicesected)?null:'#fff'),
					cursor: 'pointer',
					innerSize: innersize,
					depth: 45,
					showInLegend: true,
					pointPadding: 0,
			        groupPadding: 0,
			        pointWidth: 20,
					dataLabels: {
						softConnector: true,
						enabled: datalabel_enable,
						formatter: function() {
							if("<?=$post_data['ReportsUserSaved']['item_fn_display']?>"=="Number"){
								
								//return this.point.name+"<br>"+this.y;
								return this.y;
							}
							if("<?=$post_data['ReportsUserSaved']['item_fn_display']?>"=="Percentage"){
								//return  this.point.name+"<br>"+Math.round(this.percentage*100)/100 + ' %';
								return  Math.round(this.percentage*100)/100 + ' %';
							}
							if("<?=$post_data['ReportsUserSaved']['item_fn_display']?>"=="Number_Percentage"){
								//return this.point.name+"<br>"+this.y+" , "+ Math.round(this.percentage*100)/100 + ' %';
								return this.y+" , "+ Math.round(this.percentage*100)/100 + ' %';
							}
							
						},
						distance: datalabel_distance,
						color:datalabel_color,
						style:{
							fontFamily: 'Arial',
							fontSize:'12px',
							margin: 0,
						}
					}
				},
			},
			tooltip: {
				useHTML: true,
				formatter: function () {
					return '<span style="font-family:arial;font-size:11px;line-height:34px;margin:0;"><b>Proportion of '+this.key+': ' + this.y +'</b></span>';
				}
			},
			legend: {
				enabled: enabled_legend,
				margin: 10,
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
					return this.name;                    
				},
				itemStyle: {
					fontFamily: 'Arial',
					fontSize:'12px',
					margin: 0,
					fontWeight: 'normal !important;',
				}
			},
			exporting: {enabled: false},
			credits: {
				enabled: false
			},
			data:{
				table: 'chart_datatable',
			},
	};
	chart1 = new Highcharts.Chart(options,function(chart1){
          if(isslicesected){
			var i = chart1.series[0].yData.indexOf(Math.max.apply(null, chart1.series[0].yData));
			chart1.series[0].data[i].select();
		  }
    });	

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