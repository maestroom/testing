<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.tabletojson.js',['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<fieldset class="one-cols-fieldset-report overflow-auto">
	<div class="administration-form format_and_properties">
			<?php if(strpos(strtolower($modeReportsChartFormat->chart_format),"bar") !== false){?>
				<?=$this->renderAjax('bar_chart',['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]);?>
			<?php }?>
			<?php if(strpos(strtolower($modeReportsChartFormat->chart_format),"column") !== false){?>
				<?=$this->renderAjax('column_chart',['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]);?>
			<?php }?>
			<?php if(strpos(strtolower($modeReportsChartFormat->chart_format),"line") !== false){?>
				<?=$this->renderAjax('line_chart',['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]);?>
			<?php }?>
			<?php if(strpos(strtolower($modeReportsChartFormat->chart_format),"circle") !== false){?>
				<?=$this->renderAjax('pie_chart',['post_data'=>$post_data,'report_data'=>$report_data,'table_fields_detail' =>$table_fields_detail,'query'=>$query,'modeReportsChartFormat'=>$modeReportsChartFormat,'ytable_fields_detail'=>$ytable_fields_detail]);?>
			<?php }?>
	</div>
</fieldset>

<div class="button-set text-right">
	<?php $allReports_url = Url::toRoute(['saved-report/index']); ?>
	<?php if(isset($post_data['flag']) && $post_data['flag']=='run'){?>
		<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
	<?php }else{?>
		<?= Html::button('Previous', ['title'=>'Previous','class' =>  'btn btn-primary','onclick'=>'$( "#tabs-step-1" ).hide();$( "#tabs-step-2" ).hide(); $( "#tabs-step-3" ).hide();$( "#tabs-step-4" ).hide();$( "#tabs-step-6" ).hide();$( "#tabs-step-5" ).show(); changeheader5();']) ?>
		<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
	<?php }?>
	<?php /*if(isset($flag) && ($flag=='run' || $flag=='edit')){?>
		<?= Html::button('Update', ['title'=>'Update','class' =>  'btn btn-primary','id'=>'update','onclick'=>'update_report();']) ?>
	<?php }else{?>
		<?= Html::button('Save', ['title'=>'Save','class' =>  'btn btn-primary','id'=>'save','onclick'=>'save_report();']) ?>
	<?php }*/?>
	 <div class="dropup rightpl3">
	  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Export
	  <span class="caret"></span></button>
	    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		<a class="dropdown-item" href="javascript:void(0);" onclick="pullinpdf();" title="PDF Export">Export Chart Report</a>
		<a class="dropdown-item" href="javascript:void(0);" onclick="export_report();" title="Export">Export Tabular Report</a>
	  </div>
	</div> 
	<?php if(isset($post_data['flag']) && $post_data['flag']!='run'){?>
	<div class="dropup rightpl3">
	  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Update
	  <span class="caret"></span></button>
	    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		<a class="dropdown-item" href="javascript:void(0);" onclick="update_report();" title="Save Over Existing">Save Over Existing</a>
		<a class="dropdown-item" href="javascript:void(0);" onclick="save_report();" title="Save As New">Save As New</a>
	  </div>
	</div> 
	<?php }?>
	<input type="hidden" name="image_data" id="image_data" value="" />
</div>
<script>
	function changeheader5(){
		$(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 5: Summarize Data'>Step 5: Summarize Data</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
	}
	function export_report(){
		var imageData = $('#dynamic_chart').highcharts().createCanvas();
		$("#image_data").val(imageData);
		run_report();
	}
	function pullinpdf(){
		if($('#datatable_section').css('display') == 'none'){
			onlypdf();
		}else{
				var imageData = $('#dynamic_chart').highcharts().createCanvas();
				var form = document.createElement('form');
				$(form).attr("id",'pdfcalendar');
				$(form).attr("style",'display:none');
			    var element1 = document.createElement("input"); 
			    $(form).attr("method", "POST");
			    $(form).attr("action", baseUrl+"pdf/exportwithchart");   
			    $(form).append('<input type="hidden" name="image_data" value="'+imageData+'" />');
			    $(form).append('<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />');
			   	var myRows = [];
				var table = $('#chart_datatable').tableToJSON(); // Convert the table into a javascript object
				$(form).append("<input type='hidden' name='table_data' value='"+JSON.stringify(table)+"' />");
				var datatable_location=$('#ch_datatable_location').val();
				$(form).append("<input type='hidden' name='datatable_location' value='"+datatable_location+"' />");
				var title_location = $('#ch_title_location').val();
				$(form).append("<input type='hidden' name='title_location' value='"+title_location+"' />");
		        var title = $('#ch_title').val();
		        $(form).append("<input type='hidden' name='title' value='"+title+"' />");
				$('body').append(form);
			    hideLoader();
			    form.submit();
			    $("#pdfcalendar").remove();
        }
	}
</script>
<noscript></noscript>
