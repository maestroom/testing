<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\widgets\Select2;
    use app\components\IsataskFormFlag;
    \app\assets\SystemSLAAsset::register($this);

    $weekdays = Yii::$app->params['weekdays'];
    $timings = Yii::$app->params['timing_arr'];
    
    $endtimings = $timings;
    unset($endtimings['00:00']);
    $prependtimings['24:00']='12:00 AM';
    $endtimings = $prependtimings+$endtimings;
    
    $workinghours = "";
    $workingdays = array();

    //echo "<prE>",print_r($endtimings),"</pre>";die;
    unset($endtimings['24:00']);
?>
<div class="sub-heading"><a href="javascript:void(0);" title="Business Hours" class="tag-header-black">Business Hours</a></div>
    <?php $form = ActiveForm::begin(['id' => $BusinessHoursModel->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
    <?= IsataskFormFlag::widget(); // change flag ?>
    <fieldset class="one-cols-fieldset">
        <div class="email-confrigration-table sla-bus-hours">
            <div class="row required">
                <div class="col-md-2">
                        &nbsp;
                </div>
                <div class="col-md-10">
                    <div class="col-md-5"><label class="form_label" for="teamserviceslabusinesshours-start_time"><em class='sr-only'>Select Business Hours</em> Start Time<span class="hidden">Required</span></label></div>
                    <div class="col-md-5"><label class="form_label" for="teamserviceslabusinesshours-end_time"><em class='sr-only'>Select Business Hours</em> End Time<span class="hidden">Required</span></label></div>
                </div>
            </div>
            <div class="row required">
                <div class="form-group">
                    <div class="col-md-2">
                        <label class="form_label " for="fEmails">Select Business Hours</label>
                    </div>
                    <div class="col-md-10">
                    <?=$form->field($BusinessHoursModel, 'start_time',['options'=>['tag'=>'div','class'=>''],'template' => '<div class="col-md-5">{input}{error}{hint}</div>','labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                        'data' => $timings,
                        'options' => ['prompt' => 'Select Start Time','aria-label'=>'Start Time','nolabel'=>true,'aria-required'=>'true'],
                        /*'pluginOptions' => [
                            'allowClear' => true
                        ],*/]); ?>

                    <?=$form->field($BusinessHoursModel, 'end_time',['options'=>['tag'=>'div','class'=>''],'template' => '<div class="col-md-5">{input}{error}{hint}</div>','labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                        'data' => $endtimings,
                        'options' => ['prompt' => 'Select End Time','aria-label'=>'End Time','nolabel'=>true,'aria-required'=>'true'],
                        /*'pluginOptions' => [
                            'allowClear' => true
                        ],*/]);?>
                    </div>    
                </div>
            </div>
            
            <div class="form-group field-teamserviceslabusinesshours-workingdays required">
                <div class="row custom-full-width">
                    <div class="col-md-2">
                        <label class="form_label " for="fEmails">Select Business Days</label>
                    </div>
                    <div class="col-md-2">
                        <fieldset class="group">
                            <legend class="sr-only">Select Business Day,</legend>
                        <?php $i= 1; foreach ($weekdays as $day_id=>$day){ ?>
                        <input id="<?=$day?>" class="" name="TeamserviceSlaBusinessHours[workingdays1][]" type="checkbox" value="<?=$day_id?>" <?php if(isset($BusinessHoursModel->workingdays) && $BusinessHoursModel->workingdays!=""){if(in_array($day_id,json_decode($BusinessHoursModel->workingdays,true))){ echo 'checked';}}?> aria-setsize="7" aria-posinset="<?=$i++?>" title="This field is required"><label for="<?=$day?>"><?=$day?></label>
                        <?php } ?>
                        </fieldset>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-2">
                    <?php 
                        $BusinessHoursModel->workingdays = ($BusinessHoursModel->workingdays != '') ? implode(",",json_decode($BusinessHoursModel->workingdays)):'';
                        echo $form->field($BusinessHoursModel, 'workingdays')->hiddenInput()->label(false);
                    ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2"><label class="form_label" for="holiday">Select Business Holidays</label></div>
                    <div class="col-md-3">
                        <div class="input-group calender-group"><input id="demo2" type="text" class="form-control" placeholder="Select Holiday Date" value="" maxlength="10" readonly="readonly" aria-label="Select Holiday Date" /></div>
                    </div>
                    <div class="col-md-3">
                        <input id="holiday" type="text" maxlength="<?php echo $slaHoliday_length['holiday']; ?>"  size="30" class="form-control" placeholder="Enter Holiday Name" aria-label="Enter Holiday Name">
                    </div>
                    <div class="col-md-2">
                        <!--<label class="form_label required" for="eHost">&nbsp;</label>-->
                        <a class="btn btn-primary" title="Add" href="javascript:void(0);" onclick="addHoliday();">Add</a>
                    </div>
                </div>
            </div>
                
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped" id="holiday_table">
                            <tbody>
                                <tr>
                                    <th scope="col" id="sla_holiday_date" class="sla_holiday_date_th"><a href="javascript:void(0);" title="Holiday Date" aria-label="Holiday Date" class="tag-header-black">Holiday Date</a></th>
                                    <th scope="col" id="sla_holiday" class="sla_holiday_th"><a href="javascript:void(0);" aria-label="Holiday" title="Holiday" class="tag-header-black">Holiday</a></th>
                                    <th scope="col" id="sla_actions" class="third-th"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
                                </tr>
                                <?php if(!empty($SlaHolidaysModel)){
                                    foreach ($SlaHolidaysModel as $slaholiday){ ?>
                                        <tr id="<?= $slaholiday->id?>">
                                            <td headers="sla_holiday_date" class="sla_holiday_date_td"><?php echo str_replace('-','/',$slaholiday->holidaydate); ?><input type="hidden" class="holidaydate_input" name="TeamserviceSlaHolidays[holidaydate][]" value="<?php echo str_replace('-','/',$slaholiday->holidaydate);?>" ></td>
                                            <td headers="sla_holiday" class="sla_holiday_td"><?= $slaholiday->holiday?><input type="hidden" name="TeamserviceSlaHolidays[holiday][]" value="<?= $slaholiday->holiday?>"></td>
                                            <td headers="sla_actions" class="text-center third-td"><a aria-label="Remove" href="javascript:RemoveHoliday(<?= $slaholiday->id?>)" class="icon-fa" title="Delete SLA Holiday" aria-label="Delete SLA Holiday"><em title="Delete SLA Holiday" class="fa fa-close text-primary"></em></a></td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
                
    <div class="button-set text-right">
        <?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','id'=>'cancelsubmitbutton']) ?>
        <?= Html::button('Default', ['title'=>"Default",'class' => 'btn btn-primary','id'=>'defaultsubmitbutton']) ?>
        <?= Html::button('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitBusinessHours']) ?>
    </div>
<?php ActiveForm::end(); ?>
<script>
$(function() {
  $('input').customInput();
  $('#active_form_name').val('TeamserviceSlaBusinessHours'); // active form name;
  $('input').bind('input', function(){
	$('#TeamserviceSlaBusinessHours #is_change_form').val('1'); 
	$('#TeamserviceSlaBusinessHours #is_change_form_main').val('1');
  }); 
  $(':checkbox').change(function(){ 
	$('#TeamserviceSlaBusinessHours #is_change_form').val('1'); 
	$('#TeamserviceSlaBusinessHours #is_change_form_main').val('1');
  });
  $('select').on('change', function() {
	$('#TeamserviceSlaBusinessHours #is_change_form').val('1');
	$('#TeamserviceSlaBusinessHours #is_change_form_main').val('1'); 
  });
  datePickerController.createDatePicker({	                     
	formElements: { 
		"demo2": "%m/%d/%Y"
	},
	callbackFunctions:{
		"datereturned" : [changeflag],
	}
  });
});

//get the form id and set the event
$('#submitBusinessHours').bind('click', function(e) {
   var form = $('#TeamserviceSlaBusinessHours');
		$.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            beforeSend : function()    {
            	//$('#submitMediaDataType').attr('disabled','disabled');
                $('#loader').show();
            },
            success: function (response){
            	if(response == 'OK'){
                    commonAjax(baseUrl +'/system/slabusinesshours','admin_main_container');
        			setTitle('fa-wrench','System Management - SLA Business Hours');
                }else{
                    $('#loader').hide();
            		$('.right-main-container').html(response);
				}
            },
            error : function (){
                $('#loader').hide();
                console.log('internal server error');
            }
        });
		return false;
   // do whatever here, see the parameter \$form? is a jQuery Element to your form
});

$('input[name="TeamserviceSlaBusinessHours[workingdays1][]"]:checkbox').on('change',function(){
	var checkedValue = $('input[name="TeamserviceSlaBusinessHours[workingdays1][]"]:checkbox:checked').map(function(){
		return $(this).val();
	}).get().join(',');
	$('#teamserviceslabusinesshours-workingdays').val(checkedValue).change();
});

//get the form id and set the event
$('#defaultsubmitbutton').bind('click', function(e) {
    //$('#submitMediaDataType').attr('disabled','disabled');	
    var form = $('#TeamserviceSlaBusinessHours');
	form.find('#teamserviceslabusinesshours-start_time').val('00:00').trigger('change');
	form.find('#teamserviceslabusinesshours-end_time').val('24:00').trigger('change');
    form.find('#holiday_table tr').not('tr:first').remove();
    $('input[name="TeamserviceSlaBusinessHours[workingdays1][]"]:checkbox').each(function(){
        $(this).attr('checked','checked');
        $(this).next('label').addClass('checked');
    });

	var checkboxValue = $('input[name="TeamserviceSlaBusinessHours[workingdays1][]"]:checkbox:checked').map(function(){
		return $(this).val();
	}).get().join(',');
	$('#teamserviceslabusinesshours-workingdays').val(checkboxValue);
	
	/*$.ajax({
		url    : form.attr('action'),
		type   : 'post',
		data   : form.serialize(),
		beforeSend : function(){
			$('#loader').show();
		},
		success: function (response){
			if(response == 'OK'){
				commonAjax(baseUrl +'/system/slabusinesshours','admin_main_container');
				setTitle('fa-wrench','System Management - Business Hours');
			} 
		},
		complete: function(){
			//$('#submitMediaDataType').removeAttr("disabled");
			$('#loader').hide();
		},
		error : function (){
			console.log('internal server error');
		}
	});*/
	return false;
	// do whatever here, see the parameter \$form? is a jQuery Element to your form
});

/* cancelsubmitbutton */
$('#cancelsubmitbutton').click(function(event){
	var chk_status = checkformstatus(event);
	if(chk_status==true) commonAjax(baseUrl +'/system/slabusinesshours','admin_main_container');
});
</script>