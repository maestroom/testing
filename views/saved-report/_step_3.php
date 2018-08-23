<?php
use yii\helpers\Html;
use yii\helpers\Url;

$addon = <<< HTML
<span class="input-group-addon">
    <em class="glyphicon glyphicon-calendar"></em>
</span>
HTML;
?>
<fieldset class="one-cols-fieldset-report">
	<!--<h3 class="col-sm-12">Report Preview</h3>
	<div class="col-sm-12 add-custom-report-stap-two administration-form" id="preview-save-run">-->
	<div class="administration-form" id="preview-save-run">	
	</div>
	<div>
		<input type="hidden" id="report_saved_id" name="report_saved_id" value="0" />
	</div>
</fieldset>
<div class="button-set text-right">
	<div class="fltlft">Total Record Count - <span id="totalrecordcnt"></span></div>
	<?php $allReports_url = Url::toRoute(['saved-report/index']); ?>
	<?php if($model->report_format_id==2){?>
	<?= Html::button('Previous', ['title'=>'Previous','class' =>  'btn btn-primary','onclick'=>'$( "#tabs-step-2" ).show();$( "#tabs-step-1" ).hide();$( "#tabs-step-3" ).hide(); changesubheader(3);']) ?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
	<?= Html::button('Next', ['title'=>'Next','class' => 'btn btn-primary','id'=>'btn_add_chart','onclick'=>'$( "#tabs-step-2" ).hide();$( "#tabs-step-1" ).hide();$( "#tabs-step-3" ).hide(); addchart();']) ?>
	<?php }else{?>
		<?php if($flag=='run'){ ?>
		<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
		<?= Html::button('Export', ['title'=>'Export','class' =>  'btn btn-primary','id'=>'run1','onclick'=>'run_report();']) ?>
		<?php }else{ ?>
		<?= Html::button('Previous', ['title'=>'Previous','class' =>  'btn btn-primary','onclick'=>'$( "#tabs-step-2" ).show();$( "#tabs-step-1" ).hide();$( "#tabs-step-3" ).hide(); changesubheader(3);']) ?>
		<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'location.href="'.$allReports_url.'"'])?>
		<?php if($model->report_format_id==1){?>
			<?= Html::button('Add Chart', ['title'=>'Add Cross-Tabular or Chart Report','id'=>'btn_add_chart','class' => 'btn btn-primary','onclick'=>'$( "#tabs-step-2" ).hide();$( "#tabs-step-1" ).hide();$( "#tabs-step-3" ).hide(); addchart();']) ?>
		<?php }?>
		<?= Html::button('Update', ['title'=>'Update','class' =>  'btn btn-primary','id'=>'update','onclick'=>'update_report();']) ?>	
		<?= Html::button('Export', ['title'=>'Export','class' =>  'btn btn-primary','id'=>'run2','onclick'=>'run_report();']) ?>
		<?php } ?>
	<?php }?>
</div>
<script>
	function changesubheader(stp){
		$(".sub-heading").html("<a href='javascript:void(0);' title='Step 1: Edit Fields, Sort & Filters' class='tag-header-black'>Step 1: Edit Fields, Sort & Filters</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
	}
	function addchart(){
		$(".sub-heading").html("<a href='javascript:void(0);' class='tag-header-black' title='Step 4:  Select Format & Properties'>Step 4:  Select Format & Properties</a><div style=float:right>"+$('#reportsusersaved-report_type_id option:selected').text()+"</div>");
		$( "#tabs-step-4" ).show();
		<?php if($model->chart_format_id!=0 && $flag=='run') {?>
			next_step_summary();
		<?php }?>
	}
function update_report(){
	var form = $('#report-type-format-dates').serialize();
	$.ajax({
		type: 'post',
			url:baseUrl+'custom-report/edit-save-report&id=<?=$id?>',
			data: form,
			beforeSend:function (data) {showLoader();},
			success:function(response){
				if(response == ''){
					hideLoader();
					location.href = baseUrl+'saved-report/index';
				}else{
					alert('Something went wrong..');
					return false;
				}
				
			}
	});
}	
function save_report()
{
	var form = $('#report-type-format-dates').serialize();
	var step3='';
	if($('#tabs-step-3')){
		step3=$('#tabs-step-3').css('display');
	}
	$.ajax({
			type: 'post',
			url:baseUrl+'custom-report/save-report-popup&report_saved_id='+$('#report_saved_id').val()+'&step3='+step3,
			data: form,
			beforeSend:function (data) {showLoader();},
			success:function(response){
			hideLoader();
			if($('body').find('#save-report-access-popup').length == 0){
				$('body').append('<div class="dialog" id="save-report-access-popup" title="Save Report"></div>');
			}
			$('#save-report-access-popup').html('').html(response);		
			$('#save-report-access-popup').dialog({ 
				modal: true,
				width:'80em',
				height:692,
				create: function(event, ui){ 
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
				close:function(){
					$(this).dialog('destroy').remove();
				},
				buttons: [
						{ 
							text: "Cancel", 
							"class": 'btn btn-primary',
							"title": 'Cancel',
							click: function () { 
								$(this).dialog('destroy').remove();
							} 
						},
						{ 
							text: "Save", 
							"class": 'btn btn-primary',
							"title": 'Save',
							click: function () { 
								
								var report_name = $('#reportsusersaved-custom_report_name');
								var save_report_to = $('#reportsusersaved-report_save_to');
								var sharereport = $('#reportsusersaved-share_report_by');
								var sharedchk = $('#show_by_content').find('.chk:checked');
								
								var has_error = false;
								if($.trim(report_name.val()) == ''){
									report_name.closest('div.form-group').removeClass('has-success').addClass('has-error');
									report_name.parent().parent().parent().find('.help-block').html('Report Name cannot be blank.');
									has_error = true;
								}
								
								if(save_report_to.val() == 2 && sharereport.val() == '') {
									sharereport.closest('div.form-group').removeClass('has-success').addClass('has-error');
									sharereport.parent().parent().parent().find('.help-block').html('Share Report cannot be blank.');
									has_error = true;
								} 
								if(save_report_to.val() == 2 && sharedchk.length == 0) {
									//alert('dasnfkjdas if');
									$('.field-reportsusersaved-show_by').removeClass('has-success').addClass('has-error');
									$('.field-reportsusersaved-show_by').find('.help-block').addClass('clear').html('Option cannot be blank.');
									has_error = true;
								}
								if(save_report_to.val() == 2 && sharedchk.length > 0) {
									$('.field-reportsusersaved-show_by').removeClass('has-error').addClass('has-success');
									$('.field-reportsusersaved-show_by').find('.help-block').addClass('clear').html('');
								}
								if(!has_error){
									var reportdata = $('#report-type-format-dates').serialize();
									var usersaveddata = $('#frm_popup_ReportsUserSaved').serialize();
									$.ajax({
										url:baseUrl+'custom-report/save-report',
										type:'post',
										data:reportdata+'&'+usersaveddata,
										success:function(response){
											var obj = JSON.parse(response);
											if(obj.reports_saved_id!='' && obj.reports_saved_id!=0) {
												$('#report_saved_id').val(obj.reports_saved_id);
												$('button#save').hide();
												location.href = baseUrl+'saved-report/index';
												$('#save-report-access-popup').dialog('destroy').remove();
											} else {
												alert('Oops! something went wrong.');
												return false;
											}
										}
									});	
								}
							}
						}
					]
			});
	}});
}
</script>
<noscript></noscript>