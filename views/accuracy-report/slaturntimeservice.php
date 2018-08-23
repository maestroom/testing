<?php 
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use kartik\grid\GridView;
	use kartik\grid\datetimepicker;

	$this->title = 'SLA Turn-Time by Client/Case';
	$this->params['breadcrumbs'][] = $this->title;
	
	$filter_start_date= (isset($filter_data->start_date) ? json_encode(array('start_date' => $filter_data->start_date)) : json_encode(array(0)));
	$filter_end_date = (isset($filter_data->end_date) ? json_encode(array('end_date' => $filter_data->end_date)) : json_encode(array(0)));
	$filter_task_status = (isset($filter_data->task_status) ? json_encode(array('task_status' => $filter_data->task_status)) : json_encode(array(0)));
	$filter_servicestatus = (isset($filter_data->servicestatus) ? json_encode(array('servicestatus' => $filter_data->servicestatus)) : json_encode(array(0)));
	$filter_team_location = (isset($filter_data->team_location) ? json_encode(array('team_location' => $filter_data->team_location)) : json_encode(array(0)));
	$filter_datedropdown = (isset($filter_data->datedropdown) ? json_encode(array('datedropdown' => $filter_data->datedropdown)) : json_encode(array(0)));
?>
<div class="right-main-container" id="media_container">
	<?php $form = ActiveForm::begin([
		'id' => 'add-turntimeservice-form',
		'action' => '@web/index.php?r=accuracy-report/turnaroundtimebyservicedata',
	]); ?>
	<div class="">
    	<fieldset class="two-cols-fieldset workflow-management">
        	<div class="create-form">
					<div class="form-group field-team-team_name required">
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label">Select Project Submit Date<span class="require-asterisk">*</span> :</label>
							</div>
							<div class="col-md-2">
								<div class="input-group calender-group">
									 <input type="text" class="form-control" name="start_date" id="start_date" value="<?php echo $filter_data->start_date; ?>" placeholder="Start Date" />
								</div>	
								<div class="help-block" class="form-control" id="start_date_error"></div>
							</div>
							<div class="col-md-2">
								<div class="input-group calender-group">
									 <input type="text" class="form-control" name="end_date" id="end_date" placeholder="End Date"  value="<?php echo $filter_data->end_date; ?>" />
								</div>	
								<div class="help-block" class="form-control" id="end_date_error"></div>
							</div>
							<div class="col-md-1" style="text-align:center">
								<span class="strong">OR</span>
							</div>
							<div class="col-md-2">
								<select name="datedropdown" id="datedropdown" class="form-control SelectDataprocessDropDown" onchange="if(this.value!=0){$('#start_date').val(null);$('#end_date').val(null);}//datechange();">
									<option value="0"></option>
									<option value="1" <?php if(isset($filter_data) && $filter_data->datedropdown==1){echo "Selected";}?>>Today</option>
									<option value="2" <?php if(isset($filter_data) && $filter_data->datedropdown==2){echo "Selected";}?>>Yesterday</option>
									<option value="3" <?php if(isset($filter_data) && $filter_data->datedropdown==3){echo "Selected";}?>>Last Week</option>
									<option value="4" <?php if(isset($filter_data) && $filter_data->datedropdown==4){echo "Selected";}?>>Last Month</option>
									<option value="5" <?php if(isset($filter_data) && $filter_data->datedropdown==5){echo "Selected";}?>>Last Year</option>
								</select>
							</div>
						</div>
						
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label">Select Project Status<span class="require-asterisk">*</span> :</label>
							</div>
							<div class="col-md-2">
                                                            <input type="checkbox" name="chkprojectstatus" id="projectstatuss" value="projectstatus" <?php if(isset($filter_data->task_status) && !empty($filter_data->task_status)){ echo "Checked"; } ?> aria-label="Select Project Status" /><label for="projectstatuss"><span class="sr-only">Select Project Status</span></label>
							</div>
							<div class="clearfix">&nbsp;</div>
				    			<!-- Get all Projectstatus -->
				    			<div class="col-md-7 col-md-offset-4">
					    			<div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
					    			if(isset($filter_data->task_status) && !empty($filter_data->task_status)){
					    				echo "block";
					    			}else{
					    				echo "none";
					    			} 
					    			?>" id="displayprojectstatus"> 
					    				<span>
											<ul class='by_teamloc_sub custom-full-width' id="by_projectstatus" style='width: 100%!important;list-style:none;'></ul>
										</span>
										<div style="">
											<div class="col-md-3 custom-full-width">
												<input id="projectstatusall" class="form-control" name="Report[projectstatusall]" type="checkbox"  class="projectstatusall" onclick="" aria-label="Select All">
												<label class="form_label" for="projectstatusall">Select All</label>
											</div>
										</div>	
									</div>
									<div class="help-block" class="form-control" id="projectstatuss_error"></div>
									<div class="clearfix">&nbsp;</div>
				    			</div>
						</div>
						
						<!-- Select Service -->
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								 <label class="form_label required" for="nolabel-55"> Select Service<span class="require-asterisk">*</span> :</label>
							</div>
							<span id="select_client_case" style="display:none;"></span>
							<div class="col-md-7">
								<div class="custom-inline-block-width" >
				     				<input type="checkbox" value="client" class="form-control select_client" id="chkselectservice" name="chkselectservice" <?php if(isset($filter_data->servicestatus) && !empty($filter_data->servicestatus)){echo "checked";} ?> aria-label="Select Service">
                                                                <label for="chkselectservice"><span class="sr-only">Select Service</span></label>
							 	</div>
			    			</div>
			    			
							<!-- Get all Select Service -->
			    			<div class="col-md-7 col-md-offset-4">
				    			<div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
					    			if(isset($filter_data->servicestatus) && !empty($filter_data->servicestatus)){
					    				echo "block";
					    			}else{
					    				echo "none";
					    			} 
				    			?>;" id="displayservicestatus">
			    					<span>
										<ul class='by_teamloc_sub custom-full-width' id="by_servicestatus" style='width: 100%!important;list-style:none;'>
											<?php foreach($team_services as $key=>$teamser){ ?>
												<?php
													if(isset($filter_data->servicestatus) && !empty($filter_data->servicestatus)){
														$checked="";
														if(in_array($key,$filter_data->servicestatus))
															$checked='checked="checked"';
													} 
												?>
												<li><input type="checkbox" name="servicestatus[]" <?php echo $checked; ?> class="servicestatus" id="servicestatus_<?= $key ?>" value="<?= $key ?>" aria-label="<?=$teamser; ?>"><label class="servicestatuslabel" for="servicestatus_<?= $key ?>"><?=$teamser; ?></label></li>
											<?php } ?>
										</ul>
									</span>
									<div style="">
										<div class="col-md-3 custom-full-width">
											<input id="servicestatusall" class="form-control" name="Report[servicestatusall]" type="checkbox"  class="servicestatusall" onclick="" aria-label="Select All">
											<label class="form_label" for="servicestatusall">Select All</label>
										</div>
									</div>	
								</div>
								<div class="help-block" class="form-control" id="servicestatus_error"></div>
								<div class="clearfix">&nbsp;</div>
			    			</div>
				    	</div>
				    	
				    	<!-- Select Service -->
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								 <label class="form_label required" for="nolabel-55"> Select Service Location: </label>
							</div>
							<div class="col-md-7">
								<div class="custom-inline-block-width">
				     				<input type="checkbox" class="form-control select_client" id="chkselectserviceloc" name="chkselectserviceloc" <?php if(isset($filter_data->teamlocation) && !empty($filter_data->teamlocation)) {echo "checked";} ?> aria-label="Select Service Location" />
									<label for="chkselectserviceloc"></label>
							 	</div>
			    			</div>
			    			<div class="clearfix">&nbsp;</div>
			    			
			    			<!-- Get all Select Service -->
			    			<div class="col-md-7 col-md-offset-4">
				    			<div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
				    				if(isset($filter_data->teamlocation) && !empty($filter_data->teamlocation)){
				    					echo "block";
				    				}else{
				    					echo "none";
				    				}
				    			?>;" id="displayservicelocstatus"> 
				    				<span>
										<ul class='by_teamloc_sub custom-full-width' id="by_teamlocation" style='width: 100%!important;list-style:none;'>
											<?php foreach($teamLocation as $key=>$team_loc){ ?>
												<?php
													if(isset($filter_data->teamlocation) && !empty($filter_data->teamlocation)){
														$checked="";
														if(in_array($key, $filter_data->teamlocation))
															$checked='checked="checked"';
													} 
												?>
												<li>
													<input type="checkbox" name="teamlocation[]" <?php echo $checked; ?> id="teamlocation_<?= $key ?>" class="teamlocation" value="<?= $key ?>" aria-label="<?php echo $team_loc ?>" /><label for="teamlocation_<?= $key ?>" class="teamloclabel"><?php echo $team_loc ?></label>
												</li>
											<?php } ?>
										</ul>
									</span>
									<div style="">
										<div class="col-md-3 custom-full-width">
											<input id="servicelocstatusall" class="form-control servicelocstatusall" name="Report[servicelocstatusall]" type="checkbox"  onclick="" aria-label="Select All">
											<label class="form_label" for="servicelocstatusall">Select All</label>
										</div>
									</div>	
								</div>
								<div class="clearfix">&nbsp;</div>
			    			</div>
				    	</div>
				    	
					</div>
				</div>
		</fieldset>
	</div>
	<div class=" button-set text-right">
		<button onclick="" title="Clear" class="btn btn-primary" id="clearclientcase" type="button" name="yt1">Clear</button>
		<button onclick="" title="Run" class="btn btn-primary" id="requestclientcaserun" type="button" name="yt0">Run</button>
	</div>
	<?php ActiveForm::end(); ?>
</div>	

<script>
	
$(function () {
	if($('#projectstatuss').is(':checked')) {
		var filter_data = '<?php echo json_encode($filter_data); ?>';
		selectprojectstatus(filter_data);
    }
});

/**
 * select client service
 */
function selectprojectstatus(filter_data)
{
	$.ajax({
        type: "POST",
        url: baseUrl + "accuracy-report/get-project-status",
           data: 'filter_data='+filter_data,
           cache: false,
           success: function (data) {
            	$('#displayprojectstatus').show();
 	       		$('#by_projectstatus').html(data);
 	       		$('input').customInput();
	       }
  	});
}

$(function () {
	var start_date = datePickerController.createDatePicker({             
	 formElements: { "start_date": "%Y-%m-%d" },         
	 callbackFunctions:{
		"dateset":[ function (){
			var start_value = $('#start_date').val();
			if(start_value.length > 0){
				$('#ddduration').val('0');
		    }
			$('#start_date_error').empty();
			$('#start_date_error').parent().removeClass('has-error');
		}],
	  }
	});   
	var end_date = datePickerController.createDatePicker({             
	 formElements: { "end_date": "%Y-%m-%d" },   
	 callbackFunctions:{
		"dateset":[ function (){
			var end_value = $('#end_date').val();
			if(end_value.length > 0){
				$('#ddduration').val('0');
		    }
			$('#end_date_error').empty();
			$('#end_date_error').parent().removeClass('has-error');
		}],
	  }      
	});
});	

$('#clearclientcase').click(function(){
	$('#start_date').val('');
	$('#end_date').val('');
	$('#datedropdown').val('');
	$('input:checkbox').removeAttr('checked');
	$('#projectstatuss').siblings().removeClass('checked');
	$('#chkselectservice').siblings().removeClass('checked');
	$('#chkselectserviceloc').siblings().removeClass('checked');
	$('#displayprojectstatus').hide();
 	$('#displayservicestatus').hide();
 	$('#displayservicelocstatus').hide();
    return false;
});

$('#projectstatuss').change(function(){
	$('#displayprojectstatus').hide();
	if($('#projectstatuss').is(':checked')){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var datedropdown = $('#datedropdown').val();
		if((start_date=='' || end_date=='') && datedropdown==0){
			alert("Please Select Project Submit Date");
			return false;
		}
		$.ajax({
            type: "POST",
            url: baseUrl + "accuracy-report/get-project-status",
	           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown,
	           cache: false,
	           success: function (data) {
	           		$('#displayprojectstatus').show();
		       		$('#by_projectstatus').html(data);
		       		$('input').customInput();
		       }
      	});
		return false;
	}
});

/**
 * get clients 
 */
$('#selectclient').change(function(){
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	var datedropdown = $('#datedropdown').val();
	$.ajax({
        type: "POST",
        url: baseUrl + "accuracy-report/get-client-case-criteria",
           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown+'&type=client',
           cache: false,
           beforeSend: function(){
        	   $('#displaystatusclientcase').hide();
        	   $('#clientcasestatusall').prop('checked',false);
           },
           success: function (data) {
           		$('#displaystatusclient').show();
           		$('#by_client').html(data);
	       		$('input').customInput();
	       }
  	});
});

$('#selectrclientcases').change(function(){
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	var datedropdown = $('#datedropdown').val();
	$.ajax({
        type: "POST",
        url: baseUrl + "accuracy-report/get-client-case-criteria",
           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown+'&type=clientcase',
           cache: false,
           beforeSend: function(){
        	   $('#displaystatusclient').hide();
        	   $('#clientstatusall').prop('checked',false);
           },
           success: function (data) {
        	    $('#displaystatusclientcase').show();
        		$('#by_clientcase').html(data);
	       		$('input').customInput();
	       }
  	});
});

$('#chkselectservice').change(function(){
	$('#displayservicestatus').hide();
	if($('#chkselectservice').is(':checked')){
		$('#displayservicestatus').show();
	}
});

$('#chkselectserviceloc').change(function(){
	$('#displayservicelocstatus').hide();
	if($('#chkselectserviceloc').is(':checked')){
		$('#displayservicelocstatus').show();
	}
});

$('#requestclientcaserun').click(function()
{
	var error="<strong>Please Fix Below Given Error:-</strong><br><br>";
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	var datedrop =$("#datedropdown").val();
	var projectstatuss = $('#projectstatuss').val();
	var chkclientcases = $('.select_client').is(':checked');

	if(!$('#chkselectservice').is(":checked")){
		error+="- Select Service. <br>";
		$('#servicestatus_error').html('Select Service');
		$('#servicestatus_error').parent().addClass('has-error');
	}

	if($('#chkselectservice').is(":checked")){
		if(!$('input[name="servicestatus[]"]').is(':checked')){
			error+="- Select Task Status. <br>";
			$('#servicestatus_error').html('Select Task Status');
			$('#servicestatus_error').parent().addClass('has-error');
		}
	}
	
	if(!$('#projectstatuss').is(":checked")){
		error+="- Select Task Status. <br>";
		$('#projectstatuss_error').html('Select Task Status');
		$('#projectstatuss_error').parent().addClass('has-error');
	}

	if ($('#projectstatuss').is(":checked"))
	{
		if(!$('input[name="task_status[]"]').is(':checked')){
			error+="- Select Task Status. <br>";
			$('#projectstatuss_error').html('Select Task Status');
			$('#projectstatuss_error').parent().addClass('has-error');
		}
	}

	if(datedrop == 0 || datedrop == ""){
		if(start_date==''){
			error+="- Select project submit start date or date range is required field. <br>";
			$('#start_date_error').html('Select project submit start date or date range is required field ');
			$('#start_date_error').parent().addClass('has-error');
		}
		if(end_date==''){
			$('#end_date_error').html('Select project submit end date or date range is required field ');
			$('#end_date_error').parent().addClass('has-error');
			error+="- Select project submit end date or date range is required field. <br>";
		}
	 }

	if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
		openPopup();
		$('#errorContent').html(error);
	}else{
		console.log('submit');
		$('#add-turntimeservice-form').submit();
	}	
});

$('#projectstatusall').change(function(){
	$('.pstatus').prop('checked',false);
	$('.statusname').removeClass('checked');
	if($('#projectstatusall').is(':checked')){
		$('.pstatus').prop('checked',true);
		$('.statusname').addClass('checked');
	}		
});

$('#clientstatusall').change(function(){
	$('.client').prop('checked',false);
	$('.clientlabel').removeClass('checked');
	if($('#clientstatusall').is(':checked')){
		$('.client').prop('checked',true);
		$('.clientlabel').addClass('checked');
	}
});

$('#clientcasestatusall').change(function(){
	$('.clientcase').prop('checked',false);
	$('.clientcaselabel').removeClass('checked');
	if($('#clientcasestatusall').is(':checked')){
		$('.clientcase').prop('checked',true);
		$('.clientcaselabel').addClass('checked');
	}
});

$('#servicestatusall').change(function(){
	$('.servicestatus').prop('checked',false);
	$('.servicestatuslabel').removeClass('checked');	
	if($('#servicestatusall').is(':checked')){
		$('.servicestatus').prop('checked',true);
		$('.servicestatuslabel').addClass('checked');	
	}
});


$('#servicelocstatusall').change(function(){
	$('.teamlocation').prop('checked',false);
	$('.teamloclabel').removeClass('checked');	
	if($('#servicelocstatusall').is(':checked')){
		$('.teamlocation').prop('checked',true);
		$('.teamloclabel').addClass('checked');	
	}
});
</script>
<noscript></noscript>
