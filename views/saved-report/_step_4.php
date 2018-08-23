<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
$addon = <<< HTML
<span class="input-group-addon">
    <em class="glyphicon glyphicon-calendar"></em>
</span>
HTML;
?>
<style>
.label_header_size {
	font-size: 14px!important;
}
</style>
<fieldset class="one-cols-fieldset-report overflow-auto">
        
	<div class="administration-form format_and_properties">	
		<label class="form_label label_header_size" style="margin: 0px 14px;"><strong>Select Format</strong></label>
                <legend class="sr-only">Select Format</legend>
		<ul style="padding-left:6px;">
		<?php
		$images=['Bar Basic'=>'BarChart.png',
		'Bar Clustered'=>'BarClustered.png',
		'Bar Stacked'=>'BarStacked.png',
		'Column Basic'=>'ColumnBasic.png',
		'Column Clustered'=>'ColumnClustered.png',
		'Column Stacked'=>'ColumnStacked.png',
		'Line Basic'=>'LineBasic.png',
		'Line Clustered'=>'LineClustered.png',
		'Circle Pie'=>'CirclePie.png',
		'Circle Donut'=>'CircleDonut.png'];
		$titles=['Bar Basic'=>'
Display:Horizontal bars. 
Use to:Compare items or to show change over time within one series.',
		'Bar Clustered'=>'
Display:Horizontal bars grouped in clusters. 
Use to:Compare items or to show change over time across multiple series.',
		'Bar Stacked'=>'
Display:Horizontal bars grouped in subgroups / stacks. 
Use to:See changes in parts of a whole over time across multiple series.',
		'Column Basic'=>'
Display:Vertical columns. 
Use to:Compare items or to show change over time within one series.',
		'Column Clustered'=>'
Display:Vertical columns grouped in clusters. 
Use to:Compare items or to show change over time across multiple series.',
		'Column Stacked'=>'
Display:Vertical columns grouped in subgroups / stacks. 
Use to:See changes in parts of a whole over time across multiple series.',
		'Line Basic'=>'
Display:A series of markers connected by a line. 
Use to:See a trend in data over intervals of time within one series.',
		'Line Clustered'=>'
Display:A series of markers connected by a line. 
Use to:See a trend in data over intervals of time across multiple series.',
		'Circle Pie'=>'
Display:Slices of a pie. 
Use to:See parts of a whole.',
		'Circle Donut'=>'
Display:Pieces of a donut. 
Use to:See parts of a whole.'];
		 $i=1; foreach($modeReportsChartFormat as $id=>$format){ if (!in_array($format,array('Bar Basic','Bar Clustered','Column Basic','Column Clustered','Line Basic','Line Clustered','Circle Pie','Circle Donut'))){continue;}?>
			<li title="<?=$titles[$format]?>"><input aria-setsize="8" aria-posinset="<?= $i ?>" <?php if($model->chart_format_id==$id){?> checked="checked" <?php }?> title="<?=$titles[$format]?>" data-type="<?=$format?>" class="chart_format" type="radio" name="chart_format_id" value="<?=$id?>" id="chart_format_id_<?=$id?>"> <label for="chart_format_id_<?=$id?>" class="">&nbsp;</label><strong><?=str_replace(" ","<br><span>",$format."</span>");?><span tabindex="0"><img src="<?=Url::base(true).'/images/'.$images[$format]?>" alt="<?=$titles[$format]?>" width="32" height="32" title="<?=$titles[$format]?>"></span></strong></li>
		<?php $i++; } ?>
		</ul>	
		<!--  <div class="col-sm-12" id="configure_chart_properties" style="display:none;"> -->
                <div class="col-sm-8" id="configure_chart_properties" style="display:none;margin: 0px 14px;padding-left:0px;">
				<label class="form_label label_header_size"><strong>Configure Chart Properties</strong></label>
				<div class="form-group" style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_title">Title</label>
						</div>
						<div class="col-md-8">
							<input type="text" id="ch_title" name="ReportsUserSaved[title]" maxlength="150" class="form-control" placeholder="Enter Chart Title" />
							<div class="help-block"></div>
						</div>
					</div>
				</div>
				<div class="form-group" style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_title_location">Title Location</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[title_location]',
    'data' => ['TL'=>'Top Left','TC'=>'Top Center'],
    'options' => ['placeholder' => 'Select Title Location', "id"=>"ch_title_location"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				<div class="form-group" style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_legend_location">Legend Location</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[legend_location]',
    'data' => ['R'=>'Right','B'=>'Bottom'],
    'options' => ['placeholder' => 'Select Legend Location', "id"=>"ch_legend_location"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				<div class="form-group" style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_datatable_location">Data Table Location</label>
						</div>
						<div class="col-md-8">
						<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[datatable_location]',
    'data' => [''=>'','R'=>'Right','B'=>'Bottom'],
    'options' => ['placeholder' => 'Select Data Table Location', "id"=>"ch_datatable_location"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
	<div class="form-group required" data-field='ch_dimensions' style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3 ">
							<label class="form_label" for="ch_dimensions">Dimensions</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[dimension]',
    'data' => ['2D'=>'2D','3D'=>'3D'],
    'options' => ['placeholder' => 'Select Dimensions', "id"=>"ch_dimensions",'aria-required' => 'true','nolabel' => true],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
			</div>
		<div class="col-sm-8" id="bar_properties" style="display:none;margin: 0px 14px;padding-left:0px;">
				<label class="form_label label_header_size"><strong id="bar_title">Configure Bar Properties</strong></label>
				<div class="form-group" id="chartype_pp_gird_line" style="display:none;padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_bar_p_grid_lines">Grid Lines</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[grid_line]',
    'data' => ['H'=>'Horizontal','V'=>'Vertical'],
    'options' => ['multiple'=>true,'placeholder' => 'Select Grid Lines', "id"=>"ch_bar_p_grid_lines"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				<div class="form-group required" data-field='ch_bar_p_shape' id="chartype_pp_shape" style="display:none;padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3 ">
							<label class="form_label" for="ch_bar_p_shape">Shape</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[shape]',
    'data' => ['Rectangular'=>'Rectangular','Cylinder'=>'Cylinder'],
    'options' => ['placeholder' => 'Select Shape', "id"=>"ch_bar_p_shape"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				
				
				<div class="form-group" id="chartype_pp_marker" style="display:none;padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_bar_p_markers">Markers</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[markers]',
    'data' => [''=>'','circle'=>'Circle', 'square'=>'Square', 'diamond'=>'Diamond'],
    'options' => ['placeholder' => 'Select Markers', "id"=>"ch_bar_p_markers"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				
				<div class="form-group required" data-field='ch_bar_p_fill' id="chartype_pp_fill"  style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3 ">
							<label class="form_label" for="ch_bar_p_fill">Fill</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[fill]',
    'data' => ['Solid'=>'Solid','Gradient'=>'Gradient'],
    'options' => ['placeholder' => 'Select Fill', "id"=>"ch_bar_p_fill"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				
					
				<div class="form-group" id="ch_bar_pp_slice" style="display:none;padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_bar_p_slice">Slice Position</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[slice_position]',
    'data' => ['Connected'=>'Connected', 'Exploded'=>'Exploded'],
    'options' => ['placeholder' => 'Select Slice Position', "id"=>"ch_bar_p_slice"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
				
			
				<div class="form-group" style="padding-left:40px;">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label" for="ch_bar_p_data_label_location">Data Label Location</label>
						</div>
						<div class="col-md-8">
							<?php echo Select2::widget([
    'name' => 'ReportsUserSaved[data_label_location]',
    'data' => ['Inside'=>'Inside','Outside'=>'Outside'],
    'options' => ['placeholder' => 'Select Data Label Location', "id"=>"ch_bar_p_data_label_location"],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);?><div class="help-block"></div>
						</div>
					</div>
				</div>
			
			
			</div>
			<div class="col-sm-8" id="axis_properties" style="display:none;margin: 0px 14px;padding-left:0px;">
				<label class="form_label label_header_size"><strong>Configure Axis Properties</strong></label>
				<!-- main -->
				<div class="form-group" id="Y_axis" style="display:none;padding-left:40px;">
						<div class="row input-field">
							<div class="col-md-3">
								<label class="form_label" for="ch_axis_y_label_direction">Y Axis Label Direction</label>
							</div>
							<div class="col-md-8">
								<?php echo Select2::widget([
									'name' => 'ReportsUserSaved[y_axis_location]',
									'data' => ['Diagonal'=>'Diagonal','Horizontal'=>'Horizontal','Vertical'=>'Vertical'],
									'options' => ['placeholder' => 'Select Y Axis Label Direction', "id"=>"ch_axis_y_label_direction"],
									'pluginOptions' => [
										'allowClear' => true
									],
								]);?>
								<div class="help-block"></div>
							</div>
						</div>
				</div>

				<div class="form-group" id="X_axis" style="display:none;padding-left:40px;">
						<div class="row input-field">
							<div class="col-md-3">
								<label class="form_label" for="ch_axis_x_label_direction">X Axis Label Direction</label>
							</div>
							<div class="col-md-8">
								<?php echo Select2::widget([
									'name' => 'ReportsUserSaved[x_axis_location]',
									'data' => ['Diagonal'=>'Diagonal','Horizontal'=>'Horizontal','Vertical'=>'Vertical'],
									'options' => ['placeholder' => 'Select X Axis Label Direction', "id"=>"ch_axis_x_label_direction"],
									'pluginOptions' => [
										'allowClear' => true
									],
								]);?><div class="help-block"></div>
							</div>
						</div>
				</div>	

			</div>
	</div>
	<div>
	</div>
	<input type="hidden" value="" id="old_type">
	<input type="hidden" value="" id="current_type">
	
</fieldset>
<div class="button-set text-right">
	
	<?= Html::button('Previous', ['title'=>'Previous','class' =>  'btn btn-primary','onclick'=>'$( "#tabs-step-4" ).hide(); post_step();']) ?>
	<?php $allReports_url = Url::toRoute(['saved-report/index']); ?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
	<?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'run','onclick'=>'next_step_summary();']) ?>
</div>
<script>
	
$(function(){
	var obj=$('.chart_format:checked');
	var type=$(obj).data('type');
	firastchartbase='';
	if(type=='Column Basic' || type=='Column Clustered' || type=='Column Stacked'){
			firastchartbase='Column';
		}
		if(type=='Bar Basic' || type=='Bar Clustered' || type=='Bar Stacked'){
			firastchartbase='Bar';
		}
		if(type=='Line Basic' || type=='Line Clustered'){
			firastchartbase='Line';
		}
		if(type=='Circle Pie' || type=='Circle Donut'){
			firastchartbase='Circle';
		}
	$("#old_type").val(firastchartbase);
	$("#current_type").val(firastchartbase);
	$("#ch_bar_pp_slice").hide();
	$("#chartype_pp_gird_line").hide();
	$("#chartype_pp_marker").hide();
	$("#chartype_pp_shape").hide();
	$("#chartype_pp_fill").hide();
	$("#ch_bar_pp_slice").val('').change();
	$("#chartype_pp_gird_line").val('').change();
	$("#ch_axis_y_label_direction").val('').change();
	$("#ch_axis_x_label_direction").val('').change();
	$("#ch_bar_p_shape").val('').change();
	$("#chartype_pp_marker").val('').change();
	if(type=='Bar Basic' || type=='Bar Clustered'){
			$("#chartype_pp_gird_line").show();
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="">Select Data Label Location</option><option value="Inside">Inside</option><option value="Outside">Outside</option>');
			$("#ch_bar_p_data_label_location").val("<?=$model->data_label_location?>").change();
			$("#bar_title").html('Configure Bar Properties');
			$("#configure_chart_properties").show();
			$("#chartype_pp_shape").show();
			$("#ch_title").val("<?=$model->title?>");
			$("#ch_title_location").val('<?=$model->title_location?>').change();
			$("#ch_legend_location").val('<?=$model->legend_location?>').change();
			$("#ch_datatable_location").val('<?=$model->datatable_location?>').change();
			$("#ch_dimensions").val('<?=$model->dimension?>').change();
			
			$("#bar_properties").show();

			$("#ch_bar_p_grid_lines").val(<?=json_encode(explode(",",$model->grid_line))?>).change();
			$("#ch_bar_p_shape").val('<?=$model->shape?>').change();
			$("#ch_bar_p_fill").val('<?=$model->fill?>').change();
			$("#ch_bar_p_data_label_location").val('<?=$model->data_label_location?>').change();
			
			$("#axis_properties").show();
			$("#X_axis").hide();
			$("#Y_axis").show();
			$("#ch_axis_y_label_direction").val('<?=$model->y_axis_location?>').change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
	}	
	if(type=='Column Basic' || type=='Column Clustered'){
			$("#chartype_pp_gird_line").show();
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="">Select Data Label Location</option><option value="Inside">Inside</option><option value="Outside">Outside</option>');
			$("#bar_title").html('Configure Column Properties');
			$("#chartype_pp_shape").show();
			$("#configure_chart_properties").show();
			$("#ch_title").val("<?=$model->title?>");
			$("#ch_title_location").val('<?=$model->title_location?>').change();
			$("#ch_legend_location").val('<?=$model->legend_location?>').change();
			$("#ch_datatable_location").val('<?=$model->datatable_location?>').change();
			$("#ch_dimensions").val('<?=$model->dimension?>').change();
			
			$("#bar_properties").show();
			$("#ch_bar_p_grid_lines").val(<?=json_encode(explode(",",$model->grid_line))?>).change();
			$("#ch_bar_p_shape").val('<?=$model->shape?>').change();
			$("#ch_bar_p_fill").val('<?=$model->fill?>').change();
			$("#ch_bar_p_data_label_location").val('<?=$model->data_label_location?>').change();
			$("#axis_properties").show();
			$("#Y_axis").hide();
			$("#X_axis").show();
			$("#ch_axis_x_label_direction").val('<?=$model->x_axis_location?>').change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
	}
	if(type=='Line Basic' || type=='Line Clustered'){
			$("#chartype_pp_gird_line").show();
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="Above" selected>Above</option><option value="Below">Below</option><option value="Left">Left</option><option value="Right">Right</option>');
			
			$("#bar_title").html('Configure Line Properties');
			$("#chartype_pp_marker").show();
			$("#ch_bar_p_markers").val("<?=$model->markers?>").change();
			$("#configure_chart_properties").show();
			$("#ch_title").val("<?=$model->title?>");
			$("#ch_title_location").val('<?=$model->title_location?>').change();
			$("#ch_legend_location").val('<?=$model->legend_location?>').change();
			$("#ch_datatable_location").val('<?=$model->datatable_location?>').change();
			$("#ch_dimensions").val('<?=$model->dimension?>').change();
			
			$("#bar_properties").show();
			$("#ch_bar_p_grid_lines").val(<?=json_encode(explode(",",$model->grid_line))?>).change();
			$("#ch_bar_p_shape").val('<?=$model->shape?>').change();
			$("#ch_bar_p_fill").val('<?=$model->fill?>').change();
			
			$("#axis_properties").show();
			$("#Y_axis").hide();
			$("#X_axis").show();
			$("#ch_axis_x_label_direction").val('<?=$model->x_axis_location?>').change();
			$("#ch_bar_p_data_label_location").val('<?=$model->data_label_location?>').change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
		}
	if(type=='Circle Pie' || type=='Circle Donut'){
			$("#axis_properties").hide();
			$("#chartype_pp_gird_line").hide();
			$("#configure_chart_properties").show();
			$("#ch_title").val("<?=$model->title?>");
			$("#ch_title_location").val('<?=$model->title_location?>').change();
			$("#ch_legend_location").val('<?=$model->legend_location?>').change();
			$("#ch_datatable_location").val('<?=$model->datatable_location?>').change();
			$("#ch_dimensions").val('<?=$model->dimension?>').change();
			
			$("#ch_bar_pp_slice").show();
			$("#bar_title").html('Configure Slice Properties');
			$("#bar_properties").show();
			$("#ch_bar_p_slice").val('<?=$model->slice_position?>').change();
			$("#ch_bar_p_fill").val('<?=$model->fill?>').change();
			$("#ch_bar_p_data_label_location").val('<?=$model->data_label_location?>').change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
		}	
	$("#ch_dimensions").on('change',function(){
		var type=$('.chart_format:checked').data('type');
		if(this.value == '3D'){
			if(type=='Bar Basic' || type=='Bar Clustered' || type=='Bar Stacked'){
				$("#ch_bar_p_data_label_location").val(null).change();
			}
			if(type=='Circle Pie' || type=='Circle Donut'){
				$("#ch_bar_p_data_label_location").val('Outside').change();
			}
		}else{
			if(type=='Bar Basic' || type=='Bar Clustered' || type=='Bar Stacked' || type=='Circle Pie' || type=='Circle Donut'){
				$("#ch_bar_p_data_label_location").val('Inside').change();
			}
		}
		
	});
	$('.chart_format').on('click',function(){
		var type=$(this).data('type');
		$('#tabs-step-4 .required').removeClass('has-error');
		$('#tabs-step-4 .help-block').html(null);
		var chartbase='';
		if(type=='Column Basic' || type=='Column Clustered' || type=='Column Stacked'){
			chartbase='Column';
		}
		if(type=='Bar Basic' || type=='Bar Clustered' || type=='Bar Stacked'){
			chartbase='Bar';
		}
		if(type=='Line Basic' || type=='Line Clustered'){
			chartbase='Line';
		}
		if(type=='Circle Pie' || type=='Circle Donut'){
			chartbase='Circle';
		}
		if($('#old_type').val()==''){
			$('#old_type').val(chartbase);
		}else{
			if($('#old_type').val()==chartbase){
				$('#old_type').val(chartbase);
			}else{
				$('#old_type').val($('#current_type').val());
			}
		}
		$('#current_type').val(chartbase);
		$('#configure_chart_properties').hide();
		$('#bar_properties').hide();
		$('#axis_properties').hide();

		$("#ch_bar_pp_slice").hide();
		$("#chartype_pp_gird_line").hide();
		$("#chartype_pp_marker").hide();
		$("#chartype_pp_shape").hide();
		$("#ch_bar_pp_slice").val('').change();
		$("#chartype_pp_gird_line").val('').change();
		$("#ch_axis_y_label_direction").val('').change();
		$("#ch_axis_x_label_direction").val('').change();
		$("#ch_bar_p_shape").val('').change();
		$("#chartype_pp_marker").val('').change();
		$("#chartype_pp_fill").hide();
		if(type=='Bar Basic' || type=='Bar Clustered'){
			$("#chartype_pp_gird_line").show();
			
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="">Select Data Label Location</option><option value="Inside">Inside</option><option value="Outside">Outside</option>');
			$("#ch_bar_p_data_label_location").val("Inside").change();
			$("#bar_title").html('Configure Bar Properties');
			$("#configure_chart_properties").show();
			$("#chartype_pp_shape").show();
			$("#ch_title").val(null);
			$("#ch_title_location").val('TC').change();
			$("#ch_legend_location").val('R').change();
			$("#ch_datatable_location").val(null).change();
			$("#ch_dimensions").val('2D').change();
			
			$("#bar_properties").show();
			$("#ch_bar_p_grid_lines").val('V').change();
			$("#ch_bar_p_shape").val('Rectangular').change();
			$("#ch_bar_p_fill").val('Solid').change();
			$("#ch_bar_p_data_label_location").val('Inside').change();
			
			$("#axis_properties").show();
			$("#X_axis").hide();
			$("#Y_axis").show();
			$("#ch_axis_y_label_direction").empty();
			$("#ch_axis_y_label_direction").append('<option value="">Select Y Axis Label Direction</option><option value="Diagonal">Diagonal</option><option value="Horizontal">Horizontal</option');
			$("#ch_axis_y_label_direction").val('Diagonal').change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
		}
		if(type=='Column Basic' || type=='Column Clustered'){
			$("#chartype_pp_gird_line").show();
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="">Select Data Label Location</option><option value="Inside">Inside</option><option value="Outside">Outside</option>');
			$("#ch_bar_p_data_label_location").val("Inside").change();
			$("#bar_title").html('Configure Column Properties');
			$("#chartype_pp_shape").show();
			$("#configure_chart_properties").show();
			$("#ch_title").val(null);
			$("#ch_title_location").val('TC').change();
			$("#ch_legend_location").val('R').change();
			$("#ch_datatable_location").val(null).change();
			$("#ch_dimensions").val('2D').change();
			
			$("#bar_properties").show();
			$("#ch_bar_p_grid_lines").val('H').change();
			$("#ch_bar_p_shape").val('Rectangular').change();
			$("#ch_bar_p_fill").val('Solid').change();
			$("#ch_bar_p_data_label_location").val('Inside').change();
			
			$("#axis_properties").show();
			$("#Y_axis").hide();
			$("#X_axis").show();
			$("#ch_axis_x_label_direction").empty();
			$("#ch_axis_x_label_direction").append('<option value="">Select X Axis Label Direction</option><option value="Diagonal">Diagonal</option><option value="Vertical">Vertical</option');
			$("#ch_axis_x_label_direction").val('Diagonal').change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
		}
		if(type=='Line Basic' || type=='Line Clustered'){
			
			$("#chartype_pp_gird_line").show();
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="Above" selected>Above</option><option value="Below">Below</option><option value="Left">Left</option><option value="Right">Right</option>');
			$("#bar_title").html('Configure Line Properties');
			$("#chartype_pp_marker").show();
			$("#configure_chart_properties").show();
			$("#ch_title").val(null);
			$("#ch_title_location").val('TC').change();
			$("#ch_legend_location").val('R').change();
			$("#ch_datatable_location").val(null).change();
			$("#ch_dimensions").val('2D').change();
			
			$("#bar_properties").show();
			$("#ch_bar_p_grid_lines").val('H').change();
			$("#ch_bar_p_fill").val('Solid').change();
			$("#ch_bar_p_data_label_location").val('Inside').change();
			
			$("#axis_properties").show();
			$("#Y_axis").hide();
			$("#X_axis").show();
			$("#ch_axis_x_label_direction").empty();
			$("#ch_axis_x_label_direction").append('<option value="">Select X Axis Label Direction</option><option value="Diagonal">Diagonal</option><option value="Vertical">Vertical</option');
			$("#ch_axis_x_label_direction").val('Diagonal').change();
			$("#ch_bar_p_data_label_location").val("Above").change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
		}
		if(type=='Circle Pie' || type=='Circle Donut'){
			$("#axis_properties").hide();
			$("#configure_chart_properties").show();
			$("#ch_title").val(null);
			$("#ch_title_location").val('TC').change();
			$("#ch_legend_location").val('R').change();
			$("#ch_datatable_location").val(null).change();
			$("#ch_dimensions").val('2D').change();
			
			$("#ch_bar_pp_slice").show();
			$("#bar_title").html('Configure Slice Properties');
			$("#bar_properties").show();
			$("#ch_bar_p_slice").val('Connected').change();
			$("#ch_bar_p_fill").val('Solid').change();
			$("#ch_bar_p_data_label_location").empty();
			$("#ch_bar_p_data_label_location").append('<option value="">Select Data Label Location</option><option value="Inside">Inside</option><option value="Outside">Outside</option>');
			$("#ch_bar_p_data_label_location").val("Inside").change();
			$("#chartype_pp_shape").hide();
			$("#chartype_pp_fill").hide();
		}
		$("#ch_datatable_location").val('B').change();
	});
});
function next_step_summary(){
	var x_function=$('#x_function').val();
	var x_data=$('#x_data').val();
	var y_data=$('#y_data').val();
	var x_function_display_by=$('#x_function_display_by').val();
	var y_data_display_by=$('#y_data_display_by').val();
	$( "#tabs-step-1" ).hide();
	$( "#tabs-step-2" ).hide();
	$( "#tabs-step-3" ).hide();
	$( "#tabs-step-4" ).hide();
	$( "#tabs-step-5" ).show(); 
	$(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 5: Summarize Data'>Step 5: Summarize Data</a> <div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
	var type=$('.chart_format:checked').val();
	var form = $('#report-type-format-dates').serialize();
	$.ajax({
		type: 'post',
			url:baseUrl+'saved-report/summary-report&id='+$('#saved-report-id').val(),
			data: form,
			beforeSend:function (data) {showLoader();},
			success:function(response){
				hideLoader();
				$("#form_div_step_5").html(response);
				<?php if($model->chart_format_id!=0 && $flag=='run') {?>
				next_step_view();
				<?php }?>
				
			}
	});
}
function changesubheader3(){
	 $(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 2: Select Fields, Sorts, & Filters'>Step 2: Select Fields, Sorts, & Filters</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
}
function changesubheader(stp){
 //$(".sub-heading").html("Step1 : Select Fields, Sorts, & Filters <div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
 $(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 2: Select Fields, Sorts, & Filters'>Step 2: Select Fields, Sorts, & Filters</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
}
</script>
<noscript></noscript>
