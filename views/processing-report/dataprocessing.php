<?php 
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use kartik\grid\GridView;
	use kartik\grid\datetimepicker;

	$this->title = 'Data Processed by Client/Case';
	$this->params['breadcrumbs'][] = $this->title;
	
	$filterclient = (isset($filter_data->client) ? json_encode(array('client' => $filter_data->client)) : json_encode(array(0)));
	$filterclientcase = (isset($filter_data->clientcases) ? json_encode(array('clientcases' => $filter_data->clientcases)) : json_encode(array(0)));
	$filterchkclientcase = (isset($filter_data->chkclientcases) ? json_encode(array('clientcases' => $filter_data->chkclientcases)) : json_encode(array(0)));
	$filterteam_service = (isset($filter_data->team_service) ? $filter_data->team_service:"");
	$filterdata = (isset($filter_data->unitdata) ? json_encode(array('unitdata' => $filter_data->unitdata)) : json_encode(array(0)));
	$filterbilling = (isset($filter_data->statistics) ? json_encode(array('billingdata' => $filter_data->statistics)) : json_encode(array(0)));
	$teamlocs = (!empty($filter_data->teamlocs)?json_encode($filter_data->teamlocs): json_encode(array(0)));
?>
<div class="right-main-container" id="media_container">
	<?php $form = ActiveForm::begin([
		'id' => 'add-processclientcase-form',
		'action' => '@web/index.php?r=processing-report/dataprocessclientcasedata',
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
								 <label class="form_label required" for="nolabel-55">Select Client/Cases<span class="require-asterisk">*</span> :</label>
							</div>
							<span id="select_client_case" style="display:none;"></span>
							<div class="col-md-7">
									<div class="custom-inline-block-width" >
					     			    <div class="custom-radio">
											<input type="radio" value="client" class="form-control select_client" id="selectclient" name="chkclientcases" <?php if(isset($filter_data) && $filter_data->chkclientcases=='client'){echo "checked";}?>>
												<label for="selectclient">By Clients</label>
											<input type="radio" value="clientcases" class="form-control select_client" id="selectrclientcases" name="chkclientcases" <?php if(isset($filter_data) && $filter_data->chkclientcases=='clientcases'){echo "checked";}?>>
					    		    			<label for="selectrclientcases">By Clients/Cases</label>
					    		    	</div>
					    		 	</div>
				    			</div>
				    			<div class="clearfix">&nbsp;</div>
				    					
				    			<!-- Get all client/cases -->
				    			<div class="col-md-7 col-md-offset-4">
					    			<div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
										if (isset($filter_data->chkclientcases) && $filter_data->chkclientcases=='client')
											echo 'block';
										else
											echo 'none'; ?>" id="displaystatusclient"> 
					    				<span id="select_client_case" style="display:none;"></span>
					    				<span>
											<ul class='by_teamloc_sub custom-full-width' id="by_client" style='width: 100%!important;list-style:none;'></ul>
										</span>
										<div style="">
											<div class="col-md-3 custom-full-width">
												<input id="clientstatusall" class="form-control" name="Report[statusall]" type="checkbox"  class="statusall" onclick="" aria-label="Select All">
												<label class="form_label" for="clientstatusall">Select All</label>
											</div>
										</div>	
									</div>
									<div class="clearfix">&nbsp;</div>
				    			</div>
				    		<!-- End -->
				    		
				    		<!-- Get all client/cases -->
				    			<div class="col-md-7 col-md-offset-4">
					    			<div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
										if (isset($filter_data->chkclientcases) && $filter_data->chkclientcases=='clientcases')
											echo 'block';
										else
											echo 'none'; ?>" id="displaystatusclientcase"> 
					    				<span id="select_client_case" style="display:none;"></span>
					    				<span>
											<ul class='by_teamloc_sub custom-full-width' id="by_clientcase" style='width: 100%!important;list-style: none;'></ul>
										</span>
										<div style="">
											<div class="col-md-3 custom-full-width">
												<input id="clientcasestatusall" class="form-control" name="Report[statusall]" type="checkbox"  class="statusall" onclick="" aria-label="Select All">
												<label class="form_label" for="clientcasestatusall">Select All</label>
											</div>
										</div>	
									</div>
									<div class="help-block" class="form-control" id="clientcases_error"></div>
									<div class="clearfix">&nbsp;</div>
								</div>
							<!-- End -->	
				    	</div>
					
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
					
						<div class="row input-field">
							<div class="col-md-3 col-md-offset-1">
								<label for="team-team_name" class="form_label"> Select Service Locations :</label>
							</div>
							<div class="col-md-2">
								<div class="input-group calender-group">
									 <input type="checkbox" name="service_location" id="service_location" value="service_location" <?php if(isset($filter_data->service_location) && $filter_data->service_location!=''){echo "checked";} ?> aria-label="service Location" /><label for="service_location"></label>
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
								<div class="help-block" id="selectstatistics_error"></div>
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
		if($('#selectclient').is(':checked')) {
			var filter_data = '<?php echo json_encode($filter_data); ?>';
			selectclientservice(filter_data);
	    }
	    if ($('#selectrclientcases').is(':checked')) {
	        var filter_data = '<?php echo json_encode($filter_data); ?>';
	        selectclientcaseservice(filter_data);
	    }
	    if($('#team_service').val()){
		    var filter_data = '<?php echo json_encode($filter_data); ?>';
		    selectservicelocation(filter_data);
		}
	    if($('#service_location').is(':checked')) {
		    var filter_data = '<?php echo json_encode($filter_data); ?>';
		    selectservicelocation(filter_data);
	    }
	});

	function selectclientservice(filter_data){
		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-caseclients-criteria",
	           data: 'filter_data='+filter_data+'&type=client',
	           cache: false,
	           beforeSend: function(){
	        	   $('#displaystatusclientcase').hide();
	           },
	           success: function (data) {
	               $('#displaystatusclient').show();
	               $('#by_client').html(data);
	               $('input').customInput();
	         }
      	});
	}

	function selectservicelocation(filter_data){
		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-unit-data-by-service-task-criteria",
	           data: 'filter_data='+filter_data+'&type=client',
	           cache: false,
	           dataType:"json",
	           success: function (data) {
	        	  $('#by_teamlocation').html(data.TeamLoc);
		       	  $('#selectstatistics').html(data.unitdata);
		       	  $('input').customInput();
            }
      });
	}
	
	function selectclientcaseservice(filter_data){
		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-caseclients-criteria",
	           data: 'filter_data='+filter_data+'&type=client',
	           cache: false,
	           beforeSend: function(){
	        	   $('#displaystatusclient').hide();
	           },
	           success: function (data) {
	         	  $('#displaystatusclientcase').show();
            	  $('#by_clientcase').html(data);
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

	$('body').on('click', '#clearrequestclientcase', function() {
		$('#start_date').val('');
		$('#end_date').val('');
		$('#datedropdown').val('');
		$('#selectclient').prop('checked',false);
		$("#selectrclientcases").val('');
		$("#team_service").val('');
		$('#service_location').prop('checked',false);
		$('input:checkbox').removeAttr('checked');
		$('#selectclient').siblings().removeClass('checked');
		$('#service_location').siblings().removeClass('checked');
		$('#selectstatistics').val('');
		$('#displaystatusclientcase').hide();
		$('#displaystatusclient').hide();
		$('#displaystatusteamlocation').hide();
        return false;
	}); 
		 
	/** get Client Case Data Criteria **/
	$('#selectclient').change(function(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var datedropdown = $('.SelectDataprocessDropDown').val();
		$.ajax({
               type: "POST",
               url: baseUrl + "processing-report/get-caseclients-criteria",
	           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown+'&type=client',
	           cache: false,
	           beforeSend: function(){
	        	   $('#clientcasestatusall').prop('checked',false);
	        	   $('#displaystatusclientcase').hide();
	           },
   	           success: function (data) {
   	   	          $('#select_client_case').text('client');
   	        	  $('#displaystatusclient').show();
               	  $('#by_client').html(data);
               	  $('input').customInput();
               }
         });
	});

	$('#selectrclientcases').change(function(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var datedropdown = $('.SelectDataprocessDropDown').val();
		$.ajax({
            type: "POST",
            url: baseUrl + "processing-report/get-caseclients-criteria",
	           data: 'start_date='+start_date+'&end_date='+end_date+'&datedropdown='+datedropdown+'&type=clientcases',
	           cache: false,
	           beforeSend: function(){
	        	   $('#clientstatusall').prop('checked',false);
	        	   $('#displaystatusclient').hide();
	           },
	           success: function (data) {
	        	   $('#select_client_case').text('clientcase');
		           $('#displaystatusclientcase').show();
	        	   $('#by_clientcase').html(data);
	        	   $('input').customInput();
	        	}
      	});
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

	$('#clientstatusall').click(function(){
		var str = $('#select_client_case').text();
		if($('#clientstatusall').is(':checked')){
			$('.client').prop('checked',true);
			$('.clientlabel').addClass('checked');
		} else {
			$('.client').prop('checked',false);
			$('.clientlabel').removeClass('checked');
		}
	});

	$('#clientcasestatusall').click(function(){
		var str = $('#select_client_case').text();
		if($('#clientcasestatusall').is(':checked')){
			$('.clientcases').prop('checked',true);
			$('.clientcaselabel').addClass('checked');
		} else {
			$('.clientcases').prop('checked',false);
			$('.clientcaselabel').removeClass('checked');
		}
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
		  var chkclientcases = $('.select_client').is(':checked');
		//  var chkclient_cases = $('input[name="chkclientcases"]').is(':checked'); //$('input[name="chkclientcases"]').val();

		  if(chkclientcases==false){
			  error+="- Select client case. <br>";
			  $('#clientcases_error').html('Select client case ');
			  $('#clientcases_error').parent().addClass('has-error');
		  }

		  if(chkclientcases==true){
			  if ($('#selectrclientcases').is(":checked"))
			  {
				  if(!$('input[name="clientcases[]"]').is(':checked')){
					  error+="- Select client case. <br>";
					  $('#clientcases_error').html('Select client case ');
					  $('#clientcases_error').parent().addClass('has-error');
				  }
			  }
			  if($('#selectclient').is(":checked"))
			  {
				  if(!$('input[name="client[]"]').is(':checked')){
					  error+="- Select client case. <br>";
					  $('#clientcases_error').html('Select client case ');
					  $('#clientcases_error').parent().addClass('has-error');
				  }
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

          if(selectstatistics=='' || selectstatistics==0){
        	  $('#selectstatistics_error').html('Please Select selectstatistics');
			  $('#selectstatistics_error').parent().addClass('has-error');
			  error+="- Select Statistics. <br>";
          }
          
		  if(team_service==''){
				$('#team_service_error').html('Please Select service');
				$('#team_service_error').parent().addClass('has-error');
				error+="- Select services. <br>";
		  }
		  
 		  if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
			   openPopup();// Open a popup to Display Errors
 			   $('#errorContent').html(error);
 		  }else{
 			  console.log('submit');
			  $('#add-processclientcase-form').submit();
		  }	
       });


</script>
<noscript></noscript>

