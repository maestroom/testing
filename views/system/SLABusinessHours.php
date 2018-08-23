
<div class="right-main-container">		

	<?= $this->render('businessHours',[
		'BusinessHoursModel'=>$BusinessHoursModel,
		'SlaHolidaysModel'	=>$SlaHolidaysModel,
		'slaHoliday_length' => $slaHoliday_length
	]) ?>

</div>
<script>

function addHoliday() {
	var flag_holiday = true;
	var holiday_date    = $('#demo2').val();
	var holiday = $('#holiday').val();
	$(".holidaydate_input").each(function() {
		if($(this).val() == holiday_date) {
		  flag_holiday = false;
		  return false;
		}
	});
	if(flag_holiday == false){
		alert('This Holiday Date already exists.');
		return false;
	}
	
	var time = new Date();
	var time = time.getTime(); 
	if(holiday_date !="" && holiday!="" && flag_holiday == true){
		$('#demo2').val('');
		$('#holiday').val('');
		$('#holiday_table > tbody').append('<tr id="'+time+'"><td>'+holiday_date+'<input class="holidaydate_input" type="hidden" name="TeamserviceSlaHolidays[holidaydate][]" value="'+holiday_date+'"></td><td>'+holiday+'<input type="hidden" name="TeamserviceSlaHolidays[holiday][]" value="'+holiday+'"></td><td align="center"><a aria-label="Remove" href="javascript:RemoveHoliday('+time+')" class="icon-fa" title="Delete Sla Holiday"><em title="Delete Sla Holiday" class="fa fa-close text-danger"></em></a></td></tr>')
	}
}

function RemoveHoliday(id){
	$('#TeamserviceSlaBusinessHours #is_change_form').val('1');
	$('#TeamserviceSlaBusinessHours #is_change_form_main').val('1');
	$('#'+id).remove();
}


</script>
<noscript></noscript>
