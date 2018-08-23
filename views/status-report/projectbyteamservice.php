<?php $filterserv = (isset($filter_data->Report->teamservice) ? json_encode(array('teamservice' => $filter_data->Report->teamservice)) : json_encode(array(0)));
$filterstatus = (isset($filter_data->Report->task_status) ? json_encode(array('task_status' => $filter_data->Report->task_status)) : json_encode(array(0)));

$filterservforteamloc = (isset($filter_data->Report->teamservice) && !empty($filter_data->Report->teamservice) ? implode(",",$filter_data->Report->teamservice) : "");
$filterstatusforteamloc = (isset($filter_data->Report->task_status) && !empty($filter_data->Report->task_status) ? implode(",",$filter_data->Report->task_status) : "");
$teamlocs = (!empty($filter_data->Report->teamlocs) && !empty($filter_data->Report->teamlocs) ?implode(",",$filter_data->Report->teamlocs): "");

?>
<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;

$this->title = 'StatusReport';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container" id="media_container">
	<div class="">
    <fieldset class="two-cols-fieldset workflow-management">
        <div class="create-form">
			<?php $form = ActiveForm::begin([
			        'id' => 'add-projectteamserv-form',
			 		'options' => ['class' => 'form1','novalidate'=>'novalidate'],
			 		'action' => '@web/index.php?r=status-report/projectbyteamservicedata',
			        'fieldConfig' => [
			            'template' => "<div class=\"col-sm-12\">{label}\n{input}</div>\n{error}",
			            'labelOptions' => ['class' => 'form_label'],
			        ],
			    ]); ?>
			<div class="form-group field-team-team_name required">
				<div class="row input-field">
					 <input type="hidden" name="filterser" id="filterser" value='<?php echo $filterserv; ?>'>
					 <input type="hidden" name="filterstatus" id="filterstatus" value='<?php echo $filterstatus; ?>'>
					<div class="col-md-3 col-md-offset-1">
						<label for="team-team_name" class="form_label">Select Project Submit Date<span class="require-asterisk">*</span> :</label>
					</div>
					<div class="col-md-2">
						<div class="input-group calender-group">
							 <?= $form->field($model, 'start_date')->textInput(['class'=>'form-control','placeholder'=>'Choose a date','id'=>'start_date','value'=> $filter_data->Report->start_date])->label(''); ?>
						</div>	
						<div class="help-block" id="start_date_error"></div>
					</div>
					<div class="col-md-2">
						<div class="input-group calender-group">
							<?= $form->field($model, 'end_date')->textInput(['class'=>'form-control','placeholder'=>'Choose a date','id'=>'end_date','value'=>$filter_data->Report->end_date])->label(''); ?>
						</div>	
						<div class="help-block" id="end_date_error"></div>
					</div>
					<div class="col-md-1" style="text-align:center">
						<span>OR</span>
					</div>
					<?php $rangearray = array('0'=>"",'1' => "Today", "2" => "Yesterday", "3" => "Last Week", "4" => "Last Month", "5" => "Last Year"); ?>
					<div class="col-md-2">
						<select name ="Report[datedropdown]" id="ddduration" class="form-control SelectDataprocessDropDown" onchange="if(this.value!=0){$('#start_date').val(null);$('#end_date').val(null);}">
					
							
							<?php foreach($rangearray as $array => $value) {
								
								 $selected = "";
								 if (isset($filter_data->Report->datedropdown) && $filter_data->Report->datedropdown == $array)
                                                $selected = "selected=selected";
								?>
									<option value="<?php echo $array; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
								
							<?php } ?>	
						</select>
					</div>
					
        </div> <!-- End -->
        <div class="clearfix">&nbsp;</div>
   
		    	
		    		 	
		    		<div class="col-md-7 col-md-offset-4">
						
					</div>
       
        <div class="row input-field">
					<div class="col-md-3 col-md-offset-1">
						 <label class="form_label required" for="nolabel-55">Select Project Status<span class="require-asterisk">*</span> :</label>
					</div>
					<div class="col-md-7">
						<input id="chkprojectstatus" name="Report[chkprojectstatus]" type="checkbox" value="projectstatus" <?php if (isset($filter_data->Report->chkprojectstatus) && ($filter_data->Report->chkprojectstatus == 'projectstatus')) echo 'checked' ?>>
						<label for="chkprojectstatus"></label>
					</div>
					<div class="col-md-7">
						<div class="help-block" id="projectstatus_error"></div>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="col-md-7 col-md-offset-4">
						 <div style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
									if (isset($filter_data->Report->chkprojectstatus) && $filter_data->Report->chkprojectstatus == 'projectstatus')
										echo 'block';
									else
										echo 'none';
									?>" id="displaystatus"> 
										<span>
											<ul class='by_teamloc_sub' id="projstatusul" style='width: 100%!important;list-style: none;'>
													
											</ul>
										</span>
										<div style="border-top:1px solid #DBDBDB;width:580px;padding:10px;">
											
											<div class="">
												<input id="statusall" name="Report[statusall]" type="checkbox"  class="statusall" onclick="">
												<label for="statusall"></label>
											</div>
											<div class="col-md-3">
												 <label class="form_label" for="nolabel-55">Select All</label>
											</div>
										</div>	
									</div><br/>
					</div>				
		</div>
		<div class="clearfix">&nbsp;</div>
		<div class="col-md-9 col-md-offset-4"><div class="help-block" id="teamservice_error"></div></div>
		<div class="row input-field">
			<div class="col-md-3 col-md-offset-1">
						 <label class="form_label required" for="nolabel-55">Select Team Services<span class="require-asterisk">*</span> :</label>
					</div>
					<div class="col-md-7">
						<input id="chkprojteamserv" name="Report[chkprojteamserv]" type="checkbox" value="team" <?php if (isset($filter_data->Report->chkprojectstatus) && ($filter_data->Report->chkprojectstatus == 'projectstatus')) echo 'checked' ?>>
						<label for="chkprojteamserv"></label>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="col-md-7 col-md-offset-4">
						<div style="overflow-y: scroll;overflow-x: hidden;max-height: 150px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
									if (isset($filter_data->Report->chkprojteamserv) && ($filter_data->Report->chkprojteamserv == 'team'))
										echo 'block';
									else
										echo 'none';
									?>" id="displayteam">
										<div class="clearfix">&nbsp;</div>
										<ul style="list-style: none;" id="projteamservUl" class="by_team_sub">

										</ul>
										<div></div>
										<div style="border-top:1px solid #DBDBDB;width:580px;padding:10px;">
											<div class="">
												<input id="teamservall" name="Report[checkcases_all]" type="checkbox"  class="teamservall" onclick="$('.te').attr('checked', this.checked);" <?php if (isset($checkcases_all) && $checkcases_all == '1') {
                                            echo 'checked="checked"';
                                        } else {
                                            echo '';
                                        } ?>>
												<label for="teamservall"></label>
											</div>
											<div class="col-md-3">
												 <label class="form_label" for="nolabel-55">Select All</label>
											</div>
										</div>
						</div> <!-- End -->
					</div>
		
		</div>
		<div class="clearfix">&nbsp;</div>
		<div class="col-md-9 col-md-offset-4"><div class="help-block" id="teamserviceloc_error"></div></div>
		<div class="row input-field">
			<div class="col-md-3 col-md-offset-1">
						 <label class="form_label required" for="nolabel-55">Select Service Locations:</label>
					</div>
					<div class="col-md-7">
						<input id="chkprocessteamloc" name="Report[chkprocessteamlocs]" type="checkbox" onchange="filterbyserviceLocations('','','');" value="teamloc" <?php if (isset($filter_data->Report->chkprojectstatus) && ($filter_data->Report->chkprojectstatus == 'projectstatus')) echo 'checked' ?>>
						<label for="chkprocessteamloc"></label>
					</div>
					<div class="clearfix">&nbsp;</div>
					<div class="col-md-7 col-md-offset-4">
						<div style="overflow-y: scroll;overflow-x: hidden;max-height: 150px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
									if (isset($filter_data->Report->chkprojteamserv) && ($filter_data->Report->chkprojteamserv == 'team'))
										echo 'block';
									else
										echo 'none';
									?>" id="displayteamloc">
										<div class="clearfix">&nbsp;</div>
										<ul style="list-style: none;" id="projteamservUlloc" class="by_teamloc_sub">

										</ul>
										<div></div>
										<div style="border-top:1px solid #DBDBDB;width:580px;padding:10px;">
											<div class="">
												<input id="teamservlocall" name="" type="checkbox"  class="teamlocall" onclick="$('.teloc').attr('checked', this.checked);" >
												<label for="teamservlocall"></label>
											</div>
											<div class="col-md-3">
												 <label class="form_label" for="nolabel-55">Select All</label>
											</div>
										</div>
						</div> <!-- End -->
					</div>
		
		</div>
		
		<div class="clearfix">&nbsp;</div>
		<div class="row input-field">
					<div class="col-md-3 col-md-offset-1">
						 <label class="form_label required" for="nolabel-55">Display By :</label>
					</div>
					<div class="col-md-2">
						<?php  $rangearray = array("0"=>"","week" => "Week", "month" => "Month", "years" => "Year"); ?>
						 <select name="Report[chartgroupcriteria]" class="form-control" id="selectgroupcriteria">
                                            <option value="0"></option>
                                            <?php
                                            $rangearray = array("week" => "Week", "month" => "Month", "years" => "Year");
                                            foreach ($rangearray as $rangekey => $daterange) {
											$selected = "";
											if (isset($filter_data->Report->chartgroupcriteria) && $filter_data->Report->chartgroupcriteria == $rangekey)
                                                    $selected = "selected='selected'";	
												 ?>
                                            <option value="<?php echo $rangekey; ?>" <?php echo $selected; ?>><?php echo $daterange; ?></option>
                                              
                                            <?php }
                                            ?>
                                        </select>
					</div>
		</div>
        
        
        <div class="has-error">
			<div class="help-block col-sm-offset-2" id="task_id_error">
			</div>
        </div>
        
        <div class="clearfix">&nbsp;</div>
        <div class="clearfix">&nbsp;</div>
        <div class="clearfix">&nbsp;</div>
        <div class="row input-field">
        <div id="activity_report">
					
		</div>
		</div>
      </div>  
</fieldset>
<div class=" button-set text-right">
		<button onclick="" title="Clear" class="btn btn-primary" id="clearprojteamservice" type="button" name="yt1">Clear</button>
		<button onclick="" title="Run" class="btn btn-primary" id="exportprojteamservice" type="button" name="yt0">Run</button>
  </div>
  <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">    
	$(function () {

		var start_date = datePickerController.createDatePicker({             
		 formElements: { "start_date": "%Y-%m-%d" },         
		 callbackFunctions:{
			"dateset":[ function (){
				var start_value = datePickerController.getSelectedDate('start_date');
				if(start_value != null){
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
				var end_value = datePickerController.getSelectedDate('end_date');
				if(end_value != null){
					$('#ddduration').val('0');
			    }
				$('#end_date_error').empty();
				$('#end_date_error').parent().removeClass('has-error');
			}],
		  }      
		 });  
		 
		 $('input[name="Report[chkprojectstatus]"]').click(function ()
            {
                if ($('#chkprojectstatus').is(':checked'))
                { 
                    $('#displaystatus').show();
                    $('input[name="Report[statusall]"]').siblings().removeClass('checked');
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
		 
		  if ($('#chkprojectstatus').is(':checked')) {
                var status = '<?php if(!is_array($filterstatus)){echo $filterstatus;} ?>';
                filterRequestStatus(status,0,0, 'projstatusul','postdata');
            }
            if ($('#chkprojteamserv').is(':checked')) {
                var filterserv = '<?php echo $filterserv; ?>';
                filterUserTeamServiceData(filterserv, "projectservice", "chkprojteamserv");
            }
            if ($('#chkprocessteamloc').is(':checked')) {
                var filterserv = '<?php echo $filterservforteamloc; ?>';
                var status = '<?php echo $filterstatusforteamloc; ?>';
                var teamlocs = '<?php echo $teamlocs; ?>';
                filterbyserviceLocations(filterserv,status,teamlocs);
            }
           
          $('input[name="Report[chkprojteamserv]"]').click(function(){
                if ($('#chkprojteamserv').is(':checked')) {
                    $('#displayteam').show();
                    $('#teamservice_error').empty();
                    $('#teamservice_error').parent().removeClass('has-error');
                } else {
					$('#chkprojteamserv').siblings().removeClass('checked');
                    $('#displayteam').hide();
                    $("#filterser").val("");
                    $("input[name='teamservice[]']").prop('checked',false);
                    $(".teamservall").prop('checked', false);
                }
            }); 
	});	
	$('body').on('click','#chkprojteamserv',function(){
        var filter_data = $("#filterser").val();
        if($(this).is(":checked")){
            filterUserTeamServiceData(filter_data,"projectservice","chkprojteamserv");
        }
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
				$(this).attr("checked",false);
				$(this).siblings().removeClass("checked");
				
			} else {
				if ($(this).is(':checked')){
					var client = $("#filterclient").val();
					var clientcase = $("#filterclientcase").val();
					var status = $("#filterstatus").val();
					filterRequestStatus(status,0,0,'projstatusul','');
				}
			}
			
		});  
		
		$('body').on('click', '#clearprojteamservice', function() {
			$('#filterser').val('');
			$('#filterstatus').val('');
			$('#filterser').val('');
			$('#filterteamloc').val('');
			$("#start_date").val('');
			$("#end_date").val('');
            $(".SelectDataprocessDropDown").val('');
			$('input:checkbox').removeAttr('checked');
			$('#chkprojectstatus').siblings().removeClass('checked');
			$('#chkprojteamserv').siblings().removeClass('checked');
			$('#chkprocessteamloc').siblings().removeClass('checked');
			$('#displayteam').hide();
			$('#displayteamloc').hide();
			$("#displaystatus").hide();
			$('#selectgroupcriteria').val(0);
            return false;
		}); 
		
		
		$('body').on('click','#exportprojteamservice',function(){
		  var error="<strong>Please Fix Below Given Error:-</strong><br><br>";
		  var start_date=$('#start_date').val();
		  var end_date=$("#end_date").val();
		  var datedrop =$(".SelectDataprocessDropDown").val();
		  var addinvoiceform = $("#add-projectteamserv-form").serialize();
		 
			if(datedrop == 0 || datedrop == ""){
			   if(start_date==''){
				   $('#start_date_error').html('Select project submit start date or date range is required field ');
				   $('#start_date_error').parent().addClass('has-error');
					   error+="- Select project submit start date or date range is required field. <br>";
			   }
			   if(end_date==''){
					$('#end_date_error').html('Select project submit end date or date range is required field ');
					$('#end_date_error').parent().addClass('has-error');
					   error+="- Select project submit end date or date range is required field. <br>";
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
						$('#projectstatus_error').html('Please select at least one project status.');
						$('#projectstatus_error').parent().addClass('has-error');
					}
	  }
			 
			  if (!$("#chkprojteamserv").is(':checked')) {
					$('#teamservice_error').html('Please select team service.');
					$('#teamservice_error').parent().addClass('has-error');
					error+="- Please select team service. <br>";
			  }
			  if($("input[name='Report[chkprojteamserv]']:checked").val()=='team'){
					if (!$("input[name='Report[teamservice][]']").is(':checked')) {
						$('#teamservice_error').html('Please select at least one team service.');
						$('#teamservice_error').parent().addClass('has-error');
						error+="- Please select at least one team service. <br>";
					}
			  }
			  
		  if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
			   //openPopup();// Open a popup to Display Errors
			   console.log(error);
			   $('#errorContent').html(error);
		  }else{
			$('#add-projectteamserv-form').submit();
		  }	
		});
            
            /* To Get the service Location For Project By Team service */
            function filterbyserviceLocations(filterserv,status,teamlocs){
            if ($('#chkprocessteamloc').is(':checked')){
	        	$("#displayteamloc ul.by_teamloc_sub").html('<center>Loading...<br/></center>');
	            $('#displayteamloc').show();
	        	var start_date = $('#start_date').val();
	    		var end_date = $('#end_date').val();
	    		var datedropdown = $('.SelectDataprocessDropDown').val();
	    		var teamlocs = '<?php echo $teamlocs; ?>';
	    		var error="<strong>Please Fix Below Given Error:-</strong><br>";
	    		var value = "";
	    		if(filterserv != ""){
	    			value = filterserv;
	        	}else{
	            	$('#projteamservUl li .processclientteams').each(function(){
	                	if($(this).is(":checked")){
	                		if(value!=""){
	                			value += "," + $(this).val();
	                    	} else {
	                    		value = $(this).val();
	                    		//$('#displayteamloc ul.by_teamloc_sub').html("<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>");
	                        } 
	                    } 
	                });
	        	}
	        	
	        	var projectstatus = "";
	        	if(status != ""){
	        		projectstatus = status;
	        	}else{
	            	$('#projstatusul li .pstatus').each(function(){
	                	if($(this).is(":checked")){
	                		if(projectstatus!=""){
	                			projectstatus += "," + $(this).val();
	                    	} else {
	                    		projectstatus = $(this).val();
	                        }
	                    }
	                });
	        	}
	        	        	
	        	if((start_date!="" && end_date!="") || datedropdown != 0) {
		        	$.ajax({
		                type: "POST",
		                url: baseUrl+"status-report/getteamlocbyservicetaskcriteria",
		                data: {'filterservice':value,'projectstatus':projectstatus,'filterLoc':teamlocs, start_date:start_date, end_date:end_date, datedropdown:datedropdown},
		                dataType:'json',
		                cache: false,
		                success:function(data){
		                	$('#displayteamloc ul.by_teamloc_sub').html('');
		                    if(data.TeamLoc == ""){
		                    	data.TeamLoc="<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>";
		                    }
		                	$('#displayteamloc ul.by_teamloc_sub').html(data.TeamLoc);
		                },complete:function(){
							$('input').customInput();
						}
		            });
	    		} else {
	    			if(start_date==''){
					   $('#start_date_error').html('Select project submit start date or date range is required field ');
					   $('#start_date_error').parent().addClass('has-error');
  			          error+="- Select project submit start date or date range is required field. <br>";
	  			    }
	  			    if(end_date==''){
					  $('#end_date_error').html('Select project submit end date or date range is required field. ');
					  $('#end_date_error').parent().addClass('has-error');
	  			      error+="- Select project submit end date or date range is required field. <br>";
	  			    }
	  			    if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
		                $('#errorContent').html(error);
		                $("#projectstatuss").attr("checked",false);
		                $('#displayteamloc').hide();
		  			    $("input[name='chkprocessteamlocs']").prop('checked', false);
			            $(".teamlocall").prop('checked', false);
		            }
	  				//$('#displayteamloc ul.by_teamloc_sub').html("<li class='by_teamlocs'><label style='color:#222;'>No Location associated for above criteria</label></li>");
		    	}
	        } else {
	            $('#displayteamloc').hide();
	            $("input[name='teamlocs[]']").prop('checked', false);
	            $(".teamlocall").prop('checked', false);
	        }
	    }
    
    
    
	
</script>
<noscript></noscript>

