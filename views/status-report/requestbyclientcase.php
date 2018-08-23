<?php $filterclient = (isset($filter_data->Report->client) ? json_encode(array('client' => $filter_data->Report->client)) : "");
$filterclientcase = (isset($filter_data->Report->clientcases) ? json_encode(array('clientcases' => $filter_data->Report->clientcases)) : "");
$filterstatus = (isset($filter_data->Report->task_status) ? json_encode(array('task_status' => $filter_data->Report->task_status)) : ""); 

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
			        'id' => 'add-requestclientcase-form',
			 		'options' => ['class' => 'form1','novalidate'=>'novalidate'],
			 		'action' => '@web/index.php?r=status-report/requestclientcasedata',
			        'fieldConfig' => [
			            'template' => "<div class=\"col-sm-12\">{label}\n{input}</div>\n{error}",
			            'labelOptions' => ['class' => 'form_label'],
			        ],
			    ]); ?>
			<div class="form-group field-team-team_name required">
				<div class="row input-field">
					 <input type="hidden" name="filterclient" id="filterclient" value='<?php if(!is_array($filterclient)){echo $filterclient;} ?>'>
					 <input type="hidden" name="filterclientcase" id="filterclientcase" value='<?php if(!is_array($filterclientcase)){echo $filterclientcase;} ?>'>
					 <input type="hidden" name="filterstatus" id="filterstatus" value='<?php if(!is_array($filterstatus)){echo $filterstatus;} ?>'>
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
        <div class="row input-field">
					 <div class="col-md-3 col-md-offset-1">
						 <label class="form_label required" for="nolabel-55">Select Client/Cases<span class="require-asterisk">*</span> :</label>
					</div>
					<div class="col-md-7">
					 <div class="custom-inline-block-width" >
		     		    <div class="custom-radio">
							<input type="radio" value="ac" id="allrclient" name="Report[chkclientcases]" <?php if (isset($filter_data->Report->chkclientcases) && ($filter_data->Report->chkclientcases == 'ac')) echo 'checked' ?>><label for="allrclient" class="">By Clients</label>
							</div>
		    
		    		    <div class="custom-radio"><input type="radio" value="selac" id="selectrclientcases" <?php if (isset($filter_data->Report->chkclientcases) && ($filter_data->Report->chkclientcases == 'selac')){ ?> checked="checked" <?php } ?> name="Report[chkclientcases]"><label for="selectrclientcases"  >By Clients/Cases</label></div>
		    		 </div>
		    		 </div>
		</div>
		<div class="col-md-7">
			<div class="help-block col-md-7" id="clientcase_error"></div>
		</div>
		    	
		    		 <div class="col-md-7 col-md-offset-4">
		    		 <div id="displayclient" style="overflow-y: scroll;overflow-x: hidden;max-height: 150px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
                                if (isset($filter_data->Report->chkclientcases) && ($filter_data->Report->chkclientcases == 'ac'))
                                    echo 'block';
                                else
                                    echo 'none';
                                ?>" id="displayclient">
                                	<div class="clearfix">&nbsp;</div>
                                
                                    <ul style="list-style: none;" id="clientul">

                                    </ul>
                                    <div style="border-top:1px solid #DBDBDB;width:580px;padding:10px;">
										<div class="">
											<input id="selectall" name="Report[selectall]" type="checkbox"  class="clientall" onclick="">
											<label for="selectall"></label>
										</div>
										<div class="col-md-3">
											 <label class="form_label" for="nolabel-55">Select All</label>
										</div>
										
                                    </div> <!-- end -->	
                                </div><br/>
		    		</div>		
		    		<div class="col-md-7 col-md-offset-4">
						<div style="overflow-y: scroll;overflow-x: hidden;max-height: 150px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
									if (isset($filter_data->Report->chkclientcases) && ($filter_data->Report->chkclientcases == 'selac'))
										echo 'block';
									else
										echo 'none';
									?>" id="displayclientcases">
										<div class="clearfix">&nbsp;</div>
										<ul style="list-style: none;" id="clientcaseul">

										</ul>
										<div></div>
										<div style="border-top:1px solid #DBDBDB;width:580px;padding:10px;">
											<div class="">
												<input id="checkcases_all" name="Report[checkcases_all]" type="checkbox"  class="clientcaseall" onclick="" <?php if (isset($checkcases_all) && $checkcases_all == '1') {
                                            echo 'checked="checked"';
                                        } else {
                                            echo '';
                                        } ?>>
												<label for="checkcases_all"></label>
											</div>
											<div class="col-md-3">
												 <label class="form_label" for="nolabel-55">Select All</label>
											</div>
										</div>
						</div> <!-- End -->
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
		<button onclick="" title="Clear" class="btn btn-primary" id="clearrequestclientcase" type="button" name="yt1">Clear</button>
		<button onclick="" title="Run" class="btn btn-primary" id="exportrequestclientcase" type="button" name="yt0">Run</button>
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
					$('#ddduration').val(0);
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
					$('#ddduration').val(0);
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

