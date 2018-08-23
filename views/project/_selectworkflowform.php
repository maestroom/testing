<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use app\models\TeamserviceSlaBusinessHours;
use app\models\Options;
use app\models\Tasks;
use yii\web\JsExpression;

$timing_arr=Yii::$app->params['timing_arr'];
$disableddates=[];
if(!empty($holidayAr)){
	foreach($holidayAr as $holiday){
		$disableddates[date("Ymd",strtotime($holiday))]=1;
	}
}
//echo "<pre>",print_r(json_encode($disableddates)),"</pre>";die;
$intime = 0;
$start_time = Yii::$app->session['businessStartTime'];
$end_time = Yii::$app->session['businessEndTime'];
$disabled = [];
if($start_time != '00:00' && $end_time != '00:00'){
	foreach($timing_arr as $time => $val){
		if($time == $start_time)
			$intime = 1;
		
		if($intime == 0)
			$disabled[$time] = ['disabled' => true]; 

		if($time == $end_time)
			$intime = 0;	
	}
}
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
$businesshours = TeamserviceSlaBusinessHours::find()->select(['workinghours', 'start_time', 'end_time'])->one();
$workinghours = $businesshours->workinghours;
$task_date = '';
$task_timedue = '';
$hidduedatetime = '';
$date = (new Options)->ConvertOneTzToAnotherTz(date('Y-m-d H:i:s'), 'UTC', $_SESSION['usrTZ'], "YMD"); 
if(isset($flag) && ($flag=='Edit')) {
	
	/*$task_timedue                = (new Options)->ConvertOneTzToAnotherTz($modelInstruct->task_duedate . " " . $modelInstruct->task_timedue, 'UTC', $_SESSION['usrTZ'], "HI");
	$task_date                   = (new Options)->ConvertOneTzToAnotherTz($modelInstruct->task_duedate . " " . $modelInstruct->task_timedue, 'UTC', $_SESSION['usrTZ'], "MDY");
	*/
	$taskduedate = explode(" ",$modelInstruct->task_date_time24);
	$task_date = $taskduedate[0];
	$task_timedue = $taskduedate[1];
	
	$modelInstruct->task_duedate = $task_date;
	$modelInstruct->task_timedue = $task_timedue;
	
	if($modelInstruct->total_slack_hours!=0){
		$hidduedatetime = (new Tasks)->fnGetUpdatedDueDate($task_date, $task_timedue, '-'.$modelInstruct->total_slack_hours);
		if($hidduedatetime!=''){
			$duedateAr = json_decode($hidduedatetime,true);
			$task_date = $duedateAr['current_date'];
			$task_timedue = $duedateAr['current_time'];
		}
	}

	$taskduedate = explode("/",$task_date);
	$date = $taskduedate[2].$taskduedate[0].$taskduedate[1];
}

/* Timezone Date */
$timezone_date = str_replace('-','',$date); 
?>
<style>.multi-pt{visibility: hidden;opacity: 0;height:0px}</style>
<fieldset class="one-cols-fieldset">
    <div class="template_wrkflow-main next">
    	<div class="template_wrkflow-right">
    			<div class="head-title"><a href="javascript:void(0);" class="tag-header-black" title="Add Template / Tasks to Workflow">Add Template / Tasks to Workflow</a>
    			<div class="icon-set pull-right">
					<!-- <a title="Filter Templates/Tasks by Location"  href="javascript:void(0)" onClick="FilterLocation();"><span class="fa fa-filter text-primary"></span><span class="sr-only">Filter Templates/Tasks by Location</span></a> -->
					<?php if(isset($flag) && $flag=='Saved') {}else if(isset($flag) && $flag=='Edit'){} else {?>
						<!-- <a title="Add/Load Previous Workflow"  href="javascript:void(0);" onClick="LoadPrevious('<?= $case_id ?>');"><span class="fa fa-plus-square text-primary"></span><span class="sr-only">Add/Load Previous Workflow</span></a>-->
					<?php } ?> 
						<!-- <span title="Add Estimated Time" class="fa fa-clock-o text-danger pull-right" onClick="javascript:AddManEstimatedTime('<?= $workinghours?>');"></span> -->
                                                <a title="Build Workflow" href="javascript:void(0);" onClick="AddPorjectWorkflow('<?php echo $flag?>');"><span class="fa fa-plus text-primary"></span><span class="sr-only">Build Workflow</span> Build Workflow</a>
					</div>
				</div>
    			<div class="template_wrkflow">
			     	<ul id="service_task_container" class="ui-sortable">
			     	<?php if(!empty($modelInstruct->taskInstructServicetasks)){ ?>
							<li id="first_row">
								<div class="pull-left">
									<input type="checkbox"  id="chkall" name="checkall_workflow" class="left checkall_workflow" aria-label="Select all servicetasks of Workflow">
									<label title="Select All" class="pull-left servicetask-info-label" for="chkall"></label>
								</div>
								<div class="pull-left">
									<span class="pull-left servicetask-info-label">Select All</span>
								</div>
								<span class="est_time_header">SLA/Est Time</span>
								<div class="icon-set pull-right">
									<a  href="javascript:void(0);" onclick="AddManEstimatedTime();">
										<span title="Bulk Add Estimated Time" class="fa fa-clock-o text-primary"></span>
										<span class="sr-only">Bulk Add Estimated Time</span>
									</a>
									<a href="javascript:void(0)" aria-label="remove all tasks" onclick="removealltasks();">
										<span class="fa fa-close text-primary" title="Bulk Delete"></span>
										<span class="sr-only">Bulk Remove</span>
									</a>
								</div>
							</li>
			     		<?php //}
						foreach ($modelInstruct->taskInstructServicetasks as $taskInstructServicetasks){
						//echo "<pre>",print_r($taskInstructServicetasks),"</pre>";
						?>
							<li id="<?=$taskInstructServicetasks->servicetask_id?>" class="li_<?=$taskInstructServicetasks->servicetask_id?> clear">
								<div class="pull-left" style="">
									<input type="checkbox" class="left aaa" name="Service_tasks[]" id="workflow_servicetasks_<?=$taskInstructServicetasks->servicetask_id?>" value="<?=$taskInstructServicetasks->servicetask_id?>">
									<label for="workflow_servicetasks_<?=$taskInstructServicetasks->servicetask_id?>">&nbsp;</label>
								</div>
								
								<input type="hidden" value="<?=$taskInstructServicetasks->id?>" name="ServicetaskInstruct[<?=$taskInstructServicetasks->servicetask_id?>]">
								<input type="hidden" value="<?=$taskInstructServicetasks->team_loc?>" id="stl_<?=$taskInstructServicetasks->servicetask_id?>" class="sloc_<?=$taskInstructServicetasks->servicetask_id?>" name="ServiceteamLoc1[<?=$taskInstructServicetasks->servicetask_id?>][]">
								<span for="Service_tasks_<?=$taskInstructServicetasks->servicetask_id?>" class="pull-left servicetask-info-label" title="ServiceTask">
									<span class="sername_div"><?=$taskInstructServicetasks->teamservice->service_name?><?php if($taskInstructServicetasks->team_loc > 0){ echo " - ".$taskInstructServicetasks->teamLoc->team_location_name; }?><?php echo " - ".$taskInstructServicetasks->servicetask->service_task?></span>
								</span>
								<?php $logic_ids=0;if(!empty($taskInstructServicetasks->taskInstructServicetaskSla)){ foreach ($taskInstructServicetasks->taskInstructServicetaskSla as $sla) { if($logic_ids==0){ $logic_ids =$sla->teamservice_sla_id; }else{ $logic_ids = $logic_ids . ',' .$sla->teamservice_sla_id;} }}?>
								<input type="hidden" value="<?=$logic_ids?>" id="hdn_service_logic_<?=$taskInstructServicetasks->servicetask_id?>" name="hdn_service_logic[<?=$taskInstructServicetasks->servicetask_id?>]">
								<?php if ($taskInstructServicetasks->est_time > 0) {?>
									<input type="text" value="<?=number_format($taskInstructServicetasks->est_time,2,'.','');?>" id="est_time_<?=$taskInstructServicetasks->servicetask_id?>" name="Est_times[<?=$taskInstructServicetasks->servicetask_id?>]" class="right est_time grey"><label for="est_time_<?=$taskInstructServicetasks->servicetask_id?>" style="display:none;">&nbsp;</label>
								<?php } else { ?>
									<input type="text" value="" id="est_time_<?=$taskInstructServicetasks->servicetask_id?>" name="Est_times[<?=$taskInstructServicetasks->servicetask_id?>]" class="right est_time grey"><label for="est_time_<?=$taskInstructServicetasks->servicetask_id?>" style="display:none;">&nbsp;</label>
								<?php }?>
								<div class="icon-set pull-right" style=""><a href="javascript:void(0);" aria-label="Move" class="handel_sort"><span title="Move" class="fa fa-arrows text-primary"></span><span class="sr-only">Move</span></a><a onclick="AddServiceManEstimatedTime(<?=$taskInstructServicetasks->servicetask_id?>);" href="javascript:void(0);" aria-label="Add Service Estimated Time"><span class="fa fa-clock-o text-primary " title="Add Estimated Time"></span><span class="sr-only">Add Estimated Time</span></a><a href="javascript:void(0)" aria-label="remove task" onclick="removestask(<?=$taskInstructServicetasks->servicetask_id?>);"><span class="sr-only">Remove task</span><span title="Delete" class="fa fa-close text-primary"></span></a></div>
							</li>
						<?php }
					}?>
					</ul>
				</div>
				<div class="project-due-date-time">
					<div class="row">
						<div class="col-sm-6">
							<div id="esttime" class="esttimeDiv">
								<div class="left font_12  estprojtime blue" style="display: none;">
									<input type="hidden" value="0" id="txt_esthours">
								</div>
								<div class="clear" ></div>
								<div class="manestleft font_12 estprojtime"  style="display: none;">
									<input type="hidden" value="0" id="txt_manesthours">
								</div>
								<div class="clear" ></div>
								<div class="slackleft font_12 estprojtime"  style="display: none;">
									<input type="hidden" value="0" id="txt_slackhours">
								</div>
								<div class="clear" ></div>
								<div class="projprojecttime_left mar_rig_20 font_12 estprojtime clear"  style="display: none;">
									<input type="hidden" value="0" id="txt_prohours">
								</div>
							</div>
						</div>
	    				<div class="col-sm-6">
	    					<?php if(isset($flag) && $flag=='Edit'){ ?>
	    						<?= $form->field($modelInstruct, 'task_duedate',['template' => "<div class='row input-field text-right'><div class='col-md-3 pull-none'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-5 pull-none calender-group'>{input}</div><div class='pull-none'>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['aria-required'=>"true",'maxlength'=>'10','readonly'=>'readonly','id'=>'duedate','placeholder'=>'Select Due Date', 'aria-required'=>'true','aria-label'=>"Due Date Required"])->label('Due Date');?>	
	    						<?= $form->field($modelInstruct, 'task_timedue',['template' => "<div class='row input-field text-right'><div class='col-md-3 pull-none'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-5 pull-none text-left' id='timedue_parent'>{input}</div><div class='pull-none'>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
								'data' => $timing_arr,
								'options' => ['options'=>$disabled,'prompt' => 'Select Due Time','id'=>'duetime', 'title' => 'Due Time', 'onchange'=>'getslackhours();','nolabel'=>true,'aria-required'=>'true'],
								'pluginOptions' => [
									//'allowClear' => true,
									//'dropdownParent' => new JsExpression('$("#timedue_parent")')
								],])->label('Due Time'); ?>
	    					
	    					   <?php } else { ?>
	    						<?= $form->field($modelInstruct, 'task_duedate',['template' => "<div class='row input-field text-right'><div class='col-md-3 pull-none'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-5 pull-none calender-group'>{input}</div><div class='pull-none'>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['aria-required'=>"true",'maxlength'=>'10','readonly'=>'readonly','placeholder'=>'Select Due Date','aria-required'=>'true','aria-label'=>"Due Date Required"])->label('Due Date');?>	
	    						<?= $form->field($modelInstruct, 'task_timedue',['template' => "<div class='row input-field text-right'><div class='col-md-3 pull-none'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-5 pull-none text-left' id='timedue_parent'>{input}</div><div class='pull-none'>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
								'data' => $timing_arr,
								'options' => ['options'=>$disabled,'prompt' => 'Select Due Time', 'title' => 'Due Time', 'id'=>'taskinstruct-task_timedue', 'onchange'=>'getslackhours();','nolabel'=>true,'aria-required'=>'true'],
								'pluginOptions' => [
									//'allowClear' => true,
									//'dropdownParent' => new JsExpression('$("#timedue_parent")')
								],])->label('Due Time'); ?>
	    					<?php } ?>
	    				</div>
					</div>
				</div>
    	</div>
    </div>
</fieldset>
<div class=" button-set text-right">
  <span id="fl_locs"><?php /*if(isset($optionModel->set_loc) && $optionModel->set_loc!="") { if($filtersavedlocnames!="" && !empty($filtersavedlocnames)) {?>Filtered Location: <?php echo implode(",",$filtersavedlocnames); } }*/?></span>
  <input type="hidden" name="TaskInstruct[total_slack_hours]" value="<?= $modelInstruct->total_slack_hours!=''?$modelInstruct->total_slack_hours:0.00; ?>">
  <input type="hidden" id="workinghours" value="<?= $workinghours?>" />
  <input type="hidden" id="esttime_total" />
  <?php 
  $filter_saved_loc_ids="";
  if(isset($optionModel->set_loc) && $optionModel->set_loc!="") {
	$filter_saved_loc=json_decode($optionModel->set_loc,true);
	if(is_array($filter_saved_loc)){
		$filter_saved_loc_ids=implode(",",$filter_saved_loc);
	}
  } ?>
  <input type="hidden" id="filter_team_location" value="<?=$filter_saved_loc_ids?>">
  <input type="hidden" id="task_duedate_by_st" value="<?= $task_date ?>">
  <input type="hidden" id="task_duetime_by_st" value="<?= $task_timedue ?>">
  <input type="hidden" id="load_prev_project_id" name="load_prev_project_id" value="">
  <input type="hidden" id="service_custom_sort" name="service_custom_sort" value="">

 	<?php if(isset($flag) && $flag=='Saved') {?>

	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"saved","");']) ?>
	<?php }else if(isset($flag) && $flag=='Edit'){ ?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"change",'.$task_id.');']) ?>
	<?php }else{ ?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"","");']) ?>
	<?php }?>
	<?php if($flag!='Edit') {?>
			<?= Html::button('Clear', ['title'=>'Clear','class' => 'btn btn-primary','onclick'=>'ClearTaskWorkflow();']) ?>
	<?php }?>
 <?= Html::button('Previous', ['title'=>'Previous','class' => 'btn btn-primary','onclick'=>'gotostep(1);']) ?>
 <?php if(isset($flag) && ($flag=='Saved' || $flag=='Edit')) {?>
 <?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep3','onclick'=>'loadSaveformBuilder('.$modelInstruct->id.',"'.$flag.'");']) ?>
 <?php }else if(isset($flag) && $flag=='Edit'){} else {?>
 <?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep3','onclick'=>'loadformBuilder();']) ?>
 <?php }?>

</div>
<style>
	td.day{
		position:relative;  
	}
	td.day.disabled:hover:before {
		content: 'This date is disabled';
		color: red;
		background-color: white;
		top: -22px;
		position: absolute;
		width: 136px;
		left: -34px;
		z-index: 1000;
		text-align: center;
		padding: 2px;
	}
</style>
<script>
$('select').on('change',function(){
	$('#is_change_form_main').is('1'); // change form flag
});
<?php if(isset($flag) && $flag=='Edit'){?>
datePickerController.createDatePicker({             
	 formElements: { "duedate": "%m/%d/%Y" },         
	 callbackFunctions:{
		"datereturned":[ function (){
			$('#is_change_form_main').is('1'); // change form flag
			getslackhours();
		},changeflag],
	  },
	  disabledDays:[<?= implode(",",Yii::$app->session['businessDays']) ?>]
});
datePickerController.setRangeLow("duedate",<?= $timezone_date ?>);
<?php if(!empty($disableddates)) {?>
	datePickerController.setDisabledDates("duedate", <?=json_encode($disableddates)?>);
<?php }?>
<?php }else{ ?>
datePickerController.createDatePicker({             
		 formElements: { "taskinstruct-task_duedate": "%m/%d/%Y" },  
		 callbackFunctions:{
			"datereturned":[ function (){
				$('#is_change_form_main').is('1'); // change form flag
				getslackhours();
			},changeflag],
		  },
		  disabledDays:[<?= implode(",",Yii::$app->session['businessDays']) ?>]
	}); 
	datePickerController.setRangeLow("taskinstruct-task_duedate",<?= $timezone_date ?>);
	<?php if(!empty($disableddates)) {?>
		datePickerController.setDisabledDates("taskinstruct-task_duedate", <?=json_encode($disableddates)?>);
	<?php }?>
<?php }?>
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width((parseInt($(this).width())+parseInt(1)));
		});	
	return ui;
	};
$("#service_task_container").sortable({
	items: "li:not(#first_row)",
    connectWith: "#service_task_container",
	handle:'.handel_sort',
	helper: fixHelper,
	stop: function(e,ui) { 
	},
	change: function(event, ui) {
		var list = $(this).closest('ul');
		var topAnchor = $(list).find('#first_row');
		$(list).prepend($(topAnchor).detach());
		$('#is_change_form_main').val('1');
	},
	update: function (event, ui) {
    	updatecustom_sort();
	},
}).disableSelection(); 

function calculateprojectedtime(media, con,  priority, ltype) {
    var service = [];
    var services = [];

    $('#service_task_container li').each(function () {
        var id = $(this).attr('id');
        var loc = $('#service_task_container li .sloc_' + id).val();
        var serviceLoc = {'id': id, 'loc': loc};
        services.push(serviceLoc);
        service.push(id);
    });

    var workingHours = '<?php echo $workinghours ?>';
	
    if (media != "" && media != 0) {

        var current_date = "";
        var current_time = "";

        if (service.length > 0) {
            $.ajax({
                type: "POST",
                url: baseUrl + "project/get-sla-projected-time",
                data: {'priority': priority, 'evidence': media, 'service': services, 'current_date': current_date, 'current_time': current_time},
                beforeSend:function(){
        			showLoader();
        	    },
                success: function (data) {
					//console.log(data.length);return false;
                    if (data != "" && data != 0) {
                        var val = $.parseJSON(data);
                        var needtocalc = 0;
                        service_arr = $.parseJSON(val.service_task);
                   
                        for (var i = 0; i < service_arr.length; i++)
                        {
                            servicedetail = $.parseJSON(service_arr[i]);
							//console.log(servicedetail);
                            if (parseFloat(servicedetail.time) > 0)
                            {
                                needtocalc = 1;
                                $('#est_time_' + servicedetail.service_id).removeAttr('value');
                                $('#est_time_' + servicedetail.service_id).attr('value', servicedetail.time);
                                $('#hdn_service_logic_' + servicedetail.service_id).val(servicedetail.sla_logic);
                                $('#est_time_' + servicedetail.service_id).removeClass('est_sys');
                                $('#est_time_' + servicedetail.service_id).addClass('blue');
                                $('#est_time_' + servicedetail.service_id).removeClass("grey");
                            }
							else if($('#est_time_' + servicedetail.service_id).hasClass('blue')){
								$('#est_time_' + servicedetail.service_id).attr('value', '0.00');
                                $('#hdn_service_logic_' + servicedetail.service_id).val(0);
                                $('#est_time_' + servicedetail.service_id).addClass("grey");
								$('#est_time_' + servicedetail.service_id).removeClass('blue');
                                $('#est_time_' + servicedetail.service_id).removeClass('est_sys');
							}
                            else if ($('#est_time_' + servicedetail.service_id).val() == 0.00)
                            {
                                $('#est_time_' + servicedetail.service_id).attr('value', '0.00');
                                $('#hdn_service_logic_' + servicedetail.service_id).val(0);
								$('#est_time_' + servicedetail.service_id).removeClass('blue');
                                $('#est_time_' + servicedetail.service_id).addClass("grey");
                                $('#est_time_' + servicedetail.service_id).removeClass('est_sys');
                            }
							//console.log($('#est_time_' + servicedetail.service_id).val());
                        }
						if(ltype!='adjustedDateTime'){
							if($('input[name="TaskInstruct[total_slack_hours]"]').val()==0){
								$('#esttime .slackleft').html("");
								$('#esttime .slackleft').hide();
								$('#task_duedate_by_st').val('');
								$('#task_duetime_by_st').val('');
							}
						}
                        workingHours = val.workingHours;
                    } else if (data.replace(/^\s+|\s+$/g, "") == "") {
                        var i = 0;
                        for (i = 0; i <= service.length; i++) {
                            var service_id = service[i];
							//console.log($('#hdn_service_logic_' + service_id).val() === undefined);
							if ($('#hdn_service_logic_' + service_id).val() !== undefined && $('#hdn_service_logic_' + service_id).val() != '' && parseInt($('#hdn_service_logic_' + service_id).val()) != 0) {
								//console.log($('#hdn_service_logic_' + service_id).val() + ' -- ' + $('#hdn_service_logic_' + service_id).val().length);
                                $('#est_time_' + service_id).attr('value', '0.00');
                                $('#hdn_service_logic_' + service_id).val('');
                                $('#est_time_' + service_id).removeClass("blue");
                                $('#est_time_' + service_id).removeClass("grey");
                                $('#est_time_' + service_id).removeClass('est_sys');
                            }
                        }
                    }
                    else {
                        var i = 0;
                        for (i = 0; i <= service.length; i++) {
                            var service_id = service[i];
                            $('#est_time_' + service_id).attr('value', '0.00');
                            $('#hdn_service_logic_' + service_id).val(0);
                            $('#est_time_' + service_id).removeClass("blue");
                            $('#est_time_' + service_id).addClass("grey");
                            $('#est_time_' + service_id).removeClass('est_sys');
                        }
                    }
                },
                complete: function () {
					//console.log(ltype);
                    calculateEstByWorkingHours(workingHours);
					if(ltype!='adjustedDateTime'){
                    	getEstimatedDateTime(workingHours, "est");
					}
                    hideLoader();
                }
            });
        } else {
            $('#taskinstruct-task_duedate').val('');
            var d = new Date();
            var curr_date = d.getDate();
            var curr_month = parseInt(d.getMonth()) + 1;
            var curr_year = d.getFullYear();
            if (curr_date < 10) {
                curr_date = "0" + curr_date;
            }
            if (curr_month < 10) {
                curr_month = "0" + curr_month;
            }
            var dateform = curr_month + "/" + curr_date + "/" + curr_year;
            $("#taskinstruct-task_duedate").datepicker("option", "minDate", dateform);

            $('#taskinstruct-task_timedue').val('');
            $('#esttime .left').hide();
            $('#esttime .manestleft').hide();
            $('#esttime .slackleft').hide();
            $('#esttime .projprojecttime_left').hide();
            hideLoader();
        }
    } else {
        var i = 0;
        for (i = 0; i <= service.length; i++) {
            var service_id = service[i];
            if ($('#hdn_service_logic_' + service_id).val() != 0.00) {
                $('#est_time_' + service_id).attr('value', '');
                $('#hdn_service_logic_' + service_id).val('');
                $('#est_time_' + service_id).removeClass("blue");
                $('#est_time_' + service_id).removeClass("grey");
                $('#est_time_' + service_id).removeClass('est_sys');
            }
        }
        calculateEstByWorkingHours(workingHours);
		if(ltype!='adjustedDateTime'){
       		getEstimatedDateTime(workingHours, "est");
		}
        hideLoader();
    }
}
function ClearTaskWorkflow(){
	$("#service_task_container input").each(function () {
			removestask($(this).val());
	});
	if($("#service_task_container li").length==1){
		$("#service_task_container").empty();	
		if($('#duedate').length){
			$('#duedate').val('');
		}
		if($('#taskinstruct-task_duedate').length){
			$('#taskinstruct-task_duedate').val('');
		}
		if($('#duetime').length){
			$('#duetime').val('').trigger('change');
		}
		if($('#taskinstruct-task_timedue').length){
			$('#taskinstruct-task_timedue').val('').trigger('change');
		}
	}
	$("#esttime").html('<div class="left font_12  estprojtime blue" style="display: none;"><input type="hidden" value="0" id="txt_esthours"></div><div class="clear" ></div><div class="manestleft font_12 estprojtime"  style="display: none;"><input type="hidden" value="0" id="txt_manesthours"></div><div class="clear" ></div><div class="slackleft font_12 estprojtime"  style="display: none;"><input type="hidden" value="0" id="txt_slackhours"></div><div class="clear" ></div><div class="projprojecttime_left mar_rig_20 font_12 estprojtime clear"  style="display: none;"><input type="hidden" value="0" id="txt_prohours"></div>');
							
}
</script>
<noscript></noscript>
