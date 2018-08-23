<?php

use yii\helpers\Html;
use kartik\widgets\Select2;

\app\assets\CustomInputAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicetask';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-inner-fix">
	<div class="row services-task-top">
		<div class="col-sm-4">
			<?php
			if(!empty($teamService)){ foreach ($teamService as $teamserviceid=>$service_name) {
					$teamservice_data[$teamserviceid] = $service_name;
				}
			}		
				echo Select2::widget([
							'name' => 'teamservice_id',
							'attribute' => 'teamservice_id',
							'data' => $teamservice_data,
							'options' => ['prompt' => 'Select Service','class' => 'form-control','onchange'=>'showservicegrid(this.value,$("#task_hide").prop("checked"));','id'=>'teamservice_id'],
							/*'pluginOptions' => [
							  'allowClear' => true
							]*/
							]);
			
			 ?>
		<input type="hidden" id="team_id" value="<?= $team_id;?>" />
		</div>
		<div class="col-sm-5 pull-right text-right">
                    <input type="checkbox" name="task_hide" id="task_hide" onclick="if(this.checked) { showservicegrid($('#teamservice_id').val(),'1');} else{ showservicegrid($('#teamservice_id').val(),'0');} " />
                    <label for="task_hide">Display Hidden Tasks</label>
		</div>
		<div class="clear"></div>
	</div>
	<div class="admin-grid-element" id="teamservice-gird"></div>
</div>
<div class="button-set text-right">
    <div class="col-sm-8 text-left service-task-btn-set-inner">
        <button class="btn btn-primary" title="Add" id="addServiceTaskForm">Add</button>
        <button class="btn btn-primary" title="Edit" id="editServiceTaskForm">Edit</button>
        <input id="instruction" name="serviceTaskForm" type="radio" value="instruction" checked="checked"> 
        <label for="instruction">Instruction</label> 
        <input id="data" name="serviceTaskForm" type="radio" value="data"> 
        <label for="data">Outcome</label>
    </div>
    <?= Html::button('Remove',['title'=>"Remove",'class' => 'btn btn-primary','onclick'=>'RemoveSelectedServiceTask();'])?>
    <?= Html::button('Add', ['title'=>"Add",'class' => 'btn btn-primary','onclick'=>'AddServiceTask();'])?>
</div>
<script>
$(function() {
  $('input').customInput();
});

function RemoveSelectedServiceTask(){
	var teamservice_id = $('#teamservice_id').val();
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	if(keys=='')
		alert("Please select a record to perform this action.");
	
	var newkeys = keys.toString().split(",");
	var str = [];
	var str_val;
	for(var i=0;i<newkeys.length;i++){
		var chk_value = $('.chk_service_task_'+newkeys[i]).val();
		if(chk_value != undefined){
			var val = JSON.parse(decodeURIComponent($( '.chk_service_task_'+newkeys[i] ).val()));
			str_val =  val['service_task'];
			str.push(str_val);
		}
	}
	var str_length = str.length;
 	if(!keys.length){
 		alert('Please select at least 1 record to perform this action.');
 	}
 	else{
 		if(confirm("Are you sure you want to Remove the selected "+str_length+" record(s): "+str+"?")){
 			jQuery.ajax({
 		       url: baseUrl +'/workflow/checkisserviceuseall',
 		       data:{id:keys,teamservice_id:teamservice_id},
 		       type: 'get',
 		       beforeSend : function(){
 		            showLoader();
 		       },
 		       success: function (response) {
 	 		      if(response=='N'){
	 			    	jQuery.ajax({
	 				       url: baseUrl +'/workflow/deleteservicetaskall',
	 				       data:{keylist: keys,teamservice_id:teamservice_id},
	 				       type: 'post',
	 				       success: function (data) {
	 					       hideLoader();
	 	 				       if(data == 'OK')
	 	 				    	  showservicegridall();
	 	 				       else
	 	 			    		  alert(data);
	 	 			       }
	 				  	});
	 			    }else{
	 					 hideLoader();
	 					 alert(response);
	 				}
 		     	}
 		   });	
		}
 	}
}

function showservicegridall(){
	jQuery.ajax({
	       url: baseUrl +'/workflow/servicetaskajax',
	       type: 'post',
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
                   jQuery('#maincontainer').removeClass('slide-close');
	    	   jQuery('#teamservice-gird').html(data);
	    	   jQuery('#teamservice_id').val(teamservice_id);
	       }
	  });
}
</script>
<noscript></noscript>
