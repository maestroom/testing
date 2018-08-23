<?php
	use yii\helpers\Html;
	use yii\helpers\ArrayHelper;
	use yii\bootstrap\ActiveForm;
	use kartik\grid\GridView;
	use kartik\grid\datetimepicker;
		
	// select invoice criteria
	$this->title = 'Select Invoice Criteria';
	//print_r($filter_data);
?>
<div class="right-main-container">
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<fieldset class="one-cols-fieldset">
<?php $form = ActiveForm::begin([
    'id' => 'add-invoicemanagementdata-form',
    'action' => '@web/index.php?r=billing-generate-invoice/display-generate-invoice',
]); ?>
<div class="create-form">
	<div class="form-group field-team-team-name">
		<div class="row input-field required" style="margin-bottom: 7px;">
			<div class="col-md-3">
				<label for="team-team_name" class="form_label" id="sel-bil-ite-1"> Select Billable Items Date Range<span class="require-asterisk">*</span></label>
				<span class="screenreader" id="sel-bil-ite-0">Select Billable Items Date Range Required</span>
				<!--<span class="screenreader" id="sel-bil-ite-2">Select Start Date</span>
				<span class="screenreader" id="sel-bil-ite-3">Select End Date</span>-->
			</div>
			<div class="col-md-2">
				<div class="input-group calender-group">
					<input type="text" class="form-control" name="start_date" id="start_date" value="<?php echo $filter_data['start_date']; ?>" readonly="readonly" placeholder="Select Start Date" aria-labelledby="sel-bil-ite-0" />
				</div>	
				<div class="help-block" class="form-control" id="start_date_error"></div>
			</div>
			<div class="col-md-2">
				<div class="input-group calender-group">
					<input type="text" class="form-control" name="end_date" id="end_date" value="<?php echo $filter_data['end_date']; ?>" readonly="readonly" placeholder="Select End Date" aria-labelledby="sel-bil-ite-0"  value="" />
				</div>	
				<div class="help-block" id="end_date_error"></div>
			</div>
		</div> 

		<div class="row input-field required">
			<div class="col-md-3">
                            <label class="form_label required" for="nolabel-55">Select Clients/Cases<span class="require-asterisk">*</span></label>
			</div>
<!--			<span id="select_client_case" style="display:none;"></span>-->
				<div class="col-md-8">
				<fieldset>
					<div class="custom-inline-block-width">
						<legend class="sr-only">Select Client/Cases Required</legend>
						<div class="row">
							<div class="col-md-3">
								<div class="custom-radio">
									<input  aria-setsize="2" aria-posinset="1" type="radio" value="ALL" class="select_client" id="selectclient" aria-required="true" title="This field is required" name="chkclientcases" <?php if($filter_data['chkclientcases']=='ALL'){echo "checked"; } else{  if($filter_data['chkclientcases']!='Selected'){echo "checked"; } }  ?>/>
									<label for="selectclient">All Clients/Cases</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="custom-radio">
									<input type="radio"  aria-setsize="2" aria-posinset="2" value="Selected" class="select_client" id="selectedclientcases" aria-required="true" title="This field is required" name="chkclientcases" <?php if($filter_data['chkclientcases']=='Selected'){echo "checked"; } ?>/>
									<label for="selectedclientcases">Selected Clients/Cases</label>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row input-field required" style="display:<?php if($filter_data['chkclientcases']=='Selected'){
						echo "block";
					} else{ echo "none"; } ?>" id="displaystatusclient">	
			<div class="col-md-3">
			</div>	
			<!-- Get all client/cases -->
			<div class="col-md-8">
				<!-- <div id="displayclientcase_list" style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;"> -->
					<div id="displayclientcase_list" style="margin-top:5px;border:1px solid #DBDBDB;width:510px;">
						<span>
						<fieldset>
							<legend class="sr-only">Selected Clients/Cases</legend>
							<div class='by_clientcases' id="by_clientcases" style='width: 100%!important;height:auto!important;'>

							</div>
							<?php if(!empty($filter_data['clientcases'])){?>
							<input type="hidden" value="<?php echo implode(",",$filter_data['clientcases']);?>" id="selectedclientcasedata"/>
							<?php }?>
							<?php /*?><ul class='by_clientcases custom-full-width' id="by_clientcases" style='width: 100%!important;list-style:none;'>
								<li>
									<input id="clientcasesall" class="form-control" name="Report[statusall]" type="checkbox"  class="statusall">
									<label class="form_label" for="clientcasesall">Select All/None</label>
								</li>
							<?php $i=1; foreach($client_data_case as $key => $clientcase){ ?>
								<li>
									<?php 
										$checked=""; 
											if(!empty($filter_data['clientcases'])){
												foreach($filter_data['clientcases'] as $invoiceBatch){
													if($invoiceBatch==$key){
														$checked = 'checked="checked"';
													}
												}
											}
									?>
									<input type="checkbox" <?php echo $checked; ?> id="clientcases_<?php echo $key; ?>" name="clientcases[]" class="clientcases" value="<?php echo $key; ?>" aria-label="<?php echo $clientcase; ?>" />
									<label for="clientcases_<?php echo $key; ?>" class="clientcases"><?php echo $clientcase; ?></label>
								</li>
							<?php } ?>
							</ul>
							<a href="javascript:yHandler();" id="LM" style="display:none;margin: 0px 10px;">Load More</a>
							<?php */?>
                                                    </fieldset>    
						</span>
						<?php /*?><div style=""><div class="col-md-3 custom-full-width"><input id="clientcasesall" class="form-control" name="Report[statusall]" type="checkbox"  class="statusall"><label class="form_label" for="clientcasesall">Select All</label></div></div><?php */?>
				</div>
			<div class="help-block" id="clientcases_error"></div>
			</div>
			<!-- End -->
		</div>
	
	
	<!-- Invoice By -->
	<div class="row input-field required">
		<div class="col-md-3">
			<label class="form_label required" for="nolabel-55" id="lbl-dis-inv-by">Display Invoice By<span class="require-asterisk">*</span></label>
			<span class="screenreader" id="lbl-dis-inv-by-1">Itemized</span>
			<span class="screenreader" id="lbl-dis-inv-by-2">Consolidated</span>
		</div>
		<span id="select_client_case" style="display:none;"></span>
		<div class="col-md-7">
			<fieldset>
			<legend class="sr-only">Display Invoice By Required</legend>
			<div class="custom-inline-block-width">
				<div class="row">
					<div class="col-md-2">
						<div class="custom-radio">
							<input  aria-setsize="2" aria-posinset="1" type="radio" value="1" class="select_display_by" id="select_itemized" aria-required="true" title="This field is required" name="chkinvoiced" <?php if($filter_data['chkinvoiced']!='' && $filter_data['chkinvoiced']==1){echo "checked";} else{ if($filter_data['chkinvoiced']!=2){ echo "checked";} }?>>
							<label for="select_itemized" aria-label="Display Invoice By required, Itemized">Itemized</label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="custom-radio">
							<input  aria-setsize="2" aria-posinset="2" type="radio" value="2" class="select_display_by" id="select_consolidate" aria-required="true" title="This field is required" name="chkinvoiced" <?php if($filter_data['chkinvoiced']!='' && $filter_data['chkinvoiced']==2){echo "checked";}?>>
							<label for="select_consolidate" aria-label="Display Invoice By required, Consolidated">Consolidated</label>	
						</div>
					</div>
				</div>
				<div class="help-block" id="display_invoice_error"></div>
			</div>
			</fieldset>
          </div>
          <div class="clearfix">&nbsp;</div>
    </div>
	<!-- End -->
		
    <!-- Teams -->
	<div class="row input-field">
		<div class="col-md-3"><label class="form_label" for="nolabel-55" id="lbl-dis-inv-by">Filter Invoice By</label></div>
		<div class="col-md-8">
			<div class="custom-checkbox">
				<input type="checkbox" value="teams" class="select_teams" id="select_teams" name="chkteams" aria-label="Filter Invoice By Teams" <?php
                       if ($filter_data['chkteams'] == 'teams') {
                           echo "checked";
                       }
                       ?> />
                <label for="select_teams" style="margin-left: 0px;"><span class="sr-only">Filter Invoice By Teams</span><span class='pt-1 pl-0 pull-left' style="margin-top: -1px; margin-left: 22px;">Teams</span></label>
			
			</div>	
			
		</div>
	</div>
    <?php /*?>
	<div class="row input-field">
        <div class="col-md-2 col-md3-chk2 pr-0">
            <div class="custom-checkbox">
                <input type="checkbox" value="teams" class="select_teams" id="select_teams" name="chkteams" aria-label="Filter Invoice By Teams" <?php
                       if ($filter_data['chkteams'] == 'teams') {
                           echo "checked";
                       }
                       ?> />
                <label for="select_teams" style="margin:5px 0 0 -4px;" class="chkbox-global-design">Filter Invoice By <span class="sr-only">Teams</span></label>
            </div>
        </div>
        <span class='pt-1 pl-0 pull-left' style="margin-left:-13px">Teams</span>
        <span id="select_teams_block" style="display:<?php if ($filter_data['chkteams'] == 'teams') {
            echo "block";
        } else {
            echo "none";
        } ?>"></span>
    </div>
    <?php */?>
	<div class="row input-field">
        <!-- Get all Teams -->
        <div class="col-md-3"></div>
        <div class="col-md-8">
            <div id="displayteams" style="overflow-y: scroll;overflow-x: hidden;max-height: 114px !important;margin-top:5px;border:1px solid #DBDBDB;width:510px;display:<?php
        if ($filter_data['chkteams'] == 'teams') {
            echo "block";
        } else {
            echo "none";
        }
            ?>">
                    <!--<span id="select_client_case" style="display:none;"></span>-->
                <span>
                    <fieldset>
                        <legend class="sr-only">Filter Invoice By Teams</legend>
                        <ul class='by_teams custom-full-width' id="by_teams" style='width: 100%!important;list-style:none;'>
                            <li>
                                <input id="teamstatusall" class="form-control" name="Report[statusall]" type="checkbox"  class="teamstatusall" aria-label="Select All/None">
                                <label class="form_label" for="teamstatusall">Select All/None</label>
                            </li>
                                <?php $i = 1;
                                foreach ($teams as $key => $team) { ?>
                                <li>
    <?php
    $teamchecked = "";
    if (!empty($filter_data['teams'])) {
        foreach ($filter_data['teams'] as $teamsdata) {
            if ($teamsdata == $key) {
                $teamchecked = 'checked="checked"';
            }
        }
    }
    ?>
                                    <input type="checkbox" <?php echo $teamchecked; ?> id="teams_<?php echo $key; ?>" name="teams[]" class="teams" value="<?php echo $key; ?>" aria-label="<?php echo $team; ?>" />
                                    <label for="teams_<?php echo $key; ?>" class="teams" ><?php echo $team; ?></label>
                                </li>
<?php } ?>
                        </ul>
                    </fieldset>
                </span>
            </div>
            <div class="help-block" id="teams_error"></div>
            <div class="clearfix">&nbsp;</div>
        </div>
        <!-- End -->
    </div>
			<div class="clearfix">&nbsp;</div>
		</div>
	</div>
</fieldset>
<div class=" button-set text-right">
    <!--<button title="Display Saved Invoices" class="btn btn-primary" id="saveinvoiced" type="button" name="yt2" onClick="savedinvoice();">Display Saved Invoices</button>-->
    <button onclick="" title="Clear" class="btn btn-primary" id="clearrequestinvoiced" type="button" name="yt1">Clear</button>
    <button title="Run" class="btn btn-primary" id="runinvoiced" type="button" name="yt0" onClick="runinvoice();">Run</button>
	<input type="hidden" id="page" value="1">
	<input type="hidden" id="haspage" value="1">
</div>
<?php ActiveForm::end(); ?>
<?php if(!empty($filter_data['clientcases'])){?>
<script>
var targetUrl = baseUrl + 'billing-generate-invoice/get-clientcasedata';
showLoader();
if($("#selectedclientcasedata")){
	selectedclientcasedata = $("#selectedclientcasedata").val();
}else{
	selectedclientcasedata= 0;
}
jQuery.ajax({
	url:  targetUrl,
	data: {page:$('#page').val(),'selectedclientcasedata':selectedclientcasedata},
	type: 'post',
	contentType: "application/x-www-form-urlencoded; charset=UTF-8",
	beforeSend:function (data) {showLoader();},
	success: function (data) {
		hideLoader();
		$('#by_clientcases').html(data);
		//$('#by_clientcases').append(data);
		//$('#by_clientcases').find('input').customInput();
		//$('#page').val(parseInt($('#page').val())+1);
		//$("#LM").show();
		//console.log(data);
	} 
});
</script>
<?php }?>
<script>
function runinvoice()
{
    var error="<strong>Please Fix Below Given Error:-</strong><br><br>";
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    
    if(start_date==''){
        error+="- Select Start Date. <br>";
		$('#start_date_error').html('Start Date cannot be blank.');
		$('#start_date_error').parent().addClass('has-error');
    } 
    
    if(end_date==''){
        error+="- Select end date. <br>";
		$('#end_date_error').html('End Date cannot be blank.');
		$('#end_date_error').parent().addClass('has-error');
    }
         
    if (!$('#selectedclientcases').is(":checked") && !$('#selectclient').is(":checked"))
    {
		//if(!$('.clientcases').is(":checked")){
		if($.trim($('#clientCasesToInput').val()) == "") {
			error+="- Select client case. <br>";
            $('#clientcases_error').html('Select Clients/Cases.');
            $('#clientcases_error').parent().addClass('has-error');
        }
    }
    
    if($('#selectedclientcases').is(":checked")){
	    //if(!$('.clientcases').is(":checked")){
		if($.trim($('#clientCasesToInput').val()) == "") {	
            error+="- Select client case. <br>";
            $('#clientcases_error').html('Select Clients/Cases.');
            $('#clientcases_error').parent().addClass('has-error');
        }
    }
    
    if(!$('.select_display_by').is(":checked")){
        error+="- Select Display Invoice By. <br>";
        $('#display_invoice_error').html('Select Display Invoice By.');
        $('#display_invoice_error').parent().addClass('has-error');
    }
    
    if(error!="" && error!='<strong>Please Fix Below Given Error:-</strong><br><br>'){
     //   openPopup();// Open a popup to Display Errors
        //$('#errorContent').html(error);
    }else{
        console.log('submit');
        //$('#add-invoicemanagementdata-form').submit();

		var targetUrl = baseUrl + 'billing-generate-invoice/display-generate-invoice';
		showLoader();
		//location.href = baseUrl +'billing-generate-invoice/billing-invoice-management';
		jQuery.ajax({
			url:  targetUrl,
			type: 'post',
			data: $('#add-invoicemanagementdata-form').serialize(),
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				//jQuery('.right-side').html(data);
				$('#admin_main_container').html(data);
				$('#deletesavedinvoice_li').hide();
				$('#finalizedinvoice_li').show();
				//$('input').customInput();
			} 
		});
	}	
}

/**
 * Date picker for start_date and end_date
 */
$(function () {
	var start_date = datePickerController.createDatePicker({             
	formElements: { "start_date": "%m/%d/%Y" },         
	callbackFunctions:{
		"dateset":[ function (){
				var start_value = $('#start_date').val();
				if(start_value.length > 0){
					$('#ddduration').val('0');
				}
				$('#start_date_error').empty();
				$('#start_date_error').parent().removeClass('has-error');
				
				var sv = datePickerController.getSelectedDate("start_date");
				if(sv != null){
					//var edate= sv.getFullYear()+(parseInt(sv.getMonth())+1)+sv.getDate();
					//console.log(edate);
					datePickerController.setRangeLow("end_date", sv);
				}
			}],
        }
	});   
        
	var end_date = datePickerController.createDatePicker({             
		formElements: { "end_date": "%m/%d/%Y" },   
		callbackFunctions:{
			"dateset":[ function (){
				var end_value = $('#end_date').val();
				if(end_value.length > 0){
					$('#ddduration').val('0');
				}
				$('#end_date_error').empty();
				$('#end_date_error').parent().removeClass('has-error');
				
				var sv = datePickerController.getSelectedDate("end_date");
				if(sv != null){
					//var edate= sv.getFullYear()+(parseInt(sv.getMonth())+1)+sv.getDate();
					//console.log(edate);
					datePickerController.setRangeHigh("start_date", sv);
				}
			}],
		}      
	});
	$('.select_client').click(function() {
		//if($('#by_clientcases li').length ==1) {
		if($.trim($('#by_clientcases').html())=='') {	
			if($.trim(this.value) == 'Selected' &&  this.checked==true) {
				if($("#selectedclientcasedata")){
					selectedclientcasedata = $("#selectedclientcasedata").val();
				}else{
					selectedclientcasedata= 0;
				}
				var targetUrl = baseUrl + 'billing-generate-invoice/get-clientcasedata';
				showLoader();
				jQuery.ajax({
					url:  targetUrl,
					data: {page:$('#page').val(),'selectedclientcasedata':selectedclientcasedata},
					type: 'post',
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						hideLoader();
						$('#by_clientcases').html(data);
						//$('#by_clientcases').append(data);
						//$('#by_clientcases').find('input').customInput();
						//$('#page').val(parseInt($('#page').val())+1);
						//$("#LM").show();
						//console.log(data);
					} 
				});
			}
		}
		if($.trim(this.value) == 'ALL' &&  this.checked==true) {
			if($("#bgitree").length){
                $("#bgitree").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#clientCasesToInput').val(null);
            }
		}
	});

	/*$('#displayclientcase_list').on('scroll',function(){
		var obj=this;
		if( obj.scrollTop == (obj.scrollHeight - (obj.offsetHeight))) {
			if(parseInt($('#haspage').val())==1)
				yHandler();
		}
	});*/
});	 
function yHandler(){
	if(parseInt($('#haspage').val())==1) {
				var targetUrl = baseUrl + 'billing-generate-invoice/get-clientcasedata';
				showLoader();
				jQuery.ajax({
					url:  targetUrl,
					data: {page:$('#page').val()},
					type: 'post',
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					beforeSend:function (data) {showLoader();},
					success: function (data) {
						hideLoader();
						if($.trim(data)=="NO"){
							$('#haspage').val(0);
							$("#LM").hide();
						}else{
							$('#by_clientcases').append(data);
							$('#by_clientcases').find('input').customInput();
							$('#page').val(parseInt($('#page').val())+1);
						}
						//console.log(data);
					} 
				});
	}
}
$('#selectedclientcases').click(function(){
	$('#displaystatusclient').css('display','block');
	$('#clientcases_error').empty();
    $('#clientcases_error').parent().removeClass('has-error');
});

$('#selectclient').click(function(){
	$('#displaystatusclient').css('display','none');
    $(".clientcases").val('');
    $('.clientcases:checkbox').removeAttr('checked');
    $('#clientcases_error').empty();
    $('#clientcases_error').parent().removeClass('has-error');
});

/**
 * select all client/cases checkbox
 */
$('#clientcasesall').click(function(){
	if($('#clientcasesall').is(':checked')){
		$('.clientcases').prop('checked',true);
		$('.clientcases').addClass('checked');
	} else {
		$('.clientcases').prop('checked',false);
		$('.clientcases').removeClass('checked');
	}
	
});

/**
 * Select all teams checkbox
 */
$('#teamstatusall').click(function(){
	if($('#teamstatusall').is(':checked')){
		$('.teams').prop('checked',true);
		$('.teams').addClass('checked');
	} else {
		$('.teams').prop('checked',false);
		$('.teams').removeClass('checked');
	}
});

$('#select_teams').click(function(){
	if($('#select_teams').is(':checked') == true){
		$('#displayteams').css('display','block');
	}
	if($('#select_teams').is(':checked') == false){
		$("#teams").val('');
		$('input[name="teams[]"]:checked').each(function(){
			$(this).prop('checked',false);
			$(this).next('label').removeClass('checked');
		});
		$('#displayteams').css('display','none');
	}
});

$('.select_display_by').click(function(){
	$('#display_invoice_error').empty();
    $('#display_invoice_error').parent().removeClass('has-error');
});

/*
 * Clear button functionality 
 */
$('#clearrequestinvoiced').click(function(){
	$('#start_date').val('');
	$('#end_date').val('');
    $("#clientcase").val('');
    $("#teams").val('');
   	$('#select_consolidate').siblings().removeClass('checked');
	$('#selectedclientcases').siblings().removeClass('checked');
	$('#by_clientcases').html(null);
	$('#selectedclientcasedata').val('');
	$('#displaystatusclient').css('display','none');
	$('#displayteams').css('display','none');
	$('input:checkbox').removeAttr('checked');
	$('#select_teams').siblings().removeClass('checked');
	$('#select_itemized').siblings().removeClass('checked');
	
	$("#teamstatusall").prop('checked',false);
	$("#teamstatusall").next('label').removeClass('checked');
	$('.teams').each(function(){
			$(this).prop('checked',false);
			$(this).next('label').removeClass('checked');
	});
	$("#clientcasesall").prop('checked',false);
	$("#clientcasesall").next('label').removeClass('checked');
	$('.clientcases').each(function(){
			$(this).prop('checked',false);
			$(this).next('label').removeClass('checked');
	});
});
</script>
<noscript></noscript>
