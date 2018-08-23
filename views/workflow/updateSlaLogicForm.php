<?php
	$startlogic=Yii::$app->params['startlogic'];
	$endlogic=Yii::$app->params['endlogic'];
	$duration=Yii::$app->params['duration'];
	$new_timings=array();
?>
<div id="teamserviceform">
    <?= $this->render('_teamserviceslaform', [
        'model' => $model,
    	'modelteamservice' => $modelteamservice,
    	'evidenceType'=>$evidenceType,
    	'projectPriority'=>$projectPriority,
    	'listUnit'=>$listUnit,
    	'teamId' => $teamId,
    	'teamLocation' => $teamLocation,
    	'data'=>$data,
    	'action'=>'Edit'
    ]) ?>
	</div>
<script>
$(document).ready(function(){

	$('.qtylogic').on("keydown",function(e){
		onlyNumber(e,'keydown',$(this));
	});
	$('.qtylogic').on("blur",function(e){
		onlyNumber(e,'blur',$(this));
	});

	$('select[name="TeamserviceSla[size_start_unit_id]"]').on("change",function(){
		if($(this).val() != ""){
			$('#increment_unit').html("("+$('select[name="TeamserviceSla[size_start_unit_id]"] option:selected').text()+")");
		} else {
			$('#increment_unit').html("");
		}
	});	
});

function onlyNumber(e,event,element){
	if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
	         // Allow: Ctrl+A
	        (e.keyCode == 65 && e.ctrlKey === true) || 
	         // Allow: home, end, left, right, down, up
	        (e.keyCode >= 35 && e.keyCode <= 40)) {
	             // let it happen, don't do anything
	             return;
	    }
	    // Ensure that it is a number and stop the keypress
	    if ((e.keyCode==190 || e.keyCode==110 || e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
	        e.preventDefault();
	    }
	if(event == 'blur'){
		if(element.val() == "" || element.val() == 0){
			element.val(1);
		} 
	}    
}

function changeUnitorType(to,value){
	$('#teamservicesla-'+to).val(value);
}
</script>
<noscript></noscript>
