<?php 
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use kartik\grid\GridView;
	use kartik\grid\datetimepicker;

	$this->title = 'Data Processed by Client/Case';
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container" id="media_container">
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
								 <input type="text" />
							</div>	
							<div class="help-block" id="start_date_error"></div>
						</div>
						<div class="col-md-2">
							<div class="input-group calender-group">
								 <input type="text" />
							</div>	
							<div class="help-block" id="end_date_error"></div>
						</div>
						<div class="col-md-1" style="text-align:center">
							<span>OR</span>
						</div>
						<div class="col-md-1">
							<select name="">
								<option value="0"></option>
								<option value="1">Today</option>
								<option value="2">Yesterday</option>
								<option value="3">Last Week</option>
								<option value="4">Last Month</option>
								<option value="5">Last Year</option>
							</select>
						</div>
					</div> 
				
					<div class="row input-field">
						<div class="col-md-3 col-md-offset-1">
							 <label class="form_label required" for="nolabel-55">Select Client/Cases<span class="require-asterisk">*</span> :</label>
						</div>
						<div class="col-md-7">
							<div class="custom-inline-block-width" >
			     			    <div class="custom-radio">
									<input type="radio" value="client" id="client" name="Report[chkclientcases]"><label for="allrclient" class="">By Clients</label>
								</div>
			    		    	<div class="custom-radio"><input type="radio" value="selac" id="selectrclientcases" checked="checked" name="Report[chkclientcases]"><label for="selectrclientcases"  >By Clients/Cases</label></div>
			    		 	</div>
			    		 </div>
					</div>
				
					<div class="row input-field">
						<div class="col-md-3 col-md-offset-1">
							<label for="team-team_name" class="form_label">  Select Service:<span class="require-asterisk">*</span> :</label>
						</div>
						<div class="col-md-3">
							<div class="input-group calender-group">
								 <select name="">
								 	<option value="">-- select --</option>
								 </select>
							</div>	
							<div class="help-block" id="start_date_error"></div>
						</div>
					</div> 
				
					<div class="row input-field">
						<div class="col-md-3 col-md-offset-1">
							<label for="team-team_name" class="form_label"> Select Service Locations:<span class="require-asterisk">*</span> :</label>
						</div>
						<div class="col-md-2">
							<div class="input-group calender-group">
								 <input type="checkbox" name="service_location" aria-label="Serive Location" id="service_location" /><label for="service_location"></label>
							</div>	
							<div class="help-block" id="start_date_error"></div>
						</div>
					</div> 
				
					<div class="row input-field">
						<div class="col-md-3 col-md-offset-1">
							<label for="team-team_name" class="form_label"> Statistics (Data Out):<span class="require-asterisk">*</span> :</label>
						</div>
						<div class="col-md-2">
							<div class="input-group calender-group">
								 <select>
								 	<option>-- select --</option>
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
<script type="text/javascript">    
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
		 
		 $("#selectall").change(function () {
			 if ($('#selectall').is(':checked')){
				$(".client").prop('checked', $(this).prop("checked"));
				$(".client").siblings().addClass('checked');
			}else{
				$(".client").siblings().removeClass('checked');
			}
		 });
		 
		 $("#statusall").change(function () {
			  if ($('#statusall').is(':checked')){
				$(".pstatus").prop('checked', $(this).prop("checked"));
				 $(".pstatus").siblings().addClass('checked');
			  }else{
				 $(".pstatus").siblings().removeClass('checked');
			  }
		 });
		 
		 $("#checkcases_all").change(function (){
			 if ($('#checkcases_all').is(':checked')){
				$(".clientcases").prop('checked', $(this).prop("checked"));
				$(".clientcases").siblings().addClass('checked');
			}else{
				$(".clientcases").siblings().removeClass('checked');
			}	
		 });	
		 
		 $("#ddduration").change(function (){
			 var duration = $(this).val();
				if(duration != null || duration != '' || duration != 0){
					$('#start_date').val('');
					$('#end_date').val('');
					$('#start_date_error').empty();
					$('#start_date_error').parent().removeClass('has-error');
					$('#end_date_error').empty();
					$('#end_date_error').parent().removeClass('has-error');
				}
		 });	
		 if ($('#allrclient').is(':checked')) {
				var client = '<?php if(!is_array($filterclient)){echo $filterclient;} ?>';
				//filterRequestClientCase(client, 'ac', 'clientul');
				//var client = $("#filterclient").val();
				filterRevenueData(client,'client','clientul');
				
			}
		   if ($('#selectrclientcases').is(':checked')) {
				var clientcase = '<?php if(!is_array($filterclientcase)){echo $filterclientcase;} ?>';
				filterRevenueData(clientcase,'clientcase','clientcaseul');
			}
            if ($('#chkprojectstatus').is(':checked')) {
                var client = '<?php if(!is_array($filterclient)){echo $filterclient;} ?>';
                var clientcase = '<?php if(!is_array($filterclientcase)){echo $filterclientcase;} ?>';
                var status = '<?php if(!is_array($filterstatus)){echo $filterstatus;} ?>';
                filterRequestStatus(status,client,clientcase, 'projstatusul','postdata');
            }
		 
		 $('input[name="Report[chkclientcases]"]').click(function () {
				$('#clientcase_error').empty();
				$('#clientcase_error').removeClass('has-error');
                if ($('#allrclient').is(':checked')) {
                    $('#displayclient').show();
					$('#selectall').siblings().removeClass('checked');
                } else {
                    $('#displayclient').hide();
                    $("#filterclient").val("");
                    //$("input[name='client[]']").prop('checked', false);
                    //$('#selectall').attr('checked', false);
                    
                }
                if ($('#selectrclientcases').is(':checked')) {
                    $('#displayclientcases').show();
                    $('#checkcases_all').siblings().removeClass('checked');
                } else {
                    $('#displayclientcases').hide();
                    $("#filterclientcase").val("");
                    //$("input[name='clientcases[]']").prop('checked', false);
                   // $("#checkcases_all").prop('checked', false);
                }
            });
            
          $('input[name="Report[chkprojectstatus]"]').click(function ()
            {
            	
                if ($('#chkprojectstatus').is(':checked'))
                {
                    $('#displaystatus').show();
                    $('#statusall').siblings().removeClass('checked');
                    $('#projectstatus_error').empty();
                    $('#projectstatus_error').removeClass("has-error");
                } else {
                    $('#start_date_error').empty();
					$('#start_date_error').parent().removeClass('has-error');
					$('#end_date_error').empty();
					$('#end_date_error').parent().removeClass('has-error');
                    $('#displaystatus').hide();
                }
                
            });  
		   
	});
		 
	function changeStart(){
		$('#ddduration').val('0');
	}
	
	 
	      $('body').on('click', '#clearrequestclientcase', function() {
			$("#start_date").val('');
			$("#end_date").val('');
			$('#filterclient').val('');
			$('#filterclientcase').val('');
			$('#filterstatus').val('');
			
            $(".SelectDataprocessDropDown").val('');
			$("input[name='Report[chkclientcases]'").prop('checked',false);
			$('input:checkbox').removeAttr('checked');
			$('#selectrclientcases').siblings().removeClass('checked');
			$('#chkprojectstatus').siblings().removeClass('checked');
			$('#allrclient').siblings().removeClass('checked');
			
			$('#displayclient').hide();
            $('#displayclientcases').hide();
            $('#displaystatus').hide();
            $('#projstatusul').html(null);
            $('#selectgroupcriteria').val('0');
            $('#ddduration').val('0');
           
                        
                    return false;
		});
	 
	 
	 $('body').on('click','#allrclient',function(){
        var client = $("#filterclient").val();
        filterRevenueData(client,'client','clientul');
    });
    
    $('body').on('click','#selectrclientcases',function(){
        var clientcase = $("#filterclient").val();
        filterRevenueData(clientcase,'clientcase','clientcaseul');
    });
    
	   $('body').on('click','#chkprojectstatus',function(){
			$("#displaystatus").hide();
			var error="Please Fix Below Given Error:";
			var start_date=$('#start_date').val();
			var end_date=$("#end_date").val();
			var datedrop =$(".SelectDataprocessDropDown").val();
			if((datedrop == 0 || datedrop == "" || datedrop == null) && $(this).is(':checked')){
			  if(start_date==''){
					$('#start_date_error').html('Select project submit start date or date range is required field ');
					$('#start_date_error').parent().addClass('has-error');
					error+="- Select project submit start date or date range is required field. ";
			  }
			  if(end_date==''){
				    $('#end_date_error').html('Select project submit end date or date range is required field ');
					$('#end_date_error').parent().addClass('has-error');
					error+="- Select project submit end date or date range is required field. ";
			  }
			}        
			if(error!="" && error!='Please Fix Below Given Error:'){
				console.log('error fix');
				$(this).attr("checked",false);
				$(this).siblings().removeClass("checked");
				
			} else {
				if ($(this).is(':checked')){
					var client = $("#filterclient").val();
					var clientcase = $("#filterclientcase").val();
					var status = $("#filterstatus").val();
					filterRequestStatus(status,client,clientcase,'projstatusul','');
					
				}
			}
			
		});
		
		
		
		
    
    /* For Get Client and Clientcase */
    function filterRevenueData(filter_data,criteria,appendul){
	
		$("#"+appendul).html('<center>Loading...<br/></center>');
		$.ajax({
			type: "POST",
			url: baseUrl+"status-report/getrevenuecriteria",
			data: {'YII_CSRF_TOKEN':$("#token").val(),'criteria':criteria,'filter_data':filter_data},
			dataType:'html',
			cache: false,
			success:function(data){
				$("#"+appendul).html('');
				if(data != ""){
					$("#"+appendul).append(data);
				} else {
					$("#"+appendul).append("<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>");
				}
			},complete:function(){
				$('input').customInput();
				$('#selectall').siblings().removeClass('checked');
				$('#checkcases_all').siblings().removeClass('checked');
			}
		});
	}
	/* End */
	/* When Submit the Run Button */
	  $('body').on('click','#exportrequestclientcase',function(){
		  var error="<strong>Please Fix Below Given Error:-</strong><br><br>";
		  var start_date=$('#start_date').val();
		  var end_date=$("#end_date").val();
                  var datedrop =$(".SelectDataprocessDropDown").val();
		  
		  var addinvoiceform = $("#add-requestclientcase-form").serialize();
                  
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
		  
          if (!$("input[name='Report[chkclientcases]']").is(':checked')) {
			  $('#clientcase_error').html('Please select client/cases.');
			  $('#clientcase_error').parent().addClass('has-error');
			error+="- Please select client/cases. <br>";
		  }
          if($("input[name='Report[chkclientcases]']:checked").val()=='ac'){
                        if (!$("input[name='Report[client][]']").is(':checked')) {
							 $('#clientcase_error').html('Please select at least one clients.');
							 $('#clientcase_error').parent().addClass('has-error');
                              error+="- Please select at least one clients. <br>";
                        }
		  }
          if($("input[name='Report[chkclientcases]']:checked").val()=='selac'){
                        if (!$("input[name='Report[clientcases][]']").is(':checked')) {
							 $('#clientcase_error').html('Please select at least one client/cases.');
							 $('#clientcase_error').parent().addClass('has-error');
                              error+="- Please select at least one client/cases. <br>";
                        }
		  }
          if (!$("#chkprojectstatus").is(':checked')) {
			error+="- Please select project status. <br>";
			$('#projectstatus_error').html('Please select project status.');
		    $('#projectstatus_error').parent().addClass('has-error');
		  }
		  
          if($("input[name='Report[chkprojectstatus]']:checked").val()=='projectstatus'){
			if (!$("input[name='Report[task_status][]']").is(':checked')) {
                            error+="- Please select at least one project status. <br>";
                            $('#projectstatus_error').html('- Please select at least one project status.');
							$('#projectstatus_error').parent().addClass('has-error');
                        }
		  }
		  
		  if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
			//   openPopup();// Open a popup to Display Errors
			   $('#errorContent').html(error);
		  }else{
			  console.log('submit');
			  $('#add-requestclientcase-form').submit();
		  }	
         });
         /* End */
	
	
</script>
<noscript></noscript>

