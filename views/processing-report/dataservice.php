<?php 
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use kartik\grid\GridView;
	use kartik\grid\datetimepicker;

	$this->title = 'Data Processed by Client/Case';
	$this->params['breadcrumbs'][] = $this->title;
	
	$filterteam_service = (isset($filter_data->team_service) ? $filter_data->team_service:"");
	$filterdata = (isset($filter_data->unitdata) ? json_encode(array('unitdata' => $filter_data->unitdata)) : json_encode(array(0)));
	$filterbilling = (isset($filter_data->statistics) ? json_encode(array('billingdata' => $filter_data->statistics)) : json_encode(array(0)));
	$teamlocs = (!empty($filter_data->teamloc)?json_encode($filter_data->teamloc): json_encode(array(0)));
	$chartgroupcriteria = (isset($filter_data->chartgroupcriteria) ? json_encode(array('chartgroupcriteria' => $filter_data->chartgroupcriteria)) : json_encode(array(0)));
?>
<div class="right-main-container" id="media_container">
<?php $form = ActiveForm::begin([
		'id' => 'add-processservice-form',
		'action' => '@web/index.php?r=processing-report/dataprocess-servicedata',
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
						<div class="clearfix">&nbsp;</div>
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label">  Select Service<span class="require-asterisk">*</span> :</label>
							</div>
							<div class="col-md-3">
								<div class="input-group calender-group">
									 <select name="team_service" id="team_service" class="form-control"> 
									 	<option value="">Select Team Service</option>
									 	<?php foreach($teamservices as $key=>$team){?>
									 		<option value="<?= $key ?>" <?php if($key==$filter_data->team_service){echo "selected";} ?>><?= $team; ?></option>
									 	<?php }?>
									 </select>
								</div>	
								<div class="help-block" id="team_service_error">
								</div>
							</div>
						</div> 
						<div class="clearfix">&nbsp;</div>
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label"> Select Service Locations :</label>
							</div>
							<div class="col-md-2">
								<div class="input-group calender-group">
									 <input type="checkbox" aria-label="Service Locations" name="service_location" id="service_location" value="service_location" <?php if(isset($filter_data->service_location) && $filter_data->service_location!=''){echo "checked";} ?> /><label for="service_location"></label>
								</div>	
								<div class="help-block" id="start_date_error"></div>
							</div>
							<div class="clearfix">&nbsp;</div>
							<div class="col-md-7 col-md-offset-4">
					    		<div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
										if (isset($filter_data->service_location) && $filter_data->service_location=='service_location')
											echo 'block';
										else
											echo 'none'; ?>" id="displaystatusteamlocation"> 
					    			<span>
										<ul class='by_teamloc_sub custom-full-width' id="by_teamlocation" style='width: 100%!important;list-style: none;'>
										</ul>
									</span>
									<div style="">
										<div class="col-md-3 custom-full-width">
											<input id="teamall" class="form-control" name="Report[teamall]" type="checkbox"  class="teamall" onclick="" aria-label="Select All">
											<label class="form_label" for="teamall">Select All</label>
										</div>
									</div>	
									
								</div>
				    		</div>
				   		</div> 
				   		
				   		<div class="clearfix">&nbsp;</div>
				   		<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label"> Statistics (Data Out)<span class="require-asterisk">*</span> :</label>
							</div>
							<div class="col-md-3">
								<div class="input-group calender-group">
									<select name="statistics" class="form-control" id="selectstatistics">
										<option value="">Select Statistics</option>
									</select>
								</div>	
								<div class="help-block" id="selectstastics_error"></div>
							</div>
						</div> 
						
						<div class="clearfix">&nbsp;</div>
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label"> Display By:	</label>
							</div>
							<div class="col-md-2">
								<div class="input-group calender-group">
									<select class="SelectDataprocessDropDown chartgroup form-control" name="chartgroupcriteria" id="chartgroupcriteria">
									<option value="0"></option>
                                    <?php
                                    	$rangearray = array("week" => "Week", "month" => "Month", "years" => "Year");
                                            foreach ($rangearray as $rangekey => $daterange) {
                                                $selected = "";
                                                if (isset($filter_data->chartgroupcriteria) && $filter_data->chartgroupcriteria == $rangekey)
                                                    $selected = "selected='selected'";
                                                echo '<option value="' . $rangekey . '" ' . $selected . '>' . $daterange . '</option>';
                                    	}
                                    ?>
									</select>
								</div>	
								<div class="help-block" id="start_date_error"></div>
							</div>
						</div> 
					</div>
			</div>
	</fieldset>
</div>
<div class=" button-set text-right">
	<button onclick="" title="Clear" class="btn btn-primary" id="clearrequestclientcase" type="button" name="yt1">Clear</button>
	<button onclick="" title="Run" class="btn btn-primary" id="exportrequestclientcase" type="button" name="yt0">Run</button>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">  

	$(function () {
	    if($('#team_service').val()){
		    var filter_data = '<?php echo json_encode($filter_data); ?>';
		    selectservicelocation(filter_data);
		}
	    if($('#service_location').is(':checked')) {
		    var filter_data = '<?php echo json_encode($filter_data); ?>';
		    selectservicelocation(filter_data);
	    }
	});

	
	function selectservicelocation(filter_data){
		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-unit-data-by-service-task-criteria",
	           data: 'filter_data='+filter_data,
	           cache: false,
	           dataType:"json",
	           success: function (data) {
	        	  $('#by_teamlocation').html(data.TeamLoc);
		       	  $('#selectstatistics').html(data.unitdata);
		       	  $('input').customInput();
            }
      });
	}
	
	function filterchkclientcase(filterchkclientcase){
		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-caseclients-criteria",
	           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown+'&type=client',
	           cache: false,
	           success: function (data) {
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


	$('#teamall').click(function(){
		if($('#teamall').is(':checked')){
			$('.teloc').prop('checked',true); 
			$('.locationlabel').addClass('checked');
		} else {
			$('.teloc').prop('checked',false); 
			$('.locationlabel').removeClass('checked');
		}
	});
	
	$('body').on('click', '#clearrequestclientcase', function() {
		$('#start_date').val('');
		$('#end_date').val('');
		$('#datedropdown').val('');
		$("#team_service").val('');
		$('#service_location').prop('checked',false);
		$('input:checkbox').removeAttr('checked');
		$('#service_location').siblings().removeClass('checked');
		$('#selectstatistics').val(0);
		$('#displaystatusteamlocation').hide();
		$('#chartgroupcriteria').val('');
        return false;
	}); 
		 
	 /**
	 * get all the Statis related client or clients/case
	 */
	 $('#team_service').change(function(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var datedropdown = $('.SelectDataprocessDropDown').val();
		var str = $('#team_service').val();
		var chkclientcases = $('#chkclientcases').val();

		/** Client checkbox value **/
		var client = [];
		$("input[name='client[]']").each( function () {
			if($(this).is(':checked')){
				client.push($(this).val());
			}
		});


		/** Clientcase checkbox value **/
		var clientcase = [];
		$("input[name='clientcases[]']").each( function () {
			if($(this).is(':checked')){
				clientcase.push($(this).val());
			}
		});

		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-unit-data-by-service-task-criteria",
	           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown+'&team_service='+str+'&client='+client+'&clientcase'+clientcase,
	           cache: false,
	           dataType:"json",
	           success: function (data) {
		      	 	$('#by_teamlocation').html(data.TeamLoc);
		       		$('#selectstatistics').html(data.unitdata);
		       		$('input').customInput();
		       }
      	});
	});	 

	$('#service_location').click(function(){
		if($('#service_location').is(':checked'))
			$('#displaystatusteamlocation').show();
		else
			$('#displaystatusteamlocation').hide();
	});

	 /* When Submit the Run Button */
	  $('body').on('click','#exportrequestclientcase',function(){
		  var error="<strong>Please Fix Below Given Error:-</strong><br><br>";
		  var start_date=$('#start_date').val();
 		  var end_date=$("#end_date").val();
		  var datedrop =$(".SelectDataprocessDropDown").val();
		  var team_service = $('#team_service').val();
		  var selectstatistics = $('#selectstatistics').val();

		  if(datedrop == 0 || datedrop == ""){
				if(start_date==''){
					error+="- Select project submit start date or date range is required field. <br>";
					$('#start_date_error').html('Select project submit start date or date range is required field ');
					$('#start_date_error').parent().addClass('has-error');
				}
				if(end_date==''){
					error+="- Select project submit end date or date range is required field. <br>";
					$('#end_date_error').html('Select project submit end date or date range is required field ');
					$('#end_date_error').parent().addClass('has-error');
				}
		  }

		  if(team_service==''){
 			  $('#team_service_error').html('Select team service field');
 			  $('#team_service_error').parent().addClass('has-error');
		  } 

		  if(selectstatistics==''){
 			  $('#selectstastics_error').html('Select statistics field');
 			  $('#selectstastics_error').parent().addClass('has-error');
		  } 

     	  if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
				openPopup();// Open a popup to Display Errors
 			   $('#errorContent').html(error);
 		  }else{
 			  console.log('submit');
			  $('#add-processservice-form').submit();
		  }	
       });
		
</script>
<noscript></noscript>

